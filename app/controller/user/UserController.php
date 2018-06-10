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
        if(!$this->session_authenticate())
        {
            return;
        }

        switch($uri)
        {
            case 'user':
                $this->index();
                break;
            case 'user/alerts':
                $this->alerts();
                break;
            case 'user/logs':
                $this->logs();
                break;
            case 'user/items':
                $this->items();
                break;
            case 'user/users':
                $this->users();
                break;
            case 'user/logout':
                $this->logout();
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

    public function logs()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'logs',
            [],
            'Logs area');
    }

    public function items()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'items',
            [],
            'Items area');
    }

    public function users()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'users',
            [],
            'Users area');
    }

    public function logout()
    {
        session_destroy();
        Controller::redirect('/home');
    }
}