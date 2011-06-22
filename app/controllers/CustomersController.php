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
class CustomersController extends ChargifyController 
{
  
  /**
   * List customers
   *
   */
  public function listAction()
  {
    $page = $this->getRequest()->getParam('page');
    
    $service = $this->_getChargify();
    
    $cust = $service->customer()->listCustomers();
    $this->view->cust = $cust;
    
    $this->view->headTitle('List Customers');
    
    $this->log($cust);
  }
  
  /**
   * Read the details of a specific customer
   * 
   * Can be looked up by either Chargify customer ID or reference ID from your
   * app.
   * 
   */
  public function readAction()
  {
    $chargifyId = $this->getRequest()->getParam('customer-id');
    $reference = $this->getRequest()->getParam('reference-id');
    
    $service = $this->_getChargify();
    
    if (!empty($chargifyId))
    {
      $cust = $service->customer()->readByChargifyId($chargifyId);
    }
    elseif (!empty($reference))
    {
      $cust = $service->customer()
                      ->setReference($reference)
                      ->readByReference();
    }
    
    $this->view->cust = $cust;
    
    $subs = $service->subscription()->listByCustomer($cust['id']);
    $this->view->subs = $subs;
    
    $this->view->headTitle('Customer Data');
    
    $this->log($cust);
    $this->log($subs);
  }
  
  /**
   * Edit a customer
   *
   */
  public function editAction()
  {
    $id = $this->getRequest()->getParam('customer-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $c = $service->customer()
                   ->setFirstName($_POST['customer']['first_name'])
                   ->setLastName($_POST['customer']['last_name'])
                   ->setEmail($_POST['customer']['email'])
                   ->setOrganization($_POST['customer']['organization'])
                   ->setPhone($_POST['customer']['phone'])
                   ->setAddress($_POST['customer']['address'])
                   ->setAddress2($_POST['customer']['address_2'])
                   ->setCity($_POST['customer']['city'])
                   ->setState($_POST['customer']['state'])
                   ->setZip($_POST['customer']['zip'])
                   ->setCountry($_POST['customer']['country'])
                   ->setReference($_POST['customer']['reference'])
                   ->update($id);
    }
    
    $cust = $service->customer()->readByChargifyId($id);
    $this->view->cust      = $cust;
    $this->view->countries = $this->_getCountries();
    $this->view->states    = $this->_getStates();
    
    $this->view->headTitle('Edit Customer');
    
    $this->log($cust);
  }
  
  /**
   * Create a customer
   *
   */
  public function createAction()
  {
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $cust = $service->customer()
                      ->setFirstName($_POST['customer']['first_name'])
                      ->setLastName($_POST['customer']['last_name'])
                      ->setEmail($_POST['customer']['email'])
                      ->setOrganization($_POST['customer']['organization'])
                      ->setPhone($_POST['customer']['phone'])
                      ->setAddress($_POST['customer']['address'])
                      ->setAddress2($_POST['customer']['address_2'])
                      ->setCity($_POST['customer']['city'])
                      ->setState($_POST['customer']['state'])
                      ->setZip($_POST['customer']['zip'])
                      ->setCountry($_POST['customer']['country'])
                      ->setReference($_POST['customer']['reference'])
                      ->create();
      $this->log($cust);
    }
    
    $this->view->countries = $this->_getCountries();
    $this->view->states    = $this->_getStates();
    
    $this->view->headTitle('Create Customer');
  }
}