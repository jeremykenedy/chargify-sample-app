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
class Crucial_View_Helper_SubscriptionState extends Zend_View_Helper_Abstract 
{
  /**
   * Changes subscription state returned from the API, like "trialing" into 
   * something nicer like "Trialing".
   *
   * @param string $subscriptionState
   * @return string
   */
  public function subscriptionState($subscriptionState)
  {
    $niceName = '';
    switch ($subscriptionState)
    {
    	case 'trialing':
    		$niceName = 'Trialing';
    		break;
      case 'assessing':
    		$niceName = 'Assessing';
    		break;
    	case 'active':
    	  $niceName = 'Active';
    	  break;
      case 'soft_failure':
    		$niceName = 'Soft Failure';
    		break;
    	case 'past_due':
    	  $niceName = 'Past Due';
    	  break;
    	case 'suspended':
    	  $niceName = 'Suspended';
    	  break;
    	case 'canceled':
    	  $niceName = 'Canceled';
    	  break;
    	case 'unpaid':
    	  $niceName = 'Unpaid';
    	  break;
    	case 'expired':
    	  $niceName = 'Expired';
    	  break;
    	default:
    	  $niceName = '#undefined';
    		break;
    }
    
    return $niceName;
  }
}