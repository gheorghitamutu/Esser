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
  $goodinstall = true;
  $installing = true;
  
  //Looping through install stages
  while ($goodinstall && $installing)
  {
    /* Should make an installer class that can be called from withing the app and that will use the switch on the INSTALL_PHASE */
    switch (INSTALL_PHASE)
    {
      case 1:
      {
        $filename = ROOT . "database" . DS . "Database.php";
        $tobereplaced = array("protected \$username = null", "protected \$password = null");
        $replacewith = array("protected \$username = ROOT_ADMIN_USER", "protected \$password = ROOT_ADMIN_PASS");
        /* Linking the database username and password to the ROOT_ADMIN constants */
        for ($i = count($tobereplaced) - 1; --$i)
        {
          try
          {
            $goodinstall = inFileStrReplace($filename,$toBeReplace[i],$replacewith[i]);
            if(!$goodinstall)
            {
              $error = "Couldn't set root admin user and password for the database!";
              throw new Exception($error);
            }
          }
          catch (Exception $e)
          {
            Logger::getInstance()->log(ERROR, $e->getMessage());
            break;
          }
        }
        
        /* Parsing all important constants defined with strings that may prove 'fatal' while installing */
        $tobeparsed = array(SYS_DB_USER, SYS_DB_USER_PASS, ROOT_ADMIN_USER, ROOT_ADMIN_PASS, ROOT_ADMIN_GROUP, ROOT_MANAGER_GROUP);
        $parsed = [];
        for ($i = count($tobeparsed) - 1; --$i)
        {
          $parsed[i] = preg_replace("/[^a-zA-Z0-9]+/", "", $tobeparsed[i]);
        }
        
        /* Trying to replace to 'already-defined' constants in the config file with the parsed ones */
        $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
        for ($i = count($tobeparsed) - 1; --$i)
        {
          try
          {
            $goodinstall = inFileStrReplace($filename, $tobeparsed[i], $parsed[i]);
            if(!$goodinstall)
            {
              $error = "Couldn't finish replacing config constants with the parsed ones!";
              throw new Exception($error);
            }
          }
          catch (Exception $e)
          {
            Logger::getInstance()->log(ERROR, $e->getMessage());
            break;
          }
        }
        
        /* Trying to change the install phase to the next step */
        try
        {
          $goodinstall = inFileStrReplace($filename, "define('INSTALL_PHASE', ".INSTALL_PHASE, "define('INSTALL_PHASE', 2");
          if(!$goodinstall)
          {
            $error = "Couldn't set the INSTALL_PHASE 2 in the config file!";
            throw new Exception($error);
          }
        }
        catch (Exception $e)
        {
          Logger::getInstance()->log(ERROR, $e->getMessage());
          break;
        }
        
        if (!goodinstall)
        {
          Logger::getInstance()->log(ERROR, "Installation failed at the first phase!");
          $installing = false;
          break;
        }
        else
        {
          Logger::getInstance()->log(LOGGING, "Completed install phase number: " . INSTALL_PHASE . ", switching to next phase.");
        }
      }
      case 2:
      {
        //Shell/Cmd approach.
        //Need to find a way to detect errors
        $shellcmd = sprintf(('SQLPLUS %s/%s@%s AS SYSDBA @%s %s %s'), SYS_DB_USER, SYS_DB_USER_PASS, (HOST_IP.':'.HOST_PORT.'//'.SYS_DB), (DB_SCRIPTS.'createDBUserPrc.sql'), ROOT_ADMIN_USER, ROOT_ADMIN_PASS);
        $output = shell_exec($shellcmd);
        Logger::getInstance()->log(LOGGING, "Executed createDBUserPrc.sql script output is: " 
                                          . "\r\n==============\r\n" 
                                          . $output 
                                          . "\r\n==============\r\n");
        
        $shellcmd = sprintf(('SQLPLUS %s/%s@%s @%s'), ROOT_ADMIN_USER, ROOT_ADMIN_PASS, (HOST_IP . ':' . HOST_PORT . '//' . SYS_DB), (DB_SCRIPTS . 'dbCreate.sql'));
        $output = shell_exec($shellcmd);
        Logger::getInstance()->log(LOGGING, "Executed dbCreate.sql script output is: " 
                                          . "\r\n==============\r\n" 
                                          . $output 
                                          . "\r\n==============\r\n" );
        $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
        try
        {
          $goodinstall = inFileStrReplace($filename, "define('INSTALL_PHASE', ".INSTALL_PHASE, "define('INSTALL_PHASE', 3");
          if(!$goodinstall)
          {
            $error = "Couldn't set the INSTALL_PHASE 3 in the config file!";
            throw new Exception($error);
          }
        }
        catch (Exception $e)
        {
          Logger::getInstance()->log(ERROR, $e->getMessage());
          break;
        }
        if (!goodinstall)
        {
          Logger::getInstance()->log(ERROR, "Installation failed at the second phase!");
          $installing = false;
          break;
        }
        else
        {
          Logger::getInstance()->log(LOGGING, "Completed install phase number: " . INSTALL_PHASE . ", switching to next phase.");
        }        
      }
      case 3:
      {
        $filename = preg_replace("/(?=htdocs).+/", "php".DS."php.ini", ROOT);
        try
        {
          $goodinstall = inFileRegexReplace($filename, "/(?=\;extension=oci8_).+?(?=\;)/", "extension=php_oci8.dll;\r\nextension=php_oci8_11g.dll;\r\n");
          if(!$goodinstall)
          {
            $error = "Couldn't set the php_oci8.dll and php_oci8_11g.dll extension in the php.ini file!";
            throw new Exception($error);
          }
        }
        catch (Exception $e)
        {
          LOGGER::getInstance()->log(ERROR, $e->getMessage());
          break;
        }        
        $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
        try
        {
          $goodinstall = inFileStrReplace($filename, "define('INSTALL_PHASE', ".INSTALL_PHASE, "define('INSTALL_PHASE', 0");
          if(!$goodinstall)
          {
            $error = "Couldn't set the php_oci8.dll and php_oci8_11g.dll extension in the php.ini file!";
            throw new Exception($error);
          }
        }
        catch (Exception $e)
        {
          LOGGER::getInstance()->log(ERROR, $e->getMessage());
          break;
        }
        if (!goodinstall)
        {
          Logger::getInstance()->log(ERROR, "Installation failed at the second phase!");
          $installing = false;
          break;
        }
        else
        {
          Logger::getInstance()->log(LOGGING, "Completed install phase number: " . INSTALL_PHASE . ", proceeding to gracefull apache servervice restart.");
        }
      }
      case 0:
      {
        try
        {
          shell_exec('httpd.exe -k restart');        
        }
        catch (Exception $e)
        {
          LOGGER::getInstance()->log(ERROR, $e->getMessage());
          break;
        }
        if (!goodinstall)
        {
          Logger::getInstance()->log(ERROR, "Installation failed at the second phase!");
          $installing = false;
          break;
        }
        else
        {
          Logger::getInstance()->log(LOGGING, "Completed apache service gracefull restart. Left number of install phases is: " . INSTALL_PHASE . ". Exiting installation procedure."); 
          $installing = false;
          break;
        }
      }
    }
  }
  Logger::getInstance()->log(LOGGING, "Installation procedure exited. Error verification underway.");
  /* Implemented a certain way to detect errors, needs to be re-tested  */
  if (!$goodinstall) 
  {
    Logger::getInstance()->log(ERROR, "Bad installation detected!");
    $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
    try
    {
      $goodinstall = inFileStrReplace($filename, "define('INSTALL_PHASE', ".INSTALL_PHASE, "define('INSTALL_PHASE', 0");
      if(!$goodinstall)
      {
        $error = "Couldn't reset the INSTALL_PHASE to 1, set it manually in the " . $filename ." confing file!";
        throw new Exception($error);
      }
    }
    catch (Exception $e)
    {
      LOGGER::getInstance()->log(ERROR, $e->getMessage());
      break;
    }
    //send to bad_installation page
  }
  else
  {
    /* Changing the value of the INSTALLED constant to "true" */
    $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
    try
    {
      $goodinstall = inFileStrReplace($filename, ("'INSTALLED', false);"), ("'INSTALLED', true);\nDEFINE('PLSQL_DRIVER', 'oci8');"));
      if(!$goodinstall)
      {
        $error = "Couldn't set the INSTALLED constant to true, set it manually in the " . $filename ." confing file!";
        throw new Exception($error);
      }
    }
    catch (Exception $e)
    {
      LOGGER::getInstance()->log(ERROR, $e->getMessage());
      break;
    }
    Logger::getInstance()->log(LOGGING, "App successfully installed.");
    //send to successfully installed application page
  }
}

$modules = [ROOT, APP, CONTROLLER, CORE, LIB, MODEL, RESOURCES, VIEW, DATABASE];

set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $modules));
spl_autoload_register('spl_autoload', false);

use Application as App;

new App;