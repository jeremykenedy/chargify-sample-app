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
class Crucial_Service_Chargify_Statement extends Crucial_Service_Chargify_Abstract 
{
  /**
   * Enter description here...
   *
   * @param int $page
   * @return Crucial_Service_Chargify_Statement
   */
  public function setPage($page)
  {
    $this->setParam('page', $page);
    return $this;
  }
  
  /**
   * Enter description here...
   *
   * @param int $subscriptionId
   * @return Crucial_Service_Chargify_Statement
   * @see Crucial_Service_Chargify_Statement::setPage()
   */
  public function listStatements($subscriptionId)
  {
    $service = $this->getService();
    
    // statements for a subscription
    $response = $service->request('subscriptions/' . (int)$subscriptionId . '/statements', 'GET', NULL, $this->getParams());
    
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
   * Read a statemtn via Chargify Statemtent ID
   *
   * @param int $statementId
   * @return Crucial_Service_Chargify_Statement
   */
  public function read($statementId)
  {
    $service = $this->getService();
    
    $response = $service->request('statements/' . (int)$statementId, 'GET');
    
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['statement'];
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
      return $responseArray['statements']['statement'];
    }
    
    if ('json' == $format)
    {
      foreach ($responseArray as $trans)
      {
        $return[] = $trans['statement'];
      }
    }
    
    return $return;
  }
}