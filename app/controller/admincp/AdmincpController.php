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

        switch ($uri) {
            case 'admincp':
                $this->index();
                break;
            case 'admincp/login':
                $this->login();
                break;
            case 'admincp/logout':
                echo 'logout';
                $this->logout();
                break;
            case 'admincp/dashboard':
                $this->dashboard();
                break;
            case 'admincp/settings':
                $this->settings();
                break;
            case 'admincp/settings/changetitle':
                $this->changetitle();
                break;
            case 'admincp/usereditor':
                new UsersController($uri);
                break;
            case 'admincp/usereditor/search':
                new UsersController($uri);
                break;
            case 'admincp/usereditor/edituser':
                new UsersController($uri);
                break;
            case 'admincp/usereditor/getuser':
                new UsersController($uri);
                break;
            case 'admincp/usermanager':
                new UsersController($uri);
                break;
            case 'admincp/usermanager/deleteuser':
                new UsersController($uri);
                break;
            case 'admincp/usermanager/approveuser':
                new UsersController($uri);
                break;
            case 'admincp/usermanager/suspenduser':
                new UsersController($uri);
                break;
            case 'admincp/usermanager/unsuspenduser':
                new UsersController($uri);
                break;
            case 'admincp/usergroups':
                new UserGroupsController($uri);
                break;
            case 'admincp/usergroupeditor':
                new UserGroupsController($uri);
                break;
            case 'admincp/usergroupeditor/searchusergroup':
                new UserGroupsController($uri);
                break;
            case 'admincp/usergroupeditor/getgroup':
                new UserGroupsController($uri);
                break;
            case 'admincp/usergroupeditor/removefromgroup':
                new UserGroupsController($uri);
                break;
            case 'admincp/usergroupeditor/editgrouptitle':
                new UserGroupsController($uri);
                break;
            case 'admincp/usergroupeditor/editgroupdescription':
                new UserGroupsController($uri);
                break;
            case 'admincp/itemeditor':
                new ItemsController($uri);
                break;
            case 'admincp/itemmanager':
                new ItemsController($uri);
                break;
            case 'admincp/itemgroups':
                new ItemGroupsController($uri);
                break;
            case 'admincp/itemgroups/delitemgroup':
                new ItemGroupsController($uri);
                break;
            case 'admincp/itemgroupeditor':
                new ItemGroupsController($uri);
                break;
            case 'admincp/itemgroupeditor/searchitmgrp':
                new ItemGroupsController($uri);
                break;
            case 'admincp/itemgroupeditor/getitmgroup':
                new ItemGroupsController($uri);
                break;
            case 'admincp/itemgroupeditor/editgrouptitle':
                new ItemGroupsController($uri);
                break;
            case 'admincp/itemgroupeditor/editgroupdescription':
                new ItemGroupsController($uri);
                break;
            case 'admincp/loginlogs':
                new LogsController($uri);
                break;
            case 'admincp/loginlogs/searchuserlogs':
                new LogsController($uri);
                break;
            case 'admincp/itemlogs':
                new LogsController($uri);
                break;
            case 'admincp/userlogs':
                new LogsController($uri);
                break;
            case 'admincp/userlogs/searchuserlogs':
                new LogsController($uri);
                break;
            case 'admincp/userlogs/search':
                new LogsController($uri);
                break;
            default:
                View::CreateView(
                    '404',
                    [],
                    'Page not found!');
                break;
        }
    }

    protected function index()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'index',
            [],
            APP_TITLE);
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
    }

    protected function login()
    {
        if(!$this->is_user_approved())
        {
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
            self::redirect('/login/unapproved');
            return;
        }

        if ($this->try_authenticate($_POST["uname"], $_POST["psw"], $is_admin_cp = true)) {
            self::redirect('/admincp/dashboard');
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
            return;
        } else {
            self::redirect('/admincp');
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
            return;
        }
    }

    protected function logout()
    {
        $this->model('UserLog');
        $this->model_class->get_mapper()->insert
        (
            $talbe = 'USERLOGS',
            $fields = array
            (
                'uLogDescription' => "'Admin user " . $_SESSION['uname'] . " has logged out!'",
                'uLogSourceIP' => "'" . $_SESSION['login_ip'] . "'"
            )
        );

        session_destroy(); unset($_SESSION);
        self::redirect('/admincp');
    }

    protected function dashboard()
    {
        $this->check_rights();
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . 'dashboard',
            array
            (
                'totalUsers' => $this->getTotalUsers(),
                'onlineUsers' => $this->getTotalOnline(),
                'lastLogin' => $this->getUserLastLoginDate(),
                'timeZone' => $this->getDBTimeZone(),
                'lastDBBackupTime' => $this->getLastDBBackupTime(),
                'totalItemGroups' => $this->getTotalItemGroups(),
                'avgItemPerGroup' => $this->getTotalItemGroups() ? ($this->getTotalItems() / $this->getTotalItemGroups()) : 'N/A'
            ),
            APP_TITLE);
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
    }

    protected function settings()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'web_settings' . DIRECTORY_SEPARATOR . 'settings',
            ['currenttitle' => APP_TITLE],
            APP_TITLE);
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'], $_SESSION['userLoginLogs'], $_SESSION['logsofuser']);
    }

    protected function check_rights() {
        $this->model('Useracc');
        $checked = $this->model_class->get_mapper()->findAll
        (
            $where = 'USERID = ' . $_SESSION['userid']
        )[0]['userType'];

        if ($checked == 3) {
            return;
        }
        else {
            $this->showmessage
            (
                $opsuccess = false,
                $opmessage = 'You are not an admin!!!'
            );
            session_destroy();
            self::redirect('/');
        }
    }

    protected function is_user_approved()
    {
        // checks if the user account is approved or suspended
        // checks if requested email exists in database
        $username = $_POST["uname"];
        $password = $_POST["psw"];

        $salt = '$1_2jlh83#@J^Q';
        $password_hash = hash('sha512', $username . $salt . $password);

        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll
        (
            $where = "userName = '$username' AND userPass = '$password_hash'",
            $fields = false
        );

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

    protected function showmessage($opsucces, $opmessage, $redirectto = false)
    {
        $_SESSION['opsuccess'] = $opsucces;
        $_SESSION['opmessage'] = $opmessage;
        if ($redirectto) {
            self::redirect($redirectto);
            return;
        }
    }

    protected function getDBTimeZone()
    {
        return date_default_timezone_get();
    }

    protected function getTotalUsers()
    {
        $this->model('Useracc');
        $total_users = $this->model_class->get_mapper()->countAll('');
        return $total_users;
    }

    protected function getTotalOnline()
    {
        $this->model('Useracc');
        $online_users = $this->model_class->get_mapper()->countAll("userState = '2'");
        return $online_users;
    }

    protected function getUserLastLoginDate()
    {
        $this->model('Userlog');
        $queryresult =
            $this->model_class->get_mapper()->findAll(
                $where = "ULOGDESCRIPTION like '%" . $_SESSION['uname'] . "%' "
                    . "AND ULOGDESCRIPTION like '%has logged in%' ",
                $fields = 'to_char(ULOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ULOGCREATEDAT"',
                $order = "ULOGID DESC",
                $limit = " < 3");
        if (count($queryresult) > 2) {
            $this->showmessage(false,
                'Something went wrong during the fetch of of last login date!',
                '/admincp/usermanager');
        }
        if (empty($queryresult) || count($queryresult) == 0) {
            return 'N/A';
        }
        elseif (count($queryresult) == 1) {
            return $queryresult[0]['uLogCreatedAt'];
        }
        else {
            return $queryresult[1]['uLogCreatedAt'];
        }
    }

    protected function getLastDBBackupTime()
    {
        $this->model('AutomatedReport');
        $dbbkuptime = $this->model_class->get_mapper()->findAll(
            $where = "REPORTTYPE = 3 "
                . "AND REPORTFORMAT like '.xml' ",
            $fields = "TO_CHAR(\"RCREATEDAT\", 'DD-MM-YYYY HH24:MI:SS') ",
            $order = " 1 DESC",
            $limit = " = 1");
        if (count($dbbkuptime) > 1) {
            throw new RuntimeException('Something went wrong during the fetch of of last login date!');
        } elseif (empty($dbbkuptime)) {
            return 'N/A';
        } else {
            return $dbbkuptime['RCREATEDAT'];
        }
    }

    protected function getTotalItems()
    {
        $this->model('Item');
        $total = $this->model_class->get_mapper()->countAll();
        return $total;
    }

    protected function getTotalItemGroups()
    {
        $this->model('ItemGroup');
        $total = $this->model_class->get_mapper()->countAll();
        if ($total === 0) {
            return false;
        } else {
            return $total;
        }
    }

    protected function changetitle()
    {
        if (preg_match('/[^a-zA-Z0-9._- ]+/', filter_var($_POST['newtitle'], FILTER_SANITIZE_STRING))) {

            $this->showmessage
            (
                $opsuccess = false,
                $opmessage = "Only alpha-numeric, '.', '_' and '-' characters are allowed!"
            );
            self::redirect('/admincp/settings');
        }
        else {
            $cfgfile = ROOT . 'app' . DS . 'config' . DS . 'config.php';
            $newtitle = filter_var($_POST['newtitle'], FILTER_SANITIZE_STRING);
            $success = inFileStrReplace($cfgfile,
                    "define('APP_TITLE'                          , '" . APP_TITLE . "');",
                    "define('APP_TITLE'                          , '" . $newtitle . "');");
            if (!$success) {
                $this->showmessage
                (
                    $opsuccess = false,
                    $opmessage = 'Failed to change APP_TITLE into ' . $newtitle . '!'
                );
                self::redirect('/admincp/settings');
            }
            else {
                $this->adduserlog
                (
                    $logdescription = "Admin user " . $_SESSION['uname'] . " has changed the app title into " . $newtitle,
                    $sourceip = $_SESSION['login_ip']
                );
                $this->showmessage
                (
                    $opsuccess = $success,
                    $opmessage = "Successfully changed app title into "
                                . $newtitle
                                . "! Server is being gracefully restarted!"
                );
                self::redirect('/admincp/settings');
                shell_exec('httpd.exe -k restart');
            }
        }
    }

    protected function adduserlog($logdescription, $sourceip) {
        $this->model('UserLog');
        $this->model_class->get_mapper()->insert
        (
            $table = 'USERLOGS',
            $fields = array
            (
                'ULOGDESCRIPTION' => "'" . $logdescription . "'",
                'ULOGSOURCEIP' => "'" . $sourceip . "'"
            )
        );
    }

    protected function addusergrouplog($logdescription, $sourceip) {
        $this->model('UserGroupLog');
        $this->model_class->get_mapper()->insert
        (
            $table = 'USERGROUPLOGS',
            $fields = array
            (
                'UGLOGDESCRIPTION' => "'" . $logdescription . "'",
                'UGLOGSOURCEIP' => "'" . $sourceip . "'"
            )
        );
    }

    protected function additemlog($logdescription, $sourceip) {
        $this->model('Itemlog');
        $this->model_class->get_mapper()->insert
        (
            $table = 'ITEMLOGS',
            $fields = array
            (
                'ILOGDESCRIPTION' => "'" . $logdescription . "'",
                'ILOGSOURCEIP' => "'" . $sourceip . "'"
            )
        );
    }

    protected function additemgrouplog($logdescription, $sourceip) {
        $this->model('Itemgrouplogs');
        $this->model_class->get_mapper()->insert
        (
            $table = 'ITEMGROUPLOGS',
            $fields = array
            (
                'IGLOGDESCRIPTION' => "'" . $logdescription . "'",
                'IGLOGSOURCEIP' => "'" . $sourceip . "'"
            )
        );
    }

}