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
class Crucial_View_Helper_Zferral extends Zend_View_Helper_Abstract 
{
  
  /**
   * Embeds the Zferral tracking pixel into your signup confirmation page.
   *
   * @param string $subdomain
   * @param string $campaignId
   * @param string $revenue
   * @param string $subscriptionId
   * @param string $paymentId
   * @return string
   * @link http://docs.chargify.com/zferral-integration
   */
  public function zferral($subdomain, $campaignId, $revenue, $subscriptionId, $paymentId)
  {
    $return = '<img src="http://';
    $return .= $subdomain;
    $return .= '.zferral.com/e/';
    $return .= $campaignId;
    $return .= '?rev=';
    $return .= $revenue;
    $return .= '&customerId=';
    $return .= $subscriptionId;
    $return .= '&uniqueId=';
    $return .= $paymentId;
    $return .= '" style="border: none; display: none" alt="" />';
    
    return $return;
  }
}