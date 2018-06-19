<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 18-Jun-18
 * Time: 09:43
 */

class UserGroupsController extends AdmincpController
{
    private $uri;

    public function __construct($uri)
    {
        switch ($uri) {
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
            case 'admincp/usergroupeditor/removefromgroup':
                $this->remusergfromgroup();
                break;
            case 'admincp/usergroupeditor/editgrouptitle':
                $this->editusergrouptitle();
                break;
            case 'admincp/usergroupeditor/editgroupdescription':
                $this->editusergroupdescription();
                break;
        }
        $this->uri = $uri;
    }

    protected function usergroupsmanager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'user_groups' . DIRECTORY_SEPARATOR . 'manager',
            [
                'grouplist' => $this->getAllUserGroups()
            ],
            APP_TITLE);
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
    }

    protected function getAllUserGroups()
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

    protected function usergroupeditor()
    {
        if (isset($_SESSION['usergrouptoedit'])) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'user_groups' . DIRECTORY_SEPARATOR . 'editor',
                [
                    'memberlist' => $_SESSION['usergrouptoedit']['memberlist'],
                    'generalgroupinfo' => $_SESSION['usergrouptoedit'],
                ],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
        else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'user_groups' . DIRECTORY_SEPARATOR . 'editor',
                [
                    'memberlist' => [],
                    'generalgroupinfo' => []
                ],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
    }

    protected function searchusergroup()
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

    protected function getusergroup()
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

    protected function editusergroup($groupid)
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

    protected function remusergfromgroup()
    {
        $userid = substr($_POST['remfromgrp'],0, strpos($_POST['remfromgrp'],'/'));
        $groupid = substr($_POST['remfromgrp'], strpos($_POST['remfromgrp'],'/') + 1);
        if (!filter_var($userid,FILTER_VALIDATE_INT) || !filter_var($groupid,FILTER_VALIDATE_INT)) {
            $this->showmessage($opsuccess = false, $opmessage = 'Illegal argument supplied!');
            self::redirect('/admincp/usergroupeditor');
            return;
        }
        $this->removefromusergroup($userid, $groupid);
    }

    protected function removefromusergroup($userid, $groupid)
    {
        $userid = filter_var($userid, FILTER_SANITIZE_NUMBER_INT);
        $groupid = filter_var($groupid, FILTER_SANITIZE_NUMBER_INT);
        switch ($groupid) {
            case 1:
                $rootadmin = true;
                $rootmng = false;
                break;
            case 2:
                $rootadmin = true;
                $rootmng = true;
                break;
            default:
                $rootadmin = false;
                $rootmng = false;
                break;
        }
        if ($rootadmin && $rootmng) {
            $this->model('Grouprelation');
            $result = $this->model_class->get_mapper()->delete
            (
                $table = 'GROUPRELATIONS',
                $where = array
                (
                    'USERID' => $userid,
                    'UGROUPID' => 1
                )
            );
            if ($result) {
                $result = $this->model_class->get_mapper()->delete
                (
                    $table = 'GROUPRELATIONS',
                    $where = array
                    (
                        'USERID' => $userid,
                        'UGROUPID' => 2
                    )
                );
            }
        }
        elseif($rootadmin) {
            $this->model('Grouprelation');
            $result = $this->model_class->get_mapper()->delete
            (
                $table = 'GROUPRELATIONS',
                $where = array
                (
                    'USERID' => $userid,
                    'UGROUPID' => 1
                )
            );
        }
        else {
            $this->model('Grouprelation');
            $result = $this->model_class->get_mapper()->delete
            (
                $table = 'GROUPRELATIONS',
                $where = array
                (
                    'USERID' => $userid,
                    'UGROUPID' => $groupid
                )
            );
        }
        $opmessage= '';
        if (!$result) {
            $this->showmessage($opsucces = false, $opmessage = 'Couldn\'t remove user from the group!');
            self::redirect('/admincp/usergroupeditor');
            return;
        }
        else {
            if ($rootadmin && $rootmng) {
                $this->model('Useracc');
                $query = $this->model_class->get_mapper()->findAll($where = 'USERID ='.$userid);
                if (empty($query)) {
                    $opmessage = 'Please update user type manually!';
                }
                else {
                    $needsupdate = (($query[0]['userType'] > 1) ? true :false);
                    if ($needsupdate) {
                        $update = $this->model_class->get_mapper()->update
                        (
                            $table = 'USERACCS',
                            $fields = array('USEERTYPE' => 1),
                            $where = array ('USERID' => $userid)
                        );
                        if($update == false) {
                            $opmessage = 'You need to update user type manually!';
                        }
                    }
                }
            }
            elseif ($rootadmin) {
                $this->model('Useracc');
                $query = $this->model_class->get_mapper()->findAll($where = 'USERID ='.$userid);
                if (empty($query)) {
                    $opmessage = 'Please update user type manually!';
                }
                else {
                    $needsupdate = (($query[0]['userType'] > 1) ? true :false);
                    if ($needsupdate) {
                        $update = $this->model_class->get_mapper()->update
                        (
                            $table = 'USERACCS',
                            $fields = array('USERTYPE' => 1),
                            $where = array ('USERID' => $userid)
                        );
                        if($update == false) {
                            $opmessage = 'You need to update user type manually!';
                        }
                    }
                }
            }
//            var_dump($userid, $groupid, $rootadmin, $rootmng, $opmessage, $needsupdate, $update);die;
            $this->showmessage($opsucces = false,
                $opmessage = 'Successfully removed user from the group! ' . $opmessage);
            self::redirect('/admincp/usergroupeditor');
            return;
        }
    }

    protected function editusergrouptitle()
    {
        if (!filter_var($_POST['editgrouptitleid'],FILTER_VALIDATE_INT) ||
            filter_var($_POST['newgrpname'], FILTER_SANITIZE_STRING) == false) {
            $this->showmessage($opsuccess = false, $opmessage = 'Illegal argument supplied!');
            self::redirect('/admincp/usergroupeditor');
            return;
        }
        else {
            $groupid = filter_var($_POST['editgrouptitleid'],FILTER_SANITIZE_NUMBER_INT);
            $newtitle = filter_var($_POST['newgrpname'], FILTER_SANITIZE_STRING);
        }
        $this->model('Usergroup');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = 'UGROUPID = ' . $groupid
        );
        if (empty($query) || !$query) {
            $this->showmessage($opsuccess = false, $opmessage = 'Couldn\'t find the group! No update possible!');
            self::redirect('/admincp/usergroupeditor');
            return;
        }
        $query = $this->model_class->get_mapper()->update
        (
            $table = 'USERGROUPS',
            $fields = array
            (
                'UGROUPNAME' => "'" . $newtitle . "'"
            ),
            $where = array
            (
                'UGROUPID' => $groupid
            )
        );
        if (is_array($query) == false && $query == false) {
            $this->showmessage($opsuccess = false, $opmessage = 'Couldn\'t update the title name!');
            self::redirect('/admincp/usergroupeditor');
            return;
        }
        else {
            $this->showmessage($opsuccess = false, $opmessage = 'Group name was successfully updated!');
            self::redirect('/admincp/usergroupeditor');
        }
    }

    protected function editusergroupdescription()
    {
        if (!filter_var($_POST['editgroupdscrpid'],FILTER_VALIDATE_INT) ||
            filter_var($_POST['newgrpdescrp'], FILTER_SANITIZE_STRING) == false) {
            var_dump($_POST); die;
            $this->showmessage($opsuccess = false, $opmessage = 'Illegal argument supplied!');
            self::redirect('/admincp/usergroupeditor');
            return;
        }
        else {
            $groupid = filter_var($_POST['editgroupdscrpid'],FILTER_SANITIZE_NUMBER_INT);
            $newdescription = filter_var($_POST['newgrpdescrp'], FILTER_SANITIZE_STRING);
        }
        $this->model('Usergroup');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = 'UGROUPID = ' . $groupid
        );
        if (empty($query) || !$query) {
            $this->showmessage($opsuccess = false, $opmessage = 'Couldn\'t find the group! No update possible!');
            self::redirect('/admincp/usergroupeditor');
            return;
        }
        $query = $this->model_class->get_mapper()->update
        (
            $table = 'USERGROUPS',
            $fields = array
            (
                'UGROUPDESCRIPTION' => "'" . $newdescription . "'"
            ),
            $where = array
            (
                'UGROUPID' => $groupid
            )
        );
        if (is_array($query) == false && $query == false) {
            $this->showmessage($opsuccess = false, $opmessage = 'Couldn\'t update the title name!');
            self::redirect('/admincp/usergroupeditor');
            return;
        }
        else {
            $this->showmessage($opsuccess = false, $opmessage = 'Group description was successfully updated!');
            self::redirect('/admincp/usergroupeditor');
        }

    }

}