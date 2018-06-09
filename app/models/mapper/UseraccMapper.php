<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 18:34
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class UseraccMapper extends AbstractMapper
{
    protected $_entityTable = 'USERACCS';
    protected $_entityClass = 'UserAcc';

    /**
    * Create an useracc entity with the supplied data
    */
    protected function _createEntity(array $data)
    {
        $useracc = new $this->_entityClass(array(
            'userId'    => $data['userId'],
            'userName'  => $data['userName'],
            'userEmail' => $data['userEmail'],
            'userPass'  => $data['userPass'],
            'userType'  => $data['userType'],
            'userState' => $data['userState'],
            'userImage' => $data['userImage'],
            'userCreatedAt' => $data['userCreatedAt'],
            'userUpdatedAt' => $data['userUpdatedAt']
        ));
        return $useracc;
    }
}