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
class TransactionsController extends ChargifyController 
{
  /**
   * List transactions for a subscription
   *
   */
  public function forSubscriptionAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    $this->_helper->layout()->setLayout('subscription');
      
    $trans = $service->transaction()
                     ->setKinds($_GET['kinds'])
                     //->setSinceDate($_GET['since_date'])
                     //->setUntilDate($_GET['until_date'])
                     //->setSinceId($_GET['since_id'])
                     //->setMaxId($_GET['max_id'])
                     ->setPagination($_GET['page'], $_GET['per_page'])
                     ->listBySubscription($id);
                     
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
    
    $this->view->headTitle('Transactions for Subscription');
    
    $this->view->trans = $trans;
    
    $this->log($trans);
  }
  
  /**
   * List transactions for your entire site
   *
   */
  public function forSiteAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    $trans = $service->transaction()
                     ->setKinds($_GET['kinds'])
                     //->setSinceDate($_GET['since_date'])
                     //->setUntilDate($_GET['until_date'])
                     //->setSinceId($_GET['since_id'])
                     //->setMaxId($_GET['max_id'])
                     ->setPagination($_GET['page'], $_GET['per_page'])
                     ->listBySite();
                     
    $this->view->headTitle('Transactions for site');
    
    $this->view->trans = $trans;
    
    $this->log($trans);
  }
}