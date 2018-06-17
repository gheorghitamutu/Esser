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
        $this->model('Useracc');

        switch($uri)
        {
            case 'login':
                $this->index();
                break;
            case 'login/check':
                $this->check_login();
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
                $this->check_forgot();
                break;
            case 'login/forgot/fail':
                $this->forgot_fail();
                break;
            case 'login/forgot/success':
                $this->forgot_success();
                break;
            case 'login/unapproved':
                $this->unapproved();
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
            APP_TITLE);
    }

    private function check_login()
    {
        if($this->try_authenticate($_POST["uname"], $_POST["psw"], $is_admin_cp = false))
        {
            if(!$this->is_user_approved())
            {
                self::redirect('/login/unapproved');
                return;
            }
            self::redirect('/login/success');
        }
        else
        {
            self::redirect('/login/fail');
        }
    }

    private function fail()
    {
        $_SESSION['login_failed'] = true;

        self::redirect('/login');
    }

    private function success()
    {
        $_SESSION['login_failed'] = false;
        self::redirect('/user/index');
    }

    private function forgot()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR .
            'login' . DIRECTORY_SEPARATOR .
            'forgot_password' . DIRECTORY_SEPARATOR .
            'forgot_password',
            [],
            APP_TITLE);
    }

    private function check_forgot()
    {

        if($this->password_recover())
        {
            self::redirect('success');
        }
        else
        {
            self::redirect('fail');
        }
    }

    private function forgot_fail()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR .
            'login' . DIRECTORY_SEPARATOR .
            'forgot_password' . DIRECTORY_SEPARATOR .
            'fail',
            [],
            APP_TITLE);
    }

    private function forgot_success()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR .
            'login' . DIRECTORY_SEPARATOR .
            'forgot_password' . DIRECTORY_SEPARATOR .
            'success',
            [],
            'Esser');
    }

    private function unapproved()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR .
            'login' . DIRECTORY_SEPARATOR .
            'approval' . DIRECTORY_SEPARATOR .
            'unapproved',
            [],
            APP_TITLE);
    }

    private function password_recover()
    {
        // checks if requested email exists in database

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $email = $_POST["email"];
        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = "userEmail = '$email'",
            $fields = false);

        if (count($user) === 0 || count($user) === null)
        {
            return false;
        }
        else
        {
            // it should send an email..
            return true;
        }
    }

    private function is_user_approved()
    {
        // checks if the user account is approved or suspended
        // checks if requested email exists in database
        if (strlen($_POST['uname']) < 4 || strlen($_POST['uname']) > 16 || filter_var($_POST['uname'], FILTER_SANITIZE_STRING) == false) {

            return false;
        }
        $username = $_POST["uname"];
        $password = $_POST["psw"];

        $salt = '$1_2jlh83#@J^Q';
        $password_hash = hash('sha512', $username . $salt . $password);

        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = "userName = '$username' AND userPass = '$password_hash'",
            $fields = false);


        if (count($user) === 0 || count($user) === null)
        {
            return false;
        }
        else
        {
            if($user[0]["userType"] == 0 || $user[0]["userState"] == 0)
            {
                return false;
            }
            return true;
        }
    }
}