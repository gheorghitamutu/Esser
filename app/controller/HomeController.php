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

        $this->view('home\index');
        $this->view->setPageTitle("Esser");
        $this->view->render();
    }
}