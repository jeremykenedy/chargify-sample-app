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
 * @package Crucial_Service_Chargify
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 * @link http://www.crucialwebstudio.com
 */
class Crucial_Service_Chargify_Coupon extends Crucial_Service_Chargify_Abstract 
{
  /**
   * The coupon code to search for when using find()
   *
   * @param string $code
   * @return Crucial_Service_Chargify_Coupon
   */
  public function setCode($code)
  {
    $this->setParam('code', $code);
    return $this;
  }
  /**
   * You can retrieve a coupon via the API with the show method. Retrieving a 
   * coupon via the API will allow you to determine whether or not the coupon 
   * is valid.
   *
   * @param int $productFamilyId
   * @param int $couponId
   * @return Crucial_Service_Chargify_Coupon
   */
  public function show($productFamilyId, $couponId)
  {
    $service = $this->getService();
    
    $response = $service->request('product_families/' . $productFamilyId . '/coupons/' . $couponId, 'GET');
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['coupon'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * You can search for a coupon via the API with the find method. By passing a 
   * code parameter, the find record will attempt to locate a coupon that 
   * matches that code. This method is useful for validating coupon codes that 
   * are entered by a customer. If no coupon is found, a 404 is returned.
   *
   * @param int $productFamilyId
   * @return Crucial_Service_Chargify_Coupon
   * @see Crucial_Service_Chargify_Coupon::setCode()
   * @todo Unit test should return empty array if coupon is not found (404)
   */
  public function find($productFamilyId)
  {
    $service = $this->getService();
    $response = $service->request('product_families/' . $productFamilyId . '/coupons/find', 'GET', NULL, $this->_params);
    $responseArray = $this->getResponseArray($response);
    
    // status code must be 200, otherwise the code in $this->setCode() was not found
    if (!$this->isError() && '200' == $response->getStatus())
    {
      $this->_data = $responseArray['coupon'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
}