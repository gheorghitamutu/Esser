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
    public function __construct($uri)
    {
        Parent::__construct();
        switch($uri)
        {
            case 'login':
                $this->index();
                break;
            case 'login/check':
                $uname = $_GET["uname"];
                $pass = $_GET["psw"];
                $this->check_login($uname, $pass);
                break;
            case 'login/fail':
                $this->fail();
                break;
            case 'login/success':
                $this->success();
                break;
            case 'login/forgot':
                $this->forgot();
                break;
            case 'login/forgot/check':
                $uname = $_GET["uname"];
                $this->check_forgot($uname);
                break;
            case 'login/forgot/fail':
                $this->forgot_fail();
                break;
            case 'login/forgot/success':
                $this->forgot_success();
                break;
            default:
                break;

        }
    }

    private function index()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'login' . DIRECTORY_SEPARATOR . 'login',
            [],
            'Esser');
    }

    private function check_login($uname, $pass)
    {
        // if login successfull call user landing page controller
        $this->redirect('success');
        //$this->redirect('/login/fail');
        // else call login failed controller
        // ...
    }

    private function fail()
    {
        self::redirect('/login');
    }

    private function success()
    {
        self::redirect('/user/index');
    }

    private function forgot()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'login' . DIRECTORY_SEPARATOR . 'forgot_password',
            [],
            'Esser');
    }

    private function check_forgot($uname)
    {
        //self::redirect('fail');
        self::redirect('success');
    }

    private function forgot_fail()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'login' . DIRECTORY_SEPARATOR . 'forgot_password_fail',
            [],
            'Esser');
    }

    private function forgot_success()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'login' . DIRECTORY_SEPARATOR . 'forgot_password_success',
            [],
            'Esser');
    }
}