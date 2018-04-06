<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: PageNotFoundController.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 12:56 PM
 */


class PageNotFoundController extends Controller
{
    public function index()
    {
        $this->view('404');
        $this->view->setPageTitle("Page not found!");
        $this->view->render();
    }
}