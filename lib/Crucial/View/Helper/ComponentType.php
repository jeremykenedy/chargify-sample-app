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
class Crucial_View_Helper_ComponentType extends Zend_View_Helper_Abstract 
{
  /**
   * Changes component types returned from the API, like 
   * "quantity_based_component" into something nicer, like "Quantity-Based".
   *
   * @param string $componentType
   * @return string
   */
  public function componentType($componentType)
  {
    $niceName = '';
    switch ($componentType)
    {
    	case 'quantity_based_component':
    		$niceName = 'Quantity-Based';
    		break;
    	case 'metered_component':
    	  $niceName = 'Metered';
    	  break;
    	case 'on_off_component':
    	  $niceName = 'On/Off';
    	  break;
    	default:
    		break;
    }
    
    return $niceName;
  }
}