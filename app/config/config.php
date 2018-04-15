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

// paths macros
define('CONTROLLER', ROOT                   . 'app' . DS . 'controller' . DS);
define('CORE'      , ROOT                   . 'app' . DS . 'core'       . DS);
define('LIB'       , ROOT                   . 'app' . DS . 'lib'        . DS);
define('MODEL'     , ROOT                   . 'app' . DS . 'model'      . DS);
define('RESOURCES' , ROOT                   . 'app' . DS . 'resources'  . DS);
define('VIEW'      , ROOT                   . 'app' . DS . 'view'       . DS);
define('DATABASE'  , ROOT                                . 'database'   . DS);
define('LOGGER'    , ROOT                                . 'logger'     . DS);
define('ROUTE'     , ROOT                                . 'route'      . DS);

// logger messages macros
define('ERROR'     , '[ERROR] ');
define('WARNING'   , '[WARNING] ');

// glob input macros
define('PHP_FILE'  , '*.php');

// autoload purpose loops
foreach (glob(LOGGER . PHP_FILE) as $filename) {
    require_once($filename);
}

foreach (glob(DATABASE . PHP_FILE) as $filename) {
    require_once($filename);
}

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

