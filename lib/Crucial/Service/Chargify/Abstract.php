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
 * @link http://www.crucialwebstudio.com
 */
abstract class Crucial_Service_Chargify_Abstract implements ArrayAccess, Iterator, Countable
{
  /**
   * Container for parameters to be sent in API request
   *
   * @var array
   */
  protected $_params = array();
  
  /**
   * Container for errors thrown by the API
   *
   * @var array
   */
  protected $_errors = array();
  
  /**
   * Data container for providing ArrayAccess on this object
   *
   * @var array
   */
  protected $_data = array();
  
  /**
   * Instance of Crucial_Service_Chargify sent in constructor
   *
   * @var Crucial_Service_Chargify
   */
  protected $_service;
  
  /**
   * Simply stores service instance for use in concrete classes
   *
   * @param Crucial_Service_Chargify $service
   */
  public function __construct(Crucial_Service_Chargify $service)
  {
    $this->_service = $service;
  }
  
  /**
   * Get Crucial_Service_Chargify instance sent in constructor
   *
   * @return Crucial_Service_Chargify
   */
  public function getService()
  {
    return $this->_service;
  }
  
  /**
   * Set a single parameter. Provides fluent interface.
   *
   * @param string $param
   * @param mixed $value
   * @return Crucial_Service_Chargify_Abstract
   */
  public function setParam($param, $value)
  {
    $this->_params[$param] = $value;
    return $this;
  }
  
  /**
   * Get a single parameter.
   *
   * @param string $paramName
   * @return string|array
   */
  public function getParam($paramName)
  {
    return !empty($this->_params[$paramName]) ? $this->_params[$paramName] : NULL;
  }
  
  /**
   * Get all params.
   *
   * @return array
   */
  public function getParams()
  {
    return $this->_params;
  }
  
  /**
   * Assembles xml from given array
   *
   * @param array $array
   * @return string
   */
  public function arrayToXml($array)
  {
    $xml = '';
    foreach ($array as $k => $v)
    {
      if (is_array($v))
      {
        // load nested elements
        $v = $this->arrayToXml($v);
      }
      $xml .= "<$k>$v</$k>";
    }
    return $xml;
  }
  
  /**
   * Assmbles an object from given array
   *
   * @param array $array
   * @return stdClass
   */
  public function arrayToObject($array)
  {
    $object = new stdClass();
    foreach ($array as $k => $v)
    {
      if (is_array($v))
      {
        // load nested elements
        $v = $this->arrayToObject($v);
      }
      $object->{$k} = $v;
    }
    return $object;
  }
  
  /**
   * Assmbles a full xml document from given array
   *
   * @param array $array
   * @return string
   */
  public function getXml($array)
  {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= $this->arrayToXml($array);
    return $xml;
  }
  
  /**
   * Assembles the raw data (xml or json) from the given array
   *
   * @param array $array
   * @return string
   */
  public function getRawData($array)
  {
    $format = $this->getService()->getFormat();
    if ('xml' == $format)
    {
      return $this->getXml($array);
    }
    
    if ('json' == $format)
    {
      return Zend_Json::encode($array);
    }
  }
  
  /**
   * Helper to determine if there are errors with the request
   *
   * @return bool
   */
  public function isError()
  {
    return !empty($this->_errors);
  }
  
  /**
   * Array of errors, if any, returned from Chargify. Not necessarily HTTP errors
   * like 404 or 201 status codes.
   *
   * @return array
   */
  public function getErrors()
  {
    return $this->_errors;
  }
  
  /**
   * Transfoms the response body (xml or json) into an array we can more easily 
   * work with.
   *
   * @param Zend_Http_Response $response
   * @return array
   * @todo $this->_errors is populated with errors from Chargify. Should this 
   *  also populate a separate errors array when we get HTTP 404s or 201s?
   */
  public function getResponseArray(Zend_Http_Response $response)
  {
    $return = array();
    $format = $this->getService()->getFormat();
    $body = $response->getBody();
    $body = trim($body);
    
    /**
     * Return early if we have an empty body, which we can't turn into an array 
     * anyway. This happens in cases where the API returns a 404, and possibly 
     * other status codes.
     */
    if (empty($body))
    {
      return $return;
    }
    
    if ('json' == $format)
    {
      $return = Zend_Json::decode($body);
    }
    
    if ('xml' == $format)
    {
    	$json = Zend_Json::fromXml($body);
    	$return = Zend_Json::decode($json);
    }
    
    // set errors, if any
    if (!empty($return['errors']))
    {
      $this->_errors = $return['errors'];
    }
    
    return $return;
  }
  
  /**
   * Implementation of ArrayAccess
   */
  
  /**
   * For ArrayAccess interface
   * 
   * @param mixed $offset
   * @param mixed $value
   */
  public function offsetSet($offset, $value)
  {
    if (is_null($offset))
    {
      $this->_data[] = $value;
    }
    else
    {
      $this->_data[$offset] = $value;
    }
  }
  
  /**
   * For ArrayAccess interface
   * 
   * @param mixed $offset
   * @return bool
   */
  public function offsetExists($offset)
  {
    return isset($this->_data[$offset]);
  }
  
  /**
   * For ArrayAccess interface
   * 
   * @param mixed $offset
   */
  public function offsetUnset($offset)
  {
    unset($this->_data[$offset]);
  }
  
  /**
   * For ArrayAccess interface
   * 
   * @param mixed $offset
   * @return bool
   */
  public function offsetGet($offset)
  {
    return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
  }
  
  /**
   * Implementation of Iterator
   *
   */
  
  /**
   * For Iterator interface
   * 
   * @return void
   */
  public function rewind()
  {
    reset($this->_data);
  }
  
  /**
   * For Iterator interface
   * 
   * @return mixed
   */
  public function current()
  {
    return current($this->_data);
  }
  
  /**
   * For Iterator interface
   * 
   * @return mixed
   */
  public function key()
  {
    return key($this->_data);
  }
  
  /**
   * For Iterator interface
   * 
   * @return mixed
   */
  public function next()
  {
    return next($this->_data);
  }
  
  /**
   * For Iterator interface
   * 
   * @return bool
   */
  public function valid()
  {
    return $this->current() !== false;
  }
  
  /**
   * Implementation of countable
   */
  
  /**
   * For Countable interface
   * 
   * @return int
   */
  public function count()
  {
    return count($this->_data);
  }
}