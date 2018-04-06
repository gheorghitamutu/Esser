<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: LoginController.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 12:48 PM
 */


class LoginController extends Controller
{
    public function index()
    {
        // if login successfull call user landing page controller
        $this->redirect('user/index');
        // else call login failed controller
        // ...
    }
}