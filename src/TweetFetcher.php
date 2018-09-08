<?php

/**
 * @file
 * Contains \Drupal\twitteroauth\TweetFetcher.
 */
namespace Drupal\twitteroauth;

use Abraham\TwitterOAuth\TwitterOAuth;
use Drupal\Component\Utility\Html;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class TweetFetcher
 *
 * @package Drupal\twitteroauth
 */
class TweetFetcher {

  /**
   * Value indicating media should be displayed.
   */
  const DISPLAY_MEDIA_VALUE = '1';

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * @var \Abraham\TwitterOAuth\TwitterOAuth|bool
   */
  protected $client;

  /**
   * @var boolean
   */
  protected $validClientConnection;

  /**
   * Creates a new TweetFetcher.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactory $loggerFactory
   *  Logger factory.
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Config factory.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   Cache backend.
   */
  public function __construct(LoggerChannelFactory $loggerFactory, ConfigFactory $configFactory, CacheBackendInterface $cacheBackend) {
    $this->logger = $loggerFactory->get('twitteroauth');
    $this->config = $configFactory->get('twitteroauth.settings');
    $this->cache = $cacheBackend;
    $this->validClientConnection = FALSE;
    $this->client = $this->getClient();
  }

  /**
   * Determine if connection to twitter client was successful.
   *
   * @return boolean
   */
  public function validClientConnection() {
    return $this->validClientConnection;
  }

  /**
   * Fetch tweets.
   *
   * @param string $count
   * @param string $searchOperators
   * @param string $displayMedia
   * @param int $cacheExpiration
   *
   * @return array|NULL
   */
  public function fetch($count, $searchOperators, $displayMedia, $cacheExpiration) {
    if ($this->validClientConnection == FALSE) {
      return [];
    }

    if (empty($cacheExpiration)) {
      $cacheExpiration = 60;
    }

    $cid = $searchOperators . $displayMedia . $count . $cacheExpiration;
    if ($cached_data = $this->cache->get($cid)) {
      return $cached_data->data;
    }

    $searchOperators .= ' ' . trim($this->config->get('default_search_operators'));

    $requestParameters = [
      'q' => Html::escape($searchOperators),
      'count' => $count,
    ];

    $twitter_response = $this->client->get(
      'search/tweets',
      array_merge($requestParameters, $this->getDefaultRequestParameters())
    );

    if (!$this->isInvalidResponse($twitter_response)) {
      return NULL;
    }

    $tweets = [];

    foreach ($twitter_response->statuses as $id => $tweet) {
      $tweet_text = substr(
        $tweet->full_text,
        $tweet->display_text_range['0'],
        $tweet->display_text_range['1']
      );
      $content['#display_text'] = $this->buildRichTextRenderArray($tweet_text);
      $content['#tweet_link'] = trim(
        substr($tweet->full_text, $tweet->display_text_range['1'],
        strlen($tweet->full_text))
      );
      if (!empty( $tweet->extended_entities) && $displayMedia === self::DISPLAY_MEDIA_VALUE) {
        $content['#media_url'] = $tweet->extended_entities->media['0']->media_url_https;
      }
      $content['#screen_name'] = $this->buildRichTextRenderArray($tweet->user->screen_name);
      $content['#name'] = $this->buildRichTextRenderArray($tweet->user->name);
      $content['#created_at'] = $tweet->created_at;
      $content['#result_index'] = $id;
      $content['#theme'] = 'twitteroauth_content';
      $tweets[$id] = $content;
    }

    if (!empty($tweets)) {
      $this->cache->set($cid, $tweets,  time() + ($cacheExpiration * 60), []);
      $tweets_wrapper = [
        '#items' => $tweets,
        '#theme' => 'twitteroauth_content_wrapper',
      ];
      return $tweets_wrapper;
    }
  }

  /**
   * Validates response object from twitter API.
   *
   * @param $response
   *
   * @return boolean
   */
  protected function isInvalidResponse($response) {
    if (isset($response->errors)) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Builds render array to properly handle rich text input.
   *
   * @param $text
   * @param string $format
   *
   * @return array
   */
  protected function buildRichTextRenderArray($text, $format = 'basic_html') {
    return [
      '#type' => 'processed_text',
      '#text' => $text,
      '#format' => $format,
    ];
  }

  /**
   * Get default parameters for making API calls to twitter.
   */
  protected function getDefaultRequestParameters() {
    return [
      'lang' => 'en',
      'tweet_mode' => 'extended',
    ];
  }

  /**
   * Get twitter client object.
   *
   * @return \Abraham\TwitterOAuth\TwitterOAuth|bool
   *   An instantiated twitter oauth object; otherwise FALSE if an error occurred.
   */
  protected function getClient() {
    if ($credentials = $this->getCredentials()) {
      $client = new TwitterOAuth(
        $credentials['consumer_key'],
        $credentials['consumer_secret'],
        $credentials['access_token'],
        $credentials['access_token_secret']
      );
      if ($client) {
        $this->validClientConnection = TRUE;
        return $client;
      }
    }
  }

  /**
   * Validates that required credentials have been set.
   *
   * @return array|null
   */
  protected function getCredentials() {
    $requiredCredentials = [
      'consumer_key',
      'consumer_secret',
      'access_token',
      'access_token_secret',
    ];

    foreach ($requiredCredentials as $key => $credentialName) {
      if ($credential = $this->config->get($credentialName)) {
        $requiredCredentials[$credentialName] = $credential;
        unset($requiredCredentials[$key]);
      }
      else {
        $this->logger->notice('Invalid twitter application credentials.');
        return NULL;
      }
    }

    return $requiredCredentials;
  }

}
