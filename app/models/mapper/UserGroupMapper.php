<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:15
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

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
            'UGROUPID'     => $data['uGroupId'],
            'UGROUPNAME'   => $data['uGroupName'],
            'UGROUPDESCRIPTION'   => $data['uGroupDescription'],
            'NROFMEMBERS'   => $data['nrOfMembers'],
            'NROFMANAGERS'   => $data['nrOfManagers'],
            'UGROUPCREATEDAT'   => $data['uGroupCreatedAt'],
            'UGROUPUPDATEDAT'   => $data['uGroupUpdatedAt']
        ));
        return $usergroup;
    }
}