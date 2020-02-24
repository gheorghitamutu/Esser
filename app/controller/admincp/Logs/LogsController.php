<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 18-Jun-18
 * Time: 09:14
 */

class LogsController extends AdmincpController
{
    private $uri;

    public function __construct($uri)
    {
        switch ($uri) {
            case 'admincp/loginlogs':
                $this->loginlogs();
                break;
            case 'admincp/loginlogs/searchuserlogs':
                $this->searchuserloginlogs();
                break;
            case 'admincp/itemlogs':
                $this->itemlogs();
                break;
            case 'admincp/loginlogs/searchitemlogs':
                $this->searchitemlogs();
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
        }
        $this->uri = $uri;
    }


    protected function itemlogs()
    {
        $this->check_rights();
        if (isset($_SESSION['searcheditemlogs'])) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'itemlogs',
                [
                    'productlogs' => $_SESSION['searcheditemlogs']
                ],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
        else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'itemlogs',
                [
                    'productlogs' => $this->getAllItemLogs()
                ],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
    }

    protected function loginlogs()
    {
        if (isset($_SESSION['userLoginLogs'])) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'loginlogs',
                ['usersloginlogs' => $_SESSION['userLoginLogs']],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
        else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'loginlogs',
                ['usersloginlogs' => $this->getAllLoginLogs()],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
    }

    protected function userlogs()
    {
        if (key_exists('logsofuser', $_SESSION)) {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'userlogs',
                ['userLogs' => $this->getUserLogs($_SESSION['logsofuser'])],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
        else {
            View::CreateView(
                'admincp' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'userlogs',
                ['userLogs' => $this->getAllUsersLogs()],
                APP_TITLE);
            unset($_SESSION['opsuccess'], $_SESSION['opmessage'], $_SESSION['userToEdit'],
                $_SESSION['userLoginLogs'], $_SESSION['logsofuser'], $_SESSION['usergrouptoedit']);
        }
    }

    protected function searchuserlogs()
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
            return;
        }

