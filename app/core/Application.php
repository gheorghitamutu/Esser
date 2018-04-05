<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: Application.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/5/2018
 * Time: 1:15 PM
 */

class Application
{
    protected $controller = 'HomeController';
    protected $action = 'index';
    protected $params = [];

    public function __construct()
    {
        $this->parseURL();
        if(file_exists(CONTROLLER . $this->controller . '.php'))
        {

            $this->controller = new $this->controller;
            if(method_exists($this->controller, $this->action))
            {
                call_user_func([$this->controller, $this->action], $this->params);
            }
        }
    }

    protected function parseURL()
    {
        $request = trim($_SERVER['REQUEST_URI'], '/');

        if(!empty($request))
        {
            $url = explode('/', $request);
            $this->controller = isset($url[0]) ? ucfirst($url[0]).'Controller' : 'HomeController';
            $this->action = isset($url[1]) ? $url[1] : 'index';
            unset($url[0], $url[1]);
            $this->params = !empty($url) ? array($url) : [];
        }
    }
}