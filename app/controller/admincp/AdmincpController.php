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

    private function getUserLastLoginDate(array $session) {
        $userid = $session['userid'];
        $username= $session['uname'];
        $this->model('Userlog');
        $queryresult =
            $this->model_class->get_mapper()->findAll(
                $where = "ULOGDESCRIPTION like '%$userid%' "
                        ."AND ULOGDESCRIPTION like '%$username%' "
                        ."AND ULOGDESCRIPTION like '%has logged in%' ",
                $fields = 'ULOGCREATEDAT',
                $order = "uLogId DESC",
                $limit = " = 1");
        if (count($queryresult) > 1) {
            throw new RuntimeException('Something went wrong during the fetch of of last login date!');
        }
        elseif (empty($queryresult)) {
            return 'N/A';
        }
        else {
            return $queryresult['uLogCreatedAt'];
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

    private function dashboard()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . 'dashboard',
            array(
                'totalUsers' => $this->getTotalUsers(),
                'onlineUsers' => $this->getTotalOnline(),
                'lastLogin' => $this->getUserLastLoginDate($_SESSION),
                'timeZone' =>  $this->getDBTimeZone(),
                'lastDBBackupTime' => $this->getLastDBBackupTime(),
                'totalItemGroups' => $this->getTotalItemGroups(),
                'avgItemPerGroup' =>  $this->getTotalItemGroups() ? ($this->getTotalItems()/$this->getTotalItemGroups()) : 'N/A'
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

    private function getLastDBBackupTime()
    {
        $this->model('AutomatedReport');
        $dbbkuptime = $this->model_class->get_mapper()->findAll(
            $where = "REPORTTYPE = 3 "
                ."AND REPORTFORMAT like '.xml' ",
            $fields = "TO_CHAR(\"RCREATEDAT\", 'DD-MM-YYYY HH24:MI:SS') ",
            $order = " 1 DESC",
            $limit = " = 1");
        if (count($dbbkuptime) > 1) {
            throw new RuntimeException('Something went wrong during the fetch of of last login date!');
        }
        elseif(empty($dbbkuptime)) {
            return 'N/A';
        }
        else {
            return $dbbkuptime['RCREATEDAT'];
        }
    }

    private function getTotalItems() {
        $this->model('Item');
        $total = $this->model_class->get_mapper()->countAll();
        return $total;
    }

    private function getTotalItemGroups() {
        $this->model('ItemGroup');
        $total = $this->model_class->get_mapper()->countAll();
        if ($total === 0) {
            return false;
        }
        else {
            return $total;
        }
    }
}