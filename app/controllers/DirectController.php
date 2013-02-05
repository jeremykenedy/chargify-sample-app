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
 *
 * @link http://docs.chargify.com/chargify-direct-introduction
 * @link http://docs.chargify.com/chargify-direct-signups
 */
class DirectController extends ChargifyController
{
  /**
   * Signup using Chargify Direct
   *
   */
  public function signupAction()
  {
    $service = $this->_getChargify();
    $serviceV2 = $this->_getChargifyV2();

    /**
     * If we have $_GET['signature'] that means Chargify has redirected back to
     * our redirect_uri after a POST.
     */
    if (isset($_GET['signature']))
    {
      $this->_handleSignupRedirect($serviceV2);
    }

    // customers
    $custs = $service->customer()->listCustomers();
    $this->view->custs = $custs;

    // products
    $prods = $service->product()->listProducts();
    $prodArray = array();
    // munge into nested array for creating optgroups
    foreach ($prods as $prod)
    {
      $family = $prod['product_family']['name'];
      $prodArray[$family][$prod['id']] = '(' . $prod['name'] . ')';
    }
    $this->view->prods = $prodArray;

    // options
    $this->view->states    = $this->_getStates();
    $this->view->countries = $this->_getCountries();
    $this->view->months    = $this->_getMonths();
    $this->view->years     = $this->_getYears();

    // chargify direct
    $direct = $serviceV2->direct();
    $direct->setRedirect('http://' . $_SERVER['HTTP_HOST'] . '/direct/signup');
    $this->view->direct = $direct;

    $this->view->headTitle('Signup | Chargify Direct');
  }

  /**
   * Handle the redirection from Chargify after signup
   *
   * The GET part of POST/REDIRECT/GET
   *
   * @param Crucial_Service_ChargifyV2 $service
   * @throws Crucial_Service_ChargifyV2_Exception
   */
  protected function _handleSignupRedirect(Crucial_Service_ChargifyV2 $service)
  {
    $direct = $service->direct();

    /**
     * Example query string after redirect
     *
     * api_id=*****
     * call_id=*****
     * nonce=*****
     * result_code=4000
     * signature=*****
     * status_code=422
     * timestamp=1356143461
     */

    // Test for a valid response signature.
    if (!$direct->isValidResponseSignature())
    {
      // we should throw a hard exception here because there is a good chance we are being attacked
      throw new Crucial_Service_ChargifyV2_Exception('Invalid response signature after redirect from Chargify');
    }

    // Get the original call from Chargify
    $call = $service->call();
    $theCall = $call->readByChargifyId($_GET['call_id']);
    $this->log($theCall);

    if (!$theCall['success'])
    {
      // Tell the view there was an error so we can alert the user.
      $this->view->isError = TRUE;
      // repopulate the form with original request data
      $this->view->request = $theCall['request'];
    }
    else
    {
      $this->view->isSuccess = TRUE;
    }
  }

  /**
   * Edit payment profile using Chargify Direct
   *
   */
  public function editPaymentProfileAction()
  {
    $this->_helper->layout()->setLayout('subscription');

    $id = $this->getRequest()->getParam('subscription-id');

    $service = $this->_getChargify();
    $serviceV2 = $this->_getChargifyV2();

    /**
     * If we have $_GET['signature'] that means Chargify has redirected back to
     * our redirect_uri after a POST.
     */
    if (isset($_GET['signature']))
    {
      $this->_handleCardUpdateRedirect($serviceV2);
    }

    $subscription = $service->subscription()->read($id);

    $this->view->sub       = $subscription;
    $this->view->states    = $this->_getStates();
    $this->view->countries = $this->_getCountries();
    $this->view->months    = $this->_getMonths();
    $this->view->years     = $this->_getYears();

    // chargify direct
    $direct = $serviceV2->direct();
    $direct->setRedirect('http://' . $_SERVER['HTTP_HOST'] . '/direct/edit-payment-profile/subscription-id/' . $id);
    $direct->setData(array(
      'subscription_id' => $id
    ));
    $this->view->direct = $direct;

    $this->view->headTitle('Edit Payment Profile | Chargify Direct');

    $this->log($subscription);
  }

  /**
   * Handle the redirection from Chargify after card update
   *
   * The GET part of POST/REDIRECT/GET
   *
   * @param Crucial_Service_ChargifyV2 $service
   * @throws Crucial_Service_ChargifyV2_Exception
   */
  protected function _handleCardUpdateRedirect(Crucial_Service_ChargifyV2 $service)
  {
    $direct = $service->direct();

    /**
     * Example query string after redirect
     *
     * api_id=643c0f40-2d26-0130-27f0-026566abd2f9
     * call_id=aede0389cc7fc3d344e5f07907e49d2c3c3875ea
     * nonce=831398958abe9bccca1aebb2e506ad0c452d2bc1
     * result_code=4000
     * signature=c813313869f92aeb8dfe9ed6280922fa62465777
     * status_code=422
     * timestamp=1356143461
     */

    // Test for a valid response signature.
    if (!$direct->isValidResponseSignature())
    {
      // we should throw a hard exception here because there is a good chance we are being attacked
      throw new Crucial_Service_ChargifyV2_Exception('Invalid response signature after redirect from Chargify');
    }

    // Get the original call from Chargify
    $call = $service->call();
    $theCall = $call->readByChargifyId($_GET['call_id']);
    $this->log($theCall);

    // For some reason $theCall['status'] is always NULL for a credit card update.
    // This is different from signups where we can expect TRUE/FALSE.
    // @todo - ask Chargify about this inconsistent behavior
    if (200 != $theCall['response']['result']['status_code'])
    {
      // Tell the view there was an error so we can alert the user.
      $this->view->isError = TRUE;
      // repopulate the form with original request data
      $this->view->request = $theCall['request'];
    }
    else
    {
      $this->view->isSuccess = TRUE;
    }
  }

  public function authTestAction()
  {
    $serviceV2 = $this->_getChargifyV2();
    $direct = $serviceV2->direct();
    $result = $direct->authTest();
    $this->view->result = $result;
  }
}
