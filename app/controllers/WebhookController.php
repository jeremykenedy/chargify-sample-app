<?php
require_once('ChargifyController.php');

/**
 * Handles webhooks from Chargify
 * 
 * Webhooks will be logged to the /tmp/webhooks directory. Make sure it is 
 * writable by the web server.
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
class WebhookController extends ChargifyController 
{
  /**
   * Can we write to the webhook directory?
   *
   * @var bool
   */
  protected $_isLoggable;
  
  /**
   * File handle for logging this webhook
   *
   * @var resource
   */
  protected $_handle;
  
  /**
   * Receives and logs Webhook notifications from Chargify
   * 
   * Check out the sample code in here for ideas on how to update your app 
   * when Chargify notifies your app of subscription changes.
   */
  public function handleAction()
  {
    // disable layout and auto-rendering
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    
    $body   = $this->getRequest()->getRawBody();
    $method = $this->getRequest()->getMethod();
    $ip     = $this->getRequest()->getClientIp();
    
    $signature = $this->getRequest()->getHeader('X-Chargify-Webhook-Signature');
    $webhookId = $this->getRequest()->getHeader('X-Chargify-Webhook-Id');
    $sharedKey = $this->_getChargify()->getConfig()->shared_key;
    
    // validate that the request came from Chargify
    $isValid = $this->_isValid($sharedKey, $body, $signature);
    $validString = $isValid ? 'PASS' : '** FAIL **';
    
    /**
     * log it to /tmp
     */
    $logDir     = dirname(APPLICATION_PATH) . '/tmp/webhooks';
    $filename   = $logDir . '/' . date('YmdHis', time());
    $isLoggable = FALSE;
    
    // create webhooks directory if not already created
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
               . "Event: " . $_POST['event'] . "\n"
               . "Signature: $signature\n"
               . "Signature test: $validString\n"
               . "Webhook ID: $webhookId\n"
               . "BODY:\n$body\n"
               . "POST:\n" . print_r($_POST, TRUE)
               . "\n\n// Output from our app\n";
      fwrite($handle, $content);
      
      $this->_isLoggable = $isLoggable;
      $this->_handle     = $handle;
    }
    
    switch ($_POST['event'])
    {
      case 'signup_success':
        $this->_handleSignupSuccess();
        break;
      case 'signup_failure':
        $this->_handleSignupFailure();
        break;
      case 'renewal_success':
        $this->_handleRenewalSuccess();
        break;
      case 'renewal_failure':
        $this->_handleRenewalFailure();
        break;
      case 'payment_success':
        $this->_handlePaymentSuccess();
        break;
      case 'payment_failure':
        $this->_handlePaymentFailure();
        break;
      case 'billing_date_change':
        $this->_handleBillingDateChange();
        break;
      case 'subscription_state_change':
        $this->_handleSubscriptionStateChange();
        break;
      case 'expiring_card':
        $this->_handleExpiringCard();
        break;
      default:
        break;
    }
    
    if ($isLoggable)
    {
      fclose($handle);
    }
    
  }
  
  /**
   * Validate that the webhook request is from Chargify
   *
   * @param string $sharedKey
   *  Your site shared key
   * @param string $body
   *  The string of the raw body sent in the request
   * @param string $signature
   *  The string sent in the X-Chargify-Webhook-Signature http request header
   * @return bool
   * @throws Crucial_Service_Chargify_Exception
   */
  protected function _isValid($sharedKey, $body, $signature)
  {
    $hash = md5($sharedKey . $body);
    if ($hash == $signature)
    {
      return TRUE;
    }
    else 
    {
      return FALSE;
      /**
       * Should actually throw an exception here since it is probably a bot or 
       * some sort of attack. Simply comment out the return line above in order
       * to throw the exception instead.
       */
      throw new Crucial_Service_Chargify_Exception('Invalid webhook request');
    }
  }
  
  /**
   * Handle "signup_success" webhook event.
   *
   */
  protected function _handleSignupSuccess()
  {
    if ($this->_isLoggable)
    {
      $sub    =  $_POST['payload']['subscription'];
      $output = '--- Handling "signup_success" for subscription_id: ' 
              . $sub['id'] . " ---\n"
              . "Customer: " . $sub['customer']['first_name'] . ' ' 
                             . $sub['customer']['last_name']  . "\n\n";
      fwrite($this->_handle, $output);
    }
    
    //throw new Crucial_Service_Chargify_Exception();
  }
  
  /**
   * Handle "signup_failure" webhook event.
   *
   */
  protected function _handleSignupFailure()
  {
    if ($this->_isLoggable)
    {
      $sub    =  $_POST['payload']['subscription'];
      $output = '--- Handling "signup_failure" for subscription_id: ' 
              . $sub['id'] . " ---\n"
              . "Customer: " . $sub['customer']['first_name'] . ' ' 
                             . $sub['customer']['last_name']  . "\n\n";
      fwrite($this->_handle, $output);
    }
    
    //throw new Crucial_Service_Chargify_Exception();
  }
  
  protected function _handleRenewalSuccess()
  {
    if ($this->_isLoggable)
    {
      $sub    =  $_POST['payload']['subscription'];
      $output = '--- Handling "renewal_success" for subscription_id: ' 
              . $sub['id'] . " ---\n"
              . "Customer: " . $sub['customer']['first_name'] . ' ' 
                             . $sub['customer']['last_name']  . "\n\n";
      fwrite($this->_handle, $output);
    }
    
    //throw new Crucial_Service_Chargify_Exception();
  }
  
  /**
   * Handle "renewal_failure" webhook event.
   *
   */
  protected function _handleRenewalFailure()
  {
    if ($this->_isLoggable)
    {
      $sub    =  $_POST['payload']['subscription'];
      $output = '--- Handling "renewal_failure" for subscription_id: ' 
              . $sub['id'] . " ---\n"
              . "Customer: " . $sub['customer']['first_name'] . ' ' 
                             . $sub['customer']['last_name']  . "\n\n";
      fwrite($this->_handle, $output);
    }
    
    //throw new Crucial_Service_Chargify_Exception();
  }
  
  /**
   * Handle "payment_success" webhook event.
   *
   */
  protected function _handlePaymentSuccess()
  {
    if ($this->_isLoggable)
    {
      $sub    =  $_POST['payload']['subscription'];
      $output = '--- Handling "payment_success" for subscription_id: ' 
              . $sub['id'] . " ---\n"
              . "Customer: " . $sub['customer']['first_name'] . ' ' 
                             . $sub['customer']['last_name']  . "\n\n";
      fwrite($this->_handle, $output);
    }
    
    //throw new Crucial_Service_Chargify_Exception();
  }
  
  /**
   * Handle "payment_failure" webhook event.
   *
   */
  protected function _handlePaymentFailure()
  {
    if ($this->_isLoggable)
    {
      $sub    =  $_POST['payload']['subscription'];
      $output = '--- Handling "payment_failure" for subscription_id: ' 
              . $sub['id'] . " ---\n"
              . "Customer: " . $sub['customer']['first_name'] . ' ' 
                             . $sub['customer']['last_name']  . "\n\n";
      fwrite($this->_handle, $output);
    }
    
    //throw new Crucial_Service_Chargify_Exception();
  }
  
  protected function _handleBillingDateChange()
  {
    if ($this->_isLoggable)
    {
      $sub    =  $_POST['payload']['subscription'];
      $output = '--- Handling "billing_date_change" for subscription_id: ' 
              . $sub['id'] . " ---\n"
              . "Customer: " . $sub['customer']['first_name'] . ' ' 
                             . $sub['customer']['last_name']  . "\n\n";
      fwrite($this->_handle, $output);
    }
    
    //throw new Crucial_Service_Chargify_Exception();
  }
  
  /**
   * Handle "subscription_state_change" webhook event.
   *
   */
  protected function _handleSubscriptionStateChange()
  {
    if ($this->_isLoggable)
    {
      $sub    =  $_POST['payload']['subscription'];
      $output = '--- Handling "subscription_state_change" for subscription_id: ' . $sub['id'] . " ---\n";
      $output .= "Customer: " . $sub['customer']['first_name'] . ' ' 
                              . $sub['customer']['last_name']  . "\n\n";
      fwrite($this->_handle, $output);
    }
    
    //throw new Crucial_Service_Chargify_Exception();
  }
  
  protected function _handleExpiringCard()
  {
    if ($this->_isLoggable)
    {
      $sub    =  $_POST['payload']['subscription'];
      $output = '--- Handling "expiring_card" for subscription_id: ' 
              . $sub['id'] . " ---\n"
              . "Customer: " . $sub['customer']['first_name'] . ' ' 
                             . $sub['customer']['last_name']  . "\n\n";
      fwrite($this->_handle, $output);
    }
    
    //throw new Crucial_Service_Chargify_Exception();
  }
}