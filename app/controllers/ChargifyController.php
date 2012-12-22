<?php
/**
 * A common controller extended by all other controllers in the application.
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
class ChargifyController extends Zend_Controller_Action
{
  /**
   * Create an instance of Crucial_Service_Chargify
   *
   * Just a helper for making instantiation easier. Within a controller you can
   * simply call $service = $this->_getChargify() to get an instance.
   *
   * @return Crucial_Service_Chargify
   * @example $service = $this->_getChargify();
   */
  protected function _getChargify()
  {
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/chargify.ini', APPLICATION_ENV);
    $service = new Crucial_Service_Chargify($config);

    return $service;
  }

  /**
   * Create an instance of Crucial_Service_ChargifyV2
   *
   * Just a helper for making instantiation easier. Within a controller you can
   * simply call $service = $this->_getChargifyV2() to get an instance.
   *
   * @return Crucial_Service_ChargifyV2
   * @example $service = $this->_getChargifyV2();
   */
  protected function _getChargifyV2()
  {
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/chargifyV2.ini', APPLICATION_ENV);
    $service = new Crucial_Service_ChargifyV2($config);

    return $service;
  }

  /**
   * Log something to Firebug
   *
   * @param string $message
   *  Message to log
   * @param int $priority
   *  Priority of the message
   * @param mixed $extras
   *  Extra information to log in event
   * @return void
   * @link http://framework.zend.com/manual/en/zend.log.writers.html#zend.log.writers.firebug
   */
  public function log($message, $priority = NULL, $extras = NULL)
  {
    static $logger;

    if (!$logger)
    {
      $logger = new Zend_Log();
      $logger->addWriter(new Zend_Log_Writer_Firebug());
    }
    // default priority is INFO
    if (NULL == $priority)
    {
      $priority = Zend_Log::INFO;
    }

    $logger->log($message, $priority, $extras);
  }

  /**
   * Get an array of countries keyed by their abbreviation
   *
   * Helpful for creating select lists.
   *
   * @return array
   */
  protected function _getCountries()
  {
    $countries = array(
      'AU' => 'Australia',
      'CA' => 'Canada',
      'NZ' => 'New Zealand',
      'SG' => 'Singapore',
      'ZA' => 'South Africa',
      'GB' => 'United Kingdom',
      'US' => 'United States',
      ''   => '--',
      'AF' => 'Afghanistan',
      'AL' => 'Albania',
      'DZ' => 'Algeria',
      'AD' => 'Andorra',
      'AO' => 'Angola',
      'AG' => 'Antigua and Barbuda',
      'AR' => 'Argentina',
      'AM' => 'Armenia',
      'AU' => 'Australia',
      'AT' => 'Austria',
      'AZ' => 'Azerbaijan',
      'BS' => 'Bahamas',
      'BH' => 'Bahrain',
      'BD' => 'Bangladesh',
      'BB' => 'Barbados',
      'BY' => 'Belarus',
      'BE' => 'Belgium',
      'BZ' => 'Belize',
      'BJ' => 'Benin',
      'BM' => 'Bermuda',
      'BT' => 'Bhutan',
      'BO' => 'Bolivia, Plurinational State Of',
      'BA' => 'Bosnia and Herzegovina',
      'BW' => 'Botswana',
      'BR' => 'Brazil',
      'BN' => 'Brunei Darussalam',
      'BG' => 'Bulgaria',
      'BF' => 'Burkina Faso',
      'BI' => 'Burundi',
      'KH' => 'Cambodia',
      'CM' => 'Cameroon',
      'CA' => 'Canada',
      'CV' => 'Cape Verde',
      'KY' => 'Cayman Islands',
      'CF' => 'Central African Republic',
      'TD' => 'Chad',
      'CL' => 'Chile',
      'CN' => 'China',
      'CO' => 'Colombia',
      'KM' => 'Comoros',
      'CG' => 'Congo',
      'CD' => 'Congo, The Democratic Republic Of The',
      'CR' => 'Costa Rica',
      'CI' => 'C&ocirc;te d&quot;Ivoire',
      'HR' => 'Croatia',
      'CU' => 'Cuba',
      'CY' => 'Cyprus',
      'CZ' => 'Czech Republic',
      'DK' => 'Denmark',
      'DJ' => 'Djibouti',
      'DM' => 'Dominica',
      'DO' => 'Dominican Republic',
      'EC' => 'Ecuador',
      'EG' => 'Egypt',
      'SV' => 'El Salvador',
      'GQ' => 'Equatorial Guinea',
      'ER' => 'Eritrea',
      'EE' => 'Estonia',
      'ET' => 'Ethiopia',
      'FJ' => 'Fiji',
      'FI' => 'Finland',
      'FR' => 'France',
      'TF' => 'French Southern Territories',
      'GA' => 'Gabon',
      'GM' => 'Gambia',
      'GE' => 'Georgia',
      'DE' => 'Germany',
      'GH' => 'Ghana',
      'GR' => 'Greece',
      'GD' => 'Grenada',
      'GT' => 'Guatemala',
      'GG' => 'Guernsey',
      'GN' => 'Guinea',
      'GW' => 'Guinea-Bissau',
      'GY' => 'Guyana',
      'HT' => 'Haiti',
      'HN' => 'Honduras',
      'HU' => 'Hungary',
      'IS' => 'Iceland',
      'IN' => 'India',
      'ID' => 'Indonesia',
      'IR' => 'Iran, Islamic Republic Of',
      'IQ' => 'Iraq',
      'IE' => 'Ireland',
      'IL' => 'Israel',
      'IT' => 'Italy',
      'JM' => 'Jamaica',
      'JP' => 'Japan',
      'JE' => 'Jersey',
      'JO' => 'Jordan',
      'KZ' => 'Kazakhstan',
      'KE' => 'Kenya',
      'KI' => 'Kiribati',
      'KP' => 'Korea, Democratic People&quot;s Republic Of',
      'KR' => 'Korea, Republic of',
      'KW' => 'Kuwait',
      'KG' => 'Kyrgyzstan',
      'LA' => 'Lao People&quot;s Democratic Republic',
      'LV' => 'Latvia',
      'LB' => 'Lebanon',
      'LS' => 'Lesotho',
      'LR' => 'Liberia',
      'LY' => 'Libyan Arab Jamahiriya',
      'LI' => 'Liechtenstein',
      'LT' => 'Lithuania',
      'LU' => 'Luxembourg',
      'MK' => 'Macedonia, the Former Yugoslav Republic Of',
      'MG' => 'Madagascar',
      'MW' => 'Malawi',
      'MY' => 'Malaysia',
      'MV' => 'Maldives',
      'ML' => 'Mali',
      'MH' => 'Marshall Islands',
      'MR' => 'Mauritania',
      'MU' => 'Mauritius',
      'MX' => 'Mexico',
      'FM' => 'Micronesia, Federated States Of',
      'MD' => 'Moldova, Republic of',
      'MN' => 'Mongolia',
      'ME' => 'Montenegro',
      'MA' => 'Morocco',
      'MZ' => 'Mozambique',
      'MM' => 'Myanmar',
      'NA' => 'Namibia',
      'NR' => 'Nauru',
      'NP' => 'Nepal',
      'NL' => 'Netherlands',
      'NZ' => 'New Zealand',
      'NI' => 'Nicaragua',
      'NE' => 'Niger',
      'NG' => 'Nigeria',
      'NO' => 'Norway',
      'OM' => 'Oman',
      'PK' => 'Pakistan',
      'PW' => 'Palau',
      'PA' => 'Panama',
      'PG' => 'Papua New Guinea',
      'PY' => 'Paraguay',
      'PE' => 'Peru',
      'PH' => 'Philippines',
      'PL' => 'Poland',
      'PT' => 'Portugal',
      'QA' => 'Qatar',
      'RO' => 'Romania',
      'RU' => 'Russian Federation',
      'RW' => 'Rwanda',
      'SH' => 'Saint Helena, Ascension and Tristan Da Cunha',
      'KN' => 'Saint Kitts And Nevis',
      'VC' => 'Saint Vincent And The Grenedines',
      'WS' => 'Samoa',
      'SM' => 'San Marino',
      'ST' => 'Sao Tome and Principe',
      'SA' => 'Saudi Arabia',
      'SN' => 'Senegal',
      'RS' => 'Serbia',
      'SC' => 'Seychelles',
      'SL' => 'Sierra Leone',
      'SG' => 'Singapore',
      'SK' => 'Slovakia',
      'SI' => 'Slovenia',
      'SB' => 'Solomon Islands',
      'SO' => 'Somalia',
      'ZA' => 'South Africa',
      'ES' => 'Spain',
      'LK' => 'Sri Lanka',
      'SD' => 'Sudan',
      'SR' => 'Suriname',
      'SZ' => 'Swaziland',
      'SE' => 'Sweden',
      'CH' => 'Switzerland',
      'SY' => 'Syrian Arab Republic',
      'TW' => 'Taiwan, Province Of China',
      'TJ' => 'Tajikistan',
      'TZ' => 'Tanzania, United Republic of',
      'TH' => 'Thailand',
      'TL' => 'Timor-Leste',
      'TG' => 'Togo',
      'TO' => 'Tonga',
      'TT' => 'Trinidad and Tobago',
      'TN' => 'Tunisia',
      'TR' => 'Turkey',
      'TM' => 'Turkmenistan',
      'TV' => 'Tuvalu',
      'UG' => 'Uganda',
      'UA' => 'Ukraine',
      'AE' => 'United Arab Emirates',
      'GB' => 'United Kingdom',
      'US' => 'United States',
      'UM' => 'United States Minor Outlying Islands',
      'UY' => 'Uruguay',
      'UZ' => 'Uzbekistan',
      'VU' => 'Vanuatu',
      'VE' => 'Venezuela, Bolivarian Republic of',
      'VN' => 'Viet Nam',
      'EH' => 'Western Sahara',
      'YE' => 'Yemen',
      'ZM' => 'Zambia',
      'ZW' => 'Zimbabwe'
    );
    return $countries;
  }

  /**
   * Get an array of US states, keyed by their abbreviation
   *
   * Helpful for creating select lists.
   *
   * @return array
   */
  protected function _getStates()
  {
    $states = array(
      'AL' => 'AL - Alabama',
      'AK' => 'AK - Alaska',
      'AS' => 'AS - American Samoa',
      'AZ' => 'AZ - Arizona',
      'AR' => 'AR - Arkansas',
      'CA' => 'CA - California',
      'CO' => 'CO - Colorado',
      'CT' => 'CT - Connecticut',
      'DE' => 'DE - Delaware',
      'DC' => 'DC - District of Columbia',
      'FL' => 'FL - Florida',
      'GA' => 'GA - Georgia',
      'GU' => 'GU - Guam',
      'HI' => 'HI - Hawaii',
      'ID' => 'ID - Idaho',
      'IL' => 'IL - Illinois',
      'IN' => 'IN - Indiana',
      'IA' => 'IA - Iowa',
      'KS' => 'KS - Kansas',
      'KY' => 'KY - Kentucky',
      'LA' => 'LA - Louisiana',
      'ME' => 'ME - Maine',
      'MD' => 'MD - Maryland',
      'MA' => 'MA - Massachusetts',
      'MI' => 'MI - Michigan',
      'MN' => 'MN - Minnesota',
      'MS' => 'MS - Mississippi',
      'MO' => 'MO - Missouri',
      'MT' => 'MT - Montana',
      'NE' => 'NE - Nebraska',
      'NV' => 'NV - Nevada',
      'NH' => 'NH - New Hampshire',
      'NJ' => 'NJ - New Jersey',
      'NM' => 'NM - New Mexico',
      'NY' => 'NY - New York',
      'NC' => 'NC - North Carolina',
      'ND' => 'ND - North Dakota',
      'MP' => 'MP - Northern Mariana Islands',
      'OH' => 'OH - Ohio',
      'OK' => 'OK - Oklahoma',
      'OR' => 'OR - Oregon',
      'PA' => 'PA - Pennsylvania',
      'PR' => 'PR - Puerto Rico',
      'RI' => 'RI - Rhode Island',
      'SC' => 'SC - South Carolina',
      'SD' => 'SD - South Dakota',
      'TN' => 'TN - Tennessee',
      'TX' => 'TX - Texas',
      'UM' => 'UM - United States Minor Outlying Islands',
      'UT' => 'UT - Utah',
      'VT' => 'VT - Vermont',
      'VI' => 'VI - Virgin Islands, U.S.',
      'VA' => 'VA - Virginia',
      'WA' => 'WA - Washington',
      'WV' => 'WV - West Virginia',
      'WI' => 'WI - Wisconsin',
      'WY' => 'WY - Wyoming'
    );
    return $states;
  }

  /**
   * Get an array of years
   *
   * Helpful for creating select lists.
   *
   * @param int $ahead
   *  How many years ahead of the current year do you want to create?
   * @param int $behind
   *  How many years behind the current year do you want to create?
   * @return array
   */
  protected function _getYears($ahead = 10, $behind = 0)
  {
    $currentYear = date('Y');
    $years = range($currentYear - $behind, $currentYear + $ahead);
    return array_combine($years, $years);
  }

  /**
   * Get an array of months
   *
   * Helpful for creating select lists.
   *
   * @return array
   */
  protected function _getMonths()
  {
    $months = array(
        '1' => '01 - January',
        '2' => '02 - February',
        '3' => '03 - March',
        '4' => '04 - April',
        '5' => '05 - May',
        '6' => '06 - June',
        '7' => '07 - July',
        '8' => '08 - August',
        '9' => '09 - September',
        '10' => '10 - October',
        '11' => '11 - November',
        '12' => '12 - December'
    );

    return $months;
  }
}