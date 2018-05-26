<?php
/**
 * Created by PhpStorm.
 * User: ghita
 * Date: 4/11/2018
 * Time: 12:27 AM
 */
 
class Database
{
  protected $host = HOST_IP;
  protected $port = HOST_PORT;
  protected $sys_db = SYS_DB;
  protected $sys_user = SYS_DB_USER;
  protected $sys_user_pass = SYS_DB_USER_PASS;
  protected $installed = INSTALLED;
  protected $connection = null;
  protected $root_admin_user = ROOT_ADMIN_USER;
  protected $root_admin_user_pass = ROOT_ADMIN_PASS;
  protected $root_admin_group = ROOT_ADMIN_GROUP;
  protected $root_manager_group = ROOT_MANAGER_GROUP;
  
  private static $instace = null;
  
  public static function getInstace()
  {      
    if (Database::$instance == null)
    {
      Database::$instance = new Database();
    }
    return Database::$instace;
  }
  
  protected function __construct()
  {      
    if ($this->installed == false)
    {
      $this->connection = oci_pconnect('sys', 'Bg2017Fii', 'localhost/XE', 'UTF8', OCI_SYSDBA);
      
    }
    else
    {
      echo 'MEHEHEHEHHEHEHEHEs';
    }
  }
}
/*
class Database
{
    protected $connection = null;
    protected $host = 'localhost';
    protected $port = 5432;
    protected $dbname = 'test';
    protected $user = 'postgres';
    protected $password = 'password';

    private static $instance = null;

    // method used when class is used as singleton
    public static function getInstance()
    {
        if (Database::$instance === null)
            Database::$instance = new Database();

        return Database::$instance;
    }

    protected function __construct()
    {
        $format_connect_user_only = 'host=%s port=%d user=%s password=%s';
        $this->connection = pg_connect(
            sprintf(
                $format_connect_user_only,
                $this->host,
                $this->port,
                $this->user,
                $this->password));
        try {
            if ($this->connection === false) {
                $error = 'User connection failed!';
                throw new Exception($error);
            }
            if(!$this->databaseExists())
            {
                $sql = 'CREATE DATABASE ' . $this->dbname;
                if (!pg_query($this->connection, $sql))
                {
                    Logger::getInstance()->log(ERROR, "Creating database: " . $this->dbname);
                }
                else
                {
                    // create all the required tables
                    $this->createTables();
                }
            }
        }
        catch (Exception $e)
        {
            Logger::getInstance()->log(ERROR, $e->getMessage());
        }

        pg_close($this->connection);

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
                $error = 'User connection failed!';
                throw new Exception($error);
            }
        }
        catch (Exception $e)
        {
            Logger::getInstance()->log(ERROR, $e->getMessage());
        }
    }

    public function databaseExists()
    {
        $format = 'set PGPASSWORD=%s&& psql -h %s -U %s -p %d -c "%s"';
        $query = 'SELECT 1 FROM pg_database WHERE datname = \'' . $this->dbname . '\';';
        $cmd = sprintf($format, $this->password, $this->host, $this->user, $this->port, $query);
        $output = shell_exec($cmd);

        $pattern = '/([0-9]+).row/';
        preg_match($pattern, $output, $matches, PREG_OFFSET_CAPTURE, 3);

        // that s the actual row count
        return $matches[1][0] > 0;
    }

    public function deleteDatabase()
    {
        pg_close($this->connection);
        Database::$instance = null;

        $format = 'set PGPASSWORD=%s&& psql -h %s -U %s -p %d -c "%s"';
        $query = 'DROP DATABASE ' . $this->dbname;
        $cmd = sprintf($format, $this->password, $this->host, $this->user, $this->port, $query);
        shell_exec($cmd);
    }

    private function createTables()
    {
        // TO DO: create db required tables
    }
}
*/
