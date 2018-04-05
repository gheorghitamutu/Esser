<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: HomeController.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/5/2018
 * Time: 1:28 PM
 */


class HomeController extends Controller
{
    public function index()
    {
        //echo 'I am in ' . __CLASS__ . ' method ' . __METHOD__;
        $this->view('home\index');
        $this->view->render();
    }
}