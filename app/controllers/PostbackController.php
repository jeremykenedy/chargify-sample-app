<?php
require_once('ChargifyController.php');

/**
 * Handles postbacks from Chargify
 * 
 * Postbacks will be logged to the /tmp/postbacks directory. Make sure it is 
 * writable by the web server.
 * 
 * Postbacks are being deprecated in favor of Webhooks. See the 
 * WebhookController for more info.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to dan@crucialwebstudio.com so we can send you a copy immediately.
 * 
 * @category App
 * @package Controllers
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 */
class PostbackController extends ChargifyController 
{
  /**
   * Receives and logs postback notifications from Chargify
   * 
   * Check out the sample code in here for ideas on how to update your app 
   * when Chargify notifies your app of subscription changes.
   */
  public function handleAction()
  {
    // disable layout and auto-rendering
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    
    /**
     * Chargify sends an HTTP POST with a json array of subscription IDs. It the 
     * body looks like this:
     * [201, 345, 468]
     * 
     * This would indicate that the subscriptions with IDs 201, 345, and 468 
     * have changed in some way that you may care about (i.e. the state has 
     * changed, there was an upgrade or downgrade, etc.)
     */
    $body   = $this->getRequest()->getRawBody();
    $body   = trim($body);
    $method = $this->getRequest()->getMethod();
    $ip     = $this->getRequest()->getClientIp();
    
    if (empty($body))
    {
      return;
    }
    
    /**
     * log it to /tmp
     */
    $logDir     = dirname(APPLICATION_PATH) . '/tmp/postbacks';
    $filename   = $logDir . '/' . date('YmdHis', time());
    $isLoggable = FALSE;
    
    // create postbacks directory if not already created
    if (!is_dir($logDir) && is_writable(dirname($logDir)))
    {
       mkdir($logDir);
    }
    
    if (is_dir($logDir) && is_writable($logDir))
    {
      $isLoggable = TRUE;
      $handle = fopen($filename, 'w');
      $content = "// Input from Chargify.com\n"
               . "Request Method: $method\n"
               . "IP: $ip\n"
               . "JSON: $body\n"
               . "\n\n// Output from our app\n";
      fwrite($handle, $content);
    }
    
    /**
     * update each subscription within our app
     */
    $service = $this->_getChargify();
    $subs    = Zend_Json::decode($body);
    foreach ($subs as $subscriptionId)
    {
      // get the subscription info from Chargify
      $subscription = $service->subscription()->read($subscriptionId);
      
      if ($isLoggable)
      {
        $output = '--- Updating subscription_id: ' . $subscription['id'] . " ---\n";
        $output .= "Customer: " . $subscription['customer']['first_name'] . ' ' 
                                . $subscription['customer']['last_name']  . "\n\n";
        fwrite($handle, $output);
      }
      
      /**
       * Update this subscription within your app.
       * 
       * If for some reason your app cannot process the update, you should throw 
       * a 500 error. If we just throw an exception that bubbles up to the 
       * default error handler, the http status code of 500 will be set there.
       * 
       * This status code will tell Chargify that you were unable to process the 
       * request and will continue sending the postback until it receives a 200 
       * status code.
       * 
       * @see ErrorController.php
       */
      //throw new Crucial_Service_Chargify_Exception();
      
    }
    
    if ($isLoggable)
    {
      fclose($handle);
    }
  }
}