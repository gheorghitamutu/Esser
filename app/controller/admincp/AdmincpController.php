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
            case 'admincp/loginlogs':
                $this->loginlogs();
                break;
            case 'admincp/itemlogs':
                $this->itemlogs();
                break;
            case 'admincp/userlogs':
                $this->userlogs();
                break;
            case 'admincp/userlogs/search':
                $this->userlogs();
                break;
            case 'admincp/usereditor':
                $this->usereditor();
                break;
            case 'admincp/usereditor/search':
                $this->searchuser();
                break;
            case 'admincp/usereditor/edituser':
                $this->edituser();
                break;
            case 'admincp/usereditor/getuser':
                $this->goToUserEditor();
                break;
            case 'admincp/usermanager':
                $this->usermanager();
                break;
            case 'admincp/usermanager/deleteuser':
                $this->deleteuser();
                break;
            case 'admincp/usermanager/approveuser':
                $this->approveuser();
                break;
            case 'admincp/usermanager/suspenduser':
                $this->suspenduser();
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
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'index',
            [],
            'AdminCP');
    }

    private function login()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
        if ($this->try_authenticate($_POST["uname"], $_POST["psw"], $is_admin_cp = true)) {
            self::redirect('/admincp/dashboard');
        } else {
            self::redirect('/admincp');
        }
    }

    private function logout()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
        $this->model('UserLog');
        $this->model_class->get_mapper()->insert
        (
            'USERLOGS',
            array
            (
                'uLogDescription' => "'Admin user " . $_SESSION['uname'] . " has logged out!'",
                'uLogSourceIP' => "'" . $_SESSION['login_ip'] . "'"
            )
        );

        session_destroy();
        self::redirect('/admincp');
    }

    private function dashboard()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
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
            'AdminCP');
    }

    private function itemlogs()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'itemlogs',
            [],
            'AdminCP');
    }

    private function loginlogs()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'loginlogs',
            [0 => 'first', 1 => 'second'],
            'AdminCP');
    }

    private function userlogs()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'userlogs',
            ['userLogs' => $this->getUserLogs()],
            'AdminCP');
    }

    private function usereditor()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'editor',
                ['userToEdit' => $_SESSION['userToEdit']],
                'AdminCP');
        } else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'editor',
                [],
                'AdminCP');
        }
    }

    private function searchuser()
    {
        if (isset($_POST['searchuser'])) {
            $this->model('Useracc');
            switch ($_POST['searchuser']) {
                case filter_var($_POST['searchuser'], FILTER_VALIDATE_INT):
                    $validatedfield = ' USERID = ';
                    $user = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . $_POST['searchuser'],
                        $fields = 'USERNAME, USERTYPE, USEREMAIL'
                    );
                    break;
                case filter_var($_POST['searchuser'], FILTER_VALIDATE_EMAIL):
                    $validatedfield = ' USEREMAIL = ';
                    $user = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . "'" . $_POST['searchuser'] . "'",
                        $fields = 'USERNAME, USERTYPE, USEREMAIL'
                    );
                    break;
                default:
                    $validatedfield = ' USERNAME = ';
                    $user = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . "'" . $_POST['searchuser'] . "'",
                        $fields = 'USERNAME, USERTYPE, USEREMAIL'
                    );
                    break;
            }
        }
        else {
            if (isset($_SESSION['userToEdit'])) {
                unset($_SESSION['userToEdit']);
            }
            // Need to implement a fail message delivery .. (You need to offer some search criterias first!)
            self::redirect('/admincp/usereditor');
        }
        if (empty($user) || count($user) !== 1) {

//            echo var_dump($validatedfield);
//            echo var_dump($_POST['searchuser']);
//            echo var_dump($user);
//            exit(0);
            if (isset($_SESSION['userToEdit'])) {
                unset($_SESSION['userToEdit']);
            }
            // Need to implement a fail message delivery .. (Couldn't find any user matching the search criteria!)
            self::redirect('/admincp/usereditor');
        }
        switch ($user[0]['userType']) {
            case 0:
                $user[0]['userType'] = 'Unapproved';
                break;
            case 1:
                $user[0]['userType'] = 'User';
                break;
            case 2:
                $user[0]['userType'] = 'Root Manager';
                break;
            case 3:
                $user[0]['userType'] = 'Root Admin';
                break;
        }
        $_SESSION['userToEdit'] = $user[0];
        self::redirect('/admincp/usereditor');
    }

    private function goToUserEditor()
    {
        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = ' USERID = ' . $_POST['edituser'],
            $fields = 'USERNAME, USERTYPE, USEREMAIL'
        );
        switch ($user[0]['userType']) {
            case 0:
                $user[0]['userType'] = 'Unapproved';
                break;
            case 1:
                $user[0]['userType'] = 'User';
                break;
            case 2:
                $user[0]['userType'] = 'Root Manager';
                break;
            case 3:
                $user[0]['userType'] = 'Root Admin';
                break;
        }
        $_SESSION['userToEdit'] = $user[0];
        self::redirect('/admincp/usereditor');
    }

    private function usermanager()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'manager',
            ['approvedUsers' => $this->getActiveUserList(), 'unapprovedUsers' => $this->getUnapprovedUserList()],
            'AdminCP');
    }

    private function deleteuser()
    {
        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = 'USERID = ' . $_POST['deleteuser']
        );

        if ($_SESSION['userid'] === $user[0]['userId']) {
            // Need to implement a fail message delivery .. (Cannot delete or suspend your own account!)
            self::redirect('/admincp/usermanager');
        } else {
            $query = $this->model_class->get_mapper()->delete(
                $table = 'USERACCS',
                $where = array
                (
                    'USERID' => $user[0]['userId']
                )
            );
            if ($query) {
                $this->model('UserLog');
                $this->model_class->get_mapper()->insert(
                    $fields = array
                    (
                        'uLogDescription' => 'Admin user ' . $_SESSION['uname'] .
                            ' has deleted user ' . $user[0]['userName'] . ' !',
                        'uLogSourceIP' => $_SESSION['login_ip']
                    )
                );
                // Need to implement a success message delivery .. (User was deleted succesfully!)
                self::redirect('/admincp/usermanager');
            } else {
                // Need to implement a fail message delivery .. (Could not delete user because some reasons!)
                self::redirect('/admincp/usermanager');
            }
        }
    }

    private function approveuser()
    {
        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = 'USERID = ' . $_POST['approveuser']
        );

        $query = $this->model_class->get_mapper()->update(
            $table = 'USERACCS',
            $fields = array
            (
                'USERSTATE' => 1
            ),
            $where = array
            (
                'USERID' => $user[0]['userid']
            )
        );
        if ($query) {
            $this->model('UserLog');
            $this->model_class->get_mapper()->insert(
                array
                (
                    'uLogDescription' => 'Admin user ' . $_SESSION['uname'] .
                        ' has approved user ' . $user[0]['userName'] . ' !',
                    'uLogSourceIP' => $_SESSION['login_ip']
                )
            );
            // Need to implement a success message delivery .. (User was approved succesfully!)
            self::redirect('/admincp/usermanager');
        }
        else {
            // Need to implement a fail message delivery .. (Could not approve user because some reasons!)
            self::redirect('/admincp/usermanager');
        }
    }

    private function suspenduser()
    {
        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = 'USERID = ' . $_POST['suspenduser']
        );

        if ($_SESSION['userid'] === $user[0]['userId']) {
            // Need to implement a fail message delivery .. (Cannot delete or suspend your own account!)
            self::redirect('/admincp/usermanager');
        } else {
            $query = $this->model_class->get_mapper()->update(
                $table = 'USERACCS',
                $fields = array
                (
                    'USERSTATE' => 0
                ),
                $where = array
                (
                    'USERID' => $user[0]['userId']
                )
            );
            if ($query) {
                $this->model('UserLog');
                $this->model_class->get_mapper()->insert(
                    $fields = array
                    (
                        'uLogDescription' => 'Admin user ' . $_SESSION['uname'] .
                            ' has suspended user ' . $user[0]['userName'] . ' !',
                        'uLogSourceIP' => $_SESSION['login_ip']
                    )
                );
                // Need to implement a success message delivery .. (User was suspended succesfully!)
                self::redirect('/admincp/usermanager');
            } else {
                // Need to implement a fail message delivery .. (Could not suspend user because some reasons!)
                self::redirect('/admincp/usermanager');
            }
        }
    }

    private function databaseeditor()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'web_settings' . DIRECTORY_SEPARATOR . 'database',
            [],
            'AdminCP');
    }

    private function settings()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'web_settings' . DIRECTORY_SEPARATOR . 'settings',
            [],
            'AdminCP');
    }

    private function getDBTimeZone()
    {
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

    private function getUserLastLoginDate()
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
            throw new RuntimeException('Something went wrong during the fetch of of last login date!');
        }
        if (empty($queryresult) || count($queryresult) === 0) {
            return 'N/A';
        } else {
            return $queryresult[1]['uLogCreatedAt'];
        }
    }

    private function getLastDBBackupTime()
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

    private function getTotalItems()
    {
        $this->model('Item');
        $total = $this->model_class->get_mapper()->countAll();
        return $total;
    }

    private function getTotalItemGroups()
    {
        $this->model('ItemGroup');
        $total = $this->model_class->get_mapper()->countAll();
        if ($total === 0) {
            return false;
        } else {
            return $total;
        }
    }

    private function getActiveUserList()
    {
        $this->model('Useracc');
        $query = $this->model_class->get_mapper()->findAll(
            $where = 'userType > 0',
            $fields = 'USERID, USERNAME, USERTYPE, USERSTATE, TO_CHAR(USERCREATEDAT,\'DD-MM-YYYY HH24:MI:SS\') AS "USERCREATEDAT"',
            $order = 'userCreatedAt DESC'
        );
        for ($i = 0; $i < count($query); ++$i) {
            switch ($query[$i]['userType']) {
                case '1':
                    $query[$i]['userType'] = 'User';
                    break;
                case '2':
                    $query[$i]['userType'] = 'Root Manager';
                    break;
                case '3':
                    $query[$i]['userType'] = 'Root Admin';
                    break;
            }
        }
        return $query;
    }

    private function getUnapprovedUserList()
    {
        $this->model('Useracc');
        $query = $this->model_class->get_mapper()->findAll(
            $where = 'userType = 0',
            $fields = 'USERID, USERNAME, USERTYPE, USERSTATE, TO_CHAR(USERCREATEDAT,\'DD-MM-YYYY HH24:MI:SS\') AS "USERCREATEDAT"',
            $order = 'userCreatedAt DESC'
        );
        for ($i = 0; $i < count($query); ++$i) {
            $query[$i]['userType'] = 'Unapproved';
        }
        return $query;
    }


    private function getUserLogs()
    {
//        $this->model('UserLog');
//        $query = $this->model_class->get_mapper()->findAll(
//            $where = '',
//            $fields = '*',
//            $order = 'uLogCreatedAt DESC'
//        );
//
//        $this->model('Useracc');
//
//        for($i = 0; $i < count($query); ++$i) {
//            $result[$i]['userName'] = substr($query[$i]['uLogDescription'], 0, strpos($query[$i]['uLogDescription'],' '));
//            $result[$i]['uLogDescription'] = substr($query[$i]['uLogDescription'], strpos($query[$i]['uLogDescription'],' '));
//            //$result[$i]['']
//            $temp_result = $this->model_class->get_mapper()->findAll(
//                $where = " userName = '".$result[$i]['userName']."''"
//            );
//            $result[$i]['userId'] = $temp_result['userName'];
//            $result[$i]['']
//        }

    }

}