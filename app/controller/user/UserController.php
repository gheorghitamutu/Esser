<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: UserController.phpheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/6/2018
 * Time: 1:07 PM
 */


class UserController extends Controller
{
    public function __construct($uri)
    {
        if(!$this->session_authenticate())
        {
            new ForbiddenController();
            return;
        }
        else
        {
            $this->check_admin();
        }

        switch($uri)
        {
            case 'user':
                $this->index();
                break;
            case 'user/index':
                self::redirect('/user');
                break;
            case 'user/notifications':
                $this->set_notifications_read();
                $this->notifications();
                break;
            case 'user/logs':
                $this->logs();
                break;
            case 'user/items':
                $this->items();
                break;
            case 'user/users':
                $this->users();
                break;
            case 'user/admincp':
                $this->logout();
                self::redirect('/admincp');
                break;
            case 'user/logout':
                $this->logout();
                break;
            default:
                $this->index();
                break;

        }
    }

    public function index()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'index',
            array
            (
                'users'=>$this->getUsers(),
                'notifications_count' => $this->get_notifications()

            ),
            'Welcome ' . $_SESSION["uname"]);
    }

    private function getUsers()
    {
        $users=array();//result (userName,userEmail,userGroup)

        $this->model('GroupRelation');
        $query_group_relations = $this->model_class->get_mapper()->findAll(
            $where=" USERID= ". $_SESSION['userid'],
            $fields=false
        );

        foreach($query_group_relations as $relation){ // foreach group that current user is in relation with,
            // find all users(name,email) and their group(groupName)

            $this->model('Usergroup');
            $querry_groups=$this->model_class->get_mapper()->findAll( //  findById=>return 1 group
                $where=" UGROUPID= ".$relation['uGroupId'],
                $fields=false
            );

            $this->model('Useracc');// findById => return 1 user
            $querry_users=$this->model_class->get_mapper()->findAll(
                $where=" USERID= ".$relation['userId'],
                $fields=false
            );

            // creating result
            foreach($querry_users as $user ){
                array_push($users, array
                (
                    'userName'=>$user['userName'],
                    'userGroup' => $querry_groups['0']['uGroupName'],
                    'userEmail'=>$user['userEmail']
                ));

            }
        }

        return $users;
    }

    public function notifications()
    {
        // maybe macros for cats?
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'notifications' . DIRECTORY_SEPARATOR . 'notifications',
            [],
            'Notifications!');
    }

    public function logs()
    {
        $this->model('ItemGrouplog');

        $item_group_logs = $this->model_class->get_mapper()->findAll(
            $fields = false);

        $this->model('Itemlog');

        $item_logs = $this->model_class->get_mapper()->findAll(
            $fields = false);

        $this->model('UserGroupLog');

        $user_group_logs = $this->model_class->get_mapper()->findAll(
            $fields = false);

        $logs = array_merge($item_group_logs, $item_logs, $user_group_logs);

        usort($logs, function ($a, $b)
        {
            if ($a['uLogCreatedAt'] == $b['uLogCreatedAt'])
            {
                return 0;
            }

            return ($a['uLogCreatedAt'] < $b['uLogCreatedAt']) ? -1 : 1;
        });


        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'logs',
            ["logs" => $logs],
            'Logs area');
    }

    public function items()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'items',
            [],
            'Items area');
    }

    public function users()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'users',
            [],
            'Users area');
    }

    public function logout()
    {
        $this->model('Useracc');
        $this->model_class->get_mapper()->update(
            'USERACCS',
            array
            (
                'userState' => 1
            ),
            array
            (
                'userId' => $_SESSION['userid']
            )
        );

        $_SESSION['login_failed'] = true;

        $this->model('UserLog');
        $this->model_class->get_mapper()->insert(
            'USERLOGS',
            array
            (
                'uLogDescription'   => "'Normal user " . $_SESSION['uname']     . " has logged out!'",
                'uLogSourceIP'      => "'" . $_SESSION['login_ip']              . "'"
            )
        );

        session_destroy();
        Controller::redirect('/home');
    }

    private function check_admin()
    {
        $this->model('Useracc');

        $user_id = $_SESSION['userid'];
        $queries = $this->model_class->get_mapper()->findAll(
            $where = "userId = ". $user_id ." AND userType = 3",
            $fields = false);

        if (count($queries) === 0 || count($queries) === null)
        {
            $_SESSION["is_admin"] = false;
        }
        else
        {
            $_SESSION["is_admin"] = true;
        }
    }

    private function get_notifications()
    {
        $this->model('Usrntfrelation');

        $user_id = $_SESSION['userid'];
        $usrntfrelation = $this->model_class->get_mapper()->findAll(
            $where = "usrNNotifiedAccId = ". $user_id ." AND usrnNIsRead = 0",
            $fields = false);

        return count($usrntfrelation);
    }

    private function set_notifications_read()
    {
        $this->model('Usrntfrelation');

        $user_id = $_SESSION['userid'];
        $usrntfrelation = $this->model_class->get_mapper()->findAll(
            $where = "usrNNotifiedAccId = ". $user_id ." AND usrnNIsRead = 0",
            $fields = false);

        if(count($usrntfrelation) == 0)
        {
            return;
        }

        foreach ($usrntfrelation as $relation)
        {
            $this->model_class->get_mapper()->update(
                'USRNTFRELATIONS',
                array
                (
                    'usrNRelationId' => $relation["usrNRelationId"]
                ),
                array
                (
                    'usrnNIsRead' => 1
                ));
        }
    }
}
