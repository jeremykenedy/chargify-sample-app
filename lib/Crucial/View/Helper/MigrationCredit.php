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
 * @package Crucial_View_Helper
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 */
class Crucial_View_Helper_MigrationCredit extends Zend_View_Helper_Abstract 
{
  /**
   * Calculate the credit owed to the customer when performing prorated 
   * migrations
   *
   * @param array $sub
   * @return int
   *  credit in cents
   */
  public function migrationCredit($sub)
  {
    // get number of seconds in current period
    $seconds = strtotime($sub['current_period_ends_at']) - strtotime($sub['current_period_started_at']);
    // determine how much 1 second of this product is worth
    $perSecond = $sub['product']['price_in_cents'] / $seconds;
    // determine how many seconds have been used
    $secondsUsed = time() - strtotime($sub['current_period_started_at']);
    // determine how cost of usage so far this period
    $usageCost = $secondsUsed * $perSecond;
    // credit equals product price - usage cost
    $credit = $sub['product']['price_in_cents'] - $usageCost;
    return floor($credit);
  }
}