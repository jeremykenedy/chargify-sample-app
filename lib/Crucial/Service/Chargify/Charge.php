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
class Crucial_Service_Chargify_Charge extends Crucial_Service_Chargify_Abstract 
{
  
  /**
   * (either 'amount' or 'amount_in_cents' is required) If you use this 
   * parameter, you should pass a dollar amount represented as a string. For 
   * example, $10.00 would be represented as 10.00.
   *
   * @param string $amount
   * @return Crucial_Service_Chargify_Charge
   */
  public function setAmount($amount)
  {
    $this->setParam('amount', $amount);
    return $this;
  }
  
  /**
   * (either 'amount' or 'amount_in_cents' is required) If you use this 
   * parameter, you should pass the amount represented as a number of cents, 
   * either as a string or integer. For example, $10.00 would be represented 
   * as 1000. If you pass a value for both 'amount' and 'amount_in_cents, the 
   * value in 'amount_in_cents' will be used and 'amount' will be discarded.
   *
   * @param int $amountInCents
   * @return Crucial_Service_Chargify_Charge
   */
  public function setAmountInCents($amountInCents)
  {
    $this->setParam('amount_in_cents', $amountInCents);
    return $this;
  }
  
  /**
   * (required) A helpful explanation for the charge. This amount will remind 
   * you and your customer for the reason for the assessment of the charge.
   *
   * @param string $memo
   * @return Crucial_Service_Chargify_Charge
   */
  public function setMemo($memo)
  {
    $this->setParam('memo', $memo);
    return $this;
  }
  
  /**
   * For "live" subscriptions (i.e. subscriptions that are not canceled or expired) 
   * you have the ability to attach a one-time (or "one-off") charge of an 
   * arbitrary amount.Enter description here...
   *
   * @param int $subscriptionId
   * @return Crucial_Service_Chargify_Charge
   * @see Crucial_Service_Chargify_Charge::setAmount()
   * @see Crucial_Service_Chargify_Charge::setAmountInCents()
   * @see Crucial_Service_Chargify_Charge::setMemo()
   */
  public function create($subscriptionId)
  {
    $service = $this->getService();
    $rawData = $this->getRawData(array('charge' => $this->getParams()));
    $response = $service->request('subscriptions/' . (int)$subscriptionId . '/charges', 'POST', $rawData);
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError())
    {
      $this->_data = $responseArray['charge'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
}