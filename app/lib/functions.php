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
    clearstatcache(true, $filename);
    $filehandler = fopen($filename,"r");
    $content = fread($filehandler,filesize($filename));
    fclose($filehandler);
    $filehandler = null;
    $content = str_replace($replacedstring, $replacewithstring, $content);
    $filehandler = fopen($filename,"w");
    fwrite($filehandler, $content, strlen($content));
    fclose($filehandler);
    $filehandler = null;
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
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
    clearstatcache(true, $filename);
    $filehandler = fopen($filename,"r");
    $content = fread($filehandler,filesize($filename));
    fclose($filehandler);
    $filehandler = null;
    $content = preg_replace($replacedstring, $replacewithstring, $content);
    $filehandler = fopen($filename,"w");
    fwrite($filehandler, $content);
    fclose($filehandler);
  }
  catch (Exception $e)
  {
    Logger::getInstance()->log(ERROR, $e->getMessage());
    $noerror = false;
  }
  return $noerror;
}

function firstPhaseInstall()
{
  $goodinstall = true;
  $tobereplaced = array('protected $username = null', 'protected $password = null');
  $replacewith = array('protected $username = ROOT_ADMIN_USER', 'protected $password = ROOT_ADMIN_PASS');
  /* Linking the database username and password to the ROOT_ADMIN constants */
  for ($i = count($tobereplaced) - 1; $i >= 0 ; --$i)
  {
    try
    {
      $filename = ROOT . 'database' . DS . 'Database.php';
      $goodinstall = inFileStrReplace($filename,$tobereplaced[$i],$replacewith[$i]);
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
  for ($i = count($tobeparsed) - 1; $i >= 0 ; --$i)
  {
    $parsed[$i] = preg_replace("/[^a-zA-Z0-9]+/", "", $tobeparsed[$i]);
  }

  if (preg_match)

  $check = preg_match_all('/@/', ROOT_ADMIN_EMAIL);
  if ($check !== 1) {
    Logger::getInstance()->log(ERROR, "More than one '@' detected in defined ROOT_ADMIN_EMAIL from config file. Please correct this!");
    return false;
  }
  else
  {
    array_push($parsed, preg_replace("/[^a-zA-Z0-9@._]+/", "", $tobeparsed[count($tobeparsed)-1]));
    array_push($tobeparsed, ROOT_ADMIN_EMAIL);
  }
  
  /* Trying to replace to 'already-defined' constants in the config file with the parsed ones */
  $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
  for ($i = count($tobeparsed) - 1; $i >= 0 ; --$i)
  {
    try
    {
      $goodinstall = inFileStrReplace($filename, $tobeparsed[$i], $parsed[$i]);
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
    $goodinstall = inFileStrReplace($filename, "define('INSTALL_PHASE', 1", "define('INSTALL_PHASE', 2");
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

function secondPhaseInstall()
{
  $goodinstall = true;
  //Shell/Cmd approach.
  //Need to find a way to detect errors
  $shellcmd = sprintf(('SQLPLUS %s/%s@%s AS SYSDBA @%s %s %s'), SYS_DB_USER, SYS_DB_USER_PASS, (HOST_IP . ':' . HOST_PORT . '//' . SYS_DB), (DB_SCRIPTS . 'createDBUserPrc.sql'), ROOT_ADMIN_USER, ROOT_ADMIN_PASS);
  $output = shell_exec($shellcmd);
  Logger::getInstance()->log(LOGGING, "Executed createDBUserPrc.sql script output is: " 
                                    . "\r\n==============\r\n" 
                                    . $output 
                                    . "\r\n==============\r\n");
  
  $shellcmd = sprintf(('SQLPLUS %s/%s@%s @%s %s %s %s'), ROOT_ADMIN_USER, ROOT_ADMIN_PASS, (HOST_IP . ':' . HOST_PORT . '//' . SYS_DB), (DB_SCRIPTS . 'dbCreate.sql'), ROOT_ADMIN_USER, ROOT_ADMIN_PASS, ROOT_ADMIN_EMAIL);
  $output = shell_exec($shellcmd);
  Logger::getInstance()->log(LOGGING, "Executed dbCreate.sql script output is: " 
                                    . "\r\n==============\r\n" 
                                    . $output 
                                    . "\r\n==============\r\n" );
  $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
  try
  {
    $goodinstall = inFileStrReplace($filename, "define('INSTALL_PHASE', 2", "define('INSTALL_PHASE', 3");
    if(!$goodinstall)
    {
      $error = "Couldn't set the INSTALL_PHASE 3 in the config file!";
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

function thirdPhaseInstall()
{
  $goodinstall = true;
  $filename = preg_replace("/(?=htdocs).+/", "php" . DS . "php.ini", ROOT);
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
    return false;
  }        
  $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
  try
  {
    $goodinstall = inFileStrReplace($filename, "define('INSTALL_PHASE', 3", "define('INSTALL_PHASE', 0");
    if(!$goodinstall)
    {
      $error = "Couldn't set the php_oci8.dll and php_oci8_11g.dll extension in the php.ini file!";
      throw new Exception($error);
    }
  }
  catch (Exception $e)
  {
    LOGGER::getInstance()->log(ERROR, $e->getMessage());
    return false;
  }
  return $goodinstall;
}


//function threadExec(function()) {
//  $thread = new class extends Thread 
//  {
//	  public function run() 
//    {
//      $this->synchronized(function()
//      {
//        $this->awake = true;
//        $this->notify();
//      });
//	}
//};
//
//$thread->start();
//$thread->synchronized(function() use($thread) {
//	while (!$thread->awake) {
//		/*
//			If there was no precondition above and the Thread
//			managed to send notification before we entered this synchronized block
//			we would wait forever!
//			We check the precondition in a loop because a Thread can be awoken
//			by signals other than the one you are waiting for.
//		*/
//		$thread->wait();
//	}
//});
//
//$thread->join();
//
//}
//
?>
