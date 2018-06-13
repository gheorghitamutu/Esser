<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:15
 */

namespace ModelMapper;
use AppModel;
use DatabaseConnectivity;

class UserGroupMapper extends AbstractMapper
{
    protected $_entityTable = 'USERGROUPS';
    protected $_entityClass = 'Usergroup';

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
        $usergroup = array(
            'uGroupId'     => array_key_exists('UGROUPID', $data) ? $data['UGROUPID'] : '',
            'uGroupName'   => array_key_exists('UGROUPNAME', $data) ? $data['UGROUPNAME'] : '',
            'uGroupDescription'   => array_key_exists('UGROUPDESCRIPTION', $data) ? $data['UGROUPDESCRIPTION'] : '',
            'nrOfMembers'   => array_key_exists('NROFMEMBERS', $data) ? $data['NROFMEMBERS'] : '',
            'nrOfManagers'   => array_key_exists('NROFMANAGERS', $data) ? $data['NROFMANAGERS'] : '',
            'uGroupCreatedAt'   => array_key_exists('UGROUPCREATEDAT', $data) ? $data['UGROUPCREATEDAT'] : '',
            'uGroupUpdatedAt'   => array_key_exists('UGROUPUPDATEDAT', $data) ? $data['UGROUPUPDATEDAT'] : ''
        );
        return $usergroup;
    }
}
