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
class ChargesController extends ChargifyController 
{
  
  /**
   * Add a one-off charge to the customer's credit card.
   * 
   * For "live" subscriptions (i.e. subscriptions that are not canceled or 
   * expired) you have the ability to attach a one-time (or "one-off") charge 
   * of an arbitrary amount. For more information on assessing charges, 
   * in general, please see One-time Charges.
   * 
   * @link http://docs.chargify.com/api-charges
   */
  public function addAction()
  {
    $this->_helper->layout()->setLayout('subscription');
    
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      // I should just filter digits here
      $amount = str_replace('$', '', $_POST['charge']['amount']);
      $amount = str_replace('.', '', $amount);
      //$amount = str_replace('-', '', $amount);
      $amount = trim($amount);
      
      $c = $service->charge()
                   //->setAmount($_POST['amount'])
                   ->setAmountInCents($amount)
                   ->setMemo($_POST['charge']['memo'])
                   ->create($id);
      $this->log($c);
    }
    
    $sub = $service->subscription()->read($id);
    $this->view->sub = $sub;
    
    $this->view->headTitle('Add a Charge');
  }
}