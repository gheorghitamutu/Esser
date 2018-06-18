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
        $registtration = $this->register_user();
        if($registtration['operation'] == true)
        {
            self::redirect('success');
        }
        else
        {
            $_SESSION['failMessage'] = $registtration['message'];
            self::redirect('fail');
        }
    }

    private function fail()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'register' . DIRECTORY_SEPARATOR . 'fail',
            ['failMessage' => (isset($_SESSION['failMessage'])) ? $_SESSION['failMessage'] : 'technical issues!' ],
            'Esser');
        unset($_SESSION["failMessage"]);

    }

    private function success()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'register' . DIRECTORY_SEPARATOR . 'success',
            [],
            'Esser');
    }

    private function register_user()
    {
        $this->model('Useracc');

        if (strlen($_POST["uname"]) < 4 || strlen($_POST["uname"]) > 16) {
            return array('operation' => false, 'message' => 'username not being between 4 and 16 characters long!');
        }
        elseif (!preg_match('/[^a-zA-Z0-9._-]/',$_POST['uname'])) {
            $username = $_POST["uname"];
        }
        else {
            return array('operation' => false,
                'message' => 'username containing prohibited characters!'
                             . PHP_EOL
                             . 'Use only alpha-numeric, \'.\', \'_\' and \'-\' characters!');
        }

        if (strlen($_POST['email']) < 4 || strlen($_POST['email']) > 48) {
            return array('operation' => false, 'message' => 'email not being between 4 and 48 characters long!');
        }
        elseif (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $email = $_POST["email"];
        }
        else {
            return array('operation' => false, 'message' => 'Inputed email is in a wrong format!');
        }
        if (strlen($_POST["psw"]) > 64 || strlen($_POST["psw"]) < 4) {
            return array('operation' => false, 'message' => 'password not being between 4 and 64 characters long!');
        }
        else {
            $password = $_POST["psw"];
            $password_repeat = $_POST["cpsw"];
        }

        if($password !== $password_repeat)
        {
            return array('operation' => false, 'message' => 'password and repeat password not matching!');
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

        $this->model('UserLog');
        $this->model_class->get_mapper()->insert(
            'USERLOGS',
            array
            (
                'uLogDescription'   => "'Normal user " . $username     . " registered!'",
                'uLogSourceIP'      => "'" . (($_SERVER["REMOTE_ADDR"]=='::1')?'127.0.0.1':$_SERVER['REMOTE_ADDR']) . "'"
            )
        );

        return array('operation' => true, 'result' => $result);
    }
}



