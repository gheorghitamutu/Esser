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

session_start();

const DS = DIRECTORY_SEPARATOR;

define('ROOT'      , dirname(__DIR__)         . DS);
define('APP'       , ROOT                   . 'app' . DS);

require ROOT . 'app' . DS . 'config' . DS . 'config.php';
require_once LIB.'functions.php';
require_once LOGGER.'Logger.php';

$goodinstall = true;
$installing = true;
$installed = INSTALLED;

//This whole thing should be moved outside the public area and modularized.
if ($installed === false)
{  
    $installphase = INSTALL_PHASE;
  //Looping through install stages
  while ($goodinstall === true && $installing === true)
  {
    /* Should make an installer class that can be called from withing the app and that will use the switch on the INSTALL_PHASE */
    switch ($installphase)
    {
      case 1:
      {
        $goodinstall = firstPhaseInstall(); 
        if (!$goodinstall)
        {
          Logger::getInstance()->log(ERROR, "Installation failed at the second phase!");
        }
        else
        {
          Logger::getInstance()->log(LOGGING, "Completed install phase number 1. Switching to the next phase.");
          $installphase = 2;
        }
        break;
      }
      case 2:
      {
        $goodinstall = secondPhaseInstall();  
        if (!$goodinstall)
        {
          Logger::getInstance()->log(ERROR, "Installation failed at the second phase!");
        }
        else
        {
          Logger::getInstance()->log(LOGGING, "Completed install phase number 2. Switching to the next phase.");
          $installphase = 3;
        }
        break;
      }
      case 3:
      {
        $goodinstall = thirdPhaseInstall(); 
        $installphase = 0;       
        if (!$goodinstall)
        {
          Logger::getInstance()->log(ERROR, "Installation failed at the second phase!");
        }
        else
        {
          Logger::getInstance()->log(LOGGING, "Completed install phase number 3. Proceeding to gracefull apache servervice restart.");
        }
        try
        {
          shell_exec('httpd.exe -k restart');
        }
        catch (Exception $e)
        {
          LOGGER::getInstance()->log(ERROR, $e->getMessage());
        }
        break;
      }
      case 0:
      {
        if (!$goodinstall)
        {
          Logger::getInstance()->log(ERROR, "Installation failed at the last phase!");
        }
        else
        {
          Logger::getInstance()->log(LOGGING, "Completed apache service gracefull restart. Left number of install phases is: " . INSTALL_PHASE . ". Exiting installation procedure.");
        }
        $installing = false;
        break;
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
        $error = "Couldn't reset the INSTALL_PHASE to 1, set it manually in the " . $filename ." config file!";
        throw new Exception($error);
      }
    }
    catch (Exception $e)
    {
      Logger::getInstance()->log(ERROR, $e->getMessage());
    }
    //send to bad_installation page
  }
  else
  {
    /* Changing the value of the INSTALLED constant to "true" */
    $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
    try
    {
      $goodinstall = inFileStrReplace($filename, ("'INSTALLED', false);"), ("'INSTALLED', true);". PHP_EOL . "define('PLSQL_DRIVER', 'oci8');"));
      if(!$goodinstall)
      {
        $error = "Couldn't set the INSTALLED constant to true, set it manually in the " . $filename ." config file!";
        throw new Exception($error);
      }
    }
    catch (Exception $e)
    {
      Logger::getInstance()->log(ERROR, $e->getMessage());
    }
    Logger::getInstance()->log(LOGGING, "App successfully installed.");
    //send to successfully installed application page
    $installed = true;
  }
}
else
{
  $installing = false;
}

if ($goodinstall === true && $installing === false)
{
  require_once(APP_INIT."appinit.php");
}
else
{
  echo "Error at installation. Restart install procedure using INSTALLED false, and INSTALL_PHASE 1<br />";
}

?>