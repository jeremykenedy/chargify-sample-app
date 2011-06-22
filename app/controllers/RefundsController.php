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
class RefundsController extends ChargifyController 
{
  /**
   * Create a refund
   *
   */
  public function createAction()
  {
    $this->_helper->layout()->setLayout('subscription');
    
    $id = $this->getRequest()->getParam('subscription-id');
    $paymentId = $this->getRequest()->getParam('payment-id');
    
    $service = $this->_getChargify();
    
    $sub = $service->subscription()->read($id);
    $this->view->sub = $sub;
    
    if ($this->getRequest()->isPost())
    {
      $refund = $service->refund()
                        ->setAmount($_POST['refund']['amount'])
                        //->setAmountInCents($_POST['refund']['amount'])
                        ->setMemo($_POST['refund']['memo'])
                        ->setPaymentId($paymentId)
                        ->create($id);
      $this->log($refund);
    }
    
    $trans = array();
    
    if (!$paymentId)
    {
      $trans = $service->transaction()
                       ->setKinds(array('payment'))
                       //->setSinceDate($_GET['since_date'])
                       //->setUntilDate($_GET['until_date'])
                       //->setSinceId($_GET['since_id'])
                       //->setMaxId($_GET['max_id'])
                       //->setPagination($_GET['page'], $_GET['per_page'])
                       ->listBySubscription($id);
    }
                     
    $this->view->trans = $trans;
    $this->view->subscriptionId = $id;
    $this->view->paymentId = $paymentId;
    
    $this->view->headTitle('Add a Refund');
    
    $this->log($sub);
    $this->log($trans);
  }
}