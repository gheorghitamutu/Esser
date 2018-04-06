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
        Route::submit();
    }
}