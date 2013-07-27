Publish Product Links To Twitter - A Prestashop Module
=======================================

This module enables your Prestashop store to publish product titles, prices and links for your products to Twitter automatically via a cron job (which you will need to set up). The current version selects products at random but a future version may allow you to specify certain rules for how products should be selected. 

Benefits for the e-merchant
=======================================
This module allows you to publish your products to Twitter and take advantage of the exposure that Twitter can provide with its millions of followers and subscribers, translating into increased traffic and eventual increased sales. 

Benefits for the customer
=======================================
Customers who subscribe to Twitter will have the advantage of getting to know about the various products that you have to offer. 

Features
=======================================
Automatically publish your products to Twitter (Title, Price and Link) 

Module compatibility
====================================
* Prestashop 1.5 and above
* PHP 5.3.0 and above (with CURL enabled)
* Requires the ability to set up a cron job on your web server

Module limitations
====================================
* Does not support multi-store prestashop configurations
* Products are selected based on the default language for your store
* Pricing is based on the default currency for your store

Installation
====================================

Install the module following the module installation guidelines available at the following Prestashop link:

http://addons.prestashop.com/en/content/13-installing-modules

Configuration
====================================
Create a twitter application that has read/write access to your Twitter account. Follow the guidelines at the following Ning link:
https://www.ning.com/help/?p=4955

Configure the module by specifying the following pieces of information:

Twitter Consumer Key - The Consumer Key for your Twitter application

Twitter Consumer Secret - The Consumer Secret for your Twitter application

Twitter Access Token - The access token for your Twitter application (oauth_token)

Twitter Access Token Secret - The access token secret for your Twitter application (oauth_token_secret)

Number of Products to Publish - The number of products you would like to publish each time the cron job runs. By default this value will be set to 1

Cron job
====================================
Set up a cron job to run the following script:

```
{absolute-path-to-your-store-files-on-web-server}/modules/publishproductlinkstotwitter/cron/run-publish-cycle.php
```

e.g. on shared hosting, the path could be something like the below:

```
/home/user/public_html/modules/publishproductlinkstotwitter/cron/run-publish-cycle.php
```

So an example cron job for running the script every 15mins would look like the below:

```
*/15 * * * * /usr/bin/php /home/user/public_html/modules/publishproductlinkstotwitter/cron/run-publish-cycle.php
```

(The above is all on one line)

Note:

This module makes use of Twitters REST API and Twitter has a limitation on the number of API calls you can make through their REST API within a given day, so choose a good balance of the number of products to publish and the frequency of the cron job to ensure that you do not get rate limited. More information on rate limiting is available at the following link:
https://dev.twitter.com/docs/rate-limiting-faq
