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

    require_once MODELS . DS . 'mapper' . DS . 'MapperInterface.php'; // load this before any other model/mapper

    foreach (glob(MODELS . DS . 'mapper' . DS . PHP_FILE) as $filename)
    {
        require_once($filename);
    }

    foreach (glob(MODELS . PHP_FILE) as $filename)
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

    foreach (glob(CONTROLLER . DS . 'admincp' . DS . 'Logs' . DS . PHP_FILE) as $filename)
    {
      require_once($filename);
    }

    foreach (glob(CONTROLLER . DS . 'admincp' . DS . 'Items' . DS . PHP_FILE) as $filename)
    {
        require_once($filename);
    }

    foreach (glob(CONTROLLER . DS . 'admincp' . DS . 'Itemgroups' . DS . PHP_FILE) as $filename)
    {
        require_once($filename);
    }

    foreach (glob(CONTROLLER . DS . 'admincp' . DS . 'Users' . DS . PHP_FILE) as $filename)
    {
        require_once($filename);
    }

    foreach (glob(CONTROLLER . DS . 'admincp' . DS . 'Usergroups' . DS . PHP_FILE) as $filename)
    {
        require_once($filename);
    }

    foreach (glob(ROUTE . PHP_FILE) as $filename)
    {
      require_once($filename);
    }

    $modules = [ROOT, APP, CONTROLLER, CORE, LIB, MODELS, RESOURCES, VIEW, DATABASE];

