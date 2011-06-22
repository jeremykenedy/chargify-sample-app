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
class Crucial_Service_Chargify_Adjustment extends Crucial_Service_Chargify_Abstract 
{
  
  /**
   * (either 'amount' or 'amount_in_cents' is required) If you use this 
   * parameter, you should pass a dollar amount represented as a string. For 
   * example, $10.00 would be represented as 10.00 and -$10.00 would be 
   * represented as -10.00.
   *
   * @param string $amount
   * @return Crucial_Service_Chargify_Adjustment
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
   * as 1000 and -$10.00 would be represented as -1000. If you pass a value 
   * for both 'amount' and 'amount_in_cents', the value in 'amount_in_cents' 
   * will be used and 'amount' will be discarded.
   *
   * @param int $amountInCents
   * @return Crucial_Service_Chargify_Adjustment
   */
  public function setAmountInCents($amountInCents)
  {
    $this->setParam('amount_in_cents', $amountInCents);
    return $this;
  }
  
  /**
   * A helpful explanation for the adjustment. This amount will remind you and 
   * your customer for the reason for the assessment of the adjustment.
   *
   * @param string $memo
   * @return Crucial_Service_Chargify_Adjustment
   */
  public function setMemo($memo)
  {
    $this->setParam('memo', $memo);
    return $this;
  }
  
  /**
   * (Optional) A string that toggles how the adjustment should be applied. If 
   * target is passed for this param, the adjustment will automatically set the 
   * subscription's balance to the amount. If left blank, the amount will be 
   * added to the current balance.
   *
   * @param string $method
   * @return Crucial_Service_Chargify_Adjustment
   */
  public function setAdjustmentMethod($method)
  {
    $this->setParam('adjustment_method', $method);
    return $this;
  }
  
  /**
   * Adjustments allow you to change the current balance of a subscription. 
   * Adjustments with positive amounts make the balance go up, Adjustments with 
   * negative amounts make the balance go down (like Credits).
   *
   * @param int $subscriptionId
   * @return Crucial_Service_Chargify_Adjustment
   * @see Crucial_Service_Chargify_Adjustment::setAmount()
   * @see Crucial_Service_Chargify_Adjustment::setAmountInCents()
   * @see Crucial_Service_Chargify_Adjustment::setMemo()
   * @see Crucial_Service_Chargify_Adjustment::setAdjustmentMethod()
   */
  public function create($subscriptionId)
  {
    $service = $this->getService();
    $rawData = $this->getRawData(array('adjustment' => $this->getParams()));
    $response = $service->request('subscriptions/' . (int)$subscriptionId . '/adjustments', 'POST', $rawData);
    $responseArray = $this->getResponseArray($response);
    
    if (!$this->isError() && '201' == $response->getStatus())
    {
      $this->_data = $responseArray['adjustment'];
    }
    else 
    {
      $this->_data = array();
    }
    
    return $this;
  }
}