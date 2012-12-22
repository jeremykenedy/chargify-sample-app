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
class Crucial_Service_ChargifyV2_Call extends Crucial_Service_ChargifyV2_Abstract
{
  /**
   * Read the call data for the given Chargify ID
   *
   * @param string $callId
   * @return Crucial_Service_ChargifyV2_Call
   */
  public function readByChargifyId($callId)
  {
    $service = $this->getService();

    $response = $service->request('calls/' . $callId, 'GET');
    $responseArray = $this->getResponseArray($response);

    // a 404 will be returned if not found, so make sure we have a 200
    if (!$this->isError() && '200' == $response->getStatus())
    {
      $this->_data = $responseArray['call'];
    }
    else
    {
      $this->_data = array();
    }

    return $this;
  }
}