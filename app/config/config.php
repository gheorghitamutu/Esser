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
// All declared constant MUST BE ALPHA-NUMERIC ONLY!
// ROOT_ADMIN_USER and ROOT_ADMIN_PASS Need to be between 4 and 16 char long!
define('INSTALLED'                          , true);
define('PLSQL_DRIVER'                       , 'oci8');
define('INSTALL_PHASE'                      , 0);
define('APP_TITLE'                          , 'BMCP EsseR');
define('SYS_DB'                             , 'XE');
define('SYS_DB_USER'                        , 'SYS');
define('SYS_DB_USER_PASS'                   , 'SYS');
define('HOST_IP'                            , 'localhost');
define('HOST_PORT'                          , '1521');
define('MD5'                                , false);
define('SHA_256'                            , false);
define('SHA_512'                            , true);
define('SERVERTIMEZONE'                     , 'Europe/Bucharest');
define('ORA_CHARSET_DEFAULT'                , 'AMERICAN_AMERICA.WE8MSWIN1252');
define('ORA_CONNECTION_TYPE_DEFAULT'        , 1);
define('ORA_CONNECTION_TYPE_PERSISTENT'     , 2);
define('ORA_CONNECTION_TYPE_NEW'            , 3);
define('ORA_MESSAGES_NOT_CONNECTED'         , 'There\'s currently no connection to the database!');

// root configs
define('ROOT_ADMIN_USER'                    , 'EsseR');
define('ROOT_ADMIN_PASS'                    , 'EsserTest1234');
define('ROOT_ADMIN_EMAIL'                   , 'bugeag.george@info.uaic.ro');
define('ROOT_ADMIN_GROUP'                   , 'Admins');
define('ROOT_MANAGER_GROUP'                 , 'Managers');
define('ROOT_NORMAL_USER_GROUP'             , 'Normal Users');
 
// paths macros
define('DS'        , DIRECTORY_SEPARATOR);
define('APP'       , ROOT                   . 'app'                          . DS);
define('APP_INIT'  , ROOT                                     . 'app'        . DS);
define('CONTROLLER', ROOT                   . 'app'      . DS . 'controller' . DS);
define('CORE'      , ROOT                   . 'app'      . DS . 'core'       . DS);
define('LIB'       , ROOT                   . 'app'      . DS . 'lib'        . DS);
define('MODELS'    , ROOT                   . 'app'      . DS . 'models'     . DS);
define('RESOURCES' , ROOT                   . 'app'      . DS . 'resources'  . DS);
define('VIEW'      , ROOT                   . 'app'      . DS . 'view'       . DS);
define('DATABASE'  , ROOT                                     . 'database'   . DS);
define('LOGGER'    , ROOT                                     . 'logger'     . DS);
define('ROUTE'     , ROOT                                     . 'route'      . DS);
define('DB_SCRIPTS', ROOT                   . 'scripts'  . DS . 'database'   . DS);

// Logger messages macros
// date('d-m-Y H:i:s) represents date-time in the following format:
// numeric_day-numeric_month-numeric_year 24Hours:Minutes(00to59):Seconds(00to59)
define('ERROR'     , '[ERROR: '             . date('d-m-Y H:i:s') . ']: ');
define('WARNING'   , '[WARNING: '           . date('d-m-Y H:i:s') . ']: ');
define('DEBUGING'  , '[DEBUG_MESSAGE: '     . date('d-m-Y H:i:s') . ']: ');
define('LOGGING'   , '[APP_LOG: '           . date('d-m-Y H:i:s') . ']: ');

// glob input macros
define('PHP_FILE'  , '*.php');

