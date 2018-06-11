<?php
/**
 * Created by PhpStorm.
 * User: ghita
 * Date: 6/9/2018
 * Time: 1:12 PM
 */

class IndexController
{
    private $good_install = true;
    private $installing = true;
    private $installed = INSTALLED;

    private $logging_type = LOGGING;
    private $log_message = "";
    
    public function __construct()
    {
        //This whole thing should be moved outside the public area and modularized.
        if ($this->installed === false)
        {
            $install_phase = INSTALL_PHASE;
            //Looping through install stages
            while ($this->good_install === true && $this->installing === true)
            {
                /* Should make an installer class that can be called from withing the app
                and that will use the switch on the INSTALL_PHASE */
                switch ($install_phase)
                {
                    case 1:
                        $this->good_install = first_phase_install();
                        if (!$this->good_install)
                        {
                            $this->logging_type = ERROR;
                            $this->log_message = "Installation failed at the first phase!";
                        }
                        else
                        {
                            $this->logging_type = LOGGING;
                            $this->log_message = "Completed install phase number 1. Switching to the next phase.";
                            $install_phase = 2;
                        }
                        Logger::getInstance()->log($this->logging_type, $this->log_message);
                        break;
                    case 2:
                        $this->good_install = second_phase_install();
                        if (!$this->good_install)
                        {
                            $this->logging_type = ERROR;
                            $this->log_message = "Installation failed at the second phase!";
                        }
                        else
                        {
                            $this->logging_type = LOGGING;
                            $this->log_message = "Completed install phase number 2. Switching to the next phase.";
                            $install_phase = 3;
                        }
                        Logger::getInstance()->log($this->logging_type, $this->log_message);
                        break;
                    case 3:
                        $this->good_install = third_phase_install();
                        if (!$this->good_install)
                        {
                            $this->logging_type = ERROR;
                            $this->log_message = "Installation failed at the third phase!";
                        }
                        else
                        {
                            $this->logging_type = LOGGING;
                            $this->log_message = "Completed install phase number 3. Restarting apache...";
                        }
                        Logger::getInstance()->log($this->logging_type, $this->log_message);

                        $install_phase = 0;
                        try
                        {
                            shell_exec('httpd.exe -k restart');
                        }
                        catch (Exception $e)
                        {
                            LOGGER::getInstance()->log(ERROR, $e->getMessage());
                        }
                        break;
                    case 0:
                        if (!$this->good_install)
                        {
                            $this->logging_type = ERROR;
                            $this->log_message = "Installation failed at the last phase!";
                        }
                        else
                        {
                            $this->logging_type = LOGGING;
                            $this->log_message =
                                "Apache restarted. Left number of install phases is: " .
                                $install_phase .
                                ". Exiting installation procedure.";
                        }
                        Logger::getInstance()->log($this->logging_type, $this->log_message);
                        $this->installing = false;
                        break;
                }
            }

            Logger::getInstance()->log(
                LOGGING,
                "Installation procedure exited. Error verification underway.");

            /* Implemented a certain way to detect errors, needs to be re-tested  */
            if (!$this->good_install)
            {
                Logger::getInstance()->log(ERROR, "Bad installation detected!");

                try
                {
                    $reset_errors = array(
                        "at install phase" => false,
                        "at database username" => false,
                        "at database password" => false);

                    $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
                    $reset_errors["at install phase"] =
                        inFileStrReplace(
                            $filename,
                            "define('INSTALL_PHASE', \$installphase",
                            "define('INSTALL_PHASE', 1");
                    $filename = DATABASE . 'Database.php';
                    $reset_errors["at database username"] =
                        inFileRegexReplace(
                            $filename,
                            "/(protected\ \$username\ =\ ).+?(?=\;)/",
                            "protected \$username = null");
                    $reset_errors["at database password"] =
                        inFileRegexReplace(
                            $filename,
                            "/(protected\ \$password\ =\ ).+?(?=\;)/",
                            "protected \$password = null");
                    $reset_errors["errors detected"] = false;

                    forEach ($reset_errors as $type => $value)
                    {
                        if ($value)
                        {
                            $this->log_message =
                                "Error detected " .
                                $type .
                                " please set it manually in respective file !";
                            Logger::getInstance()->log(ERROR, $this->log_message);
                            $reset_errors["errors detected"] = true;
                        }
                    }

                    if ($reset_errors["errors detected"])
                    {
                        $error =
                            "Errors while trying to reset to initial install parameters!" .
                            " Please revert the application to the default install phase manually!";
                        throw new Exception($error);
                    }
                }
                catch (Exception $e)
                {
                    Logger::getInstance()->log(ERROR, $e->getMessage());
                    $this->good_install = false;
                    $this->installing = false;
                }
                // Maybe a send to bad_installation page (if time left, will be implemented)
            }
            else
            {
                /* Changing the value of the INSTALLED constant to "true" */
                $filename = ROOT . 'app' . DS . 'config' . DS . 'config.php';
                try
                {
                    $this->good_install = inFileStrReplace(
                        $filename,
                        ("define('INSTALLED'                          , false);"),
                        ("define('INSTALLED'                          , true);" . PHP_EOL .
                            "define('PLSQL_DRIVER'                       , 'oci8');"));

                    if (!$this->good_install)
                    {
                        $error = "Couldn't set the INSTALLED constant to true, set it manually in the " .
                            $filename . " config file!";
                        throw new Exception($error);
                    }
                }
                catch (Exception $e)
                {
                    Logger::getInstance()->log(ERROR, $e->getMessage());
                    $this->good_install = false;
                    $this->installing = false;
                }
                Logger::getInstance()->log(LOGGING, "App successfully installed.");
                // Maybe a send to successfully installed application page (if time left, will be implemented)
                $this->installed = true;
            }
        }
        else
        {
            $this->installing = false;
        }

        if ($this->good_install === true && $this->installing === false)
        {
            new Application();
        }
        else
        {
            echo
                "<h1>Error at installation. " .
                "Restart install procedure using INSTALLED false, and INSTALL_PHASE 1.<h1><br />" .
                "<h2>Check logs too for more informations!</h2><br />";
        }
    }
}