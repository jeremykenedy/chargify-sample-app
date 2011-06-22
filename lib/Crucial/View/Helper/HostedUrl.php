<?php
/**
 * Chargify Sample App
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
 * @category Crucial
 * @package Crucial_View_Helper
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 */
class Crucial_View_Helper_HostedUrl extends Zend_View_Helper_Abstract 
{
  /**
   * Determine the hosted payment page for a subscription
   *
   * @param int $subscriptionId
   * @return string
   *  the fully qualified URL of the hosted page
   * @link http://docs.chargify.com/hosted-page-integration
   */
  public function hostedUrl($subscriptionId)
  {
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/chargify.ini', APPLICATION_ENV);
    
    $key = $config->shared_key;
    $hostname = $config->hostname;
    
    $message = 'update_payment--' . $subscriptionId . '--' . $key;
    // get first 10 characters of the SHA1 hash
    $token = substr(sha1($message), 0, 10);
    $url = 'https://' . $hostname . '/update_payment/' . $subscriptionId . '/' . $token;
    return $url;
  }
}