<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: UserController.php
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
            case 'user/users/groupmembers':
                $this->renderGroup();
                break;
            case 'user/users/addmembers':
                $this->addMembers();
                break;
            case 'user/users/removemember':
                $this->removeMember();
                break;
            case 'user/users/addgroup':
                $this->addNewGroup();
                break;
            case 'user/reports':
                $this->reports();
                break;
            case 'user/reports/generate':
                $this->generate_report();
                break;
            case 'user/reports/download/csv':
            case 'user/reports/download/pdf':
            case 'user/reports/download/html':
            case 'user/reports/download/xml':
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
                new PageNotFoundController();
                break;

        }
    }

    private function addNewGroup(){

        if(filter_var($_POST['newGroupName'],FILTER_SANITIZE_STRING)==false || filter_var($_POST['newGroupDescription'],FILTER_SANITIZE_STRING)==false) {
            new ForbiddenController();
            return;
        } else {
            $name = $_POST['newGroupName'];
            $description = $_POST['newGroupDescription'];
        }

        $this->model('Usergroup');
        $this->model_class->get_mapper()->insert(
            'USERGROUPS',
            array
            (
                'uGroupName'    => "'" . $name . "'",
                'uGroupDescription'    => "'" . $description . "'",
                'nrOfMembers' => 0,
                'nrOfManagers' => 0
            )
        );

        $this->model('UserGroupLog');

        if($_SESSION['is_admin'])
        {
            $userGrade = "Admin user";
        } else if ($_SESSION['canManageMembers']){
            $userGrade = "Manager user";
        } else {
            $userGrade = "Normal user";
        }

        $this->model_class->get_mapper()->insert(
            $table = 'USERGROUPLOGS',
            $fields = array
            (
                'uGLogDescription'   => "'" . $userGrade . " " . $_SESSION['uname'] . " " . " has added group " . $description  . "'",
                'uGLogSourceIP'      => "'" . $_SESSION['login_ip'] . "'"
            )
        );

        $this->model('Userlog');
        $this->model_class->get_mapper()->insert(
            $table = 'USERLOGS',
            $fields = array
            (
                'uLogDescription'   => "'" . $userGrade . " " . $_SESSION['uname'] . " " . " has added group " . $description  . "'",
                'uLogSourceIP'      => "'" . $_SESSION['login_ip'] . "'"
            )
        );
        self::redirect('/user/users');

    }

    private function removeMember(){
        $option = $_POST['removeUser'];
        $this->model('Grouprelation');
        $querry =
            $this->model_class->get_mapper()->delete(
                $table = 'GROUPRELATIONS',
                $where = array(
                    'USERID' => $option . " AND UGROUPID = " . $_SESSION['renderedGroupId'] ,
                )
            );


        $this->model('Useracc');

        $removedUserName = $this->model_class->get_mapper()->findAll(
            $where = "USERID =" . $option,
            $field = false
        )[0]['userName'];


        $this->model('UserGroupLog');

        if($_SESSION['is_admin'])
        {
            $userGrade = "Admin user";
        } else if ($_SESSION['canManageMembers']){
            $userGrade = "Manager user";
        } else {
            $userGrade = "Normal user";
        }

        $this->model_class->get_mapper()->insert(
            $table = 'USERGROUPLOGS',
            $fields = array
            (
                'uGLogDescription'   => "'" . $userGrade . " " . $_SESSION['uname'] . " " . " has removed user " . $removedUserName . " from group " . $_SESSION['userGroupName'] . "'",
                'uGLogSourceIP'      => "'" . $_SESSION['login_ip'] . "'"
            )
        );

        $this->model('Userlog');
        $this->model_class->get_mapper()->insert(
            $table = 'USERLOGS',
            $fields = array
            (
                'uLogDescription'   => "'" . $userGrade . " " . $_SESSION['uname'] . " " . " has removed user " . $removedUserName . " from group " . $_SESSION['userGroupName'] . "'",
                'uLogSourceIP'      => "'" . $_SESSION['login_ip'] . "'"
            )
        );
        if ((isset($_SESSION['renderedGroupId']) || isset($_SESSION['listOfGroupUserIds']))) {
            unset($_SESSION['renderedGroupId']);
            unset($_SESSION['listOfGroupUserIds']);
            unset($_SESSION['canManageMembers']);
            unset($_SESSION['notInGroupUsers']);
            unset($_SESSION['userGroupName']);
            self::redirect('/user/users');
        } else {
            self::redirect('/user/users');
        }
    }

    private function getUserGroupName($groupId){
        $this->model('Usergroup');
        $groupName =
            $this->model_class->get_mapper()->findAll(
                $where = "UGROUPID=" . $groupId,
                $fields = false
            )[0]['uGroupName'];
        return $groupName;
    }

    private function addMembers(){
        $option = $_POST['selectedUser'];
        if (isset($_POST['canManageGroup'])){
            $manageGroup = 1;
        } else{
            $manageGroup = 0;
        };
        if (isset($_POST['canManItems'])){
            $manipulateItem = 1;
        } else{
            $manipulateItem = 0;
        };

        $this->model('Grouprelation');
        $result = $this->model_class->get_mapper()->insert(
            'GROUPRELATIONS',
            array
            (
                'userId'    => "'" . $option . "'",
                'uGroupId'    => "'" . $_SESSION['renderedGroupId'] . "'",
                'canUpdItm'  => "'" . $manipulateItem . "'",
                'canMngMbs'    => "'" . $manageGroup . "'"
            )
        );

        $this->model('Useracc');

        $addedUserName = $this->model_class->get_mapper()->findAll(
            $where = "USERID =" . $option,
            $field = false
        )[0]['userName'];

        $this->model('UserGroupLog');

        if($_SESSION['is_admin'])
        {
            $userGrade = "Admin user";
        } else if ($_SESSION['canManageMembers']){
            $userGrade = "Manager user";
        } else {
            $userGrade = "Normal user";
        }

        $this->model_class->get_mapper()->insert(
            $table = 'USERGROUPLOGS',
            $fields = array
            (
                'uGLogDescription'   => "'" . $userGrade . " " . $_SESSION['uname'] . " " . " has added user " . $addedUserName . " in group " . $_SESSION['userGroupName'] . "'",
                'uGLogSourceIP'      => "'" . $_SESSION['login_ip'] . "'"
            )
        );

        $this->model('Userlog');
        $this->model_class->get_mapper()->insert(
            $table = 'USERLOGS',
            $fields = array
            (
                'uLogDescription'   => "'" . $userGrade . " " . $_SESSION['uname'] . " " . " has added user " . $addedUserName . " in group " . $_SESSION['userGroupName'] . "'",
                'uLogSourceIP'      => "'" . $_SESSION['login_ip'] . "'"
            )
        );

        if ((isset($_SESSION['renderedGroupId']) || isset($_SESSION['listOfGroupUserIds']))) {
            unset($_SESSION['renderedGroupId']);
            unset($_SESSION['listOfGroupUserIds']);
            unset($_SESSION['canManageMembers']);
            unset($_SESSION['notInGroupUsers']);
            unset($_SESSION['userGroupName']);
            self::redirect('/user/users');
        } else {
            self::redirect('/user/users');
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
            unset($_SESSION['notInGroupUsers']);
            unset($_SESSION['userGroupName']);
            self::redirect('/user/users');
        } else {
            $_SESSION['renderedGroupId'] = $groupId;
            $_SESSION['listOfGroupUserIds'] = $this->getMemberOfGroup($groupId);
            $_SESSION['canManageMembers'] = $canManageMembers;
            $_SESSION['notInGroupUsers'] = $this->getNonMembers($groupId);
            $_SESSION['userGroupName'] = $this->getUserGroupName($groupId);
            self::redirect('/user/users');
        }


    }

    private function getNonMembers($groupId){

        $this->model('Useracc');

        $getAllUsers=
            $this->model_class->get_mapper()->findAll(
                $where = "USERTYPE < 2 AND USERID != " . $_SESSION['userid'],
                $fields = false
            );


        $this->model('Grouprelation');

        $userCount = 0;

        for ($i = 0; $i < count($getAllUsers); ++$i){
            $checkAvailable = $this->model_class->get_mapper()->findAll(
                $where = "USERID = " . $getAllUsers[$i]['userId'] . " AND UGROUPID = " . $groupId,
                $fields = false
            );
            if (empty($checkAvailable)){
                $result[$userCount] = $getAllUsers[$i];
                $userCount+=1;
            }
        }

        return $result;

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
        $response = [];
        $this->model('GroupRelation');
        $result = $this->model_class->get_mapper()->findAll(
            $where = "UGROUPID = " . $group . " AND USERID <> 1",
            $fields = 'USERID',
            $order = 'GRPRELCREATEDAT ASC'
        );

//        echo var_dump($result);
//        exit(0);

        for ($i = 0; $i < count($result); ++$i)
            $result[$i] = $result[$i]['userId'];

        $this->model('Useracc');
        for ($i = 0; $i < count($result); ++$i){
            $result[$i]= $this->model_class->get_mapper()->findAll(
                $where = "USERID = " . $result[$i]
            );
            //$result[$i] = $result[$i][0]['userName'];
            array_push($response, array(
                'userId' => $result[$i][0]['userId'],
                'userName' => $result[$i][0]['userName']
            ));
        }
        return $response;

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
                'notifications_count' => $this->get_notifications_count(),
                'totalusers' => $this->getTotalNrOFUserAccounts()
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
            'countItems' => count($unique_items),
            'avgQuantity' => ((count($unique_items) ? ($avg_quantity / count($unique_items)) : 0)),
            'countItemsGroups' => count($unique_groups_items)
        ];

    }

    public function notifications()
    {
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
            $where = "",
            $fields = 'IGLOGID, IGLOGDESCRIPTION, IGLOGSOURCEIP,
                       TO_CHAR(IGLOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "IGLOGCREATEDAT"');

        $this->model('Itemlog');

        $item_logs = $this->model_class->get_mapper()->findAll(
            $where = "",
            $fields = 'ILOGID, ILOGDESCRIPTION, ILOGSOURCEIP,
                       TO_CHAR(ILOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "ILOGCREATEDAT"');

        $this->model('UserGroupLog');

        $user_group_logs = $this->model_class->get_mapper()->findAll(
            $where = "",
            $fields = 'UGLOGID, UGLOGDESCRIPTION, UGLOGSOURCEIP,
                       TO_CHAR(UGLOGCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "UGLOGCREATEDAT"');

        $logs = array_merge($item_group_logs, $item_logs, $user_group_logs);

        usort($logs, function ($a, $b)
        {
            $values_a = array_values( $a );
            $values_b = array_values( $b );
            if ($values_a[3] == $values_b[3])
            {
                return 0;
            }

            return ($values_a[3] > $values_b[3]) ? -1 : 1;
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
            array(
                    'resultPostGroup'=>$this->postGroupItems(),
                    'permissionModifyItemsGroup'=>$this->getPermissionModifyItemsGroups(),
                    'groupOfItems'=>$this->getGroupsOfItems(),
                    'resultDeleteGroup'=>$this->deleteGroupsOfItems(),
                    'postItem'=>$this->postItem()
                 ),
            'Items ');
    }

    public function postItem(){

        if  (   isset($_POST["iName"]) &&
                isset($_POST["iDescription"])&&
                isset($_POST["iQuantity"])&&
                isset($_POST["iLimit"]) &&
                isset($_POST["iGroupId"])

            ) {
            $result = $this->insertingItem();
            if ($result['operation'] == true) {
                return $result['message'];
            } else {
                return "Failed to create group : " . $result['message'];
            }
        }
    }
    public function insertingItem(){
        //validation input

        if (strlen($_POST["iName"]) < 4 || strlen($_POST["iName"]) > 48) {
            return array('operation' => false, 'message' => 'item name not being between 4 and 48 characters long!');
        }
        elseif(!preg_match('/[^a-zA-Z0-9._ -]/',$_POST['iName'])) {
            $iName = $_POST["iName"];
        }else {
            return array('operation' => false,
                'message' => 'item name containing prohibited characters!'
                    . PHP_EOL
                    . 'Use only alpha-numeric, \'.\'_\' and \'-\' characters!');
        }
        $this->model('Item');// check if item name already exists
        $query_items = $this->model_class->get_mapper()->findAll(
            $where = " ITEMNAME= " ."'". $_POST["iName"]."'",
            $fields = false
        );

        if (count($query_items)!= 0) {
            return array('operation' => false, 'message' => 'item name already exists !');
        }
        //group description validation
        if (strlen($_POST['iDescription']) < 4 || strlen($_POST['iDescription']) > 2000) {
            return array('operation' => false, 'message' => 'item description not being between 4 and 2000 characters long!');
        }
        elseif(!preg_match('/[^a-zA-Z0-9.,?! -]/',$_POST['iDescription'])) {
            $iDescription = $_POST["iDescription"];
        }
        else {
            return array('operation' => false, 'message' => 'Inputed item description is in a wrong format!');
        }
        if($_POST["iQuantity"]<1 || $_POST["iQuantity"]>999999){
            return array('operation' => false, 'message' => 'Inputed item quantity is too big or too small!');
        }
        if($_POST["iLimit"]<1 || $_POST["iLimit"]>999999){
            return array('operation' => false, 'message' => 'Inputed item quantity is too big or too small!');
        }

        // INSERTING ITEM
        $this->model('Item');
        $result_item = $this->model_class->get_mapper()->insert(
            'ITEMS',
            array
            (
                'itemName'          =>  "'" .$_POST["iName"]."'",
                'itemDescription'   =>  "'" .$_POST["iDescription"]."'",
                'itemQuantity'      =>       $_POST["iQuantity"],
                'iGroupId'          =>       $_POST["iGroupId"],
                'iWarnQnty'         =>       $_POST["iLimit"],
                'itemImage'         =>  "' '"

            )
        );
        //creating logs for item post
        $this->model('Itemlog');
        $this->model_class->get_mapper()->insert(
            'ITEMLOGS',
            array
            (
                'iLogDescription'    => "'Normal user " . $_SESSION['uname']    . " has created item ".$_POST["iName"]."'" ,
                'iLogSourceIP'       => "'" .$_SESSION['login_ip'] . "'"
            )
        );

        return array('operation' => true, 'message' => " Succesfully created item !");
    }

    public function deleteGroupsOfItems(){
        if(isset($_POST['delButton'])) {

            $this->model('Itemgroup');
            $querry_item_groups = $this->model_class->get_mapper()->findAll(
                $where = " iGroupName= " . "'" . $_POST['delButton'] . "'",
                $fields = false
            );
            $this->model('Itemgroupownership');
            $querry_item_groups_owners = $this->model_class->get_mapper()->findAll(
                $where = " iGId= "  . $querry_item_groups['0']['iGroupId']
                        ." AND iGOwnerId=".$_POST['delButtonUserGroupId'],
                $fields = false
            );

            //delete ownerships
            if(!is_null($querry_item_groups_owners)) {
                foreach ($querry_item_groups_owners as $querry_item_groups_owner) {//here is just an element not array
                    $this->model('Itemgroupownership');
                    $querry_item_groups_owners = $this->model_class->get_mapper()->delete(
                        'ITEMGROUPOWNERSHIPS',
                        array
                        (
                            'iGOwnershipId' => $querry_item_groups_owner['iGOwnershipId']
                        )
                    );
                }
            }

            //delete items
            $this->model('Item');
            $querry_items = $this->model_class->get_mapper()->findAll(
                $where = " iGroupId= "  .$_POST['delButtonUserGroupId'] ,

                $fields = false
            );

            if(!is_null($querry_items)) {
                foreach ($querry_items as $querry_item) {
                    $querry_item_result = $this->model_class->get_mapper()->delete(
                        'ITEMS',
                        array
                        (
                            'itemId' => $querry_item['itemId']
                        )
                    );
                }
            }

            // deleting item group if there is no ownership

            $this->model('GroupRelation');
            $query_group_relations = $this->model_class->get_mapper()->findAll(
                $where = " USERID= " . $_SESSION['userid'],
                $fields = false
            );

            $count=0;
            foreach($query_group_relations as $group_relation){
                $this->model('Itemgroupownership');
                $querry_groups_owners = $this->model_class->get_mapper()->findAll(
                    $where = " iGOwnerId=".$group_relation['uGroupId'],
                    $fields = false
                );
                if(!is_null($querry_groups_owners)){
                    $count=$count+1;
                }
            }

            if($count == 0) {//if there is no ownership remove group item

                $this->model('Itemgroup');
                $querry_item_groups_del_result = $this->model_class->get_mapper()->delete(
                    'ITEMGROUPS',
                    array
                    (
                        'iGroupId' => $querry_item_groups['0']['iGroupId']
                    )
                );
            }
            //creating logs for deleting group
            $this->model('Itemgrouplog');
            $this->model_class->get_mapper()->insert(
                'ITEMGROUPLOGS',
                array
                (
                    'iGLogDescription'   => "'Normal user " . $_SESSION['uname']    . " has deleted group ".$_POST['delButton']."'" ,
                    'iGLogSourceIP'      => "'" .$_SESSION['login_ip'] . "'"
                )
            );

            return "Succesfully deleted group of items!";
        }
        return "Input is not set";
    }
    public function getGroupsOfItems(){
        $items_groups=array();
        $this->model('Grouprelation');
        $querry_rel_users = $this->model_class->get_mapper()->findAll(
            $where = " USERID= ". $_SESSION['userid'],
            $fields = false
        );

        foreach($querry_rel_users as $querry_rel_user) {

            $this->model('Usergroup');// find by id
            $querry_user_groups = $this->model_class->get_mapper()->findAll(
                $where = " uGroupId= ". $querry_rel_user['uGroupId'],
                $fields = false
            );

            $this->model('Itemgroupownership');
            $querry_item_groups_owners = $this->model_class->get_mapper()->findAll(
                $where = " iGOwnerId= ". $querry_rel_user['uGroupId'],
                $fields = false
            );
            $unique_items_groups = array();

            foreach($querry_item_groups_owners as $querry_item_groups_owner ){
                $this->model('Itemgroup');
                $querry_item_groups = $this->model_class->get_mapper()->findAll(
                    $where = " iGroupId= ". $querry_item_groups_owner['iGId'],
                    $fields = false
                );


                foreach ($querry_item_groups as $item) {
                    if (!in_array($item, $unique_items_groups)) {
                        array_push($unique_items_groups, $item);
                    }
                }
            }

            foreach($unique_items_groups as $unique_items_group){
                array_push($items_groups,array(
                    'iGroupName'=>$unique_items_group['iGroupName'],
                    'iGroupId'=>$unique_items_group['iGroupId'],
                    'iGroupDescription'=>$unique_items_group['iGroupDescription'],
                    'iGroupCreatedAt'=>$unique_items_group['iGroupCreatedAt'],
                    'iGroupUpdatedAt'=>$unique_items_group['iGroupUpdatedAt'],
                    'uGroupName'=>$querry_user_groups['0']['uGroupName'],
                    'uGroupId'=>$querry_user_groups['0']['uGroupId']
                ));
            }
        }

        return $items_groups;
    }
    public function getPermissionModifyItemsGroups(){

        $this->model('Grouprelation');
        $querry_rel_users = $this->model_class->get_mapper()->findAll(
            $where = " USERID= ". $_SESSION['userid'],
            $fields = false
        );

        foreach($querry_rel_users as $querry_rel_user) {
            if ($querry_rel_user['canUpdItm'] == "1") {
                return "1";
            }
        }
        return "0";
    }
    public function insertingItemGroup(){


        // group name validation
        if (strlen($_POST["gName"]) < 4 || strlen($_POST["gName"]) > 48) {
            return array('operation' => false, 'message' => 'group name not being between 4 and 48 characters long!');
        }
        elseif(!preg_match('/[^a-zA-Z0-9._ -]/',$_POST['gName'])) {
            $gName = $_POST["gName"];
        }else {
            return array('operation' => false,
                'message' => 'group name containing prohibited characters!'
                    . PHP_EOL
                    . 'Use only alpha-numeric, \'.\'_\' and \'-\' characters!');
        }
        $this->model('Itemgroup');// check if group name already exists
        $query_item_groups = $this->model_class->get_mapper()->findAll(
            $where = " IGROUPNAME= " ."'". $_POST["gName"]."'",
            $fields = false
        );

        if (count($query_item_groups)!= 0) {
            return array('operation' => false, 'message' => 'group name already exists !');
        }
        //group description validation
        if (strlen($_POST['gDescription']) < 4 || strlen($_POST['gDescription']) > 2000) {
            return array('operation' => false, 'message' => 'group description not being between 4 and 2000 characters long!');
        }
        elseif(!preg_match('/[^a-zA-Z0-9.,?! -]/',$_POST['gDescription'])) {
            $gDescription = $_POST["gDescription"];
        }
        else {
            return array('operation' => false, 'message' => 'Inputed group description is in a wrong format!');
        }

        // inserting
        $this->model('Itemgroup');
        $result_item_group = $this->model_class->get_mapper()->insert(
            'ITEMGROUPS',
            array
            (
                'iGroupName'  => "'" . $gName  . "'",
                'iGroupDescription' => "'" . $gDescription . "'"
            )
        );

        //for every group of users that session user can update groups of items, add in owner ship that group of users
        $this->model('Grouprelation');
        $querry_rel_users = $this->model_class->get_mapper()->findAll(
            $where = " USERID= ". $_SESSION['userid'],
            $fields = false
        );


        $this->model('Itemgroup');// item that was inserted
        $query_item_group = $this->model_class->get_mapper()->findAll(
            $where = " IGROUPNAME= " ."'". $_POST["gName"]."'",
            $fields = false
        );
        foreach($querry_rel_users as $querry_rel_user){
            if($querry_rel_user['canUpdItm'] !="0"){
                $this->model('Itemgroupownership');
                $result_item_group_owner = $this->model_class->get_mapper()->insert(
                    'ITEMGROUPOWNERSHIPS',
                    array
                    (
                        'iGOwnerId'  => $querry_rel_user['uGroupId'] ,
                        'iGId' =>  $query_item_group['0']['iGroupId']
                    )
                );
            }
        }

        //creating logs for groups post
        $this->model('Itemgrouplog');
        $this->model_class->get_mapper()->insert(
            'ITEMGROUPLOGS',
            array
            (
                'iGLogDescription'   => "'Normal user " . $_SESSION['uname']    . " has created group ".$gName."'" ,
                'iGLogSourceIP'      => "'" .$_SESSION['login_ip'] . "'"
            )
        );

        $this->model('Itemlog');
        $this->model_class->get_mapper()->insert(
            'ITEMGROUPLOGS',
            array
            (
                'iLogDescription'   => "'Normal user " . $_SESSION['uname']    . " has created group ".$gName."'" ,
                'iLogSourceIP'      => "'" .$_SESSION['login_ip'] . "'"
            )
        );
        return array('operation' => true, 'message' => " Succesfully created group !");
    }
    public function postGroupItems(){
        $this->cleanRemainingGroupsWithoutOwner();
        if(isset($_POST["gName"]) && isset($_POST["gDescription"])) {
            $result = $this->insertingItemGroup();
            if ($result['operation'] == true) {
                return $result['message'];
            } else {
                return "Failed to create group : " . $result['message'];
            }
        }
    }
    public function cleanRemainingGroupsWithoutOwner(){
        $this->model('Itemgroup');
        $querry_item_groups = $this->model_class->get_mapper()->findAll(
            $where = '',
            $fields = false
        );
        foreach($querry_item_groups as $item_group) {
            $this->model('Itemgroupownership');
            $querry_item_groups_owners = $this->model_class->get_mapper()->findAll(
                $where = " iGId= " . $item_group['iGroupId'],
                $fields = false
            );
            if(is_null($querry_item_groups_owners)){
                $this->model('Itemgroup');
                $querry_result= $this->model_class->get_mapper()->delete(
                    $table='ITEMGROUPS',
                    $field= array
                    (
                        'iGroupId' => $item_group['iGroupId']
                    )
                );
            }
        }
    }
    public function users()
    {
        if (isset($_SESSION['renderedGroupId'])) {
            View::CreateView(
                'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'users',
                [
                    'memberGroup' => $this->getUserGroups(),
                    'usersToDisplay' => $_SESSION['listOfGroupUserIds'],
                    'canManageMembers' => $_SESSION['canManageMembers'],
                    'notInGroupUsers' => $_SESSION['notInGroupUsers'],
                    'userGroupName' => $_SESSION['userGroupName']
                ],
                'Users area');

        } else {
            View::CreateView(
                'user' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'users',
                [
                    'memberGroup' => $this->getUserGroups(),
                    'usersToDisplay' => [],
                    'canManageMembers' => 0,
                    'notInGroupUsers' => [],
                    'userGroupName' => ""
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
        
        self::log_user_activity("'Normal user " . $_SESSION['uname']     . " has logged out!'");

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

        if (count($usrntfrelation) == 0)
        {
            return;
        }

        foreach ($usrntfrelation as $relation)
        {
            $this->model('Usrntfrelation');
            $this->model_class->get_mapper()->update(
                'USRNTFRELATIONS',
                array
                (
                    'usrnNIsRead' => 1
                ),
                array
                (
                    'usrNRelationId' => $relation["usrNtfRelationId"]
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

        if (count($usrntfrelation) === 0)
        {
            return [];
        }

        $notifications = [];
        $this->model('Notification');
        foreach ($usrntfrelation as $relation)
        {
            $notifications[] = $this->model_class->get_mapper()->findAll(
                $where = "ntfId = " . $relation["usrNNotificationId"],
                $fields = 'NTFID, NITEMID, NTFTYPE, NTFDSCRP,
                       TO_CHAR(NTFCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "NTFCREATEDAT"')[0];
        }

        $this->model('Item');
        for ($i = 0; $i < count($notifications); $i++)
        {
            $notifications[$i]["item_name"] = $this->model_class->get_mapper()->findAll(
                $where = "itemId = " . $notifications[$i]["nItemId"],
                $fields = false)[0]["itemName"];
        }

        return $notifications;
    }

    private function reports()
    {
        $this->model('AutomatedReport');

        $reports = $this->model_class->get_mapper()->findAll(
            $where = "",
            $fields = 'REPORTID, REPORTPATH, REPORTTYPE, REPORTFORMAT,
                       TO_CHAR(RCREATEDAT, \'DD-MM-YYYY HH24:MI:SS\') AS "RCREATEDAT"');

        usort($reports, function ($a, $b)
        {
            $values_a = array_values( $a );
            $values_b = array_values( $b );
            if ($values_a[4] == $values_b[4])
            {
                return 0;
            }

            return ($values_a[4] > $values_b[4]) ? -1 : 1;
        });


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

        $file_type = $file;


        $uri = $_SERVER['REQUEST_URI'];
        $split_uri = explode("/", $uri);

        // array(5)
        // {
        //      [0]=> string(0) ""
        //      [1]=> string(4) "user"
        //      [2]=> string(7) "reports"
        //      [3]=> string(8) "download"
        //      [4]=> string(3) "csv"
        // }

        //create file type here
        $file_type .= "." . $split_uri[4];
        $output = null;

        switch($split_uri[4])
        {
            case 'csv':
                $output = $file;
                break;
            case 'xml':
                $output = 'temp';

                $xw = $this->get_xml_writer_logs($file);
                $temp_buffer = xmlwriter_output_memory($xw);

                $handle = fopen($output, "w+");
                fwrite($handle, $temp_buffer);
                fclose($handle);

                break;
            case 'html':
                $output = 'temp';

                $temp_buffer = $this->get_html_buffer($file);
                $handle = fopen($output, "w+");
                fwrite($handle, $temp_buffer);
                fclose($handle);

                break;
            case 'pdf':
                $output = $this->get_pdf($file);
                break;
            default:
                break;
        }

        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename='" . basename($file_type) . "'");

        if($split_uri[4] !== 'pdf')
        {
            readfile($output);
        }
        else
        {
            $output->Output($dest='D', $name=basename($file) . '.pdf');
        }

        if($output === 'temp')
        {
            // delete temp file
            unlink($output);
        }
        exit();
    }

    private function generate_report()
    {
        // generate report
        $date = date("Y-m-d-h-i-s");
        $path = RESOURCES . 'reports' . DS . $date;
        $h_report = fopen($path, "w+");

        $report_header = "itemId,itemName,itemDescription,itemQuantity,iGroupId,iWarnQnty,itemImage,itemCreatedAt,itemUpdatedAt\n";
        fwrite($h_report, $report_header);

        $this->model('Item');
        $reports = $this->model_class->get_mapper()->findAll($fields = false);

        if(isset($reports) && $reports !== null && is_array($reports))
        {
            foreach ($reports as $rep)
            {
                $row = $rep["itemId"] . ",";
                $row .= $rep["itemName"] . ",";
                $row .= str_replace(',','', $rep["itemDescription"]) . ",";
                $row .= $rep["itemQuantity"] . ",";
                $row .= $rep["iGroupId"] . ",";
                $row .= $rep["iWarnQnty"] . ",";
                $row .= $rep["itemImage"] . ",";
                $row .= $rep["itemCreatedAt"] . ",";
                $row .= $rep["itemUpdatedAt"] . "\n";

                fwrite($h_report, $row);
            }
        }

        fclose($h_report);

        $this->model('AutomatedReport');

        $this->model_class->get_mapper()->insert(
            'AUTOMATEDREPORTS',
            array
            (
                'reportPath'         => "'$path'" ,
                'reportType'         => 1 ,
                'reportFormat'       => "'all'"
            )
        );

        $log_description = "'Normal user " . $_SESSION['uname'] . " generated log ". $date ."'";
        self::log_user_activity($log_description);

        self::redirect('/user/reports');
    }

    private function get_xml_writer_logs($path)
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);

        // Sets the string which will be used to indent each element/attribute of the resulting xml.
        $res = xmlwriter_set_indent_string($xw, ' ');

        xmlwriter_start_document($xw, '1.0', 'UTF-8');

        // A first element
        xmlwriter_start_element($xw, 'CATALOG');

        $handle = fopen($path, "r");

        if ($handle)
        {
            $file_headers = [];

            // get headers
            if (($line = fgets($handle)) !== false)
            {
                $file_headers = explode(',', $line);
            }

            // parse file lines
            while (($line = fgets($handle)) !== false)
            {

                $split_line = explode(',', $line);

                // write content of an item (column values)
                xmlwriter_start_element($xw, 'item');

                for ($i = 0; $i < count($split_line); $i++)
                {
                    xmlwriter_start_element($xw, trim($file_headers[$i]));
                    xmlwriter_text($xw, $split_line[$i]);
                    xmlwriter_end_element($xw);
                }

                xmlwriter_end_element($xw);
            }

            fclose($handle);
        }
        else
        {
            // error opening the file.
        }

        xmlwriter_end_document($xw);

        return $xw;
    }

    private function get_html_buffer($path)
    {
        $buffer = "";

        $buffer .= "<html><body>";

        $buffer .= '<br><br><br><br>';

        $buffer .= '<h2 style="text-align:center;">';
        $buffer .= 'REPORT';
        $buffer .= '</h2>';

        $buffer .= '<br><br><br><br>';

        $buffer .= '<ul style="list-style-type: none;">';

        $handle = fopen($path, "r");

        if ($handle)
        {
            $file_headers = [];

            // get headers
            if (($line = fgets($handle)) !== false)
            {
                $file_headers = explode(',', $line);
            }

            $buffer .= '<li><h3 style="text-align:center; margin-left: -100px;"><pre>';

            foreach ($file_headers as $head)
            {
                $buffer .= "$head ";
            }

            $buffer .= '</pre></h3></li>';

            // parse file lines
            while (($line = fgets($handle)) !== false)
            {

                $split_line = explode(',', $line);

                $buffer .= '<li><h5 style="text-align:center;"><pre>';

                foreach ($split_line as $value)
                {
                    $buffer .= "$value     ";
                }

                $buffer .= '</pre></h5></li>';
            }

            fclose($handle);
        }
        else
        {
            // error opening the file.
        }

        $buffer .= '</ul>';
        $buffer .= "</html></body>";

        return $buffer;
    }

    private function get_pdf($path)
    {
        $pdf = new FPDF();
        $pdf->AddPage();

        $pdf->SetFont('Arial','',20);
        $pdf->Cell( 46, 12, " REPORT", 1, 0, 'C', false );


        $pdf->SetFont('Arial','',11);

        $handle = fopen($path, "r");

        if ($handle)
        {
            $file_headers = [];

            // get headers
            if (($line = fgets($handle)) !== false)
            {
                $file_headers = explode(',', $line);
            }

            $data_headers = "";
            for ($i = 0; $i < count($file_headers); $i++)
            {
                $data_headers .= $file_headers[$i] . " ";
            }

            $pdf->Ln( 20 );
            $pdf->Cell( 0, 6, $data_headers, 1, 0, 'C', false );


            // parse file lines
            while (($line = fgets($handle)) !== false)
            {

                $split_line = explode(',', $line);

                // write content of an item (column values)
                $data = "";

                for ($i = 0; $i < count($split_line); $i++)
                {
                    //$data .= $split_line[$i] . " ";
                    $pdf->Ln( 6 );
                    $pdf->Cell( 0, 6, $split_line[$i] . " ", 1, 0, 'C', false );
                }

            }

            fclose($handle);
        }
        else
        {
            // error opening the file.
        }

        return $pdf;
    }

    private function getTotalNrOFUserAccounts()
    {
        $this->model('Useracc');
        $number = count($this->model_class->get_mapper()->findAll());
        return $number;
    }
}
