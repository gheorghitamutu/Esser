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
            //new ForbiddenController();
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
            'Welcome ' . $_SESSION["uname"]);
        $this->model('GroupRelation');
        $result = $this->model_class->get_mapper()->findAll();
        echo var_dump($result);
        exit(0);

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
        $this->model_class->setState(1);
        $this->model_class->get_mapper()->update($this->model_class);

        $_SESSION['login_failed'] = true;
        session_destroy();
        Controller::redirect('/home');
    }
}