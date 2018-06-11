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

    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter)
    {
        parent::__construct($adapter, array(
            'entityTable' => $this->_entityTable,
            'entityClass' => $this->_entityClass
        ));
    }

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $usergroup = new $this->_entityClass(array(
            'uGroupId'     => $data['UGROUPID'] ? $data['UGROUPID'] : '',
            'uGroupName'   => $data['UGROUPNAME'] ? $data['UGROUPNAME'] : '',
            'uGroupDescription'   => $data['UGROUPDESCRIPTION'] ? $data['UGROUPDESCRIPTION'] : '',
            'nrOfMembers'   => $data['NROFMEMBERS'] ? $data['NROFMEMBERS'] : '',
            'nrOfManagers'   => $data['NROFMANAGERS'] ? $data['NROFMANAGERS'] : '',
            'uGroupCreatedAt'   => $data['UGROUPCREATEDAT'] ? $data['UGROUPCREATEDAT'] : '',
            'uGroupUpdatedAt'   => $data['UGROUPUPDATEDAT'] ? $data['UGROUPUPDATEDAT'] : ''
        ));
        return $usergroup;
    }
}
