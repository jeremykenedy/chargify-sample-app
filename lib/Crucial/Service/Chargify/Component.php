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
class Crucial_Service_Chargify_Component extends Crucial_Service_Chargify_Abstract 
{
  /**
   * The quantity of a Quantity Based Component to assign to a subscription
   *
   * @param int $quantity
   * @return Crucial_Service_Chargify_Component
   */
  public function setAllocatedQuantity($quantity)
  {
    $this->setParam('allocated_quantity', $quantity);
    return $this;
  }
  
  /**
   * Create a usage for a Metered Usage Component. Note that you can also send a 
   * negative number to decrease the usage.
   *
   * @param int $quantity
   * @return Crucial_Service_Chargify_Component
   */
  public function setUsageQuantity($quantity)
  {
    $this->setParam('quantity', $quantity);
    return $this;
  }
  
  /**
   * Set the memo for this usage or allocation.
   *
   * @param string $memo
   * @return Crucial_Service_Chargify_Component
   */
  public function setMemo($memo)
  {
    $this->setParam('memo', $memo);
    return $this;
  }
  
  /**
   * Set the name of the component to be created, i.e. "Text Messages"
   *
   * @param string $name
   * @return Crucial_Service_Chargify_Component
   */
  public function setName($name)
  {
    $this->setParam('name', $name);
    return $this;
  }
  
  /**
   * (Not required for On/Off Components) The name of the unit that the 
   * component's usage is measured in. i.e. message
   *
   * @param string $name
   * @return Crucial_Service_Chargify_Component
   */
  public function setUnitName($name)
  {
    $this->setParam('unit_name', $name);
    return $this;
  }
  
  /**
   * The amount the customer will be charged per unit. The price can contain up 
   * to 4 decimal places. i.e. $1.00, $0.0012, etc.
   *
   * @param string $price
   * @return Crucial_Service_Chargify_Component
   */
  public function setUnitPrice($price)
  {
    $this->setParam('unit_price', $price);
    return $this;
  }
  
  /**
   * (Not required for On/Off Components or 'per_unit' pricing schemes) One or 
   * more price brackets. See Product Components for an overview of how price 
   * brackets work for different pricing schemes. 
   *
   * @param string $scheme
   * @return Crucial_Service_Chargify_Component
   * @link http://docs.chargify.com/product-components
   */
  public function setPricingScheme($scheme)
  {
    $this->setParam('pricing_scheme', $scheme);
    return $this;
  }
  
  /**
   * An array of price brackets. If the component uses the 'per_unit' pricing 
   * scheme, this array will be empty. Available options:
   * - starting_quantity
   * - ending_quantity
   * - unit_price
   * 
   * array(
   *  array(
   *    'starting_quantity' => 1,
   *    'ending_quantity' => 20,
   *    'unit_price' => 19.00
   *  )
   * )
   *
   * @param string $prices
   * @return Crucial_Service_Chargify_Component
   * @link http://docs.chargify.com/product-components
   */
  public function setPrices($prices)
  {
    $this->setParam('prices', $prices);
    return $this;
  }
  
  /**
   * List components for a subscription
   *
   * @param int $id
   * @return Crucial_Service_Chargify_Component
   */
  public function listSubscription($id)
  {
    $service = $this->getService();
    
    $response = $service->request('subscriptions/' . (int)$id . '/components', 'GET');
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
   * Read component for a subscription
   *
   * @param int $subscriptionId
   * @param int $componentId
   * @return Crucial_Service_Chargify_Component
   */
  public function readSubscription($subscriptionId, $componentId)
  {
    $service = $this->getService();
    
    $response = $service->request('subscriptions/' . (int)$subscriptionId . '/components/' . (int)$componentId, 'GET');
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['component'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * Set quantity of Quantity Based Components for a subscription
   *
   * @param int $subscriptionId
   * @param int $componentId
   * @return Crucial_Service_Chargify_Component
   * @see Crucial_Service_Chargify_Component::setAllocatedQuantity()
   */
  public function setQuantityAllocation($subscriptionId, $componentId)
  {
    $service = $this->getService();
    $rawData = $this->getRawData(array('component' => $this->_params));
    $response = $service->request('subscriptions/' . (int)$subscriptionId . '/components/' . (int)$componentId, 'PUT', $rawData);
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['component'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * Create a usage for a Metered Usage Component
   *
   * @param int $subscriptionId
   * @param int $componentId
   * @return Crucial_Service_Chargify_Component
   * @see Crucial_Service_Chargify_Component::setUsageQuantity()
   * @see Crucial_Service_Chargify_Component::setUsageMemo()
   */
  public function createUsage($subscriptionId, $componentId)
  {
    $service = $this->getService();
    $rawData  = $this->getRawData(array('usage' => $this->_params));
    $response = $service->request('subscriptions/' . (int)$subscriptionId . '/components/' . (int)$componentId . '/usages', 'POST', $rawData);
    
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['usage'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * List components for a product family
   *
   * @param int $productFamilyId
   * @return Crucial_Service_Chargify_Component
   */
  public function listProductFamily($productFamilyId)
  {
    $service = $this->getService();
    
    $response = $service->request('product_families/' . (int)$productFamilyId . '/components', 'GET');
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
   * Read a component belonging to a product family
   *
   * @param int $productFamilyId
   * @param int $componentId
   * @return Crucial_Service_Chargify_Component
   */
  public function readProductFamily($productFamilyId, $componentId)
  {
    $service = $this->getService();
    
    $response = $service->request('product_families/' . (int)$productFamilyId . '/components/' . (int)$componentId, 'GET');
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['component'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * Create a component
   *
   * @param int $productFamilyId
   * @param string $componentType; one of the following:
   *  - metered_components
   *  - quantity_based_components
   *  - on_off_components
   * @return Crucial_Service_Chargify_Component
   * @see Crucial_Service_Chargify_Component::setName()
   * @see Crucial_Service_Chargify_Component::setUnitName()
   * @see Crucial_Service_Chargify_Component::setUnitPrice()
   * @see Crucial_Service_Chargify_Component::setPricingScheme()
   * @see Crucial_Service_Chargify_Component::setPrices()
   */
  public function createComponent($productFamilyId, $componentType)
  {
    $service = $this->getService();
    
    $rawDataKey = '';
    switch ($componentType)
    {
    	case 'metered_components':
    		$rawDataKey = 'metered_component';
    		break;
      case 'quantity_based_components':
    		$rawDataKey = 'quantity_based_component';
    		break;
      case 'on_off_components':
    		$rawDataKey = 'on_off_component';
    		break;
    	default:
    		break;
    }
    $rawData  = $this->getRawData(array($rawDataKey => $this->_params));
    $response = $service->request('product_families/' . (int)$productFamilyId . '/' . $componentType, 'POST', $rawData);
    
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError() && '201' == $response->getStatus())
    {
      $this->_data = $responseArray['component'];
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
      return $responseArray['components']['component'];
    }
    
    if ('json' == $format)
    {
      foreach ($responseArray as $prod)
      {
        $return[] = $prod['component'];
      }
    }
    
    return $return;
  }
}