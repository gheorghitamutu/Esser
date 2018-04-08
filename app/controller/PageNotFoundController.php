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
    public function __construct()
    {
        $this->index();
    }

    public function index()
    {
        View::CreateView(
            '404',
            [],
            'Page not found!');
    }
}