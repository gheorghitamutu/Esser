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
			case 'user/normaluser':
                $this->normaluser();
                break;
			case 'user/adminuser':
                $this->adminuser();
                break;
			case 'user/manageruser':
                $this->manageruser();
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
	
	public function normaluser()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'normalUser', 
			[
				'generateGroup' => $this->normaluser()
			],
            'Normal Users Area');
    }
	
	public function adminuser()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'adminUser', 
			[
				'generateGroup' => $this->getGroupOfUser()
			],
            'Admin Users Area');
    }
	
	public function manageruser()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'managerUser', 
			[
				'generateGroup' => $this->getGroupOfUser()
			],
            'Admin Users Area');
    }
	
	private function inserUser(){
		
	}
	
	private function removeUser(){
		
	}
	
	private function getGroupOfUser(/*$group*/){
		/*$this->model('GroupRelation');
		$result = array();
		if (($useidquery = $this->model_class->get_mapper()->findAll(
				$where = ' UGROUPID = ' . $group['idGroup'],
				$fields = 'USERID'
				)) !== false) {
				$result = array_push($result, $useridquerry);
		}
		echo var_dump($result);
		exit(0);
		return $result;*/
	} 
	
	private function getItemGroups() {
		
		$groupsResult = array();
        $querryGroups = array();
        $userid = $_SESSION['userid'];
        $this->model('Grouprelation');
        $groupsOfInterest =
            $this->model_class->get_mapper()->findAll(
                $where = "USERID=" . $userid,
                $fields = false
				);

		foreach($groupsOfInterest as $grupulet){
			$this->model('Usergroup');
			$idul = $grupulet['uGroupId'];
			$querry=$this->model_class->get_mapper()->findAll( //  findById=>return 1 group
                $where=" UGROUPID= ".$idul,
                $fields = false
            );

            //array_push($querryGroups, $querry[0]['uGroupName']); // old type of function, returning only the name
            array_push($querryGroups, array
            (
                'idGroup'=>$querry[0]['uGroupId'],
                'userGroup' => $querry[0]['uGroupName']
            ));
		}

        return $querryGroups;
		
    }
	
	private function getUserGroups() {
		
		$groupsResult = array();
        $querryGroups = array();
     
        $this->model('Grouprelation');
        $groupsOfInterest =
            $this->model_class->get_mapper()->findAll(
                $where = "USERID=" . $_SESSION['userid'],
                $fields = false
				);

		foreach($groupsOfInterest as $grupulet){
			$this->model('Usergroup');
			$idul = $grupulet['uGroupId'];
			$querry=$this->model_class->get_mapper()->findAll( //  findById=>return 1 group
                $where=" UGROUPID= ".$idul,
                $fields = false
            );
			
			$this->model('Usergroup');
			$idul = $grupulet['uGroupId'];
			$querry=$this->model_class->get_mapper()->findAll( //  findById=>return 1 group
                $where=" UGROUPID= ".$idul,
                $fields = false
            );
			
            //array_push($querryGroups, $querry[0]['uGroupName']); // old type of function, returning only the name
            array_push($querryGroups, array
            (
                'idGroup'=>$querry[0]['uGroupId'],
                'userGroup' => $querry[0]['uGroupName']
            ));
		}

        return $querryGroups;
		
    }
	
	public function index()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'index',
            array('users'=>$this->getUsers()),
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
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'logs',
            [],
            'Logs area');
    }

    public function items()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . 'items',
            ['itemGroups' => $this->getItemGroups()],
            'Items area');
    }

    public function users()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'users',
			[
				'memberGroup' => $this->getUserGroups(),
				'generateGroup' => $this->getGroupOfUser()
			],
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
