<?php

namespace Drupal\twitteroauth\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure twitteroauth settings.
 */
class TwitterOauthSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'twitteroauth_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'twitteroauth.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('twitteroauth.settings');

    $form['consumer_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Consumer Key'),
      '#default_value' => $config->get('consumer_key'),
      '#required' => TRUE,
    ];

    $form['consumer_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Consumer Secret'),
      '#default_value' => $config->get('consumer_secret'),
      '#required' => TRUE,
    ];

    $form['access_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Access Token'),
      '#default_value' => $config->get('access_token'),
      '#required' => TRUE,
    ];

    $form['access_token_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Access Token Secret'),
      '#default_value' => $config->get('access_token_secret'),
      '#required' => TRUE,
    ];

    $form['default_search_operators'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default Search Operators'),
      '#default_value' => $config->get('default_search_operators'),
      '#description' => $this->t('Enter search operators that should be merged into the query of every twitter search block. An example would be filter:safe which instructs Twitter not to return potentially inappropriate content.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable('twitteroauth.settings')
      ->set('consumer_key', $form_state->getValue('consumer_key'))
      ->set('consumer_secret', $form_state->getValue('consumer_secret'))
      ->set('access_token', $form_state->getValue('access_token'))
      ->set('access_token_secret', $form_state->getValue('access_token_secret'))
      ->set('default_search_operators', $form_state->getValue('default_search_operators'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
