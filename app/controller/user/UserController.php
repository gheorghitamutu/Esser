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
    public function __construct($uri)
    {
        switch($uri)
        {
            case 'user':
                $this->index();
                break;
            case 'user/alerts':
                $this->alerts();
                break;
            default:
                $this->index();
                break;

        }
    }
    public function index()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'index',
            [],
            'Welcome Username');
    }

    public function alerts()
    {
        // maybe macros for cats?
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'alerts' . DIRECTORY_SEPARATOR . 'alerts',
            [],
            'You have alerts!');
    }
}