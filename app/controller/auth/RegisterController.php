<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: RegisterController.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 12:49 PM
 */

class RegisterController extends Controller
{
    public function __construct($uri)
    {
        switch($uri)
        {
            case 'register':
                $this->index();
                break;

            case 'register/check':
                $this->check_registration();
                break;
            case 'register/fail':
                $this->fail();
                break;
            case 'register/success':
                $this->success();
                break;
            default:
                break;

        }
    }

    private function index()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'register' . DIRECTORY_SEPARATOR . 'register',
            [],
            'Esser');
    }

    private function check_registration()
    {
        if($this->register_user())
        {
            self::redirect('success');
        }
        else
        {
            self::redirect('fail');
        }
    }

    private function fail()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'register' . DIRECTORY_SEPARATOR . 'fail',
            [],
            'Esser');

        unset($_SESSION["registration_wrong_pass_repeat"]);
    }

    private function success()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'register' . DIRECTORY_SEPARATOR . 'success',
            [],
            'Esser');

        unset($_SESSION["registration_wrong_pass_repeat"]);
    }

    private function register_user()
    {
        $this->model('Useracc');

        $username           = $_POST["uname"];
        $email              = $_POST["email"];
        $password           = $_POST["psw"];
        $password_repeat    = $_POST["cpsw"];

        if($password !== $password_repeat)
        {
            $_SESSION["registration_wrong_pass_repeat"] = true;
            return false;
        }
        else
        {
            $_SESSION["registration_wrong_pass_repeat"] = false;
        }

        $salt = '$1_2jlh83#@J^Q';
        $password_hash = hash('sha512', $username . $salt . $password);

        $result = $this->model_class->get_mapper()->insert(
            'USERACCS',
            array
            (
                'userName'  => "'" . $username      . "'",
                'userEmail' => "'" . $email         . "'",
                'userPass'  => "'" . $password_hash . "'",
                'userType'  => 0,
                'userState' => 1,
                'userImage' => "'" . 'undefined'    . "'"
            )
        );

        $log_description = "'Normal user " . $username     . " registered!'";
        $this->log_user_activity($log_description);

        $email_subject = "[Esser] Registration";

        if($result)
        {
            $email_body = "Successfully registered!";
        }
        else
        {
            $email_body = "Failed to register!";
        }

        GMail::send_email($email, $email_subject, $email_body);

        return $result;
    }

    private function log_user_activity($uLogDescription)
    {
        $this->model('UserLog');
        $this->model_class->get_mapper()->insert(
            'USERLOGS',
            array
            (
                'uLogDescription'   => $uLogDescription,
                'uLogSourceIP'      => "'" . $_SERVER["REMOTE_ADDR"] . "'"
            )
        );
    }

}



