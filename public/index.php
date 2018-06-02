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

//This whole thing should be moved outside the public area and modularized.
if (INSTALLED === false)
{
  $fname = null;
  $fhandle = null;
  $content = null;
  $sys_user = null;
  $sys_user_pass = null;
  $root_admin_user = null;
  $root_admin_pass = null;
  $root_admin_group = null;
  $root_manager_group = null;
  $format_connection = null;
  $path = null;
  $full_call = null;
  $output = null;
  
  switch (INSTALL_PHASE)
  {
    case 1:
    {      
      $fname = ROOT."database".DS."Database.php";
      $fhandle = fopen($fname,"r");
      $content = fread($fhandle,filesize($fname));
      $content = str_replace("protected \$username = null", "protected \$username = ROOT_ADMIN_USER", $content);
      $content = str_replace("protected \$password = null", "protected \$password = ROOT_ADMIN_PASS", $content);
      
      $fhandle = fopen($fname,"w");
      fwrite($fhandle,$content);
      fclose($fhandle);  
      
      $sys_user = SYS_DB_USER;
      $sys_user_pass = SYS_DB_USER_PASS;
      $root_admin_user = ROOT_ADMIN_USER;
      $root_admin_pass = ROOT_ADMIN_PASS;
      $root_admin_group = ROOT_ADMIN_GROUP;
      $root_manager_group = ROOT_MANAGER_GROUP;
      $sys_user = preg_replace("/[^a-zA-Z0-9]+/", "", $sys_user);
      $sys_user_pass = preg_replace("/[^a-zA-Z0-9]+/", "", $sys_user_pass);
      $root_admin_user = preg_replace("/[^a-zA-Z0-9]+/", "", $root_admin_user);
      $root_admin_pass = preg_replace("/[^a-zA-Z0-9]+/", "", $root_admin_pass);
      $root_admin_group = preg_replace("/[^a-zA-Z0-9]+/", "", $root_admin_group);
      $root_manager_group = preg_replace("/[^a-zA-Z0-9]+/", "", $root_manager_group);
      
      $fname = ROOT . 'app' . DS . 'config' . DS . 'config.php';
      $fhandle = fopen($fname,"r");
      $content = fread($fhandle,filesize($fname));
      $content = str_replace(SYS_DB_USER, $sys_user, $content);
      $content = str_replace(SYS_DB_USER_PASS, $sys_user_pass, $content);
      $content = str_replace(ROOT_ADMIN_USER, $root_admin_user, $content);
      $content = str_replace(ROOT_ADMIN_PASS, $root_admin_pass, $content);
      $content = str_replace(ROOT_ADMIN_GROUP, $root_admin_group, $content);
      $content = str_replace(ROOT_MANAGER_GROUP, $root_manager_group, $content);
      $content = str_replace("define('INSTALL_PHASE', ".INSTALL_PHASE, "define('INSTALL_PHASE', 2", $content);
      $fhandle = fopen($fname,"w");
      fwrite($fhandle,$content);
      fclose($fhandle);
      Logger::getInstance()->log(LOGGING, "Completed install phase number: " . INSTALL_PHASE . ", switching to next phase."); 
    }
    case 2:
    {
      //Shell/Cmd approach.      
      $fname = ROOT . 'app' . DS . 'config' . DS . 'config.php';
      $fhandle = fopen($fname,"r");
      $content = fread($fhandle,filesize($fname));
      $content = str_replace("define('INSTALL_PHASE', ".INSTALL_PHASE, "define('INSTALL_PHASE', 3", $content);
      $fhandle = fopen($fname,"w");
      fwrite($fhandle,$content);
      fclose($fhandle);
      
      $sys_user = SYS_DB_USER;
      $sys_user_pass = SYS_DB_USER_PASS;
      $root_admin_user = ROOT_ADMIN_USER;
      $root_admin_pass = ROOT_ADMIN_PASS;
      $format_connection = HOST_IP . ':' . HOST_PORT . '//' . SYS_DB;
      $path = (DB_SCRIPTS . 'createDBUserPrc.sql');
      $full_call = 'SQLPLUS ' . $sys_user . '/' . $sys_user_pass . '@' . $format_connection . ' AS SYSDBA @' . $path . ' ' . $root_admin_user . ' ' . $root_admin_pass;
      $output = shell_exec($full_call);
      Logger::getInstance()->log(LOGGING, "Executed createDBUserPrc.sql script output is: " 
                                        . "\r\n==============\r\n" 
                                        . $output 
                                        . "\r\n==============\r\n");
      
      $full_call = 'SQLPLUS %s/%s@%s @%s';
      $format = sprintf($full_call, $root_admin_user, $root_admin_pass, $format_connection, (DB_SCRIPTS . 'dbCreate.sql'));
      $output = shell_exec($format);
      Logger::getInstance()->log(LOGGING, "Executed dbCreate.sql script output is: " 
                                        . "\r\n==============\r\n" 
                                        . $output 
                                        . "\r\n==============\r\n" );      
      Logger::getInstance()->log(LOGGING, "Completed install phase number: " . INSTALL_PHASE . ", switching to next phase.");
    }
    case 3:
    {  
      $fname = ROOT;
      $fname = preg_replace("/(?=htdocs).+/", "php".DS."php.ini", $fname);
      
      $fhandle = fopen($fname,"r");
      $content = fread($fhandle,filesize($fname));
      $content = preg_replace("/(?=\;extension=oci8_).+?(?=\;)/", "extension=php_oci8.dll;\r\nextension=php_oci8_11g.dll;\r\n", $content);
      
      $fhandle = fopen($fname,"w");
      fwrite($fhandle,$content);
      fclose($fhandle);  
      
      $fname = ROOT . 'app' . DS . 'config' . DS . 'config.php';
      $fhandle = fopen($fname,"r");
      $content = fread($fhandle,filesize($fname));
      $content = str_replace("define('INSTALL_PHASE', ".INSTALL_PHASE, "define('INSTALL_PHASE', 0", $content);
      $fhandle = fopen($fname,"w");
      fwrite($fhandle,$content);
      fclose($fhandle);
      Logger::getInstance()->log(LOGGING, "Completed install phase number: " . INSTALL_PHASE . ", performing gracefull apache servervice restart.");
    }
    case 0:
    {
      shell_exec('httpd.exe -k restart');
      Logger::getInstance()->log(LOGGING, "Completed apache service gracefull restart. Left number of install phases is: " . INSTALL_PHASE . ". Exiting installation procedure."); 
      break;
    }
  }
  Logger::getInstance()->log(LOGGING, "Installation procedure exited. Error verification underway.");
  //Need to implement a certain way to detect errors
  if (strcmp("ERROR", $output) === 0) 
  {
    //send to bad_installation page
    Logger::getInstance()->log(ERROR, "Error at DB creation!");
  }
  else
  {
    //Changing the value of the INSTALLED constant to "true"
    $fname = '' . ROOT . 'app' . DS . 'config' . DS . 'config.php';
    $fhandle = fopen($fname,"r");
    $content = fread($fhandle,filesize($fname));
    $content = str_replace("'INSTALLED', false);", "'INSTALLED', true);\nDEFINE('PLSQL_DRIVER', 'oci8');", $content);
    
    $fhandle = fopen($fname,"w");
    fwrite($fhandle,$content);
    fclose($fhandle);
    //send to successfully installed application page
    Logger::getInstance()->log(LOGGING, "App successfully installed.");
  }
}

$modules = [ROOT, APP, CONTROLLER, CORE, LIB, MODEL, RESOURCES, VIEW, DATABASE];

set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $modules));
spl_autoload_register('spl_autoload', false);

use Application as App;

new App;