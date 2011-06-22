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
class SubscriptionsController extends ChargifyController 
{
  /**
   * Standard preDispatch() hook
   * 
   * Simply changes the layout for displaying subscriptions.
   *
   */
  public function preDispatch()
  {
    parent::preDispatch();
    $this->_helper->layout()->setLayout('subscription');
  }
  
  /**
   * Read the details of a specific subscription
   *
   */
  public function readAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    $sub = $service->subscription()->read($id);
    $this->view->sub = $sub;
    
    $subs = $service->subscription()->listByCustomer($sub['customer']['id']);
    $this->view->subs = $subs;
    
    $this->view->headTitle('Subscription Summary');
    
    $this->log($sub);
    $this->log($subs);
  }
  
  /**
   * List the components for a specific subscription
   *
   */
  public function listComponentsAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    $sub = $service->subscription()->read($id);
    $this->view->sub = $sub;
    
    $comps = $service->component()->listSubscription($id);
    $this->view->comps = $comps;
    
    $this->log($comps);
  }
  
  /**
   * Change the product for a subscription without proration
   *
   */
  public function editAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $service->subscription()
              ->setProductId($_POST['product_id'])
              ->update($id);
    }
    
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
    
    $products = $service->product()->listProducts();
    $prods = array();
    
    // do a little preprocessing to group into families
    foreach ($products as $p)
    {
      // subscriptions can only migrate to products within the same family
      if ($p['product_family']['id'] == $subscription['product']['product_family']['id'])
      {
        $prods[] = $p;
      }
    }
    $this->view->prods = $prods;
    
    $this->view->headTitle('Edit Subscription');
    
    $this->log($prods);
  }
  
  /**
   * Change product with proration
   *
   */
  public function migrateAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $sub = $service->subscription();
      $sub->setProductId($_POST['product_id']);
      if (isset($_POST['include_trial']))
      {
        $sub->setIncludeTrial(1);
      }
      $sub->migrate($id);
    }
    
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
    
    $products = $service->product()->listProducts();
    $prods = array();
    
    // do a little preprocessing to group into families
    foreach ($products as $p)
    {
      // subscriptions can only migrate to products within the same family
      if ($p['product_family']['id'] == $subscription['product']['product_family']['id'])
      {
        $prods[] = $p;
      }
    }
    $this->view->prods = $prods;
    
    $this->view->headTitle('Prorated Migration');
    
    $this->log($prods);
  }
  
  /**
   * Create a subscription
   * 
   * Includes Zferral integration
   */
  public function createAction()
  {
    $this->_helper->layout()->setLayout('default');
    
    /**
     * Initialize Zferral. This gets populated below if Zferral is enabled 
     * and the subscription is successful.
     */
    $this->view->zferral = array();
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      // @todo - allocate components at create time?
      $s = $service->subscription()
                   ->setProductId($_POST['subscription']['product_id'])
                   ->setCustomerId($_POST['subscription']['customer_id'])
                   ->setPaymentProfileAttributes(array(
                     'first_name'        => $_POST['subscription']['payment_profile_attributes']['first_name'],
                     'last_name'         => $_POST['subscription']['payment_profile_attributes']['last_name'],
                     'full_number'       => $_POST['subscription']['payment_profile_attributes']['full_number'],
                     'expiration_month'  => $_POST['subscription']['payment_profile_attributes']['expiration_month'],
                     'expiration_year'   => $_POST['subscription']['payment_profile_attributes']['expiration_year'],
                     'cvv'               => $_POST['subscription']['payment_profile_attributes']['cvv'],
                     'billing_address'   => $_POST['subscription']['payment_profile_attributes']['billing_address'],
                     'billing_address_2' => $_POST['subscription']['payment_profile_attributes']['billing_address_2'],
                     'billing_city'      => $_POST['subscription']['payment_profile_attributes']['billing_city'],
                     'billing_state'     => $_POST['subscription']['payment_profile_attributes']['billing_state'],
                     'billing_zip'       => $_POST['subscription']['payment_profile_attributes']['billing_zip'],
                     'billing_country'   => $_POST['subscription']['payment_profile_attributes']['billing_country']
                  ))
                  ->setCouponCode($_POST['subscription']['coupon_code'])
                  ->create();
      
      $this->log($s);
      //$this->log($s->isError());
      //$this->log($s->getErrors());
      
      /**
       * Set up variables for the Zferral tracking pixel
       */
      if (!$s->isError())
      {
        if (file_exists(APPLICATION_PATH . '/configs/zferral.ini'))
        {
          $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/zferral.ini', APPLICATION_ENV);
          if (1 == $config->zferral->enabled)
          {
            $this->view->zferral = array(
              'subdomain'       => $config->zferral->subdomain,
              'campaign_id'     => $config->zferral->campaign_id,
              'revenue'         => $s['signup_revenue'],
              'subscription_id' => $s['id'],
              'payment_id'      => $s['signup_payment_id']
            );
          }
        }
      }
    }
    
    $custs = $service->customer()->listCustomers();
    $this->view->custs = $custs;
    
    $prods = $service->product()->listProducts();
    
    $prodArray = array();
    // munge into nested array for creating optgroups
    foreach ($prods as $prod)
    {
      $family = $prod['product_family']['name'];
      $prodArray[$family][$prod['id']] = '(' . $prod['name'] . ')';
    }
    $this->view->prods = $prodArray;
    
    $this->view->countries = $this->_getCountries();
    $this->view->states = $this->_getStates();
    $this->view->months = $this->_getMonths();
    $this->view->years = $this->_getYears();
    
    $this->view->headTitle('Create Subscription');
    
    $this->log($custs);
    $this->log($prods);
    $this->log($prodArray);
  }
  
  /**
   * List subscriptions for your site
   *
   */
  public function listAction()
  {
    $this->_helper->layout()->setLayout('default');
    
    $page = $this->getRequest()->getParam('page', 1);
    
    $service = $this->_getChargify();
    
    $subs = $service->subscription()
                    ->setPage($page)
                    ->setPerPage(100)
                    ->listSubscriptions();
    
    // set up hosted URLS
    $hosted_urls = array();
    foreach ($subs as $s)
    {
      $hosted_urls[$s['id']] = $this->view->hostedUrl($s['id']);
    }
    
    $this->view->subs        = $subs;
    $this->view->hosted_urls = $hosted_urls;
    
    $this->view->headTitle('List Subscriptions');
    
    $this->log($subs);
    $this->log($hosted_urls);
  }
  
  /**
   * Add Metered components to a subscription.
   */
  public function componentUsageAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    $componentId = $this->getRequest()->getParam('component-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $c = $service->component()
                    ->setUsageQuantity($_POST['usage']['quantity'])
                    ->setMemo($_POST['usage']['memo'])
                    ->createUsage($id, $componentId);
      $this->log($c);
    }
    
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
    
    $comp = $service->component()->readSubscription($id, $componentId);
    $this->view->comp = $comp;
    
    $this->view->headTitle('Create Metered Usage');
    
    $this->log($subscription);
    $this->log($comp);
  }
  
  /**
   * Toggle the on/off state of an on/off component
   * 
   * On/Off components are handled exactly the same as quantity-based 
   * components, you are just setting the quantity to 1 for "on" or 0 for "off".
   * 
   */
  public function componentOnOffAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    $componentId = $this->getRequest()->getParam('component-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $c = $service->component()
                   ->setAllocatedQuantity($_POST['allocation']['quantity'])
                   ->setMemo($_POST['allocation']['memo'])
                   ->setQuantityAllocation($id, $componentId);
      $this->log($c);
    }
    
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
    
    $comp = $service->component()->readSubscription($id, $componentId);
    $this->view->comp = $comp;
    
    $this->view->headTitle('Toggle On/Off');
    
    $this->log($subscription);
    $this->log($comp);
  }
  
  /**
   * Set Quantity-based allocations on a subscription
   * 
   */
  public function componentAllocationAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    $componentId = $this->getRequest()->getParam('component-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $c = $service->component()
                   ->setAllocatedQuantity($_POST['allocation']['quantity'])
                   ->setMemo($_POST['allocation']['memo'])
                   ->setQuantityAllocation($id, $componentId);
      $this->log($c);
    }
    
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
    
    $comp = $service->component()->readSubscription($id, $componentId);
    $this->view->comp = $comp;
    
    $this->view->headTitle('New Quantity-based Allocation');
    
    $this->log($subscription);
    $this->log($comp);
  }
  
  /**
   * Edit payment profile for a subscription
   * 
   * DO NOT ENTER REAL CREDIT CARD INFORMATION!!
   *
   */
  public function editPaymentProfileAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $service->subscription()
              ->setPaymentProfileAttributes(array(
                'first_name'        => $_POST['subscription']['payment_profile_attributes']['first_name'],
                'last_name'         => $_POST['subscription']['payment_profile_attributes']['last_name'],
                'full_number'       => $_POST['subscription']['payment_profile_attributes']['full_number'],
                'expiration_month'  => $_POST['subscription']['payment_profile_attributes']['expiration_month'],
                'expiration_year'   => $_POST['subscription']['payment_profile_attributes']['expiration_year'],
                'cvv'               => $_POST['subscription']['payment_profile_attributes']['cvv'],
                'billing_address'   => $_POST['subscription']['payment_profile_attributes']['billing_address'],
                'billing_address_2' => $_POST['subscription']['payment_profile_attributes']['billing_address_2'],
                'billing_city'      => $_POST['subscription']['payment_profile_attributes']['billing_city'],
                'billing_state'     => $_POST['subscription']['payment_profile_attributes']['billing_state'],
                'billing_zip'       => $_POST['subscription']['payment_profile_attributes']['billing_zip'],
                'billing_country'   => $_POST['subscription']['payment_profile_attributes']['billing_country']
              ))
              ->update($id);
    }
    
    $subscription = $service->subscription()->read($id);
    
    $this->view->sub       = $subscription;
    $this->view->states    = $this->_getStates();
    $this->view->countries = $this->_getCountries();
    $this->view->months    = $this->_getMonths();
    $this->view->years     = $this->_getYears();
    
    $this->view->headTitle('Edit Payment Profile');
    
    $this->log($subscription);
  }
  
  /**
   * Cancel a subscription
   *
   */
  public function cancelAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $sub = $service->subscription();
      $sub->setCancellationMessage($_POST['subscription']['cancellation_message']);
      
      if (isset($_POST['cancel_immediately']))
      {
        $sub->cancelImmediately($id);
      }
      elseif (isset($_POST['cancel_delayed']))
      {
        $sub->cancelDelayed($id);
      }
    }
    
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
    
    $this->view->headTitle('Cancel Subscription');
    
    $this->log($subscription);
  }
  
  /**
   * Re-activate a subscription
   *
   */
  public function reactivateAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      if (!empty($_POST['reset_balance']))
      {
        $service->subscription()->resetBalance($id);
      }
      
      $service->subscription()->reactivate($id);
    }
    
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
    
    $this->view->headTitle('Reactivate Subscription');
    
    $this->log($subscription);
  }
  
  /**
   * Reset balance to 0.00 for a specific subscription
   *
   */
  public function resetBalanceAction()
  {
    $id = $this->getRequest()->getParam('subscription-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $service->subscription()->resetBalance($id);
    }
    
    $subscription = $service->subscription()->read($id);
    $this->view->sub = $subscription;
    
    $this->view->headTitle('Reset Balance');
    
    $this->log($subscription);
  }
  
}