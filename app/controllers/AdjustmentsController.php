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
class AdjustmentsController extends ChargifyController 
{
  
  /**
   * Create an adjustment
   * Adjustments allow you to change the current balance of a subscription. 
   * Adjustments with positive amounts make the balance go up. Adjustments with 
   * negative amounts make the balance go down (like Credits).
   * 
   * @link http://docs.chargify.com/api-adjustments
   */
  public function createAction()
  {
    $this->_helper->layout()->setLayout('subscription');
    
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $adj = $service->adjustment();
      
      // I should just filter digits here
      $amount = str_replace('$', '', $_POST['adjustment']['amount']);
      $amount = str_replace('.', '', $amount);
      $amount = trim($amount);
      
      switch ($_POST['adjustment']['adjustment_method'])
      {
        case 'decrease':
          $amount = str_replace('-', '', $amount);
          $amount = '-' . $amount;
          break;
        case 'increase':
          break;
        case 'target':
          $adj->setAdjustmentMethod('target');
          break;
      }
      
      $adj//->setAmount($_POST['adjustment']['amount'])
          ->setAmountInCents($amount)
          ->setMemo($_POST['adjustment']['memo']);
      $a = $adj->create($id);
      $this->log($a);
    }
    
    $sub = $service->subscription()->read($id);
    $this->view->sub = $sub;
    
    $this->view->headTitle('Adjust Subscription Balance');
    
    $this->log($sub);
  }
}