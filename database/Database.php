<?php 
class Database
{
  protected $installed = INSTALLED;
  protected $format_connection = HOST_IP . ':' . HOST_PORT . '/' . SYS_DB;
  protected $username = ROOT_ADMIN_USER;
  protected $password = ROOT_ADMIN_PASS;
  protected $connection = null;  
  private static $instance = null;
  
  public static function getInstance()
  {      
    if (Database::$instance == null)
    {
      Database::$instance = new Database();
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

