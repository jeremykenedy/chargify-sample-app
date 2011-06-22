<?php
require_once('ChargifyController.php');

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
 * @category App
 * @package Controllers
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 */
class CouponsController extends ChargifyController 
{
  /**
   * Show the details of a coupon
   * 
   * This page is not linked to anywhere within the app. Access it
   * at the URL specified and check Firebug for details.
   * 
   * @link /coupons/show/family-id/{product-family-id}/coupon-id/{coupon-id}
   * @link http://docs.chargify.com/api-coupons
   */
  public function showAction()
  {
    $productFamily = $this->getRequest()->getParam('family-id');
    $couponId = $this->getRequest()->getParam('coupon-id');
    
    $service = $this->_getChargify();
    
    $c = $service->coupon()->show($productFamily, $couponId);
    
    $this->log($c);
  }
  
  /**
   * Find a coupon
   * 
   * Helpful for validating coupons entered by customers.
   * 
   * This page is not linked to anywhere within the app. Access it at the URL 
   * specified and check Firebug for details.
   * 
   * @link /coupons/show/family-id/{product-family-id}/code/{coupon-code}
   * @link http://docs.chargify.com/api-coupons
   */
  public function findAction()
  {
    $productFamily = $this->getRequest()->getParam('family-id');
    $code = $this->getRequest()->getParam('code');
    
    $service = $this->_getChargify();
    
    $c = $service->coupon()
                 ->setCode($code)
                 ->find($productFamily);
    
    $this->log($c);
  }
}