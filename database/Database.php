<?php 
class Database
{
  protected $installed = INSTALLED;
  protected $format_connection = HOST_IP . ':' . HOST_PORT . '/' . SYS_DB;
  protected $username = null;
  protected $password= null;
  protected $connection = null;  
  private static $instance = null;
  
  public static function getInstance()
  {      
    if (Database::$instance == null)
    {
      if ($this->installed = false)
      {
        $this->username = SYS_DB_USER;
        $this->password = SYS_DB_USER_PASS;
        Database::$instance = new Database();
      }
      else
      {        
        $this->username = ROOT_ADMIN_USER;
        $this->password = ROOT_ADMIN_PASS;
        Database::$instance = new Database();
      }
    }
    return Database::$instance;
  }
  
  protected function __construct()
  {
    try
    {
      $this->connection = oci_connect($this->username, $this->password, $this->format_connection);
      if ($this->connection === false) 
      {
        $error = 'Could not connect to the database as user: ' . $this->username . ' !';
        throw new Exception($error);
      }
    }
    catch (Exception $e)
    {
      Logger::getInstance()->log(ERROR, $e->getMessage());
    }
  }
}

