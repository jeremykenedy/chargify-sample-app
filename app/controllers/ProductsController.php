<?php
require_once('ChargifyController.php');

/**
 * Controller for handling Products
 * 
 * @category App
 * @package Controllers
 * @link http://docs.chargify.com/api-products
 */
class ProductsController extends ChargifyController 
{
  
  /**
   * List products for your site
   *
   */
  public function listAction()
  {
    $service = $this->_getChargify();
    
    $products = $service->product()->listProducts();
    
    $families   = array();
    $components = array();
    // do a little preprocessing to group into families
    foreach ($products as $p)
    {
      $families[$p['product_family']['id']]['name']               = $p['product_family']['name'];
      $families[$p['product_family']['id']]['products'][$p['id']] = $p;
      $components[$p['product_family']['id']]                     = $service->component()->listProductFamily($p['product_family']['id']);
    }
    
    $this->view->families = $families;
    $this->view->hostname = $service->getConfig()->hostname;
    $this->view->comps = $components;
    
    $this->view->headTitle('List Products');
    
    $this->log($families);
    $this->log($components);
  }
  
  /**
   * Read the details of a specific product
   *
   */
  public function readAction()
  {
    $service = $this->_getChargify();
    
    $product = $service->product();

    if ($this->getRequest()->getParam('id'))
    {
      $p = $product->readByChargifyId($this->getRequest()->getParam('id'));
    }
    elseif ($this->getRequest()->getParam('handle'))
    {
      $p = $product->readByHandle($this->getRequest()->getParam('handle'));
    }
    
    $this->view->headTitle('Product Data');
    
    $this->view->product = $p;
  }
}