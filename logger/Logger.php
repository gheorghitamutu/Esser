<?php
/**
 * Created by PhpStorm.
 * User: ghita
 * Date: 4/15/2018
 * Time: 1:15 PM
 */

class Logger
{
    private static $instance = null;
    private $file = LOGGER . 'log.txt';

    public static function getInstance()
    {
        if(self::$instance === null)
        {
            self::$instance = new Logger();
        }
        return self::$instance;
    }

    /**
     * @param $type         @log type macro defined in config.php
     * @param $message      @log content
     */
    public function log($type, $message)
    {
        // Write the contents to the file,
        // using the FILE_APPEND flag to append the content to the end of the file
        // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
        file_put_contents(
            $this->file,
            $type . $message . PHP_EOL,
            FILE_APPEND | LOCK_EX);
    }
};