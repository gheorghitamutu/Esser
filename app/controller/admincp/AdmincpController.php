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
            case 'admincp/loginlogs/searchuserlogs':
                $this->searchuserloginlogs();
                break;
            case 'admincp/itemlogs':
                $this->itemlogs();
                break;
            case 'admincp/userlogs':
                $this->userlogs();
                break;
            case 'admincp/userlogs/searchuserlogs':
                $this->searchuserlogs();
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
            case 'admincp/usermanager/unsuspenduser':
                $this->unsuspenduser();
                break;
            case 'admincp/usergroups':
                $this->usergroupsmanager();
                break;
            case 'admincp/usergroupeditor':
                $this->usergroupeditor();
                break;
            case 'admincp/usergroupeditor/searchusergroup':
                $this->searchusergroup();
                break;
            case 'admincp/usergroupeditor/getgroup':
                $this->getusergroup();
                break;
            case 'admincp/usergroupeditor/remuserfromgroup':
                $this->remusergromgroup();
                break;
            case 'admincp/itemeditor':
                $this->itemeditor();
                break;
            case 'admincp/itemmanager':
                $this->itemmanager();
                break;
            case 'admincp/itemgroups':
                $this->itemgroupsmanager();
                break;
            case 'admincp/settings':
                $this->settings();
                break;
            case 'admincp/settings/changetitle':
                $this->changetitle();
                break;
            default:
                View::CreateView(
                    '404',
                    [],
                    'Page not found!');
                break;
        }
    }

    private function check_rights() {
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

    private function index()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'index',
            [],
            'AdminCP');
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
    }

    private function login()
    {
        if(!$this->is_user_approved())
        {
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
            self::redirect('/login/unapproved');
            return;
        }

        if ($this->try_authenticate($_POST["uname"], $_POST["psw"], $is_admin_cp = true)) {
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
            self::redirect('/admincp/dashboard');
        } else {
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
            self::redirect('/admincp');
        }
    }

    private function is_user_approved()
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

    private function logout()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            unset($_SESSION['userToEdit']);
        }
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

    private function dashboard()
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
            'AdminCP');
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
    }

    private function itemlogs()
    {
        $this->check_rights();
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'itemlogs',
            [],
            'AdminCP');
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
    }

    private function loginlogs()
    {
        if (isset($_SESSION['userLoginLogs'])) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'loginlogs',
                ['usersloginlogs' => $_SESSION['userLoginLogs']],
                'AdminCP');
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
        else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'loginlogs',
                ['usersloginlogs' => $this->getAllLoginLogs()],
                'AdminCP');
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
    }

    private function userlogs()
    {
        if (key_exists('logsofuser', $_SESSION)) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'userlogs',
                ['userLogs' => $this->getUserLogs($_SESSION['logsofuser'])],
                'AdminCP');
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
        else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'userlogs',
                ['userLogs' => $this->getAllUsersLogs()],
                'AdminCP');
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
    }

    private function searchuserlogs()
    {
        if (isset($_POST['searchuserlogs'])) {
            $this->model('Useracc');
            switch ($_POST['searchuserlogs']) {
                case filter_var($_POST['searchuserlogs'], FILTER_VALIDATE_INT):
                    $validatedfield = ' USERID = ';
                    $user = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . filter_var($_POST['searchuserlogs'], FILTER_SANITIZE_NUMBER_INT),
                        $fields = 'USERID, USERNAME, USEREMAIL'
                    );
                    break;
                case filter_var($_POST['searchuserlogs'], FILTER_VALIDATE_EMAIL):
                    $validatedfield = ' USEREMAIL = ';
                    $user = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . "'" . filter_var($_POST['searchuserlogs'],FILTER_SANITIZE_EMAIL) . "'",
                        $fields = 'USERID, USERNAME, USEREMAIL'
                    );
                    break;
                default:
                    $validatedfield = ' USERNAME = ';
                    $user = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . "'" . filter_var($_POST['searchuserlogs'], FILTER_SANITIZE_STRING) . "'",
                        $fields = 'USERID, USERNAME, USEREMAIL'
                    );
                    break;
            }
        }
        else {
            unset($_SESSION['logsofuser']);
            $this->showmessage
            (
                $opsuccess = true,
                $opmessage ='You need to offer some search criterias first!'
            );
            self::redirect('/admincp/userlogs');
        }

        if (empty($user)) {
            unset($_SESSION['logsofuser']);
            $this->showmessage
            (
                $opsuccess = true,
                $opmessage ='Couldn\'t find any user matching the search criteria!'
            );
            self::redirect('/admincp/userlogs');
        }
        else {
            $_SESSION['logsofuser'] = $user;
            self::redirect('/admincp/userlogs');
        }
    }

    private function usereditor()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'editor',
                ['userToEdit' => $_SESSION['userToEdit']],
                'AdminCP');
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        } else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'editor',
                [],
                'AdminCP');
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
    }

    private function edituser()
    {
        if (!isset($_POST['accname']) || strlen($_POST['accname']) == 0 ) {
            $this->showmessage($opsuccess = false, $opmessage = 'You must set an existing acccount name!');
            self::redirect('/admincp/usereditor');
            return;
        }
        else
        {
            $this->model('Useracc');
            $user = $this->model_class->get_mapper()->findAll(
                $where = ' USERNAME = \'' . $_POST['accname'] . '\'',
                $fields = '*'
            );

            if (count($user) == 0 || empty($user))
            {
                $this->showmessage($opsuccess = false, $opmessage = 'You must set an existing acccount name!');
                self::redirect('/admincp/usereditor');
                return;
            }
            else {
                if (strlen($_POST['acclevel']) < 4) {
                    $islevel = false;
                }
                else {
                    $islevel = true;
                }

                if (strlen($_POST['accemail']) < 4) {
                    $isemail = false;
                }
                else {
                    $isemail = true;
                }

                if (strlen($_POST['accstate']) < 4) {
                    $isstate = false;
                }
                else {
                    $isstate = true;
                }

                if (strlen($_POST['accnewpswd']) < 4 or strlen($_POST['accnewpswd']) > 64) {
                    $ispswd = false;
                    $opsuccess = false;
                    $opmessage = 'Password must be between 4 and 64 characters long!';
                }
                else {
                    $ispswd = true;
                    $opmessage = '';
                    $opsuccess = true;
                }

                if ($isemail == true && !filter_var($_POST['accemail'], FILTER_VALIDATE_EMAIL)) {
                    $opmessage = $opmessage . "\n". 'Bad email format detected!';
                    $opsuccess = false;
                }
                elseif($isemail == true) {
                    $email = $_POST['accemail'];
                }

                if ($islevel == true && !filter_var($_POST['acclevel'], FILTER_SANITIZE_STRING)) {
                    $opmessage = $opmessage . "\n" . "Account level must be in a string format!";
                    $opsuccess = false;
                }
                elseif ($islevel == true) {
                    if (!in_array($_POST['acclevel'], ['Root Admin', 'Root Manager', 'User', 'Unapproved'], true)) {
                        $opmessage = $opmessage . "\n" . "Account level can only be: 'Root Admin', 'Root Manager', 'User' or 'Unapproved' !";
                        $opsuccess = false;
                    }
                    else {
                        switch ($_POST['acclevel']) {
                            case 'Root Admin':
                                $level = 3;
                                break;
                            case 'Root Manager':
                                $level = 2;
                                break;
                            case 'User':
                                $level = 1;
                                break;
                            case 'Unapproved':
                                $level = 0;
                                break;
                        }
                    }
                }

                if ($isstate == true && !filter_var($_POST['accstate'], FILTER_SANITIZE_STRING)) {
                    $state = false;
                    $opmessage = $opmessage . "\n" . "Account level must be in a string format!";
                    $opsuccess = false;
                }
                elseif ($isstate == true) {
                    if (!in_array($_POST['accstate'], ['Active', 'Suspended'], true)) {
                        $state = false;
                        $opmessage = $opmessage . "\n" . "Account state can only be: ''Active' or 'Suspended'!";
                        $opsuccess = false;
                    }
                    else {
                        switch ($_POST['accstate']) {
                            case 'Active':
                                $state = 1;
                                break;
                            case 'Suspended':
                                $state = 0;
                                break;
                        }
                    }
                }

                if ($opsuccess) {
                    $newpswd = $_POST['accnewpswd'];
                    $salt = '$1_2jlh83#@J^Q';
                    $newpswd = hash('sha512', $_POST['accname'] . $salt . $newpswd);
                    $this->model('Useracc');

                    if ($user[0]['userType'] == 3 && $level < 3) {
                        $this->model('Grouprelation');
                        $this->model_class->get_mapper()->delete
                        (
                            $table = 'GROUPRELATIONS',
                            $where = array
                            (
                                'USERID' => $user[0]['userId'],
                                'UGROUPID' => 1
                            )
                        );
                        if ($level < 2) {
                            $this->model_class->get_mapper()->delete
                            (
                                $table = 'GROUPRELATIONS',
                                $where = array
                                (
                                    'USERID' => $user[0]['userId'],
                                    'UGROUPID' => 2
                                )
                            );
                        }
                    }

                    if ($user[0]['userType'] == 2 && $level < 2) {
                        $this->model('Grouprelation');
                        $this->model_class->get_mapper()->delete
                        (
                            $table = 'GROUPRELATIONS',
                            $where = array
                            (
                                'USERID' => $user[0]['userId'],
                                'UGROUPID' => 2
                            )
                        );
                    }


                    $query = $this->model_class->get_mapper()->update
                    (
                        $table = 'USERACCS',
                        $fields = array
                        (
                            'USERTYPE'  => ($islevel      ? ($level)           : ('USERTYPE') ),
                            'USERSTATE' => ($isstate      ? ($state)           : ('USERSTATE') ),
                            'USEREMAIL' => ($isemail      ? ("'".$email."'")   : ('USEREMAIL') ),
                            'USERPASS'  => ($ispswd       ? ("'".$newpswd."'") : ('USERPASS') )
                        ),
                        $where = array
                        (
                            'USERID' => $user[0]['userId']
                        )
                    );
                    if (is_array($query)) {
                        $this->model('UserLog');
                        $this->model_class->get_mapper()->insert
                        (
                            $table = 'USERLOGS',
                            $fields = array
                            (
                                'uLogDescription' => "'Admin user " . $_SESSION['uname'] .
                                    ' has edited user ' . $user[0]['userName'] . " !'",
                                'uLogSourceIP' => "'" . $_SESSION['login_ip'] . "''"
                            )
                        );
                        if ($islevel && $level > 1) {
                            $this->model('Grouprelation');
                            $isalready = $this->model_class->get_mapper()->findAll
                            (
                                $where = 'USERID = ' . $user[0]['userId'] .
                                        ' AND UGROUPID = ' . (($level==2)? 2 : 1)
                            );
                            if (empty($isalready) || $isalready == false) {
                                $this->model_class->get_mapper()->insert
                                (
                                    $table = 'GROUPRELATIONS',
                                    $fields = array
                                    (
                                        'USERID' => $user[0]['userId'],
                                        'UGROUPID' => (($level==2) ? 2 : 1),
                                        'CANUPDITM' => 1,
                                        'CANMNGMBS' => 1
                                    )
                                );
                            }
                            $rootadminscount = count($this->model_class->get_mapper()->findAll
                            (
                                $where = " UGROUPID = 3",
                                $fields = '*'
                            )['relationId']);
                            $rootadmmngcount = count($this->model_class->get_mapper()->findAll
                            (
                                $where = " UGROUPID = 3 AND canMngMbs = 1",
                                $fields = '*'
                            )['relationId']);
                            $rootmanagerscount = count($this->model_class->get_mapper()->findAll
                            (
                                $where = " UGROUPID = 2",
                                $fields = '*'
                            )['relationId']);
                            $rootmngmngcount = count($this->model_class->get_mapper()->findAll
                            (
                                $where = " UGROUPID = 3 AND canMngMbs = 1",
                                $fields = '*'
                            )['relationId']);

                            $this->model('Usergroup');
                            $this->model_class->get_mapper()->update
                            (
                                $table = 'USERGROUPS',
                                $fields = array
                                (
                                    'NROFMEMBERS' => $rootadminscount
                                ),
                                $where = array
                                (
                                    'UGROUPID' => 3
                                )
                            );
                            $this->model_class->get_mapper()->update
                            (
                                $table = 'USERGROUPS',
                                $fields = array
                                (
                                    'NROFMANAGERS' => $rootadmmngcount
                                ),
                                $where = array
                                (
                                    'UGROUPID' => 3
                                )
                            );
                            $this->model_class->get_mapper()->update
                            (
                                $table = 'USERGROUPS',
                                $fields = array
                                (
                                    'NROFMEMBERS' => $rootmanagerscount
                                ),
                                $where = array
                                (
                                    'UGROUPID' => 2
                                )
                            );
                            $this->model_class->get_mapper()->update
                            (
                                $table = 'USERGROUPS',
                                $fields = array
                                (
                                    'NROFMANAGERS' => $rootmngmngcount
                                ),
                                $where = array
                                (
                                    'UGROUPID' => 2
                                )
                            );
                        }
                        $this->showmessage($opsuccess,
                            'You have succesfully edited the user!'
                        );
                        self::redirect('/admincp/usereditor');
                        return;
                    }
                    else {
                        $this->showmessage($opsuccess,
                            'Something went wrong while trying to edit user!'
                        );
                        self::redirect('/admincp/usereditor');
                        return;
                    }
                }
                else {
                    $this->showmessage($opsuccess,
                        $opmessage
                    );
                    self::redirect('/admincp/usereditor');
                    return;
                }
            }
        }
    }

    private function showmessage($opsucces, $opmessage, $redirectto = false)
    {
        $_SESSION['opsuccess'] = $opsucces;
        $_SESSION['opmessage'] = $opmessage;
        if  ($redirectto) {
            self::redirect($redirectto);
        }
    }

    private function searchuser()
    {
        if (isset($_POST['searchuser'])) {
            $this->model('Useracc');
            switch ($_POST['searchuser'])
            {
                case filter_var($_POST['searchuser'], FILTER_VALIDATE_INT):
                    $validatedfield = ' USERID = ';
                    $user = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . $_POST['searchuser'],
                        $fields = 'USERNAME, USERTYPE, USEREMAIL, USERSTATE'
                    );
                    break;
                case filter_var($_POST['searchuser'], FILTER_VALIDATE_EMAIL):
                    $validatedfield = ' USEREMAIL = ';
                    $user = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . "'" . $_POST['searchuser'] . "'",
                        $fields = 'USERNAME, USERTYPE, USEREMAIL, USERSTATE'
                    );
                    break;
                default:
                    $validatedfield = ' USERNAME = ';
                    $user = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . "'" . $_POST['searchuser'] . "'",
                        $fields = 'USERNAME, USERTYPE, USEREMAIL, USERSTATE'
                    );
                    break;
            }
        }
        else {
            unset($_SESSION['userToEdit']);
            $this->showmessage
            (
                $opsuccess = true,
                $opmessage ='You need to offer some search criterias first!'
            );
            self::redirect('/admincp/usereditor');
        }
        if (empty($user)) {
            unset($_SESSION['userToEdit']);
            $this->showmessage
            (
                $opsuccess = true,
                $opmessage ='Couldn\'t find any user matching the search criteria!'
            );
            self::redirect('/admincp/usereditor');
        }
        else {
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
            switch ($user[0]['userState']) {
                case 0:
                    $user[0]['userState'] = 'Suspended';
                    break;
                case 1:
                    $user[0]['userState'] = 'Offline';
                    break;
                case 2:
                    $user[0]['userState'] = 'Online';
                    break;
            }
            $_SESSION['userToEdit'] = $user[0];
            self::redirect('/admincp/usereditor');
        }
    }

    private function goToUserEditor()
    {
        $this->model('Useracc');
        if (!filter_var($_POST['edituser'], FILTER_VALIDATE_INT)) {
            $this->showmessage($opsuccess =false,
                    $opmessage = 'Illegal argument supplied!');
            self::redirect('admincp/usermanager');
            return;
        }
        $user = $this->model_class->get_mapper()->findAll(
            $where = ' USERID = ' . $_POST['edituser'],
            $fields = 'USERNAME, USERTYPE, USEREMAIL, USERSTATE'
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
        switch ($user[0]['userState']) {
            case 0:
                $user[0]['userState'] = 'Suspended';
                break;
            case 1:
                $user[0]['userState'] = 'Offline';
                break;
            case 2:
                $user[0]['userState'] = 'Online';
                break;
        }
        $_SESSION['userToEdit'] = $user[0];
        self::redirect('/admincp/usereditor');
    }

    private function usermanager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'manager',
            [
                'approvedUsers' => array
                                (
                                    'activeUsers' => $this->extractActiveUsers($this->getActivatedUserList()),
                                    'suspendedUsers' => $this->extractSuspendedUsers($this->getActivatedUserList())
                                ),
                'unapprovedUsers' => $this->getUnapprovedUserList()
            ],

            'AdminCP');
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
    }

    private function extractActiveUsers($userlist)
    {
        if (!is_array($userlist)) {
            return [];
        }
        else {
            if (count($userlist) == 0) {
                return [];
            }
            else {
                $activeusers = [];
                $nrofusers = 0;
                for ($i = 0; $i < count($userlist); ++$i) {
                    if ($userlist[$i]['userState'] !== 'Suspended') {
                        $activeusers[$nrofusers] = $userlist[$i];
                        $nrofusers += 1;
                    }
                }
                return $activeusers;
            }
        }
    }

    private function extractSuspendedUsers($userlist)
    {
        if (!is_array($userlist)) {
            return [];
        }
        else {
            if (count($userlist) == 0) {
                return [];
            }
            else {
                $suspendedusers = [];
                $nrofusers = 0;
                for ($i = 0; $i < count($userlist); ++$i) {
                    if ($userlist[$i]['userState'] == 'Suspended') {
                        $suspendedusers[$nrofusers] = $userlist[$i];
                        $nrofusers += 1;
                    }
                }
                return $suspendedusers;
            }
        }
    }

    private function getActivatedUserList()
    {
        $this->model('Useracc');
        $query = $this->model_class->get_mapper()->findAll(
            $where = 'userType > 0',
            $fields = 'USERID, USERNAME, USEREMAIL, USERTYPE, USERSTATE, TO_CHAR(USERCREATEDAT,\'DD-MM-YYYY HH24:MI:SS\') AS "USERCREATEDAT"',
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
            switch ($query[$i]['userState']) {
                case '0':
                    $query[$i]['userState'] = 'Suspended';
                    $query[$i]['labelType'] = 'suspended-status';
                    break;
                case '1':
                    $query[$i]['userState'] = 'Offline';
                    $query[$i]['labelType'] = 'offline-status';
                    break;
                case '2':
                    $query[$i]['userState'] = 'Online';
                    $query[$i]['labelType'] = 'online-status';
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
            $fields = 'USERID, USERNAME, USERTYPE, USEREMAIL, USERSTATE, TO_CHAR(USERCREATEDAT,\'DD-MM-YYYY HH24:MI:SS\') AS "USERCREATEDAT"',
            $order = 'userCreatedAt DESC'
        );
        for ($i = 0; $i < count($query); ++$i) {
            $query[$i]['userType'] = 'Unapproved';
        }
        return $query;
    }

    private function deleteuser()
    {
        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = 'USERID = ' . $_POST['deleteuser']
        );
        if ($_SESSION['userid'] === $user[0]['userId']) {
            $this->showmessage(false,
                'Cannot delete your own account!',
                '/admincp/usermanager');
        } else {
//            $this->model('')
            $query = $this->model_class->get_mapper()->delete(
                $table = 'USERACCS',
                $where = array
                (
                    'USERID' => $user[0]['userId']
                )
            );

            if ($query) {
                $this->model('UserLog');
                $this->model_class->get_mapper()->insert
                (
                    $table = 'USERLOGS',
                    $fields = array
                    (
                        'uLogDescription' => "'Admin user " . $_SESSION['uname'] .
                            ' has deleted user ' . $user[0]['userName'] . " !'",
                        'uLogSourceIP' => "'" . $_SESSION['login_ip'] . "'"
                    )
                );
                $this->showmessage(true,
                    'User was deleted successfully!',
                    '/admincp/usermanager');
            } else {
                $this->showmessage(false,
                    'Something went wrong while trying to delete the user!',
                    '/admincp/usermanager');
            }
        }
    }

    private function approveuser()
    {
        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = 'USERID = ' . $_POST['approveuser']
        );

        $query = $this->model_class->get_mapper()->update
        (
            $table = 'USERACCS',
            $fields = array
            (
                'USERTYPE' => 1
            ),
            $where = array
            (
                'USERID' => $user[0]['userId']
            )
        );
        if (is_array($query)) {
            $this->model('UserLog');
            $this->model_class->get_mapper()->insert
            (
                $table = 'USERLOGS',
                $fields = array
                (
                    'uLogDescription' => "'Admin user " . $_SESSION['uname'] .
                        ' has approved user ' . $user[0]['userName'] . ' !\'',
                    'uLogSourceIP' => "'" . $_SESSION['login_ip'] . "'"
                )
            );
            $this->showmessage(true,
                'User was approved successfully!',
                '/admincp/usermanager');
        }
        else {
            $this->showmessage(false,
                'Something went wrong while trying to approve the user!',
                '/admincp/usermanager');
        }
    }

    private function suspenduser()
    {
        if (!filter_var($_POST['suspenduser'], FILTER_VALIDATE_INT)) {
            $this->showmessage
            (
                $opsucces = false,
                $opmessage = 'Bad request!'
            );
            self::redirect('/admincp/usermanager');
        }
        else {
            $this->model('Useracc');
            $user = $this->model_class->get_mapper()->findAll(
                $where = 'USERID = ' . filter_var($_POST['suspenduser'], FILTER_SANITIZE_NUMBER_INT)
            );

            if ($_SESSION['userid'] === $user[0]['userId']) {
                $this->showmessage
                (
                    $opsucces = false,
                    $opmessa = 'Cannot suspend your own account!'
                );
                self::redirect('/admincp/usermanager');
            }
            else {
                $query = $this->model_class->get_mapper()->update
                (
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
                if (is_array($query)) {
                    $this->model('UserLog');
                    $this->model_class->get_mapper()->insert
                    (
                        $table = 'USERLOGS',
                        $fields = array
                        (
                            'uLogDescription' => '\'Admin user ' . $_SESSION['uname'] .
                                ' has suspended user ' . $user[0]['userName'] . ' !\'',
                            'uLogSourceIP' => '\''.$_SESSION['login_ip'].'\''
                        )
                    );
                    $this->showmessage(true,
                        'User was suspended successfully!',
                        '/admincp/usermanager');
                } else {
                    $this->showmessage(false,
                        'Something went wrong while trying to suspend the user!',
                        '/admincp/usermanager');
                }
            }
        }
    }

    private function unsuspenduser()
    {
        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = 'USERID = ' . $_POST['unsuspenduser']
        );

        if ($_SESSION['userid'] === $user[0]['userId']) {
            session_destroy();
        } else {
            $query = $this->model_class->get_mapper()->update
            (
                $table = 'USERACCS',
                $fields = array
                (
                    'USERSTATE' => 1
                ),
                $where = array
                (
                    'USERID' => $user[0]['userId']
                )
            );
            if (is_array($query)) {
                $this->model('UserLog');
                $this->model_class->get_mapper()->insert
                (
                    $table = 'USERLOGS',
                    $fields = array
                    (
                        'uLogDescription' => '\'Admin user ' . $_SESSION['uname'] .
                            ' has unsuspended user ' . $user[0]['userName'] . ' !\'',
                        'uLogSourceIP' => '\''. $_SESSION['login_ip'] . '\''
                    )
                );
                $this->showmessage(true,
                    'User was unsuspended successfully!',
                    '/admincp/usermanager');
            } else {
                $this->showmessage(false,
                    'Something went wrong while trying to unsuspend the user!',
                    '/admincp/usermanager');
            }
        }
    }

    private function usergroupsmanager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'user_groups' . DIRECTORY_SEPARATOR . 'manager',
            [
                'grouplist' => $this->getAllUserGroups()
            ],
            'AdminCP');
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
    }

    private function getAllUserGroups()
    {
        $this->model('Usergroup');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = '',
            $fields = 'UGROUPID, UGROUPNAME, UGROUPDESCRIPTION, NROFMEMBERS, NROFMANAGERS,
                        TO_CHAR(UGROUPCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "UGROUPCREATEDAT"',
            $order = 'UGROUPID ASC'
        );
        $this->model('Grouprelation');
        for ($i = 0; $i < count($query); ++$i) {
            $result[$i]['groupid'] = $query[$i]['uGroupId'];
            $result[$i]['groupname'] = $query[$i]['uGroupName'];
            $result[$i]['groupdscrp'] = $query[$i]['uGroupDescription'];
            $result[$i]['datetime'] = $query[$i]['uGroupCreatedAt'];
            $result[$i]['nrofmembers'] = count($this->model_class->get_mapper()->findAll
            (
                $where = ' UGROUPID = ' . $query[$i]['uGroupId'],
                $fields = ''
            ));
            $result[$i]['nrofmanagers'] = count($this->model_class->get_mapper()->findAll
            (
                $where = ' UGROUPID = ' . $query[$i]['uGroupId'] . ' AND CANMNGMBS = 1',
                $fields = ''
            ));
        }
        $nrofgroupsupdated = 0;
        $this->model('Usergroup');
        for ($i = 0; $i < count($query); ++$i) {
            $update = $this->model_class->get_mapper()->update
            (
                $table = 'USERGROUPS',
                $fields = array
                (
                    'nrOfMembers' => $result[$i]['nrofmembers'],
                    'nrOfManagers' => $result[$i]['nrofmanagers']
                ),
                $where = array
                (
                    'UGROUPID' => $result[$i]['groupid']
                )
            );
            if ($update) {
                $nrofgroupsupdated += 1;
            }
        }
        LOGGER::getInstance()->log(LOGGING,
            'Successfully updated the number of members and managers of '
            . $nrofgroupsupdated . ' usergroups from ' . count($query) );
        return $result;
    }

    private function usergroupeditor()
    {
        if (isset($_SESSION['usergrouptoedit'])) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'user_groups' . DIRECTORY_SEPARATOR . 'editor',
                [
                    'memberlist' => $_SESSION['usergrouptoedit']['memberlist'],
                    'generalgroupinfo' => $_SESSION['usergrouptoedit'],
                ],

                'AdminCP');
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
        else {
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'user_groups' . DIRECTORY_SEPARATOR . 'editor',
                [
                    'memberlist' => [],
                    'generalgroupinfo' => []
                ],

                'AdminCP');
        }
    }

    private function searchusergroup()
    {
        if (!filter_var($_POST['searchusergroup'], FILTER_VALIDATE_INT) && filter_var($_POST['searchusergroup'], FILTER_SANITIZE_STRING) == false) {
            $this->showmessage($opsuccess = false, $opmessage = 'Illegal characters found in search criteria!');
            self::redirect('/admincp/usergroupeditor');
            return;
        }
        $this->model('Usergroup');
        if (filter_var($_POST['searchusergroup'], FILTER_VALIDATE_INT)) {
            $group = $this->model_class->get_mapper()->findAll
            (
                $where = 'UGROUPID = ' . $_POST['searchusergroup']
            );
            if (count($group)!= 1 || empty($group) || $group == false) {
                $this->showmessage($opsuccess = false, $opmessage = 'No group matching the criteria was found!');
                self::redirect('/admincp/usergroupeditor');
                return;
            }
            $this->editusergroup($group[0]['uGroupId']);
        }
        else {
            $group = $this->model_class->get_mapper()->findAll
            (
                $where = 'UGROUPNAME LIKE ' . "'" . filter_var($_POST['searchusergroup'], FILTER_SANITIZE_STRING) . "'"
            );
            if (count($group)!= 1 || empty($group) || $group == false) {
                $this->showmessage($opsuccess = false, $opmessage = 'No group matching the criteria was found!');
                self::redirect('/admincp/usergroupeditor');
                return;
            }
            $this->editusergroup($group[0]['uGroupId']);
        }
    }
    private function getusergroup()
    {
        $this->model('Usergroup');
        if (filter_var($_POST['editusergroup'], FILTER_VALIDATE_INT)) {
            $this->editusergroup($_POST['editusergroup']);
        }
        else {
            $this->showmessage($opsuccess = false,
                $opmessage = 'Invalid arguments provided!');
            self::redirect('/admincp/usergroups');
            return;
        }
    }
    private function editusergroup($groupid)
    {
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = 'UGROUPID = ' . $groupid,
            $fields = 'UGROUPID, UGROUPNAME, UGROUPDESCRIPTION, NROFMEMBERS, NROFMANAGERS,
                        TO_CHAR(UGROUPCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "UGROUPCREATEDAT"'
        );

        if (!is_array($query) || count($query) == 0 || !$query) {
            $this->showmessage($opsuccess = false,
                $opmessage = 'Couldn\'t find any usergroup that matches that criteria!');
            self::redirect('/admincp/usergroups');
            return;
        }

        $this->model('Grouprelation');
        $result[0]['groupid'] = $query[0]['uGroupId'];
        $result[0]['groupname'] = $query[0]['uGroupName'];
        $result[0]['groupdscrp'] = $query[0]['uGroupDescription'];
        $result[0]['datetime'] = $query[0]['uGroupCreatedAt'];
        $result[0]['memberlist'] = $this->model_class->get_mapper()->findAll
        (
            $where = ' UGROUPID = ' . $query[0]['uGroupId'],
            $fields = 'RELATIONID, USERID, UGROUPID, CANUPDITM, CANMNGMBS, 
                TO_CHAR(GRPRELCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "GRPRELCREATEDAT"',
            $order = 'CANMNGMBS DESC, CANUPDITM DESC, GRPRELCREATEDAT ASC, USERID ASC'
        );
        $result[0]['nrofmembers'] = count($result[0]['memberlist']);
        $result[0]['nrofmanagers'] = count($this->model_class->get_mapper()->findAll
        (
            $where = ' UGROUPID = ' . $query[0]['uGroupId'] . ' AND CANMNGMBS = 1',
            $fields = ''
        ));

        $this->model('Useracc');
        for ($i = 0; $i < count($result[0]['memberlist']); ++$i) {
            $result[0]['memberlist'][$i]['userinfo'] = $this->model_class->get_mapper()->findAll
            (
                $where = 'USERID = ' . $result[0]['memberlist'][$i]['userId']
            );
            $result[0]['memberlist'][$i]['userName'] = $result[0]['memberlist'][$i]['userinfo'][0]['userName'];
            $result[0]['memberlist'][$i]['userEmail'] = $result[0]['memberlist'][$i]['userinfo'][0]['userEmail'];
            switch ($result[0]['memberlist'][$i]['canMngMbs']) {
                case 1:
                    $result[0]['memberlist'][$i]['canMngMbs'] = 'Yes';
                    break;
                default:
                    $result[0]['memberlist'][$i]['canMngMbs'] = 'No';
                    break;
            }
            switch ($result[0]['memberlist'][$i]['canUpdItm']) {
                case 1:
                    $result[0]['memberlist'][$i]['canUpdItm'] = 'Yes';
                    break;
                default:
                    $result[0]['memberlist'][$i]['canUpdItm'] = 'No';
                    break;
            }
            switch($result[0]['memberlist'][$i]['userinfo'][0]['userType']) {
                case 0:
                    $result[0]['memberlist'][$i]['userType'] = 'Unapproved';
                    break;
                case 1:
                    $result[0]['memberlist'][$i]['userType'] = 'User';
                    break;
                case 2:
                    $result[0]['memberlist'][$i]['userType'] = 'Root Manager';
                    break;
                case 3:
                    $result[0]['memberlist'][$i]['userType'] = 'Root Admin';
                    break;
            }
            switch($result[0]['memberlist'][$i]['userinfo'][0]['userState']) {
                case 0:
                    $result[0]['memberlist'][$i]['userState'] = 'Suspended';
                    break;
                case 1:
                    $result[0]['memberlist'][$i]['userState'] = 'Offline';
                    break;
                case 2:
                    $result[0]['memberlist'][$i]['userState'] = 'Online';
                    break;
            }
            switch ($result[0]['memberlist'][$i]['userState']) {
                case "Suspended":
                    $result[0]['memberlist'][$i]['labelType'] = 'suspended-status';
                    break;
                case "Offline":
                    $result[0]['memberlist'][$i]['labelType'] = 'offline-status';
                    break;
                case "Online":
                    $result[0]['memberlist'][$i]['labelType'] = 'online-status';
                    break;
            }
            unset($result[0]['memberlist'][$i]['userinfo']);
        }

        $_SESSION['usergrouptoedit'] = $result[0];
        self::redirect('/admincp/usergroupeditor');
    }

    private function itemeditor()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'editor',
            [
                'grouplist' => []
            ],

            'AdminCP');
    }

    private function itemmanager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'manager',
            [
                'grouplist' => []
            ],

            'AdminCP');
    }

    private function itemgroupsmanager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'item_groups' . DIRECTORY_SEPARATOR . 'manager',
            [
                'grouplist' => []
            ],

            'AdminCP');
    }

    private function settings()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'web_settings' . DIRECTORY_SEPARATOR . 'settings',
            ['currenttitle' => APP_TITLE],
            'AdminCP');
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'], $_SESSION['userLoginLogs'], $_SESSION['logsofuser']);
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

    private function getUserLogs($user)
    {
        $this->model('UserLog');
        $query = $this->model_class->get_mapper()->findAll(
            $where = ' ULOGDESCRIPTION like \'%' . $user[0]['userName'] . '%\'',
            $fields = 'ULOGID, ULOGDESCRIPTION, ULOGSOURCEIP, TO_CHAR(ULOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ULOGCREATEDAT"',
            $order = 'uLogCreatedAt DESC'
        );
        $result = [];

        for($i=0; $i<count($query); ++$i) {
            $result[$i]['userName'] = $user[0]['userName'];
            $result[$i]['datetime'] = $query[$i]['uLogCreatedAt'];
            $result[$i]['action']   = $query[$i]['uLogDescription'];
            $result[$i]['sourceIP'] = $query[$i]['uLogSourceIP'];
        }

        return $result;
    }

    private function getAllUsersLogs()
    {
        $this->model('Useracc');
        $users = $this->model_class->get_mapper()->findAll
        (
            $where = " USERID = USERID",
            $fields = 'USERNAME'
        );



        $this->model('UserLog');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = '',
            $fields = 'ULOGID, ULOGDESCRIPTION, ULOGSOURCEIP, TO_CHAR(ULOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ULOGCREATEDAT"',
            $order = ' ULOGCREATEDAT DESC'
        );

        for($i = 0; $i < count($users); ++$i) {
            $users[$i] = $users[$i]['userName'];
        }

        for($i=0; $i<count($query); ++$i) {
            if (preg_match('/(Admin\ user\ )/', $query[$i]['uLogDescription'])) {
                $result[$i]['userName'] = substr(
                    $query[$i]['uLogDescription'],
                    strlen('Admin user '),
                    strpos(
                        substr(
                            $query[$i]['uLogDescription'],
                            strlen('Admin user ')
                        ),
                        " "));
            }
            elseif (preg_match('/(Normal\ user\ )/', $query[$i]['uLogDescription'])) {
                $result[$i]['userName'] = substr(
                    $query[$i]['uLogDescription'],
                    strlen('Normal user '),
                    strpos(
                        substr(
                            $query[$i]['uLogDescription'],
                            strlen('Normal user ')
                        ),
                        " "));
            }
            elseif (preg_match('/(Manager\ user\ )/', $query[$i]['uLogDescription'])) {
                $result[$i]['userName'] = substr(
                    $query[$i]['uLogDescription'],
                    strlen('Manager user '),
                    strpos(
                        substr(
                            $query[$i]['uLogDescription'],
                            strlen('Manager user ')
                        ),
                        " "));
            }
            else {
                $result[$i]['userName'] = 'N/A';
            }
            $result[$i]['datetime'] = $query[$i]['uLogCreatedAt'];
            $result[$i]['action']   = $query[$i]['uLogDescription'];
            $result[$i]['sourceIP'] = $query[$i]['uLogSourceIP'];
        }
        return $result;
    }

    private function searchuserloginlogs() {
        if (!isset($_POST['searchuserloginlogs'])) {
            $this->showmessage
            (
                $opsuccess = false,
                $opmessage = 'You need to offer a search criteria first!'
            );
            self::redirect('/admincp/loginlogs');
            return;
        }
        $this->model('Useracc');
        switch ($_POST['searchuserloginlogs'])
        {
            case filter_var($_POST['searchuserloginlogs'], FILTER_VALIDATE_INT):
                $validatedfield = ' USERID = ';
                $user = $this->model_class->get_mapper()->findAll(
                    $where = $validatedfield . filter_var($_POST['searchuserloginlogs'], FILTER_SANITIZE_NUMBER_INT),
                    $fields = 'USERNAME, USERTYPE, USEREMAIL, USERSTATE'
                );
                break;
            case filter_var($_POST['searchuser'], FILTER_VALIDATE_EMAIL):
                $validatedfield = ' USEREMAIL = ';
                $user = $this->model_class->get_mapper()->findAll(
                    $where = $validatedfield . "'" . filter_var($_POST['searchuserloginlogs'],FILTER_SANITIZE_EMAIL) . "'",
                    $fields = 'USERNAME, USERTYPE, USEREMAIL, USERSTATE'
                );
                break;
            default:
                $validatedfield = ' USERNAME = ';
                $user = $this->model_class->get_mapper()->findAll(
                    $where = $validatedfield . "'" . filter_var($_POST['searchuserloginlogs'],FILTER_SANITIZE_STRING) . "'",
                    $fields = 'USERNAME, USERTYPE, USEREMAIL, USERSTATE'
                );
                break;
        }
        if ((!$user) || (count($user) != 1)) {
            $user[0]['userName'] = filter_var($_POST['searchuserloginlogs'],FILTER_SANITIZE_STRING);
        }
        $this->model('Userlog');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = " ULOGDESCRIPTION LIKE '%" . $user[0]['userName'] ."'",
            $fields = 'ULOGID, ULOGDESCRIPTION, ULOGSOURCEIP, TO_CHAR(ULOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ULOGCREATEDAT"',
            $order = ' ULOGCREATEDAT DESC'
        );

        if (is_array($query) && count($query) > 0) {
            for ($i = 0; $i < count($query);  ++$i ) {
                $_SESSION['userLoginLogs'][$i]['userName'] = $user[0]['userName'];
                $_SESSION['userLoginLogs'][$i]['datetime'] = $query[$i]['uLogCreatedAt'];
                $_SESSION['userLoginLogs'][$i]['action']   = $query[$i]['uLogDescription'];
                $_SESSION['userLoginLogs'][$i]['sourceIP'] = $query[$i]['uLogSourceIP'];
            }
            self::redirect('/admincp/loginlogs');
        }
        else {
            $this->showmessage
            (
                $opsuccess = false,
                $opmessage = 'Couldn\'t find any login logs that belong to that user!'
            );
            self::redirect('/admincp/loginlogs');
            return;
        }
    }

    private function getAllLoginLogs()
    {
        $this->model('Userlog');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = "ULOGDESCRIPTION LIKE '%has logged%'",
            $fields = 'ULOGID, ULOGDESCRIPTION, ULOGSOURCEIP, TO_CHAR(ULOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ULOGCREATEDAT"',
            $order = 'ULOGCREATEDAT DESC '
        );
        $result = [];
        if (is_array($query) && count($query) > 0) {
            for ($i = 0; $i < count($query);  ++$i ) {
                if (preg_match('/(Admin\ user\ )/', $query[$i]['uLogDescription'])) {
                    $result[$i]['userName'] = substr(
                        $query[$i]['uLogDescription'],
                        strlen('Admin user '),
                        strpos(
                            substr(
                                $query[$i]['uLogDescription'],
                                strlen('Admin user ')
                            ),
                            " "));
                }
                elseif (preg_match('/(Normal\ user\ )/', $query[$i]['uLogDescription'])) {
                    $result[$i]['userName'] = substr(
                        $query[$i]['uLogDescription'],
                        strlen('Normal user '),
                        strpos(
                            substr(
                                $query[$i]['uLogDescription'],
                                strlen('Normal user ')
                            ),
                            " "));
                }
                elseif (preg_match('/(Manager\ user\ )/', $query[$i]['uLogDescription'])) {
                    $result[$i]['userName'] = substr(
                        $query[$i]['uLogDescription'],
                        strlen('Manager user '),
                        strpos(
                            substr(
                                $query[$i]['uLogDescription'],
                                strlen('Manager user ')
                            ),
                            " "));
                }
                else {
                    $result[$i]['userName'] = 'N/A';
                }
                $result[$i]['datetime'] = $query[$i]['uLogCreatedAt'];
                $result[$i]['action']   = $query[$i]['uLogDescription'];
                $result[$i]['sourceIP'] = $query[$i]['uLogSourceIP'];

            }
            return $result;
        }
        else {
            return [];
        }
    }

    private function changetitle()
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

}