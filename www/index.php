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
 * @package Bootstrap
 * @copyright Copyright (c) 2011 Crucial Web Studio. (http://www.crucialwebstudio.com)
 * @license New BSD License
 */

// Push lib directory onto front of include_path.
ini_set('include_path', dirname(dirname(__FILE__)) 
                        . '/lib' . DIRECTORY_SEPARATOR 
                        . get_include_path());
                        
/**
 * Grab the minimal files to get started. The bootstrap process will set up 
 * autoloading for us after this point.
 */
require_once 'Zend/Registry.php';
require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';

// Define path to application directory
if (!defined('APPLICATION_PATH'))
{
  define('APPLICATION_PATH', dirname(dirname(__FILE__)) . '/app');
}

// Define application environment
if (!defined('APPLICATION_ENV'))
{
  define('APPLICATION_ENV', 'development');
}

$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

// throw the original config in the registry for easy access later.
Zend_Registry::set('config', $config);

/** Zend_Application */
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    $config
);
$application->bootstrap()
            ->run();