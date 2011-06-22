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
class StatementsController extends ChargifyController 
{
  /**
   * List statements for a subcsription
   *
   */
  public function listAction()
  {
    $this->_helper->layout()->setLayout('subscription');
    
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    $statements = $service->statement()
                          //->setPage($_GET['page'])
                          ->listStatements($id);
    $this->view->statements = $statements;
    
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
  }
  
  /**
   * Read the details of a specific statement
   *
   */
  public function readAction()
  {
    $this->_helper->layout()->setLayout('subscription');
    
    $id = $this->getRequest()->getParam('statement-id');
    
    $service = $this->_getChargify();
    
    $statement = $service->statement()
                         ->read($id);
                         
    $this->view->statement = $statement['html_view'];
    
    $subscription = $service->subscription()->read($statement['subscription_id']);
    $this->view->sub = $subscription;
    
    $this->log($statement);
  }
}