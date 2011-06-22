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
 * @package Bootstrap
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
  protected function _initAutoload()
  {
    require_once 'Zend/Loader/Autoloader.php';
    $loader = Zend_Loader_Autoloader::getInstance();
    $loader->registerNamespace(array('Crucial_'));
  }
  
  protected function _initView()
  {
    $view = new Zend_View();
    $view->doctype('XHTML1_TRANSITIONAL');
    $view->setEncoding('UTF-8');
    $view->headMeta()
         ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
         ->appendHttpEquiv('Content-Language', 'en-US');
    $view->headTitle('Chargify Sample App')->setSeparator(' | ');
    $view->addHelperPath('Crucial/View/Helper/', 'Crucial_View_Helper');
    
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
      'ViewRenderer'
    );
    $viewRenderer->setView($view);
  }
  
  protected function _initRequest()
  {
    // Ensure front controller instance is present, and fetch it
    $this->bootstrap('FrontController');
    $front = $this->getResource('FrontController');
    // Initialize the request object
    $request = new Zend_Controller_Request_Http();
    // Add it to the front controller
    $front->setRequest($request);
    // Bootstrap will store this value in the 'request' key of its container
    return $request;
  }
}
