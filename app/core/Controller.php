<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: Controller.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/5/2018
 * Time: 2:08 PM
 */

class Controller
{
    protected $view;

    public function view($viewName, $data = [])
    {
        $this->view = new View($viewName, $data);
        return $this->view;
    }

    function redirect($url){
        header("location: {$url}");
    }
}