<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 18-Jun-18
 * Time: 09:44
 */

class UsersController extends AdmincpController
{
    private $uri;

    public function __construct($uri)
    {
        Parent::__construct($uri);

        switch ($uri) {
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
        }
        $this->uri = $uri;
    }

    protected function usereditor()
    {
        if (key_exists('userToEdit', $_SESSION)) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'editor',
                ['userToEdit' => $_SESSION['userToEdit']],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        } else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'users_manager' . DIRECTORY_SEPARATOR . 'editor',
                [],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
    }

    protected function searchuser()
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
            return;
        }
        if (empty($user)) {
            unset($_SESSION['userToEdit']);
            $this->showmessage
            (
                $opsuccess = true,
                $opmessage ='Couldn\'t find any user matching the search criteria!'
            );
            self::redirect('/admincp/usereditor');
            return;
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
            return;
        }
    }

    protected function edituser()
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

    protected function goToUserEditor()
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
        return;
    }

    protected function usermanager()
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

            APP_TITLE);
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
    }

    protected function extractActiveUsers($userlist)
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

    protected function extractSuspendedUsers($userlist)
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

    protected function getActivatedUserList()
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

    protected function getUnapprovedUserList()
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

    protected function deleteuser()
    {
        $this->model('Useracc');
        $user = $this->model_class->get_mapper()->findAll(
            $where = 'USERID = ' . $_POST['deleteuser']
        );
        if ($_SESSION['userid'] === $user[0]['userId']) {
            $this->showmessage(false,
                'Cannot delete your own account!',
                '/admincp/usermanager');
        }
        else {
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

    protected function approveuser()
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

    protected function suspenduser()
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

    protected function unsuspenduser()
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

}