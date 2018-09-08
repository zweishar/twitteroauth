# Twitteroauth

Allows for creation of custom blocks which display results from the Twitter search API. Once installed, this module adds a custom block type called "Twitter Search". This block allows you to use Twitter's [standard search operators](https://developer.twitter.com/en/docs/tweets/search/guides/standard-operators.html) in order to specify what types of search results to pull back.

## Usage instructions

- Once installed, navigate to `admin/config/services/twitteroauth/settings` to enter your Twitter API keys. If you do not yet have Twitter API keys generated for your application, visit https://apps.twitter.com/ and create a new twitter application.
- Next, navigate to `block/add/twitteroauth_search` in order to add your first twitter search block.
- Finally, navigate to `admin/structure/block` and place your newly created custom block in a region of your theme.
