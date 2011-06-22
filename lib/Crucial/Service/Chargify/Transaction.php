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
class Crucial_Service_Chargify_Transaction extends Crucial_Service_Chargify_Abstract 
{
  /**
   * An array of transaction types. Multiple values can be passed in the url, 
   * for example: http://example.com?kinds[]=charge&kinds[]=payment&kinds[]=credit
   * 
   * The following is a list of available transaction types.
   * 
   * charge
   * refund
   * payment
   * credit
   * payment_authorization
   * info
   * adjustment
   * 
   * @param array $kinds
   * @return Crucial_Service_Chargify_Transaction
   */
  public function setKinds($kinds)
  {
    $this->setParam('kinds', $kinds);
    return $this;
  }
  
  /**
   * Returns transactions with an id greater than or equal to the one specified
   *
   * @param int $sinceId
   * @return Crucial_Service_Chargify_Transaction
   */
  public function setSinceId($sinceId)
  {
    $this->setParam('since_id', $sinceId);
    return $this;
  }
  
  /**
   * Returns transactions with an id less than or equal to the one specified
   *
   * @param int $maxId
   * @return Crucial_Service_Chargify_Transaction
   */
  public function setMaxId($maxId)
  {
    $this->setParam('max_id', $maxId);
    return $this;
  }
  
  /**
   * Returns transactions with a created_at date greater than or equal to the 
   * one specified
   *
   * @param string $sinceDate; format YYYY-MM-DD
   * @return Crucial_Service_Chargify_Transaction
   */
  public function setSinceDate($sinceDate)
  {
    $this->setParam('since_date', $sinceDate);
    return $this;
  }
  
  /**
   * Returns transactions with a created_at date less than or equal to the one specified
   *
   * @param string $untilDate; format YYYY-MM-DD
   * @return Crucial_Service_Chargify_Transaction
   */
  public function setUntilDate($untilDate)
  {
    $this->setParam('until_date', $untilDate);
    return $this;
  }
  
  /**
   * The page number and number of results used for pagination. By default 
   * results are paginated 20 per page.
   *
   * @param int $page
   * @param int $perPage
   * @return Crucial_Service_Chargify_Transaction
   */
  public function setPagination($page, $perPage)
  {
    $this->setParam('page', $page);
    $this->setParam('per_page', $perPage);
    return $this;
  }
  
  /**
   * Retrieve transactions for a specific subscription
   *
   * @param int $subscriptionId; Chargify subscription_id
   * @return Crucial_Service_Chargify_Transaction
   * @see Crucial_Service_Chargify_Transaction:setKinds()
   * @see Crucial_Service_Chargify_Transaction::setPagination()
   * @see Crucial_Service_Chargify_Transaction:setSinceDate()
   * @see Crucial_Service_Chargify_Transaction::setUntilDate()
   * @see Crucial_Service_Chargify_Transaction::setMaxId()
   * @see Crucial_Service_Chargify_Transaction::setSinceId()
   */
  public function listBySubscription($subscriptionId)
  {
    $service = $this->getService();
    
    // transactions for a subscription
    $response = $service->request('subscriptions/' . $subscriptionId . '/transactions', 'GET', NULL, $this->getParams());
    
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
   * Retrieve transactions for your entire site
   *
   * @return Crucial_Service_Chargify_Transaction
   * @see Crucial_Service_Chargify_Transaction:setKinds()
   * @see Crucial_Service_Chargify_Transaction::setPagination()
   * @see Crucial_Service_Chargify_Transaction:setSinceDate()
   * @see Crucial_Service_Chargify_Transaction::setUntilDate()
   * @see Crucial_Service_Chargify_Transaction::setMaxId()
   * @see Crucial_Service_Chargify_Transaction::setSinceId()
   */
  public function listBySite()
  {
    $service = $this->getService();
    
    // transactions for a subscription
    $response = $service->request('transactions', 'GET', NULL, $this->getParams());
    
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
      return $responseArray['transactions']['transaction'];
    }
    
    if ('json' == $format)
    {
      foreach ($responseArray as $trans)
      {
        $return[] = $trans['transaction'];
      }
    }
    
    return $return;
  }
}