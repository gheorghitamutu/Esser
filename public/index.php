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

define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);
define('VIEW', ROOT . 'app' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
define('MODEL', ROOT . 'app' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR);
define('DATA', ROOT . 'app' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
define('CORE', ROOT . 'app' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR);
define('CONTROLLER', ROOT . 'app' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR);
$modules = [ROOT, APP, CORE, CONTROLLER, DATA];

set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $modules));
spl_autoload_register('spl_autoload', false);

use Application as App;

new App;