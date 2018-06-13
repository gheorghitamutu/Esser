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
                new PageNotFoundController();
                break;

        }
    }

    public function index()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'index',
            [],
            'Welcome ' . $_SESSION["uname"]);
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
        session_destroy();

        $this->model('UserLog');
        $this->model_class->get_mapper()->insert(
            'USERLOGS',
            array
            (
                'uLogDescription'   => "'Normal user " . $_SESSION['uname']     . " has logged out!'",
                'uLogSourceIP'      => "'" . $_SESSION['login_ip']              . "'"
            )
        );
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
}
