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
    protected $params = array();
    protected $currentuser = array();

    public function __construct($uri)
    {
        $this->model('Useracc');

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
                $this->dashboard($this->getTotalUsers(), $this->getTotalOnline(), $this->getUserLastLoginDate($_SESSION), $this->getDBTimeZone());
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

    private function getUserLastLoginDate(array $session) {
        $userid = $session['userid'];
        $username= $session['uname'];
        $this->model('Userlog');
        $queryresult =
            $this->model_class->get_mapper()->findAll(
                $where = "ULOGDESCRIPTION like '%$userid%' AND ULOGDESCRIPTION like '%$username%' AND ULOGDESCRIPTION like '%has logged in%'",
                $fields = 'ULOGCREATEDAT',
                $order = "uLogId DESC",
                $limit = " = 1");
        if (count($queryresult) > 1) {
            throw new RuntimeException('Something went wrong during the fetch of of last login date!');
        }
        else if (count($queryresult) === 0) {
            return 'N/A';
        }
        else {
            return $queryresult['uLogCreatedAt'];
        }
        //return 'N/A';
    }

    private function getDBTimeZone() {
        $this->model('Dual');
        $result = $this->model_class->get_mapper()->findAll();
        return $result['timezonestamp'];
    }

    private function getTotalUsers()
    {
        $this->model('Useracc');
        $total_users = $this->model_class->get_mapper()->countAll('');
        return $total_users;
    }

    private function getTotalOnline()
    {
        $this->model('Useracc');
        $online_users = $this->model_class->get_mapper()->countAll("userState = '2'");
        return $online_users;
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
        if(($result['0']['0'] = $this->auth_user($uname, $pass, $isadmcp = true)) !== false) {
            $this->currentuser = $result['1'];
            self::redirect('/admincp/dashboard');
        }
        else {
            self::redirect('/admincp');
        }
    }

    private function logout()
    {
        session_destroy();
        self::redirect('/admincp');
    }

    private function dashboard($total_users, $online_users, $last_login, $time_zone)
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . 'dashboard',
            array(
                'totalUsers' => $total_users,
                'onlineUsers' => $online_users,
                'lastLogin' => $last_login,
                'timeZone' => $time_zone
            ),
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