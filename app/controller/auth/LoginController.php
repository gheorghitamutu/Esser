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
            case 'login/password/change':
                $this->try_change_password();
                break;
            default:
                if(strpos($uri, 'password_recover/') !== false)
                {
                    $this->password_recover();
                    break;
                }

                new PageNotFoundController();
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
                session_destroy();
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

        if($this->is_valid_password_recover_request())
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

    private function is_valid_password_recover_request()
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
            $subject = "[EsseR] Password recovery";
            $body = "Here's the link in order to reset your account's password:\n";
            $body .= "http://";
            $body .= DOMAIN;
            $body .= "/";
            $body .= "login";
            $body .= "/";
            $body .= "password_recover/";
            $body .= $user[0]["userName"];
            $body .= "/";
            $body .= $user[0]["userPass"];

            if(GMail::send_email($user[0]['userEmail'], $subject, $body))
            {
                $description = $log_description = "'Normal user " . $user[0]['userName']     . " password recovery email success!'";
            }
            else
            {
                $description = $log_description = "'Normal user " . $user[0]['userName']     . " password recovery email fail!'";
            }

            self::log_user_activity($description);

            return true;
        }
    }

    private function is_user_approved()
    {
        // checks if the user account is approved or suspended
        // checks if requested email exists in database
        if (strlen($_POST['uname']) < 4 ||
            strlen($_POST['uname']) > 16 ||
            filter_var($_POST['uname'], FILTER_SANITIZE_STRING) == false)
        {
            return false;
        }

        $username = $_POST["uname"];
        $password = $_POST["psw"];

        $password_hash = hash(HASH_TYPE, $username . SALT . $password);

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

    private function password_recover()
    {
        $uri = $_SERVER['REQUEST_URI'];

        $split_uri = explode("/", $uri);
        //array(5)
        // {
        //      [0]=> string(0) ""
        //      [1]=> string(5) "login"
        //      [2]=> string(16) "password_recover"
        //      [3]=> string(4) "gmgm"
        //      [4]=> string(128) "882cf14749ad9d4b42e7b1935e5d196b5c2ec1cee2b6070068c729834af9caee36b50d3128aff7ef2ed4314f133ccbd1a92717fa17641732919c8b080085e14b"
        // }

        if (count($split_uri) < 2)
        {
            $this->password_recover_invalid_view();
            return false;
        }

       $this->model('Useracc');
       $user = $this->model_class->get_mapper()->findAll(
           $where = "userName = '$split_uri[3]' AND userPass = '$split_uri[4]'",
           $fields = false);

        if (count($user) === 0 || count($user) === null)
        {
            $this->password_recover_invalid_view();
            return false;
        }
        else
        {
            $this->password_recover_valid_view($user[0]);
            return true;
        }

    }

    private function password_recover_invalid_view()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR .
            'login' . DIRECTORY_SEPARATOR .
            'password_recover' . DIRECTORY_SEPARATOR .
            'fail',
            [],
            APP_TITLE);
    }

    private function password_recover_valid_view($user)
    {
        if(!isset($user))
        {
            $this->password_recover_invalid_view();
        }

        View::CreateView(
            'home' . DIRECTORY_SEPARATOR .
            'login' . DIRECTORY_SEPARATOR .
            'password_recover' . DIRECTORY_SEPARATOR .
            'success',
            [
                "user_array" => $user
            ],
            APP_TITLE);
    }

    private function try_change_password()
    {
        if($_POST["new_password"] !== $_POST["confirm_new_password"])
        {
            $message = "Passwords don't match!";
            $this->password_changed_invalid_view($message);
            return false;
        }

        // validate password
        if (strlen($_POST["new_password"]) > 64 || strlen($_POST["confirm_new_password"]) < 4)
        {
            $message = "New password needs to be between 4 and 64 characters! Please try again!";
            $this->password_changed_invalid_view($message);
            return false;
        }

        $password_hash = hash('sha512', $_POST["username"] . SALT . $_POST["new_password"]);

        $this->model('Useracc');
        $query = $this->model_class->get_mapper()->update
        (
            $table = 'USERACCS',
            $fields = array
            (
                'USERPASS'  => "'" . $password_hash . "'"
            ),
            $where = array
            (
                'USERNAME' => "'" . $_POST["username"] . "'"
            )
        );

        if (!is_array($query))
        {
            $log_description = "'Normal user " . $_POST["username"] . " failed to change password!'";
            parent::log_user_activity($log_description);

            $message = "Server Error! Please try again or contact support!";
            $this->password_changed_invalid_view($message);
            return false;
        }

        $log_description = "'Normal user " . $_POST["username"] . " successfully changed password!'";
        parent::log_user_activity($log_description);

        $this->password_change_valid_view();

        return true;
    }

    private function password_changed_invalid_view($message)
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR .
            'login' . DIRECTORY_SEPARATOR .
            'password_recover' . DIRECTORY_SEPARATOR .
            'changed' . DIRECTORY_SEPARATOR .
            'fail',
            [
                "message" => $message
            ],
            APP_TITLE);
    }

    private function password_change_valid_view()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR .
            'login' . DIRECTORY_SEPARATOR .
            'password_recover' . DIRECTORY_SEPARATOR .
            'changed' . DIRECTORY_SEPARATOR .
            'success',
            [],
            APP_TITLE);
    }
}