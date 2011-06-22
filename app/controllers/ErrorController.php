<?php
require_once('ChargifyController.php');

/**
 * Standard error controller
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
class ErrorController extends ChargifyController 
{
  /**
   * Standard preDispatch() hook
   * 
   * Determines what type of errors are present, sets appropriate http reponse 
   * code, and clears previous body content prior to rendering.
   *
   */
  public function preDispatch()
  {
    parent::preDispatch();
    
    // set up errors
    $errors = $this->_getParam('error_handler');
    
    // clear response body and set layout
    $this->getResponse()->clearBody();
    
    // handle errors from default error handler
    if ($errors)
    {
      switch ($errors->type)
      {   
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
          // 404 error -- controller or action not found
          $this->getResponse()->setHttpResponseCode(404);
          $this->_helper->viewRenderer->setRender('error-404');
          $this->view->headTitle('Page Not Found');
          $this->view->message = 'Page Not Found';
          break;
          
        default:
          // application error
          $this->getResponse()->setHttpResponseCode(500);
          $this->_helper->viewRenderer->setRender('error-500');
          $this->view->headTitle('Internal Server Error');
          $this->view->message = 'Internal Server Error';
          break;
      }
    }
  }
  
  /**
   * Display the error page
   *
   */
  public function errorAction()
  {
    
  }
  
  /**
   * Standard postDispatch() hook
   * 
   * Detect environment and give ourselves good debug output while in 
   * development. Also log the error to PHP's standard error log. Since we're 
   * catching it here it wouldn't be logged otherwise.
   *
   */
  public function postDispatch()
  {
    // give ourselves good debug output in development
    if ('development' == APPLICATION_ENV)
    {
      // regardless of the error we want a debug view while in development
      $this->_helper->viewRenderer->setRender('error-debug');
      $errors = $this->_getParam('error_handler');
      
      if ($errors)
      {
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
        
        // put the exception in the error_log
        error_log($errors->exception);
      }
    }
  }
}