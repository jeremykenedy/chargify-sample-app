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
 * @package Crucial_View_Helper
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 */
class Crucial_View_Helper_CurrentTab extends Zend_View_Helper_Abstract 
{
  /**
   * Returns the string "current" if the give tab is the active one. An empty
   * string if not.
   * 
   * I know there is Zend_Navigation but it forces tight coupling of your tabs 
   * to your modules/controllers/actions. I don't think navigation tabs should 
   * enforce the structure of your application.
   *
   * @param string $tab
   * @return string
   */
  public function currentTab($tab)
  {
    // start out with empty class
    $class = '';
    
    // determine what module/controller/action we're in
    $request    = Zend_Controller_Front::getInstance()->getRequest();
    $module     = $request->getModuleName();
    $controller = $request->getControllerName();
    $action     = $request->getActionName();
    
    // set up a resource for easy checking against $tabs[$tab] array
    $resource = "$module/$controller/$action";
    
    // set up array of tabs
    $tabs = array(
      'dashboard' => array(
        'default/index/index'
      ),
      'products' => array(
        'default/products/list',
        'default/products/read',
        'default/components/new',
        'default/components/create-metered',
        'default/components/create-quantity',
        'default/components/create-on-off'
      ),
      'subscriptions' => array(
        'default/subscriptions/list',
        'default/subscriptions/create',
        'default/subscriptions/read',
        'default/subscriptions/list-components',
        'default/subscriptions/reset-balance',
        'default/subscriptions/migrate',
        'default/subscriptions/edit',
        'default/subscriptions/component-allocation',
        'default/subscriptions/component-usage',
        'default/subscriptions/cancel',
        'default/subscriptions/edit-payment-profile',
        'default/transactions/for-subscription',
        'default/statements/list',
        'default/statements/read',
        'default/charges/add',
        'default/adjustments/create',
        'default/refunds/create'
      ),
      'transactions' => array(
        'default/transactions/for-site'
      ),
      'customers' => array(
        'default/customers/list',
        'default/customers/create',
        'default/customers/read',
        'default/customers/edit'
      ),
      'logs' => array(
        'default/logs/index',
        'default/logs/read-webhook',
        'default/logs/read-postback'
      )
    );
    
    // if the resource belongs to the $tabs[$tab] array, set the class to "current"
    if (in_array($resource, $tabs[$tab]))
    {
      $class = 'current';
    }
    
    return $class;
  }
}