        if (empty($user)) {
            unset($_SESSION['logsofuser']);
            $this->showmessage
            (
                $opsuccess = true,
                $opmessage ='Couldn\'t find any user matching the search criteria!'
            );
            self::redirect('/admincp/userlogs');
            return;
        }
        else {
            $_SESSION['logsofuser'] = $user;
            self::redirect('/admincp/userlogs');
            return;
        }
    }


    protected function getUserLogs($user)
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

    protected function getAllUsersLogs()
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

    protected function searchuserloginlogs() {
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

    protected function getAllLoginLogs()
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

    private function searchitemlogs()
    {
        if (!isset($_POST['searchitemlogs'])) {
            $this->showmessage
            (
                $opsuccess = false,
                $opmessage = 'You need to offer a search criteria first!'
            );
            self::redirect('/admincp/loginlogs');
            return [];
        }
        else {
            switch($_POST['searchitemlogs']) {
                case filter_var($_POST['searchitemlogs'], FILTER_VALIDATE_INT):
                    $this->model('Itemlog');
                    $validatedfield = ' ILOGID = ';
                    $logs['items'] = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . filter_var($_POST['searchitemlogs'], FILTER_SANITIZE_NUMBER_INT),
                        $fields = 'ILOGID, ILOGDESCRIPTION, ILOGSOURCEIP,
                            TO_CHAR(ILOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ILOGCREATEDAT"');
                    $this->model('Itemgrouplog');
                    $validatedfield = 'IGLOGID =';
                    $logs['itemgroups'] = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . filter_var($_POST['searchitemlogs'], FILTER_SANITIZE_NUMBER_INT),
                    $fields = 'IGLOGID, IGLogDescription, IGLOGSOURCEIP,
                       TO_CHAR(IGLOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "IGLOGCREATEDAT"');
                    $result = array_merge($logs['items'], $logs['itemgroups']);
                    break;
                default:
                    $this->model('Itemlog');
                    $validatedfield = ' ILOGDESCRIPTION LIKE \'%';
                    $logs['items'] = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . filter_var($_POST['searchitemlogs'], FILTER_SANITIZE_STRING) . "'%",
                        $fields = 'ILOGID, ILOGDESCRIPTION, ILOGSOURCEIP,
                            TO_CHAR(ILOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ILOGCREATEDAT"');
                    $this->model('Itemgrouplog');
                    $validatedfield = 'IGLogDescription LIKE \'%';
                    $logs['itemgroups'] = $this->model_class->get_mapper()->findAll(
                        $where = $validatedfield . filter_var($_POST['searchitemlogs'], FILTER_SANITIZE_STRING) . "'%",
                        $fields = 'IGLOGID, IGLogDescription, IGLOGSOURCEIP,
                       TO_CHAR(IGLOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "IGLOGCREATEDAT"');
                    $result = array_merge($logs['items'], $logs['itemgroups']);
                    break;
            }
            for ($i = 0; $i < count($result); ++$i) {
                if (preg_match('/(Admin\ user\ )/',
                    (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']))) {
                    $result[$i]['userName'] = substr(
                        (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                        strlen('Admin user '),
                        strpos(
                            substr(
                                (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                                strlen('Admin user ')
                            ),
                            " "));
                }
                elseif (preg_match('/(Normal\ user\ )/', (
                    isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']))) {
                    $result[$i]['userName'] = substr(
                        (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                        strlen('Normal user '),
                        strpos(
                            substr(
                                (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                                strlen('Normal user ')
                            ),
                            " "));
                }
                elseif (preg_match('/(Manager\ user\ )/',
                    (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']))) {
                    $result[$i]['userName'] = substr(
                        (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                        strlen('Manager user '),
                        strpos(
                            substr(
                                (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                                strlen('Manager user ')
                            ),
                            " "));
                }
                else {
                    $result[$i]['userName'] = 'N/A';
                }
                $result[$i]['datetime'] = (isset($result[$i]['iLogCreatedAt'])?$result[$i]['iLogCreatedAt']:$result[$i]['iGLogCreatedAt']);
                $result[$i]['action'] = (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']);
                $result[$i]['sourceIP'] = (isset($result[$i]['iLogSourceIP'])?$result[$i]['iLogSourceIP']:$result[$i]['iGLogSourceIP']);
            }
            return $result;
        }
    }

    private function getAllItemLogs()
    {
        $this->model('Itemlog');
        $query['itemlogs'] = $this->model_class->get_mapper()->findAll($where = '',
            $fields = 'ILOGID, ILOGDESCRIPTiON, ILOGSOURCEIP,
                       TO_CHAR(ILOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ILOGCREATEDAT"');

        $this->model('Itemgrouplog');
        $query['itemgrouplogs'] = $this->model_class->get_mapper()->findAll($where = '',
            $fields = 'IGLOGID, IGLogDescription, IGLOGSOURCEIP,
                       TO_CHAR(IGLOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "IGLOGCREATEDAT"');

        $result = array_merge($query['itemlogs'],$query['itemgrouplogs']);
        for ($i = 0; $i < count($result); ++$i) {
            if (preg_match('/(Admin\ user\ )/',
                (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']))) {
                $result[$i]['userName'] = substr(
                    (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                    strlen('Admin user '),
                    strpos(
                        substr(
                            (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                            strlen('Admin user ')
                        ),
                        " "));
            }
            elseif (preg_match('/(Normal\ user\ )/', (
            isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']))) {
                $result[$i]['userName'] = substr(
                    (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                    strlen('Normal user '),
                    strpos(
                        substr(
                            (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                            strlen('Normal user ')
                        ),
                        " "));
            }
            elseif (preg_match('/(Manager\ user\ )/',
                (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']))) {
                $result[$i]['userName'] = substr(
                    (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                    strlen('Manager user '),
                    strpos(
                        substr(
                            (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']),
                            strlen('Manager user ')
                        ),
                        " "));
            }
            else {
                $result[$i]['userName'] = 'N/A';
            }
            $result[$i]['datetime'] = (isset($result[$i]['iLogCreatedAt'])?$result[$i]['iLogCreatedAt']:$result[$i]['iGLogCreatedAt']);
            $result[$i]['action'] = (isset($result[$i]['iLogDescription'])?$result[$i]['iLogDescription']:$result[$i]['IGLogDescription']);
            $result[$i]['sourceIP'] = (isset($result[$i]['iLogSourceIP'])?$result[$i]['iLogSourceIP']:$result[$i]['iGLogSourceIP']);
        }
        return $result;
    }

}