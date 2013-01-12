This sample application was created to help with getting feedback on my proposed
Zend_Service_Chargify component. Please check out the proposal and sample app
and give me feedback.

http://framework.zend.com/wiki/display/ZFPROP/Zend_Service_Chargify+-+Dan+Bowen

The component is currently named Crucial_Service_Chargify and is included in
the {APP_PATH}/lib/Crucial/Service folder. It is placed there since the
component is not officially accepted into Zend Framework, yet. I'm hoping to
get this component accepted into Zend Framework 2.0.

# Features

  * Makes use of every documented function of the Chargify API.

  * No database included. Runs completely off of calls to the API using
    Crucial_Service_Chargify.

  * Controllers to handle your Postbacks and Webhooks. See
    PostbackController.php and WebhookController.php

  * View helper for getting hosted payment page URLs. See
    {APP_PATH}/lib/Crucial/View/Helper/HostedUrl.php

  * Helper function for calculating the credit a customer would be owed when
    performing prorated migrations. See
    {APP_PATH}/lib/Crucial/View/Helper/MigrationCredit.php

  * Zferral integration. See /lib/Crucial/View/Helper/Zferral.php and
    SubscriptionsController.php.

    See {APP_PATH}/app/configs/zferral.ini.dist for more info.

  * Works with Chargify Direct (http://docs.chargify.com/chargify-direct-introduction)
    to lower your PCI compliance scope.

  * Developer friendly

    * DocBlox API documentation is in the {APP_PATH}/www/docs folder.

    * Within any controller you can call $this->log($object) to send a log to your
      Firebug console.

    * ErrorController.php provides debug output in development mode.

    * View logs from Postbacks and Webhooks sent to your app.

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