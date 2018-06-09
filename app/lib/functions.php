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

function inFileStrReplace($filename, $replaced_string, $replace_with_string)
{
    $file_handler = null;
    $content = null;
    $no_error = true;
    try
    {
        clearstatcache(true, $filename);
        $file_handler = fopen($filename,"r");
        $content = fread($file_handler,filesize($filename));
        fclose($file_handler);
        $file_handler = null;
        $content = str_replace($replaced_string, $replace_with_string, $content);

        $file_handler = fopen($filename,"w");
        fwrite($file_handler, $content, strlen($content));
        fclose($file_handler);
        $file_handler = null;
    }
    catch (Exception $e)
    {
        Logger::getInstance()->log(ERROR, $e->getMessage());
        $no_error = false;
    }
    return $no_error;
}

function inFileRegexReplace($filename, $replaced_string, $replace_with_string)
{
    $file_handler = null;
    $content = null;
    $no_error = true;
    try
    {
        clearstatcache(true, $filename);
        $file_handler = fopen($filename,"r");
        $content = fread($file_handler,filesize($filename));
        fclose($file_handler);
        $file_handler = null;
        $content = preg_replace($replaced_string, $replace_with_string, $content);

        $file_handler = fopen($filename,"w");
        fwrite($file_handler, $content);
        fclose($file_handler);
    }
    catch (Exception $e)
    {
        Logger::getInstance()->log(ERROR, $e->getMessage());
        $no_error = false;
    }
    return $no_error;
}

