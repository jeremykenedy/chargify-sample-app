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
class Crucial_Service_Chargify_Product extends Crucial_Service_Chargify_Abstract 
{
  /**
   * List all products for your site
   *
   * @return Crucial_Service_Chargify_Product
   */
  public function listProducts()
  {
    $service = $this->getService();
    
    $response = $service->request('products', 'GET');
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $this->_normalizeResponseArray($responseArray);
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * Get product details by Chargify ID
   *
   * @param int $id
   * @return Crucial_Service_Chargify_Product
   */
  public function readByChargifyId($id)
  {
    $service = $this->getService();
    
    $response = $service->request('products/' . $id, 'GET');
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['product'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * Get product details by API handle
   *
   * @param string $handle
   * @return Crucial_Service_Chargify_Product
   */
  public function readByHandle($handle)
  {
    $service = $this->getService();
    
    $response = $service->request('products/handle/' . $handle, 'GET');
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['product'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * When returning multiple products the array is different depending on which 
   * format (xml/json) you are using. This normalizes the array for us so we can 
   * rely on a consistent structure.
   *
   * @param array $responseArray
   * @return array
   */
  protected function _normalizeResponseArray($responseArray)
  {
    $service = $this->getService();
    $format = $service->getFormat();
    
    $return = array();
    
    if ('xml' == $format)
    {
      return $responseArray['products']['product'];
    }
    
    if ('json' == $format)
    {
      foreach ($responseArray as $prod)
      {
        $return[] = $prod['product'];
      }
    }
    
    return $return;
  }
}