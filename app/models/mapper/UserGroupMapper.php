<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:15
 */

namespace ModelMapper;
use AppModel\Useracc;
use DatabaseConnectivity;

class UserGroupMapper extends AbstractMapper
{
    protected $_entityTable = 'USERGROUPS';
    protected $_entityClass = 'usergroup';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $usergroup = new $this->_entityClass(array(
            'uGroupId'     => $data['UGROUPID'],
            'uGroupName'   => $data['UGROUPNAME'],
            'uGroupDescription'   => $data['UGROUPDESCRIPTION'],
            'nrOfMembers'   => $data['NROFMEMBERS'],
            'nrOfManagers'   => $data['NROFMANAGERS'],
            'uGroupCreatedAt'   => $data['UGROUPCREATEDAT'],
            'uGroupUpdatedAt'   => $data['UGROUPUPDATEDAT']
        ));
        return $usergroup;
    }
}
