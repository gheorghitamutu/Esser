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
        if (!$this->session_authenticate()) {
            new ForbiddenController();
            return;
        } else {
            $this->check_admin();
        }

        switch ($uri) {
            case 'user':
                $this->index();
                break;
            case 'user/index':
                $this->index();
//                self::redirect('/user');
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
            case 'user/users/groupmembers':
                $this->renderGroup();
                break;
            case 'user/reports':
                $this->reports();
                break;
            case 'user/reports/download':
                $this->download_report();
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

    private function renderGroup()
    {
        $groupId = $_POST['renderGroup'];
        $this->model('Grouprelation');
        $canManageMembers =
            $this->model_class->get_mapper()->findAll(
                $where = "USERID=" . $_SESSION['userid'] .
                    " AND UGROUPID = " . $groupId,
                $fields = false
            )[0]['canMngMbs'];

        if ((isset($_SESSION['renderedGroupId']) || isset($_SESSION['listOfGroupUserIds'])) && ($groupId == $_SESSION['renderedGroupId'])) {
            unset($_SESSION['renderedGroupId']);
            unset($_SESSION['listOfGroupUserIds']);
            unset($_SESSION['canManageMembers']);
            self::redirect('/user/users');
        } else {
            $_SESSION['renderedGroupId'] = $groupId;
            $_SESSION['listOfGroupUserIds'] = $this->getMemberOfGroup($groupId);
            $_SESSION['canManageMembers'] = $canManageMembers;
            self::redirect('/user/users');
        }


    }


    private function getUserGroups()
    {

        $groupsResult = array();
        $querryGroups = array();

        $this->model('Grouprelation');
        $groupsOfInterest =
            $this->model_class->get_mapper()->findAll(
                $where = "USERID=" . $_SESSION['userid'],
                $fields = false
            );

        foreach ($groupsOfInterest as $grupulet) {
            $this->model('Usergroup');
            $idul = $grupulet['uGroupId'];
            $querry = $this->model_class->get_mapper()->findAll( //  findById=>return 1 group
                $where = " UGROUPID= " . $idul,
                $fields = false
            );

            $this->model('Usergroup');
            $idul = $grupulet['uGroupId'];
            $querry = $this->model_class->get_mapper()->findAll( //  findById=>return 1 group
                $where = " UGROUPID= " . $idul,
                $fields = false
            );

            //array_push($querryGroups, $querry[0]['uGroupName']); // old type of function, returning only the name
            array_push($querryGroups, array
            (
                'idGroup' => $querry[0]['uGroupId'],
                'userGroup' => $querry[0]['uGroupName']
            ));
        }

        return $querryGroups;

    }

    private function getMemberOfGroup($group)
    {
        $this->model('GroupRelation');
        $result = $this->model_class->get_mapper()->findAll(
            $where = "UGROUPID = " . $group,
            $fields = 'USERID',
            $order = 'GRPRELCREATEDAT ASC'
        );

        for ($i = 0; $i < count($result); ++$i)
            $result[$i] = $result[$i]['userId'];

        $this->model('Useracc');
        for ($i = 0; $i < count($result); ++$i){
            $result[$i]= $this->model_class->get_mapper()->findAll(
                $where = "USERID = " . $result[$i]
            );
            $result[$i] = $result[$i][0]['userName'];
        }
        return $result;

    }

    private function getMembersOfGroups($groups)
    {
        $this->model('GroupRelation');
        $idGroups = [];
        $result = [];

        for ($i = 0; $i < count($groups); ++$i) {
            $idGroups[$i] = $groups[$i]['idGroup'];
        }
        for ($i = 0; $i < count($idGroups); ++$i) {
            $result[$i] = $this->model_class->get_mapper()->findAll(
                $where = "UGROUPID = " . $idGroups[$i],
                $fields = 'USERID',
                $order = 'GRPRELCREATEDAT ASC'
            );
        }

        for ($i = 0; $i < count($result); ++$i)
            for ($j = 0; $j < count($result[$i]); ++$j) {
                $result[$i][$j] = $result[$i][$j]['userId'];
            }
        return $result;

    }

    public function index()
    {
        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'index',
            array
            (
                'usersContainer' => $this->getUsers(),
                'productsContainer' => $this->getProducts(),
                'notifications_count' => $this->get_notifications_count()
            ),
            'Welcome ' . $_SESSION["uname"]);
    }

    private function getUsers()
    {
        $users = array();

        $this->model('GroupRelation');
        $query_group_relations = $this->model_class->get_mapper()->findAll(
            $where = " USERID= " . $_SESSION['userid'],
            $fields = false
        );

        foreach ($query_group_relations as $relation) { // foreach group in relation find relation that user are involved in.

            $this->model('Usergroup');
            $querry_groups = $this->model_class->get_mapper()->findAll( //  findById=>return 1 group
                $where = " UGROUPID= " . $relation['uGroupId'],
                $fields = false
            );

            $this->model('GroupRelation');// findById => return 1 user
            $querry_rel_users = $this->model_class->get_mapper()->findAll(
                $where = "UGROUPID= " . $relation['uGroupId'],
                $fields = false
            );

            foreach ($querry_rel_users as $querry_rel_user) {
                $this->model('Useracc');// findById => return 1 user
                $querry_users = $this->model_class->get_mapper()->findAll(
                    $where = " USERID= " . $querry_rel_user['userId'],
                    $fields = false
                );

                // creating result
                foreach ($querry_users as $user) {
                    array_push($users, array
                    (
                        'userName' => $user['userName'],
                        'userGroup' => $querry_groups['0']['uGroupName'],
                        'userEmail' => $user['userEmail']
                    ));
                }
            }
        }
        //result users[(userName,userEmail,userGroup)]
        //
        return [
            'users' => $users,
            'countUsers' => count($users),
            'countGroups' => count($query_group_relations)
        ];
    }

    public function getProducts()
    {

        $avg_quantity = 0;
        $array_ownership_items_count_groups=array();
        $items = array();
        // fetch all groups  that use is part of
        $this->model('GroupRelation');
        $query_group_relations = $this->model_class->get_mapper()->findAll(
            $where = " USERID= " . $_SESSION['userid'],
            $fields = false
        );

        foreach ($query_group_relations as $relation) {

            // for every group that user if part of, search in itemGroupOwnership the items
            $this->model('Itemgroupownership');
            $querry_ownership_items = $this->model_class->get_mapper()->findAll(
                $where = " iGOwnerId= " . $relation['uGroupId'],
                $fields = false
            );
            //for every itemGroupOwnership  search for  group item
            foreach ($querry_ownership_items as $querry_ownership_item) {
                array_push($array_ownership_items_count_groups,$querry_ownership_item['iGId']);
                $this->model('Itemgroup');
                $querry_group_item = $this->model_class->get_mapper()->findAll(
                    $where = " IGROUPID= " . $querry_ownership_item['iGId'],
                    $fields = false
                );
                //for every group item search for items
                foreach ($querry_group_item as $item_group) {

                    $this->model('Item');
                    $querry_items = $this->model_class->get_mapper()->findAll(
                        $where = " iGroupId= " . $item_group['iGroupId'],
                        $fields = false
                    );

                    //for every items create result
                    foreach ($querry_items as $item) {
                        $avg_quantity = $avg_quantity + $item['itemQuantity'];
                        array_push($items, array
                        (
                            'itemName' => $item['itemName'],
                            'itemId' => $item['itemId'],
                            'itemQuantity' => $item['itemQuantity'],
                            'itemGroup' => $item_group['iGroupName'],
                        ));
                    }
                }
            }
        }
        $unique_items = array();
        foreach ($items as $item) {
            if (!in_array($item, $unique_items)) {
                array_push($unique_items, $item);
            }
        }

        $unique_groups_items=array();
        foreach($array_ownership_items_count_groups as $array_ownership_items_count_group){
            if(!in_array($array_ownership_items_count_group,$unique_groups_items)){
                array_push($unique_groups_items,$array_ownership_items_count_group);
            }
        }
        return [
            'items' => $unique_items,
            'countItems' => count($items),
            'avgQuantity' => ((count($items) ? ($avg_quantity / count($items)) : 0)),
            'countItemsGroups' => count($unique_groups_items)
        ];

    }

    public function notifications()
    {
        // get the notifications

        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'notifications' . DIRECTORY_SEPARATOR . 'notifications',
            array
            (
                'notifications' => $this->get_notifications()

            ),
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

        usort($logs, function ($a, $b) {
            if ($a['uLogCreatedAt'] == $b['uLogCreatedAt']) {
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
            ['itemGroups' => $this->getItemGroups()],
            'Items area');
    }

    public function users()
    {
        if (isset($_SESSION['renderedGroupId'])) {
            View::CreateView(
                'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'users',
                [
                    'memberGroup' => $this->getUserGroups(),
                    'usersToDisplay' => $_SESSION['listOfGroupUserIds'],
                    'canManageMembers' => $_SESSION['canManageMembers']
                ],
                'Users area');

        } else {
            View::CreateView(
                'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'users',
                [
                    'memberGroup' => $this->getUserGroups(),
                    'usersToDisplay' => [],
                    'canManageMembers' => 0
                ],
                'Users area');
        }
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
                'uLogDescription' => "'Normal user " . $_SESSION['uname'] . " has logged out!'",
                'uLogSourceIP' => "'" . $_SESSION['login_ip'] . "'"
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
            $where = "userId = " . $user_id . " AND userType = 3",
            $fields = false);

        if (count($queries) === 0 || count($queries) === null) {
            $_SESSION["is_admin"] = false;
        } else {
            $_SESSION["is_admin"] = true;
        }
    }

    private function get_notifications_count()
    {
        $this->model('Usrntfrelation');

        $user_id = $_SESSION['userid'];
        $usrntfrelation = $this->model_class->get_mapper()->findAll(
            $where = "usrNNotifiedAccId = " . $user_id . " AND usrnNIsRead = 0",
            $fields = false);

        return count($usrntfrelation);
    }

    private function set_notifications_read()
    {
        $this->model('Usrntfrelation');

        $user_id = $_SESSION['userid'];
        $usrntfrelation = $this->model_class->get_mapper()->findAll(
            $where = "usrNNotifiedAccId = " . $user_id . " AND usrnNIsRead = 0",
            $fields = false);

        if (count($usrntfrelation) == 0) {
            return;
        }

        foreach ($usrntfrelation as $relation) {
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

    private function get_notifications()
    {
        $this->model('Usrntfrelation');

        $user_id = $_SESSION['userid'];
        $usrntfrelation = $this->model_class->get_mapper()->findAll(
            $where = "usrNNotifiedAccId = " . $user_id,
            $fields = false);

        if (count($usrntfrelation) == 0) {
            return [];
        }

        $this->model('Usrntfrelation');

        $notifications = [];
        $this->model('Notification');
        foreach ($usrntfrelation as $relation) {
            $notifications[] = $this->model_class->get_mapper()->findAll(
                $where = "ntfId = " . $relation["usrNNotificationId"],
                $fields = false)[0];
        }

        $this->model('Item');
        for ($i = 0; $i < count($notifications); $i++) {
            $notifications[$i]["item_name"] = $this->model_class->get_mapper()->findAll(
                $where = "itemId = " . $notifications["nItemId"],
                $fields = false)[0]["itemName"];
        }

        return $notifications;
    }

    private function reports()
    {
        $this->model('AutomatedReport');

        $reports = $this->model_class->get_mapper()->findAll(
            $fields = false);

        View::CreateView(
            'user' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'reports',
            array
            (
                'reports' => $reports

            ),
            'Reports!');
    }

    private function download_report()
    {
        $this->model('AutomatedReport');

        $reports = $this->model_class->get_mapper()->findAll(
            $where = "reportId = " . $_POST["download_report"],
            $fields = false);

        $file = $reports[0]["reportPath"];
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename='" . basename($file) . "'");
        readfile($file);
        exit();
    }
}