function first_phase_install()
{
    $to_be_replaced = array('protected $username = null', 'protected $password = null');
    $replace_with = array('protected $username = ROOT_ADMIN_USER', 'protected $password = ROOT_ADMIN_PASS');

    /* Linking the database username and password to the ROOT_ADMIN constants */
    for ($i = count($to_be_replaced) - 1; $i >= 0 ; --$i)
    {
        try
        {
            $filename = ROOT . 'database' . DS . 'Database.php';
            $good_install = inFileStrReplace($filename, $to_be_replaced[$i], $replace_with[$i]);
            if(!$good_install)
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
    $to_be_parsed = array(SYS_DB_USER, SYS_DB_USER_PASS, ROOT_ADMIN_USER, ROOT_ADMIN_PASS);
    $parsed = [];
    for ($i = count($to_be_parsed) - 1; $i >= 0 ; --$i)
    {
        $parsed[$i] = preg_replace("/[^a-zA-Z0-9]+/", "", $to_be_parsed[$i]);
    }

    $check = preg_match_all('/@/', ROOT_ADMIN_EMAIL);
    if ($check !== 1) {
        Logger::getInstance()->log(ERROR, "More than one '@' detected in defined ROOT_ADMIN_EMAIL from config file. Please correct this!");
        return false;
    }
    else
    {
        array_push($to_be_parsed, ROOT_ADMIN_EMAIL);
        array_push($parsed,
            preg_replace("/[^a-zA-Z0-9@._]+/", "", $to_be_parsed[count($to_be_parsed)-1]));
    }

    array_push($to_be_parsed, ROOT_ADMIN_GROUP, ROOT_MANAGER_GROUP, ROOT_NORMAL_USER_GROUP);
    array_push($parsed,
        preg_replace("/[^a-zA-Z0-9-_ ]+/", "", $to_be_parsed[count($to_be_parsed)-3]),
        preg_replace("/[^a-zA-Z0-9-_ ]+/", "", $to_be_parsed[count($to_be_parsed)-2]),
        preg_replace("/[^a-zA-Z0-9-_ ]+/", "", $to_be_parsed[count($to_be_parsed)-1]));
  
    /* Trying to replace to 'already-defined' constants in the config file with the parsed ones */
    $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
    for ($i = count($to_be_parsed) - 1; $i >= 0 ; --$i)
    {
        try
        {
            $good_install = inFileStrReplace($filename, $to_be_parsed[$i], $parsed[$i]);
            if(!$good_install)
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
        $good_install = inFileStrReplace(
            $filename,
            "define('INSTALL_PHASE'                      , 1",
            "define('INSTALL_PHASE'                      , 2");
        if(!$good_install)
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

    return true;
}

function second_phase_install()
{
    //Shell/Cmd approach.
    //Need to find a way to detect errors
    $command =
        sprintf(('SQLPLUS %s/%s@%s AS SYSDBA @%s %s %s'),
            SYS_DB_USER,
            SYS_DB_USER_PASS,
            (HOST_IP . ':' . HOST_PORT . '//' . SYS_DB),
            (DB_SCRIPTS . 'createDBUserPrc.sql'),
            ROOT_ADMIN_USER,
            ROOT_ADMIN_PASS);
    $output = shell_exec($command);
    Logger::getInstance()->log(LOGGING,
        "Executed createDBUserPrc.sql script output is: " .
        "\r\n==============\r\n" .
        $output .
        "\r\n==============\r\n");

    //$shellcmd = sprintf(
        //('SQLPLUS %s/%s@%s @%s %s %s %s %s %s %s'),
        // ROOT_ADMIN_USER,
        // ROOT_ADMIN_PASS,
        // (HOST_IP . ':' . HOST_PORT . '//' . SYS_DB),
        // (DB_SCRIPTS . 'dbCreate.sql'),
        // ROOT_ADMIN_USER,
        // ROOT_ADMIN_PASS,
        // ROOT_ADMIN_EMAIL,
        // ROOT_ADMIN_GROUP,
        // ROOT_MANAGER_GROUP,
        // ROOT_NORMAL_USER_GROUP);

    $command =
        sprintf(('SQLPLUS %s/%s@%s @%s'),
            ROOT_ADMIN_USER,
            ROOT_ADMIN_PASS,
            (HOST_IP . ':' . HOST_PORT . '//' . SYS_DB),
            (DB_SCRIPTS . 'createDBTables.sql'));
    $output = shell_exec($command);
    Logger::getInstance()->log(LOGGING,
        "Executed createDBTables.sql script output is: " .
        "\r\n==============\r\n" .
        $output .
        "\r\n==============\r\n" );

    $command =
        sprintf(('SQLPLUS %s/%s@%s @%s'),
            ROOT_ADMIN_USER,
            ROOT_ADMIN_PASS,
            (HOST_IP . ':' . HOST_PORT . '//' . SYS_DB),
            (DB_SCRIPTS . 'createDBSequences.sql'));
    $output = shell_exec($command);
    Logger::getInstance()->log(LOGGING,
        "Executed createDBSequences.sql script output is: " .
        "\r\n==============\r\n" .
        $output .
        "\r\n==============\r\n" );

    $command =
        sprintf(('SQLPLUS %s/%s@%s @%s'),
            ROOT_ADMIN_USER,
            ROOT_ADMIN_PASS,
            (HOST_IP . ':' . HOST_PORT . '//' . SYS_DB),
            (DB_SCRIPTS . 'createDBAutoInsTriggers.sql'));
    $output = shell_exec($command);
    Logger::getInstance()->log(LOGGING,
        "Executed createDBAutoInsTriggers.sql script output is: " .
        "\r\n==============\r\n" .
        $output .
        "\r\n==============\r\n" );

    $command =
        sprintf(('SQLPLUS %s/%s@%s @%s'),
            ROOT_ADMIN_USER,
            ROOT_ADMIN_PASS,
            (HOST_IP . ':' . HOST_PORT . '//' . SYS_DB),
            (DB_SCRIPTS . 'createDBComplexTriggers.sql'));
    $output = shell_exec($command);
    Logger::getInstance()->log(LOGGING,
        "Executed createDBComplexTriggers.sql script output is: "
                                    . "\r\n==============\r\n" 
                                    . $output 
                                    . "\r\n==============\r\n" );

    $command = 'SQLPLUS '.ROOT_ADMIN_USER.'/'.ROOT_ADMIN_PASS.'@'.HOST_IP.':'.HOST_PORT.'//'.SYS_DB.' @"'.DB_SCRIPTS.'createDBPrcsFcts.sql"'.' '.'\''.ROOT_ADMIN_USER.'\''.' '.'\''.ROOT_ADMIN_PASS.'\''.' '.'\''.ROOT_ADMIN_EMAIL.'\''.' '.'\''.ROOT_ADMIN_GROUP.'\''.' '.'\''.ROOT_MANAGER_GROUP.'\''.' '.'\''.ROOT_NORMAL_USER_GROUP.'\'';
    echo $command."<br />";
    Logger::getInstance()->log(LOGGING,
        "Executed command for the createDBPrcsFcts.sql script output is: " .
        $command );
    $output = shell_exec($command);
    Logger::getInstance()->log(LOGGING,
        "Executed createDBPrcsFcts.sql script output is: " .
        "\r\n==============\r\n" .
        $output .
        "\r\n==============\r\n" );

    $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
    try
    {
        $good_install = inFileStrReplace(
            $filename,
            "define('INSTALL_PHASE'                      , 2",
            "define('INSTALL_PHASE'                      , 3");
        if(!$good_install)
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

    return true;
}

function third_phase_install()
{
    $filename = preg_replace("/(?=htdocs).+/", "php" . DS . "php.ini", ROOT);
    try
    {
        $good_install =
            inFileRegexReplace(
                $filename,
                "/(?=\;extension=oci8_).+?(?=\;)/",
                "extension=php_oci8.dll;\r\nextension=php_oci8_11g.dll;\r\n");
        if(!$good_install)
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
        $good_install =
            inFileStrReplace(
                $filename,
                "define('INSTALL_PHASE'                      , 3",
                "define('INSTALL_PHASE'                      , 0");
        if(!$good_install)
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

    return true;
}


//function threadExec(function())
//{
//  $thread = new class extends Thread
//  {
//	    public function run()
//      {
//          $this->synchronized(function()
//          {
//              $this->awake = true;
//              $this->notify();
//          });
//	     }
//   };
//
//  $thread->start();
//  $thread->synchronized(function() use($thread)
//  {
//	    while (!$thread->awake)
//      {
//		/*
//			If there was no precondition above and the Thread
//			managed to send notification before we entered this synchronized block
//			we would wait forever!
//			We check the precondition in a loop because a Thread can be awoken
//			by signals other than the one you are waiting for.
//		*/
//		$thread->wait();
//	     }
//  });
//
//  $thread->join();
//
//}
//


