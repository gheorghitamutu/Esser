<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: ForbiddenController.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 6/11/2018
 * Time: 21:52 PM
 */


class ForbiddenController extends Controller
{
    public function __construct()
    {
        $this->index();
    }

    public function index()
    {
        View::CreateView(
            '403',
            [],
            'Forbidden!');
    }
}
