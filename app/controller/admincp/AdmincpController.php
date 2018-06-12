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
            case 'admincp/loginlogs':
                $this->loginlogs();
                break;
            case 'admincp/itemlogs':
                $this->itemlogs();
                break;
            case 'admincp/userlogs':
                $this->userlogs();
                break;
            case 'admincp/usereditor':
                $this->usereditor();
                break;
            case 'admincp/usermanager':
                $this->usermanager();
                break;
            case 'admincp/databaseeditor':
                $this->databaseeditor();
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
        if(($result = $this->auth_user($uname, $pass, $isadmcp = true))[0] !== false) {
            $this->currentuser = $result[1];
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
                'lastLogin' => $this->getUserLastLoginDate(),
                'timeZone' =>  $this->getDBTimeZone(),
                'lastDBBackupTime' => $this->getLastDBBackupTime(),
                'totalItemGroups' => $this->getTotalItemGroups(),
                'avgItemPerGroup' =>  $this->getTotalItemGroups() ? ($this->getTotalItems()/$this->getTotalItemGroups()) : 'N/A'
            ),
            'AdminCP');
    }

    private function itemlogs()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'itemlogs',
            [],
            'AdminCP');
    }

    private function loginlogs()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'loginlogs',
            [0=>'first',1=>'second'],
            'AdminCP');
    }

    private function userlogs()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'userlogs',
            [],
            'AdminCP');
    }

    private function usereditor()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'editor',
            [],
            'AdminCP');
    }

    private function usermanager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'manager',
            [],
            'AdminCP');
    }

    private function databaseeditor()
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
        //$this->model('Dual');
//        $result = $this->model_class->get_mapper()->findAlls('DUAL','DUAL');
//        return $result['timezonestamp'];
        return date_default_timezone_get();
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

    private function getUserLastLoginDate() {
        $this->model('Userlog');
        $queryresult =
            $this->model_class->get_mapper()->findAll(
                $where = "ULOGDESCRIPTION like '%" . $_SESSION['uname'] . "%' "
                    ."AND ULOGDESCRIPTION like '%has logged in%' ",
                $fields = 'to_char(ULOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ULOGCREATEDAT"',
                $order = "ULOGID DESC",
                $limit = " < 3");
//        echo var_dump($queryresult)."<br /><br />";
//        echo $queryresult[0]['uLogCreatedAt']."<br /><br />";
//        $queryresult = array();
        if (count($queryresult) > 2) {
            throw new RuntimeException('Something went wrong during the fetch of of last login date!');
        }
        if (empty($queryresult) || count($queryresult) === 0) {
            return 'N/A';
        }
        else {
            return $queryresult[1]['uLogCreatedAt'];
        }
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