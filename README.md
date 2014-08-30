This sample application was created as an experiment to create a customer
portal for Chargify.com (before Chargify developed their own). It is written
entirely in PHP and Zend Framework 1.

It attempts to utilize every function of the Chargify API, including a sample
using Chargify Direct.

# Features

  * No database included. Runs completely off of calls to the API using
    `Crucial_Service_Chargify`. See the `lib/Crucial` folder.

  * Controllers to handle your Postbacks and Webhooks. See
    `PostbackController.php` and `WebhookController.php`

  * View helper for getting hosted payment page URLs. See
    `lib/Crucial/View/Helper/HostedUrl.php`

  * Zferral integration. See `lib/Crucial/View/Helper/Zferral.php` and
    SubscriptionsController.php.

    See `app/configs/zferral.ini.dist` for more info.

  * Works with Chargify Direct (http://docs.chargify.com/chargify-direct-introduction)
    to lower your PCI compliance scope.

  * Developer friendly

    * DocBlox API documentation is in the `/www/docs` folder.

    * Within any controller you can call `$this->log($object);` to send a log to your
      Firebug console.

    * `ErrorController.php` provides debug output in development mode.

    * View logs from Postbacks and Webhooks sent to your app. See the `tmp` folder.

# In the wild

This sample app has served as the basis for several products at Crucial Web Studio,
including Chargely (http://www.getchargely.com).

We'd love to hear from you if you've built something awesome based on this project.

# DISCLAIMER

  * This app is intended for demonstration purposes only. THERE IS NO PASSWORD
    PROTECTION so please do not install this app on the public internet. Only
    install on a secure local development environment that is inaccessbile to
    the outside world.

  * This app is distributed in the hope that it will be useful, but WITHOUT ANY
    WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
    FOR A PARTICULAR PURPOSE.