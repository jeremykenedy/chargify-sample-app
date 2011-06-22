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
class Crucial_Service_Chargify_Customer extends Crucial_Service_Chargify_Abstract 
{
  /**
   * (Required)
   *
   * @param string $firstName
   * @return Crucial_Service_Chargify_Customer
   */
  public function setFirstName($firstName)
  {
    $this->setParam('first_name', $firstName);
    return $this;
  }
  
  /**
   * (Required)
   *
   * @param string $lastName
   * @return Crucial_Service_Chargify_Customer
   */
  public function setLastName($lastName)
  {
    $this->setParam('last_name', $lastName);
    return $this;
  }
  
  /**
   * (Required)
   *
   * @param string $email
   * @return Crucial_Service_Chargify_Customer
   */
  public function setEmail($email)
  {
    $this->setParam('email', $email);
    return $this;
  }
  
  /**
   * (Optional) Company/Organization name
   *
   * @param string $organization
   * @return Crucial_Service_Chargify_Customer
   */
  public function setOrganization($organization)
  {
    $this->setParam('organization', $organization);
    return $this;
  }
  
  /**
   * (Optional) Phone
   *
   * @param string $phone
   * @return Crucial_Service_Chargify_Customer
   */
  public function setPhone($phone)
  {
    $this->setParam('phone', $phone);
    return $this;
  }
  
  /**
   * (Optional) Address
   *
   * @param string $address
   * @return Crucial_Service_Chargify_Customer
   */
  public function setAddress($address)
  {
    $this->setParam('address', $address);
    return $this;
  }
  
  /**
   * (Optional) Address2
   *
   * @param string $address
   * @return Crucial_Service_Chargify_Customer
   */
  public function setAddress2($address)
  {
    $this->setParam('address_2', $address);
    return $this;
  }
  
  /**
   * (Optional) Country
   *
   * @param string $country
   * @return Crucial_Service_Chargify_Customer
   */
  public function setCountry($country)
  {
    $this->setParam('country', $country);
    return $this;
  }
  
  /**
   * (Optional) State
   *
   * @param string $state
   * @return Crucial_Service_Chargify_Customer
   */
  public function setState($state)
  {
    $this->setParam('state', $state);
    return $this;
  }
  
  /**
   * (Optional) City
   *
   * @param string $city
   * @return Crucial_Service_Chargify_Customer
   */
  public function setCity($city)
  {
    $this->setParam('city', $city);
    return $this;
  }
  
  /**
   * (Optional) Zip
   *
   * @param string $zip
   * @return Crucial_Service_Chargify_Customer
   */
  public function setZip($zip)
  {
    $this->setParam('zip', $zip);
    return $this;
  }
  
  /**
   * (Optional, but encouraged) The unique identifier used within your own 
   * application for this customer
   *
   * @param string|int $reference
   * @return Crucial_Service_Chargify_Customer
   */
  public function setReference($reference)
  {
    $this->setParam('reference', $reference);
    return $this;
  }
  
  /**
   * The 'page' parameter. Used when listing customers since you can only get 50 
   * at a time.
   *
   * @param int $page
   * @return Crucial_Service_Chargify_Customer
   */
  public function setPage($page)
  {
    $this->setParam('page', $page);
    return $this;
  }
  
  /**
   * Create a new customer
   *
   * @return Crucial_Service_Chargify_Customer
   * @see Crucial_Service_Chargify_Customer::setFirstName()
   * @see Crucial_Service_Chargify_Customer::setLastName()
   * @see Crucial_Service_Chargify_Customer::setEmail()
   * @see Crucial_Service_Chargify_Customer::setOrganization()
   * @see Crucial_Service_Chargify_Customer::setPhone()
   * @see Crucial_Service_Chargify_Customer::setAddress()
   * @see Crucial_Service_Chargify_Customer::setAddress2()
   * @see Crucial_Service_Chargify_Customer::setCity()
   * @see Crucial_Service_Chargify_Customer::setState()
   * @see Crucial_Service_Chargify_Customer::setZip()
   * @see Crucial_Service_Chargify_Customer::setCountry()
   * @see Crucial_Service_Chargify_Customer::setReference()
   */
  public function create()
  {
    $service = $this->getService();
    $rawData = $this->getRawData(array('customer' => $this->getParams()));
    $response = $service->request('customers', 'POST', $rawData);
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['customer'];
    }
    else
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * List all customers for a site
   * 
   * @return Crucial_Service_Chargify_Customer
   * @see Crucial_Service_Chargify_Customer::setPage()
   */
  public function listCustomers()
  {
    $service = $this->getService();
    
    $response = $service->request('customers', 'GET', NULL, $this->getParams());
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
   * Read the customer data for the given Chargify ID
   *
   * @param int $id
   * @return Crucial_Service_Chargify_Customer
   */
  public function readByChargifyId($id)
  {
    $service = $this->getService();
    
    $response = $service->request('customers/' . $id, 'GET');
    $responseArray = $this->getResponseArray($response);
    
    // a 404 will be returned if not found, so make sure we have a 200
    if (!$this->isError() && '200' == $response->getStatus())
    {
      $this->_data = $responseArray['customer'];
    }
    else
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * Read the customer data for the given reference (from your app)
   *
   * @return Crucial_Service_Chargify_Customer
   * @see Crucial_Service_Chargify_Customer::setReference()
   */
  public function readByReference()
  {
    $service = $this->getService();
    
    $response = $service->request('customers/lookup', 'GET', '', $this->getParams());
    $responseArray = $this->getResponseArray($response);
    
    // a 404 will be returned if not found, so make sure we have a 200
    if (!$this->isError() && '200' == $response->getStatus())
    {
      $this->_data = $responseArray['customer'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
  
  /**
   * Update the customer record in Chargify.
   *
   * @param int $id
   * @return Crucial_Service_Chargify_Customer
   * @see Crucial_Service_Chargify_Customer::setFirstName()
   * @see Crucial_Service_Chargify_Customer::setLastName()
   * @see Crucial_Service_Chargify_Customer::setEmail()
   * @see Crucial_Service_Chargify_Customer::setOrganization()
   * @see Crucial_Service_Chargify_Customer::setPhone()
   * @see Crucial_Service_Chargify_Customer::setAddress()
   * @see Crucial_Service_Chargify_Customer::setAddress2()
   * @see Crucial_Service_Chargify_Customer::setCity()
   * @see Crucial_Service_Chargify_Customer::setState()
   * @see Crucial_Service_Chargify_Customer::setZip()
   * @see Crucial_Service_Chargify_Customer::setCountry()
   * @see Crucial_Service_Chargify_Customer::setReference()
   */
  public function update($id)
  {
    $service = $this->getService();
    
    $rawData = $this->getRawData(array('customer' => $this->getParams()));
    $response = $service->request('customers/' . (int)$id, 'PUT', $rawData);
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['customer'];
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
      return $responseArray['customers']['customer'];
    }
    
    if ('json' == $format)
    {
      foreach ($responseArray as $prod)
      {
        $return[] = $prod['customer'];
      }
    }
    
    return $return;
  }
}