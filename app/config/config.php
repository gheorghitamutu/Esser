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

// application configs
define('INSTALLED', false);
define('SYS_DB', 'XE');
define('SYS_DB_USER', 'SYS');
define('SYS_DB_USER_PASS', 'SYS');
define('HOST_IP', 'localhost');
define('HOST_PORT', '');
define('SQL_DRIVER', '');
define('MD5', true);
define('SHA_256', false);
define('SHA_512', false);
define('DB_CHAR_ENCRYPTION', 'UTF8');

// root configs
define('ROOT_ADMIN_USER', 'EsseR');
define('ROOT_ADMIN_PASS', 'EsserTest123');
define('ROOT_ADMIN_GROUP', 'Admins');
define('ROOT_MANAGER_GROUP', 'Managers');
 
// paths macros
define('CONTROLLER', ROOT                   . 'app'      . DS . 'controller' . DS);
define('CORE'      , ROOT                   . 'app'      . DS . 'core'       . DS);
define('LIB'       , ROOT                   . 'app'      . DS . 'lib'        . DS);
define('MODEL'     , ROOT                   . 'app'      . DS . 'model'      . DS);
define('RESOURCES' , ROOT                   . 'app'      . DS . 'resources'  . DS);
define('VIEW'      , ROOT                   . 'app'      . DS . 'view'       . DS);
define('DATABASE'  , ROOT                                     . 'database'   . DS);
define('LOGGER'    , ROOT                                     . 'logger'     . DS);
define('ROUTE'     , ROOT                                     . 'route'      . DS);
define('DB_SCRIPTS', ROOT                   . 'scripts'  . DS . 'database'   . DS);

// logger messages macros
define('ERROR'     , '[ERROR] ');
define('WARNING'   , '[WARNING] ');

// glob input macros
define('PHP_FILE'  , '*.php');

// autoload purpose loops
foreach (glob(LOGGER . PHP_FILE) as $filename)
{
  require_once($filename);
}

foreach (glob(DATABASE . PHP_FILE) as $filename)
{
  require_once($filename);
}

foreach (glob(CORE . PHP_FILE) as $filename)
{
  require_once($filename);
}

foreach (glob(LIB . PHP_FILE) as $filename)
{
  require_once($filename);
}

foreach (glob(CONTROLLER . PHP_FILE) as $filename)
{
  require_once($filename);
}

foreach (glob(CONTROLLER . DS . 'auth' . DS . PHP_FILE) as $filename)
{
  require_once($filename);
}

foreach (glob(CONTROLLER . DS . 'user' . DS . PHP_FILE) as $filename)
{
  require_once($filename);
}

foreach (glob(ROUTE . PHP_FILE) as $filename)
{
  require_once($filename);
}


