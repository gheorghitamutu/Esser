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
        $registration = $this->register_user();
        if($registration['operation'] == true)
        {
            self::redirect('success');
        }
        else
        {
            $_SESSION['validation_message'] = $registration['message'];
            self::redirect('fail');
        }
    }

    private function fail()
    {
        View::CreateView(
            'home' . DIRECTORY_SEPARATOR . 'register' . DIRECTORY_SEPARATOR . 'fail',
            ['validation_message' => (isset($_SESSION['validation_message'])) ? $_SESSION['validation_message'] : 'technical issues!' ],
            'Esser');
        unset($_SESSION["validation_message"]);

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

        // validate user
        if (strlen($_POST["uname"]) < 4 || strlen($_POST["uname"]) > 16)
        {
            return array('operation' => false, 'message' => 'username not being between 4 and 16 characters long!');
        }
        elseif (!preg_match('/[^a-zA-Z0-9._-]/',$_POST['uname']))
        {

            $username = $_POST["uname"];
        }
        else
        {
            return array('operation' => false,
                'message' => 'username containing prohibited characters!'
                             . PHP_EOL
                             . 'Use only alpha-numeric, \'.\', \'_\' and \'-\' characters!');
        }

        // validate email
        if (strlen($_POST['email']) < 4 || strlen($_POST['email']) > 48)
        {
            return array('operation' => false, 'message' => 'email must be between 4 and 48 characters long!');
        }
        elseif (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            $email = $_POST["email"];
        }
        else
        {
            return array('operation' => false, 'message' => 'Wrong email format!');
        }

        // validate password
        if (strlen($_POST["psw"]) > 64 || strlen($_POST["psw"]) < 4)
        {
            return array('operation' => false, 'message' => 'password must be between 4 and 64 characters long!');
        }
        else
        {
            $password = $_POST["psw"];
            $password_repeat = $_POST["cpsw"];
        }

        // validate password matching
        if($password !== $password_repeat)
        {
            return array('operation' => false, 'message' => 'password and repeat password not matching!');
        }

        $password_hash = hash('sha512', $username . SALT . $password);

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

        $log_description = "'Normal user " . $username     . " has registered!'";
        parent::log_user_activity($log_description);

        $email_subject = "[Esser] Registration";

        if($result)
        {
            $email_body = "Successfully registered!";
        }
        else
        {
            $email_body = "Failed to register!";
        }

        if(GMail::send_email($email, $email_subject, $email_body) === true)
        {
            $log_description = "'Normal user " . $username     . " registration email success!'";
        }
        else
        {
            $log_description = "'Normal user " . $username     . " registration email fail!'";
        }

        parent::log_user_activity($log_description);

        return array('operation' => true, 'result' => $result);
    }
}



