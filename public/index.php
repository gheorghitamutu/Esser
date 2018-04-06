<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: index.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/5/2018
 * Time: 12:50 PM
 */

const DS = DIRECTORY_SEPARATOR;

define('ROOT'      , dirname(__DIR__)         . DS);
define('APP'       , ROOT                   . 'app' . DS);

require ROOT . 'app' . DS . 'config' . DS . 'config.php';

$modules = [ROOT, APP, CONTROLLER, CORE, LIB, MODEL, RESOURCES, VIEW, DATABASE];

set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $modules));
spl_autoload_register('spl_autoload', false);

use Application as App;

new App;