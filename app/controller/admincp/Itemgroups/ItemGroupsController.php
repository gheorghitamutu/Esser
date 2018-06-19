<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 18-Jun-18
 * Time: 09:43
 */

class ItemGroupsController extends AdmincpController
{
    private $uri;

    public function __construct($uri)
    {
        switch ($uri) {
            case 'admincp/itemgroups':
                $this->itemgroupsmanager();
                break;
            case 'admincp/itemgroups/delitemgroup':
                $this->deleteitmgroup();
                break;
            case 'admincp/itemgroupeditor':
                $this->itemgroupeditor();
                break;
            case 'admincp/itemgroupeditor/searchitmgrp':
                $this->searchitmgroup();
                break;
            case 'admincp/itemgroupeditor/getitmgroup':
                $this->getitmgroup();
                break;
            case 'admincp/itemgroupeditor/editgrouptitle':
                $this->edititmgroupname();
                break;
            case 'admincp/itemgroupeditor/editgroupdescription':
                $this->edititmgroupdscrp();
                break;
        }
        $this->uri = $uri;
    }

    private function itemgroupsmanager()
    {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'item_groups' . DIRECTORY_SEPARATOR . 'manager',
            [
                'grouplist' => $this->getAllItemGroups()
            ],

            APP_TITLE);
        unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
            $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit'],
            $_SESSION['itmgrptoedit']);
    }
    private function itemgroupeditor()
    {
        if (isset($_SESSION['itmgrptoedit'])) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'item_groups' . DIRECTORY_SEPARATOR . 'editor',
                [
                    'itmgroup' => $_SESSION['itmgrptoedit']
                ],

                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit'],
                $_SESSION['itmgrptoedit']);
        }
        else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'item_groups' . DIRECTORY_SEPARATOR . 'editor',
                [
                    'itmgroup' => []
                ],

                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit'],
                $_SESSION['itmgrptoedit']);
        }

    }

    private function deleteitmgroup()
    {
        if (filter_var($_POST['delitemgroup'], FILTER_VALIDATE_INT)) {
            $itmgrpid = filter_var($_POST['delitemgroup'], FILTER_SANITIZE_NUMBER_INT);
            $this->model('Itemgroup');
            $group = $this->model_class->get_mapper()->findAll($where = 'IGROUPID = ' . $itmgrpid);
            $query = $this->model_class->get_mapper()->delete
            (
                $table = 'ITEMGROUPS',
                $where = array('IGROUPID' => $itmgrpid)
            );
            if(!is_array($query) && $query != false){
                $this->adduserlog
                (
                    $description = 'Admin user ' . $_SESSION['uname'] . " has deleted item group " .
                                $group[0]['iGroupName'],
                    $sourceip = $_SESSION['login_ip']
                );
                $this->additemgrouplog
                (
                    $description = 'Admin user ' . $_SESSION['uname'] . " has deleted item group " .
                        $group[0]['iGroupName'],
                    $sourceip = $_SESSION['login_ip']
                );
                $this->showmessage($opsuccess = true, $opmessage = 'Successfully deleted the item group!');
                self::redirect('/admincp/itemgroups');
                return;
            }
            else {
                $this->showmessage($opsuccess = true, $opmessage = 'Couldn\'t delete the item group!');
                self::redirect('/admincp/itemgroups');
                return;
            }
        }
        else {
            $this->showmessage($opsuccess = false, $opmessage = 'Illegal argument supplied!');
            self::redirect('/admincp/itemgroups');
            return;
        }
    }

    private function getAllItemGroups()
    {
        $this->model('Itemgroup');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = '',
            $fields = 'IGROUPID, IGROUPNAME, IGROUPDESCRIPTION, 
                TO_CHAR(IGROUPCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "IGROUPCREATEDAT"'
        );

        if (empty($query)) {
            return [];
        }
        else {
            for ($i = 0; $i < count($query); ++$i) {
                $this->model('Itemgroupownership');
                $query[$i]['owner'] = $this->model_class->get_mapper()->findAll
                (
                    $where = 'IGID = ' . $query[$i]['iGroupId']
                );
                if (count($query[$i]['owner']) > 2) {
                    $query[$i]['owner'] = $this->model_class->get_mapper()->findAll
                    (
                        $where = 'IGID = ' . $query[$i]['iGroupId'] . ' AND IGOWNERID > 2'
                    );
                    $this->model('Usergroup');
                    $query[$i]['owner'] = $this->model_class->get_mapper()->findAll
                    (
                        $where = 'UGROUPID = ' . $query[$i]['owner'][0]['iGOwnerId']
                    )[0]['uGroupName'];
                }
                elseif (count($query) == 2) {
                    $query[$i]['owner'] = 'Root Amdins and Managers';
                }
                $this->model('Item');
                $query[$i]['itemlist'] = $this->model_class->get_mapper()->findAll
                (
                    $where = 'IGROUPID = ' . $query[$i]['iGroupId']
                );
                $query[$i]['nrofitems'] = count($query[$i]['itemlist']);
                $query[$i]['totalproducts'] = 0;
                for ($idx = 0; $idx < count($query[$i]['itemlist']); ++$idx) {
                    $query[$i]['totalproducts'] = $query[$i]['totalproducts'] + $query[$i]['itemlist'][$idx]['itemQuantity'];
                }
            }
        }
        return $query;
    }

    private function getitmgroup()
    {
        if (!filter_var($_POST['edititemgroup'], FILTER_VALIDATE_INT)) {
            $this->showmessage($opsuccess = false, $opmessage = 'Invalid arguments passed!');
            self::redirect('/admincp/itemgroupmanager');
            return;
        }
        else {
            $itmgrpid = filter_var($_POST['edititemgroup'], FILTER_SANITIZE_NUMBER_INT);
            $this->model('Itemgroup');
            $result = $this->model_class->get_mapper()->findAll
            (
                $where = 'IGROUPID = ' . $itmgrpid
            );
            if (empty($result)) {
                $_SESSION['itmgrptoedit'] = [];
            }
            else {
                $_SESSION['itmgrptoedit'] = $result[0];
            }
        }
        self::redirect('/admincp/itemgroupeditor');
    }

    private function searchitmgroup()
    {
        if (!filter_var($_POST['searchitmgrp'], FILTER_VALIDATE_INT) &&
            !filter_var($_POST['searchitmgrp'], FILTER_SANITIZE_STRING)) {
            $this->showmessage($opsuccess = false,
                                $opmessage = 'Enter either the number ID of the group or its name!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
        else {
            if (filter_var($_POST['searchitmgrp'], FILTER_VALIDATE_INT)) {
                $itmgrpid = filter_var($_POST['searchitmgrp'], FILTER_SANITIZE_NUMBER_INT);
                $this->model('Itemgroup');
                $result = $this->model_class->get_mapper()->findAll
                (
                    $where = 'IGROUPID = ' . $itmgrpid
                );

                if (empty($result) || !$result) {
                    $this->showmessage($opsuccess = false,
                        $opmessage = 'Couldn\'t find any item groups matching the search criteria!');
                    self::redirect('/admincp/itemgroupeditor');
                    return;
                }
                else {
                    $_SESSION['itmgrptoedit'] = $result[0];
                    self::redirect('/admincp/itemgroupeditor');
                    return;
                }
            }
            else {
                $itmgrpname = filter_var($_POST['searchitmgrp'], FILTER_SANITIZE_STRING);
                $this->model('Itemgroup');
                $result = $this->model_class->get_mapper()->findAll
                (
                    $where = 'IGROUPNAME LIKE ' . "'" . $itmgrpname . "'"
                );

                if (empty($result) || !$result) {
                    $this->showmessage($opsuccess = false,
                        $opmessage = 'Couldn\'t find any item groups matching the search criteria!');
                    self::redirect('/admincp/itemgroupeditor');
                    return;
                }
                else {
                    $_SESSION['itmgrptoedit'] = $result[0];
                    self::redirect('/admincp/itemgroupeditor');
                    return;
                }
            }
        }
    }

    private function edititmgroupname()
    {
        if (!filter_var($_POST['edititmgrpnameid'], FILTER_VALIDATE_INT)) {
            $this->showmessage($opsucces = false, $opmessage = 'Illegal argument supplied!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
        if (!filter_var($_POST['newgrpname'], FILTER_SANITIZE_STRING)) {
            $this->showmessage($opsucces = false, $opmessage = 'New name contains illegal character(s)!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
        $itmgrpid = filter_var($_POST['edititmgrpnameid'], FILTER_SANITIZE_NUMBER_INT);
        $newname = filter_var($_POST['newgrpname'], FILTER_SANITIZE_STRING);
        if (strlen($newname) < 4 or strlen($newname) > 48) {
            $this->showmessage($opsucces = false, $opmessage = 'New name must be between 4 and 48 characters long!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
        $this->model('Itemgroup');
        $group = $this->model_class->get_mapper()->findAll($where = 'IGROUPID =' . $itmgrpid);
        $query = $this->model_class->get_mapper()->update
        (
            $table = 'ITEMGROUPS',
            $fields = array
            (
                'IGROUPNAME' => "'" . $newname . "'"
            ),
            $where = array
            (
                'IGROUPID' => $itmgrpid
            )
        );

        if (!is_array($query) && $query === false) {
            $this->showmessage($opsucces = false, $opmessage = 'Couldn\'t update the item group!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
        else {
            $this->adduserlog
            (
                $description = 'Admin user ' . $_SESSION['uname'] . " has updated the " . $group[0]['iGroupName'] .
                    " item group's name into " . $newname,
                $sourceip = $_SESSION['login_ip']
            );
            $this->additemgrouplog
            (
                $description = 'Admin user ' . $_SESSION['uname'] . " has updated the " . $group[0]['iGroupName'] .
                    " item group's name into " . $newname,
                $sourceip = $_SESSION['login_ip']
            );
            $this->showmessage($opsucces = false, $opmessage = 'Successfully updated item group name!!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
    }

    private function edititmgroupdscrp()
    {
        if (!filter_var($_POST['edititmgrpdscrpid'], FILTER_VALIDATE_INT)) {
            $this->showmessage($opsucces = false, $opmessage = 'Illegal argument supplied!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
        if (!filter_var($_POST['newitmgrpdescrp'], FILTER_SANITIZE_STRING)) {
            $this->showmessage($opsucces = false, $opmessage = 'New description contains illegal character(s)!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
        $itmgrpid = filter_var($_POST['edititmgrpdscrpid'], FILTER_SANITIZE_NUMBER_INT);
        $newdescription = filter_var($_POST['newitmgrpdescrp'], FILTER_SANITIZE_STRING);
        if (strlen($newdescription) < 4 or strlen($newdescription) > 2000) {
            $this->showmessage($opsucces = false, $opmessage = 'New description must be between 4 and 2000 characters long!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
        $this->model('Itemgroup');
        $group = $this->model_class->get_mapper()->findAll($where = 'IGROUPID =' . $itmgrpid);
        $query = $this->model_class->get_mapper()->update
        (
            $table = 'ITEMGROUPS',
            $fields = array
            (
                'IGROUPDESCRIPTION' => "'" . $newdescription . "'"
            ),
            $where = array
            (
                'IGROUPID' => $itmgrpid
            )
        );

        if (!is_array($query) && $query === false) {
            $this->adduserlog
            (
                $description = 'Admin user ' . $_SESSION['uname'] . " has updated the " . $group[0]['iGroupName'] .
                    " item group's description into " . $newdescription,
                $sourceip = $_SESSION['login_ip']
            );
            $this->additemgrouplog
            (
                $description = 'Admin user ' . $_SESSION['uname'] . " has updated the " . $group[0]['iGroupName'] .
                    " item group's description into " . $newdescription,
                $sourceip = $_SESSION['login_ip']
            );
            $this->showmessage($opsucces = false, $opmessage = 'Couldn\'t update the item group!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
        else {
            $this->showmessage($opsucces = false, $opmessage = 'Successfully updated item group description!!');
            self::redirect('/admincp/itemgroupeditor');
            return;
        }
    }
}