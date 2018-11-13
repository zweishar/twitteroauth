CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

Allows for creation of custom blocks which display results from the Twitter
search API. Once installed, this module adds a custom block type called "Twitter
Search". This block allows you to use Twitter's standard search operators in
order to specify what types of search results to pull back.

This module does not come packaged with any associated styling. If you wish to
update the styling or layout, you can copy the template file bundled with this
module into your theme in order to override it.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/twitteroauth

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/twitteroauth


REQUIREMENTS
------------

This module requires the following outside of Drupal core:

If you do not yet have Twitter API keys generated for your application, visit
https://apps.twitter.com/ and create a new twitter application.


INSTALLATION
------------

 * Install the Twitter Oauth module as you would normally install a contributed
   Drupal module. Visit
   https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

    1. Navigate to Administration > Extend and enable the module.
    2. Navigate to Administration > Configuration > Web Services > Twitter Oauth
       Settings to configure the settings and credentials.
    3. Next, navigate to `block/add/twitteroauth_search` in order to add your
       first twitter search block.
    4. Finally, navigate to `admin/structure/block` and place your newly created
       custom block in a region of your theme.


MAINTAINERS
-----------

 * Zachary Weishar (zweishar) - https://www.drupal.org/u/zweishar

Supporting organizations:

 * Isovera - https://www.drupal.org/isovera
