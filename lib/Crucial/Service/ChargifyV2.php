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
 * @package Crucial_Service_ChargifyV2
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 * @link http://www.crucialwebstudio.com
 */
class Crucial_Service_ChargifyV2
{
  /**
   * The base URL for all api calls. NO TRAILING SLASH!
   *
   * @var string
   */
  protected $_baseUrl = 'https://api.chargify.com/api/v2';

  /**
   * Your api_d
   *
   * @var string
   */
  protected $_apiId;

  /**
   * Your api password
   *
   * @var string
   */
  protected $_apiPassword;

  /**
   * Secret key
   *
   * @var string
   */
  protected $_apiSecret;

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
   * Http client
   *
   * @var Zend_Http_Client
   */
  protected $_client;

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
    $this->_apiId       = $config['api_id'];
    $this->_apiPassword = $config['api_password'];
    $this->_apiSecret   = $config['api_secret'];
    $this->_format      = strtolower($config['format']);

    // set up http client
    $this->_client = new Zend_Http_Client();

    /**
     * @todo should these be config options?
     */
    $this->_client->setConfig(array(
      'maxredirects' => 0,
      'timeout'      => 30,
      'keepalive'    => TRUE,
      'useragent'    => 'Crucial_Service_ChargifyV2/1.0 (https://github.com/crucialwebstudio/Crucial_Service_Chargify)'
    ));

    // username, password for http authentication
    $this->_client->setAuth($this->_apiId, $this->_apiPassword, Zend_Http_Client::AUTH_BASIC);
  }

  /**
   * Get the base URL for all requests made to the api.
   *
   * Does not contain a trailing slash.
   *
   * @return string
   */
  public function getBaseUrl()
  {
    return $this->_baseUrl;
  }

  /**
   * Getter for api ID
   *
   * @return string
   */
  public function getApiId()
  {
    return $this->_apiId;
  }

  /**
   * Getter for api secret.
   *
   * Be careful not to expose this to anyone, especially in your html.
   *
   * @return string
   */
  public function getApiSecret()
  {
    return $this->_apiSecret;
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
   * Getter for $this->_client
   *
   * @return Zend_Http_Client
   */
  public function getClient()
  {
    return $this->_client;
  }

  /**
   * Send the request to Chargify
   *
   * @param string $path    URL path we are requesting such as: /subscriptions/<subscription_id>/adjustments
   * @param string $method  GET, POST, PUST, DELETE
   * @param string $rawData
   * @param array $params
   * @return Zend_Http_Response
   */
  public function request($path, $method, $rawData = NULL, $params = array())
  {
    $method = strtoupper($method);
    $path = ltrim($path, '/');

    $client = $this->getClient();
    $client->setUri($this->_baseUrl . '/' . $path . '.' . $this->_format);

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

    $response = $client->request($method);

    return $response;
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_ChargifyV2_Direct
   *
   * @return Crucial_Service_ChargifyV2_Direct
   */
  public function direct()
  {
    return new Crucial_Service_ChargifyV2_Direct($this);
  }

  /**
   * Helper for instantiating an instance of Crucial_Service_ChargifyV2_Call
   *
   * @return Crucial_Service_ChargifyV2_Call
   */
  public function call()
  {
    return new Crucial_Service_ChargifyV2_Call($this);
  }
}