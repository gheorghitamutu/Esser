<?php
  foreach (glob(LOGGER . PHP_FILE) as $filename)
  {
    require_once($filename);
  }
  
  foreach (glob(DATABASE . PHP_FILE) as $filename)
  {
    require_once($filename);
  }
  
  foreach (glob(CORE . PHP_FILE) as $filename)
  {
    require_once($filename);
  }
  
  foreach (glob(LIB . PHP_FILE) as $filename)
  {
    require_once($filename);
  }
  
  foreach (glob(CONTROLLER . PHP_FILE) as $filename)
  {
    require_once($filename);
  }
  
  foreach (glob(CONTROLLER . DS . 'auth' . DS . PHP_FILE) as $filename)
  {
    require_once($filename);
  }
  
  foreach (glob(CONTROLLER . DS . 'user' . DS . PHP_FILE) as $filename)
  {
    require_once($filename);
  }
  
  foreach (glob(CONTROLLER . DS . 'admincp' . DS . PHP_FILE) as $filename)
  {
    require_once($filename);
  }
  
  foreach (glob(ROUTE . PHP_FILE) as $filename)
  {
    require_once($filename);
  }
  
  $modules = [ROOT, APP, CONTROLLER, CORE, LIB, MODEL, RESOURCES, VIEW, DATABASE];
  set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $modules));
  
  spl_autoload_register('spl_autoload', false);
  
  use Application as App;
  
  new App;

?>
