<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 18-Jun-18
 * Time: 09:43
 */

class ItemsController extends AdmincpController
{
    private $uri;

    public function __construct($uri)
    {
        switch ($uri) {
            case 'admincp/itemeditor':
                $this->itemeditor();
                break;
            case 'admincp/itemmanager':
                $this->itemmanager();
                break;
            case 'admincp/itemmanager/renderitmgroup':
                $this->renderitmgroup();
                break;
            case 'admincp/itemmanager/deleteitem':
                $this->deleteitem();
                break;
            case 'admincp/itemeditor/getitemid':
                $this->getitemid();
                break;
            case 'admincp/itemeditor/searchitem':
                $this->searchitem();
                break;
            case 'admincp/itemeditor/edititemname':
                $this->edititemname();
                break;
            case 'admincp/itemeditor/edititemdescription':
                $this->edititemdescription();
                break;
            case 'admincp/itemeditor/edititemquantity':
                $this->edititemquantity();
                break;
            case 'admincp/itemeditor/edititemwarning':
                $this->edititemwarning();
                break;
        }
        $this->uri = $uri;
    }

    protected function itemeditor()
    {
        if (isset($_SESSION['itemtoedit'])) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'editor',
                [
                    'itemtoedit' =>  $_SESSION['itemtoedit']
                ],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit'],
                $_SESSION['itmgrptoedit'], $_SESSION['renderitemlist'], $_SESSION['itemtoedit']);
        }
        else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'editor',
                [
                    'itemtoedit' => []
                ],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit'],
                $_SESSION['itmgrptoedit'], $_SESSION['renderitemlist'], $_SESSION['itemtoedit']);
        }
    }

    protected function itemmanager()
    {
        if (isset($_SESSION['renderitemlist']) && isset($_SESSION['groupidtorender'])) {
        View::CreateView(
            'admincp' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'manager',
            [
                'itmgroups' => $this->getItemGroups(),
                'groupitemlist' => $_SESSION['renderitemlist'],
                'grouptorender' => $_SESSION['groupidtorender']
            ],
            APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit'],
                $_SESSION['itmgrptoedit']);
        }
        else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'manager',
                [
                    'itmgroups' => $this->getItemGroups(),
                    'groupitemlist' => [],
                    'grouptorender' => false
                ],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit'],
                $_SESSION['itmgrptoedit']);
        }
    }

    protected function getItemGroups()
    {
        $this->model('Itemgroup');
        $querry = $this->model_class->get_mapper()->findAll
        (
            $where = '',
            $fields = 'IGROUPID, IGROUPNAME'
        );

        if (!$querry or empty($querry)) {
            return [];
        }
        return $querry;
    }

    protected function renderitmgroup()
    {
        if (!filter_var($_POST['groupitmid'], FILTER_VALIDATE_INT)) {
            $this->showmessage($opsuccess = false, $opmessage = 'Illegal Argument passed!');
            self::redirect('/admincp/itemmanager');
            return;
        }
        $groupid = filter_var($_POST['groupitmid'], FILTER_SANITIZE_NUMBER_INT);
        if (isset($_SESSION['renderitemlist']) && ($_SESSION['renderitemlist'][0]['iGroupId'] == $groupid)){
            unset($_SESSION['renderitemlist'], $_SESSION['groupidtorender']);
            self::redirect('/admincp/itemmanager');
            return;
        }
        else {
            $this->model('Item');
            $query = $this->model_class->get_mapper()->findAll
            (
                $where = 'IGROUPID =' . $groupid,
                $fields = ' ITEMID, ITEMNAME, ITEMDESCRIPTION, ITEMQUANTITY, IGROUPID, IWARNQNTY, 
                        TO_CHAR(ITEMCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ITEMCREATEDAT"'
            );

            if (!$query || empty($query)) {
                $_SESSION['renderitemlist'] = [];
                self::redirect('/admincp/itemmanager');
                return;
            }
            $_SESSION['renderitemlist'] = $query;
            $_SESSION['groupidtorender'] = $groupid;
            self::redirect('/admincp/itemmanager');
            return;
        }
    }

    protected function deleteitem()
    {
        if (!filter_var($_POST['delitemid'], FILTER_VALIDATE_INT)) {
            $this->showmessage($opsuccess = false, $opmessage = 'Illegal Argument passed!');
            self::redirect('/admincp/itemmanager');
            return;
        }
        $itemid = filter_var($_POST['delitemid'], FILTER_SANITIZE_NUMBER_INT);

        $query[0] = $this->getitemtoeditbyid($itemid);

        if (!$query[0] or empty($query[0])) {
            self::redirect('/admincp/itemmanager');
            return;
        }
        else {
            $query[1] = $this->model_class->get_mapper()->delete
            (
                $table = 'ITEMS',
                $where = array
                (
                    'ITEMID' => $itemid
                )
            );

            if (!$query[1]) {
                $this->showmessage($opsuccess = false, $opmessage = 'Couldn\'t delete the item!');
                self::redirect('/admincp/itemmanager');
                return;
            }
            else {
                $this->adduserlog
                (
                    $description = 'Admin user ' . $_SESSION['uname'] . ' has deleted item ' .
                        $query[0][0]['itemName'],
                    $sourceip = $_SESSION['login_ip']
                );
                $this->additemlog
                (
                    $description = 'Admin user ' . $_SESSION['uname'] . ' has deleted item ' .
                        $query[0][0]['itemName'],
                    $sourceip = $_SESSION['login_ip']
                );
                $this->showmessage($opsuccess = true, $opmessage = 'Successfully deleted the item!');
                unset($_SESSION['renderitemlist'], $_SESSION['groupidtorender']);
                self::redirect('/admincp/itemmanager');
                return;
            }
        }
    }

    private function getitemid()
    {
        if (!filter_var($_POST['edititemid'], FILTER_VALIDATE_INT)) {
            $this->showmessage($opsuccess = false, $opmessage = 'Illegal Argument passed!');
            self::redirect('/admincp/itemmanager');
            return;
        }

        $itemid = filter_var($_POST['edititemid'], FILTER_SANITIZE_NUMBER_INT);

        $this->model('Item');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = 'ITEMID = ' . $itemid
        );

        if (!$query || empty($query)) {
            $_SESSION['itemtoedit'] = [];
            self::redirect('/admincp/itemeditor');
            return;
        }
        else {
            $_SESSION['itemtoedit'] = $query;
            self::redirect('/admincp/itemeditor');
            return;
        }
    }

    private function searchitem()
    {
        if (!filter_var($_POST['itemtosearch'], FILTER_VALIDATE_INT) &&
            filter_var($_POST['itemtosearch'], FILTER_SANITIZE_STRING) == false){
            $this->showmessage('false', 'Illegal Argument Passed!');
            self::redirect('/admincp/itemeditor');
            return;
        }
        if (filter_var($_POST['itemtosearch'], FILTER_VALIDATE_INT)) {
            $itemid = filter_var($_POST['itemtosearch'], FILTER_SANITIZE_NUMBER_INT);
            $_SESSION['itemtoedit'] = $this->getitemtoeditbyid($itemid);
            self::redirect('/admincp/itemeditor');
            return;
        }
        else {
            $itemname = filter_var($_POST['itemtosearch'], FILTER_SANITIZE_STRING);
            $_SESSION['itemtoedit'] = $this->getitemtoeditbyname($itemname);
            self::redirect('/admincp/itemeditor');
            return;
        }
    }

    private function getitemtoeditbyid($itemid)
    {
        $this->model('Item');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = 'ITEMID = ' . $itemid
        );
        if (!$query || empty($query)) {
            $this->showmessage($opsuccess = false, $opmessage = 'This item doesn\'t exist anymore!');
            return [];
        }
        else {
            return $query;
        }
    }

    private function getitemtoeditbyname($itemname)
    {
        $this->model('Item');
        $query = $this->model_class->get_mapper()->findAll
        (
            $where = 'ITEMNAME LIKE ' . "'" . $itemname . "'"
        );
        if (!$query || empty($query)) {
            $this->showmessage($opsuccess = false, $opmessage = 'This item doesn\'t exist!');
            return [];
        }
        else {
            return $query;
        }
    }

    private function edititemname()
    {
        if (filter_var($_POST['edititemnameid'], FILTER_VALIDATE_INT)
            && filter_var($_POST['newitemname'], FILTER_SANITIZE_STRING)) {
            $itemid = filter_var($_POST['edititemnameid'], FILTER_SANITIZE_NUMBER_INT);
            $newname = filter_var($_POST['newitemname'], FILTER_SANITIZE_STRING);

            $item = $this->getitemtoeditbyid($itemid);
            if (!$item || empty($item)) {
                self::redirect('/admincp/itemeditor');
                return;
            }

            $this->model('Item');
            $result = $this->model_class->get_mapper()->update
            (
                $table = 'ITEMS',
                $fields = array ('ITEMNAME' => "'" . $newname . "'"),
                $where = array ('ITEMID' => $itemid)
            );
//            var_dump($result); die;
            if (!$result && !is_array($result)) {
                $this->showmessage(false, 'Couldn\'t update the item!');
                self::redirect('/admincp/itemeditor');
                return;
            }
            else {
                $this->additemlog
                (
                    $description = "'Admin user " . $_SESSION['uname'] . ' has updated item ' .
                        $item[0]['itemName'] . "!'",
                    $sourceip = $_SESSION['login_ip']
                );
                $this->adduserlog
                (
                    $description = "'Admin user " . $_SESSION['uname'] . ' has updated item ' .
                        $item[0]['itemName'] . "!'",
                    $sourceip = $_SESSION['login_ip']
                );
                $this->showmessage(false, 'Successfully updated the item!');
                self::redirect('/admincp/itemeditor');
                return;
            }
        }
        else {
            $this->showmessage(false, 'Illegal Argument Passed!');
            self::redirect('/admincp/itemeditor');
            return;
        }
    }

    private function edititemdescription()
    {
        if (filter_var($_POST['edititemdescriptionid'], FILTER_VALIDATE_INT)
            && filter_var($_POST['newitemdescription'], FILTER_SANITIZE_STRING)) {
            $itemid = filter_var($_POST['edititemdescriptionid'], FILTER_SANITIZE_NUMBER_INT);
            $newdescription = filter_var($_POST['newitemdescription'], FILTER_SANITIZE_STRING);

            $item = $this->getitemtoeditbyid($itemid);
            if (!$item || empty($item)) {
                self::redirect('/admincp/itemeditor');
                return;
            }

            $this->model('Item');
            $result = $this->model_class->get_mapper()->update
            (
                $table = 'ITEMS',
                $fields = array ('ITEMDESCRIPTION' => "'" . $newdescription . "'"),
                $where = array ('ITEMID' => $itemid)
            );
            if (!$result && !is_array($result)) {
                $this->showmessage(false, 'Couldn\'t update the item!');
                self::redirect('/admincp/itemeditor');
                return;
            }
            else {
                $this->additemlog
                (
                    $description = "'Admin user " . $_SESSION['uname'] . ' has updated item ' .
                        $item[0]['itemName'] . "!'",
                    $sourceip = $_SESSION['login_ip']
                );
                $this->adduserlog
                (
                    $description = "'Admin user " . $_SESSION['uname'] . ' has updated item ' .
                        $item[0]['itemName'] . "!'",
                    $sourceip = $_SESSION['login_ip']
                );
                $this->showmessage(false, 'Successfully updated the item!');
                self::redirect('/admincp/itemeditor');
                return;
            }
        }
        else {
            $this->showmessage(false, 'Illegal Argument Passed!');
            self::redirect('/admincp/itemeditor');
            return;
        }
    }

    private function edititemquantity()
    {
        if (filter_var($_POST['edititemqntyid'], FILTER_VALIDATE_INT)
            && filter_var($_POST['newitemqnty'], FILTER_VALIDATE_INT)
            && $_POST['newitemqnty'] >= 0) {
            $itemid = filter_var($_POST['edititemqntyid'], FILTER_SANITIZE_NUMBER_INT);
            $newqnty = filter_var($_POST['newitemqnty'], FILTER_VALIDATE_INT);

            $item = $this->getitemtoeditbyid($itemid);
            if (!$item || empty($item)) {
                self::redirect('/admincp/itemeditor');
                return;
            }

            $this->model('Item');
            $result = $this->model_class->get_mapper()->update
            (
                $table = 'ITEMS',
                $fields = array ('ITEMQUANTITY' => $newqnty),
                $where = array ('ITEMID' => $itemid)
            );
            if (!$result && !is_array($result)) {
                $this->showmessage(false, 'Couldn\'t update the item!');
                self::redirect('/admincp/itemeditor');
                return;
            }
            else {
                $this->additemlog
                (
                    $description = "'Admin user " . $_SESSION['uname'] . ' has updated item ' .
                        $item[0]['itemName'] . "!'",
                    $sourceip = $_SESSION['login_ip']
                );
                $this->adduserlog
                (
                    $description = "'Admin user " . $_SESSION['uname'] . ' has updated item ' .
                        $item[0]['itemName'] . "!'",
                    $sourceip = $_SESSION['login_ip']
                );
                $this->showmessage(false, 'Successfully updated the item!');
                self::redirect('/admincp/itemeditor');
                return;
            }
        }
        else {
            $this->showmessage(false, 'Illegal Argument Passed!');
            self::redirect('/admincp/itemeditor');
            return;
        }
    }

    private function edititemwarning()
    {
        if (filter_var($_POST['edititemwarnid'], FILTER_VALIDATE_INT)) {
            $itemid = filter_var($_POST['edititemwarnid'], FILTER_SANITIZE_NUMBER_INT);
            if ((filter_var($_POST['newitemwarn'], FILTER_SANITIZE_STRING) == "")
                || (strtolower (filter_var($_POST['newitemwarn'], FILTER_SANITIZE_STRING)) === strtolower("false"))) {
                $newwarn = '\'NULL\'';
            }
            elseif (filter_var($_POST['newitemwarn'], FILTER_VALIDATE_INT)
                && filter_var($_POST['newitemwarn'], FILTER_VALIDATE_INT) >=0 ) {
                $newwarn = filter_var($_POST['newitemwarn'], FILTER_SANITIZE_NUMBER_INT);
            }
            else {
                $this->showmessage(false, 'Illegal Argument Passed!');
                self::redirect('/admincp/itemeditor');
                return;
            }

            $item = $this->getitemtoeditbyid($itemid);
            if (!$item || empty($item)) {
                self::redirect('/admincp/itemeditor');
                return;
            }

            $this->model('Item');
            $result = $this->model_class->get_mapper()->update
            (
                $table = 'ITEMS',
                $fields = array ('IWARNQNTY' => $newwarn),
                $where = array ('ITEMID' => $itemid)
            );
            if (!$result && !is_array($result)) {
                $this->showmessage(false, 'Couldn\'t update the item!');
                self::redirect('/admincp/itemeditor');
                return;
            }
            else {
                $this->additemlog
                (
                    $description = "'Admin user " . $_SESSION['uname'] . ' has updated item ' .
                        $item[0]['itemName'] . "!'",
                    $sourceip = $_SESSION['login_ip']
                );
                $this->adduserlog
                (
                    $description = "'Admin user " . $_SESSION['uname'] . ' has updated item ' .
                        $item[0]['itemName'] . "!'",
                    $sourceip = $_SESSION['login_ip']
                );
                $this->showmessage(false, 'Successfully updated the item!');
                self::redirect('/admincp/itemeditor');
                return;
            }
        }
        else {
            $this->showmessage(false, 'Illegal Argument Passed!');
            self::redirect('/admincp/itemeditor');
            return;
        }
    }
}