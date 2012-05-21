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
class Crucial_Service_Chargify_Event extends Crucial_Service_Chargify_Abstract
{
  /**
   * The page number and number of results used for pagination. By default
   * results are paginated 30 per page.
   *
   * @param int $page
   * @param int $perPage
   * @return Crucial_Service_Chargify_Event
   */
  public function setPagination($page, $perPage)
  {
    $this->setParam('page', $page);
    $this->setParam('per_page', $perPage);
    return $this;
  }

  /**
   * Set the lowermost event ID that you want returned.
   *
   * Only events with an event ID higher than this will be returned.
   *
   * @param int $sinceId
   * @return Crucial_Service_Chargify_Event
   */
  public function setSinceId($sinceId)
  {
    $this->setParam('since_id', $sinceId);
    return $this;
  }

  /**
   * Set the uppermost event ID that you want returned.
   *
   * Only events with an event ID lower than this will be returned.
   *
   * @param int $sinceId
   * @return Crucial_Service_Chargify_Event
   */
  public function setMaxId($maxId)
  {
    $this->setParam('max_id', $maxId);
    return $this;
  }

  /**
   * Set direction events should be returned.
   *
   * I believe this should be 'asc' or 'desc'. It's not documented in Chargify's
   * docs.
   *
   * @param string $direction
   * @return Crucial_Service_Chargify_Event
   */
  public function setDirection($direction)
  {
    $this->setParam('direction', $direction);
    return $this;
  }

  /**
   * Return events for a site
   *
   * @return Crucial_Service_Chargify_Event
   * @see Crucial_Service_Chargify_Event::setPagination()
   * @see Crucial_Service_Chargify_Event::setSinceId()
   * @see Crucial_Service_Chargify_Event::setMaxId()
   * @see Crucial_Service_Chargify_Event::setDirection()
   */
  public function forSite()
  {
    $service = $this->getService();

    // events for a site
    $response = $service->request('events', 'GET', NULL, $this->getParams());

    $responseArray = $this->getResponseArray($response);

    if (!$this->isError())
    {
      $this->_data = $this->_normalizeResponseArray($responseArray);
    }
    else
    {
      $this->_data = array();
    }

    return $this;
  }

  /**
   * Return events for the given subscription
   *
   * @param int $subscriptionId
   * @return Crucial_Service_Chargify_Event
   * @see Crucial_Service_Chargify_Event::setPagination()
   * @see Crucial_Service_Chargify_Event::setSinceId()
   * @see Crucial_Service_Chargify_Event::setMaxId()
   * @see Crucial_Service_Chargify_Event::setDirection()
   */
  public function forSubscription($subscriptionId)
  {
    $service = $this->getService();

    // events for a subscription
    $response = $service->request('subscriptions/' . $subscriptionId . '/events', 'GET', NULL, $this->getParams());

    $responseArray = $this->getResponseArray($response);

    if (!$this->isError())
    {
      $this->_data = $this->_normalizeResponseArray($responseArray);
    }
    else
    {
      $this->_data = array();
    }

    return $this;
  }

  /**
   * When returning multiple products the array is different depending on which
   * format (xml/json) you are using. This normalizes the array for us so we can
   * rely on a consistent structure.
   *
   * @param array $responseArray
   * @return array
   */
  protected function _normalizeResponseArray($responseArray)
  {
    $service = $this->getService();
    $format = $service->getFormat();

    $return = array();

    if ('xml' == $format)
    {
      return $responseArray['events']['event'];
    }

    if ('json' == $format)
    {
      foreach ($responseArray as $event)
      {
        $return[] = $event['event'];
      }
    }

    return $return;
  }
}