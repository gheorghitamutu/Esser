<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: config.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 2:23 PM
 */

define('CONTROLLER', ROOT                   . 'app' . DS . 'controller' . DS);
define('CORE'      , ROOT                   . 'app' . DS . 'core'       . DS);
define('LIB'       , ROOT                   . 'app' . DS . 'lib'        . DS);
define('MODEL'     , ROOT                   . 'app' . DS . 'model'      . DS);
define('RESOURCES' , ROOT                   . 'app' . DS . 'resources'  . DS);
define('VIEW'      , ROOT                   . 'app' . DS . 'view'       . DS);
define('DATABASE'  , ROOT                           . DS . 'database'   . DS);
define('ROUTE'     , ROOT                           . DS . 'route'      . DS);

define('PHP_FILE'  , '*.php');

foreach (glob(CORE . PHP_FILE) as $filename)
    require_once($filename);

foreach (glob(LIB . PHP_FILE) as $filename)
    require_once($filename);

foreach (glob(CONTROLLER . PHP_FILE) as $filename)
    require_once($filename);

foreach (glob(CONTROLLER . DS . 'auth' . DS . PHP_FILE) as $filename)
    require_once($filename);

foreach (glob(CONTROLLER . DS . 'user' . DS . PHP_FILE) as $filename)
    require_once($filename);

foreach (glob(ROUTE . PHP_FILE) as $filename)
    require_once($filename);

