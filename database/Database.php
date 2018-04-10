<?php
/**
 * Created by PhpStorm.
 * User: ghita
 * Date: 4/11/2018
 * Time: 12:27 AM
 */

class Database
{
    private $connection = null;
    private $host = 'localhost';
    private $port = 5432;
    private $dbname = 'test';
    private $user = 'postgres';
    private $password = 'password';

    private static $instance = null;

    /**
     * Call this method to get singleton
     *
     * @return Database
     */
    public static function getInstance()
    {
        if (Database::$instance === null)
            Database::$instance = new Database();

        return Database::$instance;
    }

    /**
     * Private ctor so nobody else can instantiate it
     *
     */
    private function __construct()
    {
        $format = 'host=%s port=%d dbname=%s user=%s password=%s';
        $this->connection = pg_connect(
            sprintf(
                $format,
                $this->host,
                $this->port,
                $this->dbname,
                $this->user,
                $this->password));
        try {
            if ($this->connection === false) {
                $error = 'Connection to the ' . $this->dbname . 'database failed!';
                throw new Exception($error);
            }
        }
        catch (Exception $e)
        {
            echo $e->getMessage() . '<br>';
        }
    }
}