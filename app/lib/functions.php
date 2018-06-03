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