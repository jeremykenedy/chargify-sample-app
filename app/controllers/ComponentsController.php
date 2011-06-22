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
class ComponentsController extends ChargifyController 
{
  
  /**
   * Intermediate page for choosing which type of component to create
   */
  public function newAction()
  {
    $familyId = $this->getRequest()->getParam('family-id');
    
    $this->view->familyId = $familyId;
  }
  
  /**
   * Create a metered component
   *
   */
  public function createMeteredAction()
  {
    $familyId = $this->getRequest()->getParam('family-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $component = $service->component();
      
      // add $prices array to any pricing scheme that is NOT per_unit
      if ('per_unit' != $_POST['metered_component']['pricing_scheme'])
      {
        $prices = array();
        foreach ($_POST['metered_component']['prices_attributes'] as $att)
        {
          if (!empty($att['starting_quantity']) && !empty($att['ending_quantity']) && !empty($att['unit_price']))
          {
          	$prices[] = $att;
          }
        }
        $component->setPrices($prices);
      }
      else 
      {
        $component->setUnitPrice($_POST['metered_component']['unit_price']);
      }
      
      $comp = $component->setName($_POST['metered_component']['name'])
                        ->setUnitName($_POST['metered_component']['unit_name'])
                        ->setPricingScheme($_POST['metered_component']['pricing_scheme'])
                        ->createComponent($familyId, 'metered_components');
                        
      $this->log($comp);
    }
  }
  
  /**
   * Create a quantity-based component
   *
   */
  public function createQuantityAction()
  {
    $familyId = $this->getRequest()->getParam('family-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $component = $service->component();
      
      // add $prices array to any pricing scheme that is NOT per_unit
      if ('per_unit' != $_POST['quantity_based_component']['pricing_scheme'])
      {
        $prices = array();
        foreach ($_POST['quantity_based_component']['prices_attributes'] as $att)
        {
          if (!empty($att['starting_quantity']) && !empty($att['ending_quantity']) && !empty($att['unit_price']))
          {
          	$prices[] = $att;
          }
        }
        $component->setPrices($prices);
      }
      else 
      {
        $component->setUnitPrice($_POST['quantity_based_component']['unit_price']);
      }
      
      $comp = $component->setName($_POST['quantity_based_component']['name'])
                        ->setUnitName($_POST['quantity_based_component']['unit_name'])
                        ->setPricingScheme($_POST['quantity_based_component']['pricing_scheme'])
                        ->createComponent($familyId, 'quantity_based_components');
                        
      $this->log($comp);
    }
  }
  
  /**
   * Create an on-off component
   *
   */
  public function createOnOffAction()
  {
    $familyId = $this->getRequest()->getParam('family-id');
    
    $service = $this->_getChargify();
    
    if ($this->getRequest()->isPost())
    {
      $comp = $service->component()
                      ->setName($_POST['on_off_component']['name'])
                      ->setUnitPrice($_POST['on_off_component']['unit_price'])
                      ->createComponent($familyId, 'on_off_components');
                      
      $this->log($comp);
    }
  }
}