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
class Crucial_View_Helper_ActiveTab extends Zend_View_Helper_Abstract
{
  /**
   * Returns the string "active" if the give tab is the active one. An empty
   * string if not.
   *
   * I know there is Zend_Navigation but it forces tight coupling of your tabs
   * to your modules/controllers/actions. I don't think navigation tabs should
   * enforce the structure of your application.
   *
   * @param string $tab
   * @return string
   */
  public function activeTab($tab)
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
      'subscription_summary' => array(
        'default/subscriptions/read',
        'default/subscriptions/migrate',
        'default/subscriptions/edit',
        'default/subscriptions/cancel',
        'default/subscriptions/edit-payment-profile'
      ),
      'subscription_components' => array(
        'default/subscriptions/list-components',
        'default/subscriptions/component-allocation',
        'default/subscriptions/component-usage',
      ),
      'subscription_transactions' => array(
        'default/transactions/for-subscription',
        'default/charges/add',
        'default/adjustments/create',
        'default/refunds/create'
      ),
      'subscription_statements' => array(
        'default/statements/list',
        'default/statements/read'
      ),
      'subscription_reset_balance' => array(
        'default/subscriptions/reset-balance'
      ),
      'subscription_events' => array(
        'default/subscriptions/events'
      )
    );

    // if the resource belongs to the $tabs[$tab] array, set the class to "current"
    if (in_array($resource, $tabs[$tab]))
    {
      $class = 'active';
    }

    return $class;
  }
}