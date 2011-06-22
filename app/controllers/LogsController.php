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
class LogsController extends ChargifyController 
{
  /**
   * The directory where postbacks are logged
   *
   * @var string
   */
  protected $_postbackDir;
  
  /**
   * The directory where webhooks are logged
   *
   * @var string
   */
  protected $_webhookDir;
  
  /**
   * Standard preDispatch() hook
   * 
   * Simply sets the webhook and postback directories for easy access/re-use in
   * all actions.
   */
  public function preDispatch()
  {
    $this->_postbackDir = dirname(APPLICATION_PATH) . '/tmp/postbacks';
    $this->_webhookDir  = dirname(APPLICATION_PATH) . '/tmp/webhooks';
  }
  
  /**
   * Dashboard for listing webhooks and postbacks
   * 
   */
  public function indexAction()
  {
    // delete webhook and postback logs
    if ($this->getRequest()->isPost())
    {
      if (is_dir($this->_postbackDir))
      {
        exec("rm -rf $this->_postbackDir/*");
      }
      
      if (is_dir($this->_webhookDir))
      {
        exec("rm -rf $this->_webhookDir/*");
      }
    }
    
    $postbacks = array();
    if (is_dir($this->_postbackDir))
    {
      $postbacks = new DirectoryIterator($this->_postbackDir);
    }
    $this->view->postbacks = $postbacks;
    
    $webhooks = array();
    if (is_dir($this->_webhookDir))
    {
      $webhooks = new DirectoryIterator($this->_webhookDir);
    }
    $this->view->webhooks = $webhooks;
  }
  
  /**
   * Read the contents of a postback log
   *
   */
  public function readPostbackAction()
  {
    $file = $this->getRequest()->getParam('file');
    $log = file_get_contents($this->_postbackDir . '/' . $file);
    $this->view->log = $log;
  }
  
  /**
   * Read the contents of a webhook log
   *
   */
  public function readWebhookAction()
  {
    $file = $this->getRequest()->getParam('file');
    $log = file_get_contents($this->_webhookDir . '/' . $file);
    $this->view->log = $log;
  }
}