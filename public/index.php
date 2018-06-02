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
if (INSTALLED === false)
{  
  $fname = ROOT;
  $fname = str_replace("".DS."htdocs".DS, "".DS."php".DS."php.ini", $fname);
  echo $fname."<br />";
  
  $fhandle = fopen($fname,"r");
  $content = fread($fhandle,filesize($fname));
  $content = str_replace("oci8.privileged_connect = Off", "oci8.privileged_connect = On", $content);
  
  $fhandle = fopen($fname,"w");
  fwrite($fhandle,$content);
  fclose($fhandle);
  //Shell/Cmd approach.
  //$format_connection = HOST_IP . ':' . HOST_PORT . '//' . SYS_DB;
  //$path = (DB_SCRIPTS . 'createDBUserPrc.sql');
  //$full_call = 'SQLPLUS ' . SYS_DB_USER . '/' . SYS_DB_USER_PASS . '@' . $format_connection . ' AS SYSDBA @' . $path . ' ' . ROOT_ADMIN_USER . ' ' . ROOT_ADMIN_PASS;
  //$output = shell_exec($full_call);
  //
  //$full_call = 'SQLPLUS %s/%s@%s @%s';
  //$format = sprintf($full_call, ROOT_ADMIN_USER, ROOT_ADMIN_PASS, $format_connection, (DB_SCRIPTS . 'dbCreate.sql'));
  //$output = shell_exec($format);
  if (strcmp("ERROR", "ERROR") == 0) 
  {
    //send to bad_installation page
    echo "Error at DB creation" . "<br />";
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
    echo "Database successfully installed. <br />";
  }
}
use Application as App;

new App;