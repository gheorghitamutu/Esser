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


class AdmincpController extends Controller
{
    protected $params = array(array(array()));
    public function __construct($uri)
    {
        // no logincontroller here so handle this in a messy way
        if($uri !== 'admincp' && $uri !== 'admincp/login')
        {
            if(!Auth::sessionAuthenticate())
            {
                return;
            }
        }

        switch($uri)
        {
            case 'admincp':
                $this->index();
                break;
            case 'admincp/login':
                $this->login($_POST["uname"], $_POST["psw"]);
                break;
            case 'admincp/logout':
                echo 'logout';
                $this->logout();
                break;
            case 'admincp/dashboard':
                $this->dashboard();
               break;
            case 'admincp/activity':
                $this->activity($this->params[1], $this->params[0]);
                break;
            case 'admincp/data':
                $this->data();
                break;
            case 'admincp/user':
                $this->user();
                break;
            case 'admincp/editor':
                $this->editor();
                break;
            case 'admincp/manager':
                $this->manager();
                break;
            case 'admincp/database':
                $this->database();
                break;
            case 'admincp/settings':
                $this->settings();
                break;
            default:
                break;
        }
    }

    private function index()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'index',
            [],
            'AdminCP');
    }

    private function login($uname, $pass)
    {
        // TO DO: check login
        // if login true, redirect to dashboard
        $md5_pass = md5($pass);

        if(Auth::auth_user("connection", $uname, $pass))
            self::redirect('dashboard');
    }

    private function logout()
    {
        session_destroy();
        self::redirect('/admincp');
    }

    private function dashboard()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . 'dashboard',
            [],
            'AdminCP');
    }

    private function data()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'data',
            [],
            'AdminCP');
    }

    private function activity()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'activity',
            $this->params,
            'AdminCP');
    }

    private function user()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'user',
            [],
            'AdminCP');
    }

    private function editor()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'editor',
            [],
            'AdminCP');
    }

    private function manager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'manager',
            [],
            'AdminCP');
    }

    private function database()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'web_settings' . DIRECTORY_SEPARATOR . 'database',
            [],
            'AdminCP');
    }

    private function settings()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'web_settings' . DIRECTORY_SEPARATOR . 'settings',
            [],
            'AdminCP');
    }
}