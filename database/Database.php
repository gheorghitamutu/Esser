<?php 
class Database
{
  protected $installed = INSTALLED;
  protected $format_connection = HOST_IP . ':' . HOST_PORT . '/' . SYS_DB;
  protected $username = null;
  protected $password = null;
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
    if ($this->installed === false)
    {
      $fname = ROOT."database".DS."Database.php";
      $fhandle = fopen($fname,"r");
      $content = fread($fhandle,filesize($fname));
      $content = str_replace("protected \$username = ROOT_ADMIN_USER", "protected \$username = null", $content);
      $content = str_replace("protected \$password = ROOT_ADMIN_PASS", "protected \$password = null", $content);
      $fhandle = fopen($fname,"w");
      fwrite($fhandle,$content);
      fclose($fhandle);

      $fname = ROOT . 'app' . DS . 'config' . DS . 'config.php';
      $fhandle = fopen($fname,"r");
      $content = fread($fhandle,filesize($fname));
      $content = str_replace("define('INSTALL_PHASE', ".INSTALL_PHASE, "define('INSTALL_PHASE', 1", $content); 
      $fhandle = fopen($fname,"w");
      fwrite($fhandle,$content);
      fclose($fhandle);
      Logger::getInstance()->log(ERROR, "Application is not installed! Restart the proccess with INSTALLED constat as false and INSTALL_PHASE equal to 1 in the confing.php file!");
    }
    else
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
}

