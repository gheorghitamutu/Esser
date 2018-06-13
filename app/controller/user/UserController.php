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
                $this->index();
                break;

        }
    }

    public function index()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'index',
            array('usersContainer'=>$this->getUsers(),'productsContainer' =>$this->getProducts()),
            'Welcome ' . $_SESSION["uname"]);
    }

    private function getUsers()
    {
        $users=array();

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
        //result users[(userName,userEmail,userGroup)]
        //
        return [
            'users'=>$users,
            'countUsers' => count($users),
            'countGroups' => count($query_group_relations)
        ];
    }

    public function getProducts(){

        $avg_quantity=0;
        $count_items_groups=0;
        $items=array();
        // fetch all groups  that use is part of
        $this->model('GroupRelation');
        $query_group_relations = $this->model_class->get_mapper()->findAll(
            $where=" USERID= ". $_SESSION['userid'],
            $fields=false
        );

        foreach($query_group_relations as $relation){
        // for every group that user if part of, search in itemGroupOwnership the items
            $this->model('Itemgroupownership');
            $querry_ownership_items=$this->model_class->get_mapper()->findAll(
                $where=" iGOwnerId= ".$relation['uGroupId'],
                $fields=false
            );
            //for every itemGroupOwnership  search for  group item
            foreach($querry_ownership_items as $querry_ownership_item){

                $this->model('Itemgroup');
                $querry_group_item=$this->model_class->get_mapper()->findAll(
                    $where=" IGROUPID= ".$querry_ownership_item['iGId'],
                    $fields=false
                );
                //for every group item search for items
                foreach($querry_group_item as $item_group ){

                    $this->model('Item');
                    $querry_items=$this->model_class->get_mapper()->findAll(
                        $where=" iGroupId= ".$item_group['iGroupId'],
                        $fields=false
                    );
                    ++$count_items_groups;
                    //for every items create result
                    foreach($querry_items as $item) {
                        array_push($items, array
                        (
                            'itemName' => $item['itemName'],
                            'itemQuantity' => $item['itemQuantity'],
                            'itemGroup' => $item_group['iGroupName']
                        ));
                    }
                }
            }
        }

        return [
            'items'=>$items,
            'countItems' => count($items),
            'avgQuantity' => ( (count($items)? $avg_quantity/count($items): 0)),
            'countItemsGroups' =>$count_items_groups
        ];

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
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'logs',
            [],
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
