<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: functions.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 2:44 PM
 * @param $name
 * @param string $default
 * @return string
 */

function getParams($name, $default = '')
{
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

function inFileStrReplace($filename, $replacedstring, $replacewithstring)
{
  $filehandler = null;
  $content = null;
  $noerror = true;
  try
  {
    $filehandler = fopen($filename,"r");
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  try
  {
    $content = fread($filehandler,filesize($filename));
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  try
  {
    $content = str_replace($replacedstring, $replacewithstring, $content);
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  try 
  {
    $filehandler = fopen($filename,"w");
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  try
  {
    fwrite($filehandler, $content);
  }
  catch (Exception e)
  {
    Logger:getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }  
  try
  {
    fclose($filehandler);
  }
  catch (Exception e)
  {
    Logger:getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  return $noerror;
}

function inFileRegexReplace($filename, $replacedstring, $replacewithstring)
{
  $filehandler = null;
  $content = null;
  $noerror = true;
  try
  {
    $filehandler = fopen($filename,"r");
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  try
  {
    $content = fread($filehandler,filesize($filename));
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  try
  {
    $content = preg_replace($replacedstring, $replacewithstring, $content);
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  try 
  {
    $filehandler = fopen($filename,"w");
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  try
  {
    fwrite($filehandler, $content);
  }
  catch (Exception e)
  {
    Logger:getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }  
  try
  {
    fclose($filehandler);
  }
  catch (Exception e)
  {
    Logger:getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  return $noerror;
}

function firstPhase()
{
  $goodinstall = true;
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
      return false;
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
      return false;
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
    return false;
  }
  return $goodinstall;
}

function secondPhase()
{
  
}

function thirdPhase()
{
  
}

function installPhase($phasenumber)
{
  
}