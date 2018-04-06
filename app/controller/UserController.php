<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: UserController.phpheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 1:07 PM
 */


class UserController extends Controller
{
    public function index()
    {
        $this->view('user' . DIRECTORY_SEPARATOR . 'index');
        $this->view->setPageTitle("Welcome Username!");
        $this->view->render();
    }

    public function alerts()
    {
        // maybe macros for cats?
        $this->view('user' . DIRECTORY_SEPARATOR . 'alerts' . DIRECTORY_SEPARATOR . 'alerts');
        $this->view->setPageTitle("You have alerts!");
        $this->view->render();
    }
}