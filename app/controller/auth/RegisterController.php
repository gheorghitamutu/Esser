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
    // ..
    public function __construct($uri)
    {
        Parent::__construct();

        switch($uri)
        {
            case 'register':
                $this->index();
                break;

            case 'register/check':
                $fname = $_GET["fname"];
                $sname = $_GET["sname"];
                $email = $_GET["email"];
                $uname = $_GET["uname"];
                $psw = $_GET["psw"];
                $cpsw = $_GET["cpsw"];

                $this->check_registration($fname, $sname, $email, $uname, $psw, $cpsw);
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

    private function check_registration($fname, $sname, $email, $uname, $psw, $cpsw)
    {
        // if valid registration then /register_success
        // else register fail

        //self::redirect('fail');
        self::redirect('success');
    }

    private function fail()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'register' . DIRECTORY_SEPARATOR . 'fail',
            [],
            'Esser');
    }

    private function success()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'register' . DIRECTORY_SEPARATOR . 'success',
            [],
            'Esser');
    }
}



