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
 * @package Crucial_Service_Chargify
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 */
class Crucial_Service_Chargify
{
  /**
   * The complete hostname; e.g. "my-app-subdomain.chargify.com",
   * not just "my-app-subdomain"
   *
   * @var string
   */
  protected $_hostname;

  /**
   * Your http authentication password. The password is always "x".
   *
   * @var string
   */
  protected $_password = 'x';

  /**
   * Your api key
   *
   * @var string
   */
  protected $_apiKey;

  /**
   * Shared key
   *
   * @var string
   */
  protected $_sharedKey;

  /**
   * xml or json
   *
   * @var string
   */
  protected $_format;

  /**
   * Config used in constructor.
   *
   * @var Zend_Config|array
   */
  protected $_config;

  /**
   * Initialize the service
   *
   * @param Zend_Config|array $config
   */
  public function __construct($config)
  {
    // store a copy
    $this->_config = $config;

    if ($config instanceof Zend_Config)
    {
      $config = $config->toArray();
    }

    // set individual properties
    $this->_hostname  = $config['hostname'];
    $this->_apiKey    = $config['api_key'];
    $this->_sharedKey = $config['shared_key'];
    $this->_format    = strtolower($config['format']);
  }

  /**
   *
   * @return Zend_Http_Client
   */
  protected function _getHttpClient()
  {
    $client = new Zend_Http_Client();
    /**
     * @todo should these be config options?
     */
    $client->setConfig(array(
      'maxredirects' => 0,
      'timeout'      => 30,
      'keepalive'    => TRUE,
      'useragent'    => 'Crucial_Service_Chargify/1.1 (https://github.com/crucialwebstudio/Crucial_Service_Chargify)'
    ));

    // username, password for http authentication
    $client->setAuth($this->_apiKey, $this->_password, Zend_Http_Client::AUTH_BASIC);
    return $client;
  }

  /**
   * xml or json
   *
   * @return string
   */
  public function getFormat()
  {
    return $this->_format;
  }

  /**
   * Returns config sent in constructor
   *
   * @return Zend_Config|array
   */
  public function getConfig()
  {
    return $this->_config;
  }

  /**
   * Send the request to Chargify
   *
   * @param string $path    URL path we are requesting such as: /subscriptions/<subscription_id>/adjustments
   * @param string $method  GET, POST, PUST, DELETE
   * @param string $rawData
   * @param array $params
   * @return Zend_Http_Response|FALSE Response object or FALSE if there was an exception during the request
   */
  public function request($path, $method, $rawData = NULL, $params = array())
  {
    $method = strtoupper($method);
    $path = ltrim($path, '/');

    $client = $this->_getHttpClient();
    $client->setUri('https://' . $this->_hostname . '/' . $path . '.' . $this->_format);

    // unset headers. they don't get cleared between requests
    $client->setHeaders(array(
      'Content-Type' => NULL,
      'Accept'       => NULL
    ));

    // clear parameters
    $client->resetParameters();

    $client->setHeaders(array(
      'Content-Type' => 'application/' . $this->_format
    ));

    // set headers if POST or PUT
    if (in_array($method, array('POST', 'PUT')))
    {
      if (NULL === $rawData)
      {
        throw new Crucial_Service_Chargify_Exception('You must send raw data in a POST or PUT request');
      }

      $client->setHeaders(array(
        'Content-Type' => 'application/' . $this->_format
      ));

      if (!empty($params))
      {
        $client->setParameterGet($params);
      }

      $client->setRawData($rawData, 'application/' . $this->_format);
    }

    // set headers if GET or DELETE
    if (in_array($method, array('GET', 'DELETE')))
    {
      $client->setHeaders(array(
        'Accept' => 'application/' . $this->_format
      ));

      if (!empty($rawData))
      {
        $client->setRawData($rawData, 'application/' . $this->_format);
      }

      if (!empty($params))
      {
        foreach ($params as $k => $v)
        {
          /**
           * test for array and adjust URI accordingly
           * this is needed for ?kinds[]=charge&kinds[]=info since Zend_Http_Client
           * doesn't handle this well with setParameterGet()
           */
          if (is_array($v))
          {
            $uri = '?';
            foreach ($v as $value)
            {
              $uri .= $k . '[]=' . $value . '&';
            }
            $uri = $client->getUri(TRUE) . trim($uri, '&');
            $client->setUri($uri);
          }
          else
          {
            $client->setParameterGet($k, $v);
          }
        }
      }
    }

    try
    {
      $response = $client->request($method);
    }
    catch (Exception $e)
    {
      return FALSE;
    }

    return $response;
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_Chargify_Customer
   *
   * @return Crucial_Service_Chargify_Customer
   */
  public function customer()
  {
    return new Crucial_Service_Chargify_Customer($this);
  }

  /**
   * Helper for instantiating an instance of
   * Crucial_Service_Chargify_Subscription
   *
   * @return Crucial_Service_Chargify_Subscription
   */
  public function subscription()
  {
    return new Crucial_Service_Chargify_Subscription($this);
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_Chargify_Product
   *
   * @return Crucial_Service_Chargify_Product
   */
  public function product()
  {
    return new Crucial_Service_Chargify_Product($this);
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_Chargify_Adjustment
   *
   * @return Crucial_Service_Chargify_Adjustment
   */
  public function adjustment()
  {
    return new Crucial_Service_Chargify_Adjustment($this);
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_Chargify_Charge
   *
   * @return Crucial_Service_Chargify_Charge
   */
  public function charge()
  {
    return new Crucial_Service_Chargify_Charge($this);
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_Chargify_Component
   *
   * @return Crucial_Service_Chargify_Component
   */
  public function component()
  {
    return new Crucial_Service_Chargify_Component($this);
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_Chargify_Coupon
   *
   * @return Crucial_Service_Chargify_Coupon
   */
  public function coupon()
  {
    return new Crucial_Service_Chargify_Coupon($this);
  }

  /**
   * Helper for instantiating an instance of
   * Crucial_Service_Chargify_Transaction
   *
   * @return Crucial_Service_Chargify_Transaction
   */
  public function transaction()
  {
    return new Crucial_Service_Chargify_Transaction($this);
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_Chargify_Refund
   *
   * @return Crucial_Service_Chargify_Refund
   */
  public function refund()
  {
    return new Crucial_Service_Chargify_Refund($this);
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_Chargify_Statement
   *
   * @return Crucial_Service_Chargify_Statement
   */
  public function statement()
  {
    return new Crucial_Service_Chargify_Statement($this);
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_Chargify_Event
   *
   * @return Crucial_Service_Chargify_Event
   */
  public function event()
  {
    return new Crucial_Service_Chargify_Event($this);
  }
}