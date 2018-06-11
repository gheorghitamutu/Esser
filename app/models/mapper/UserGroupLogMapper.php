<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 21:08
 */

namespace ModelMapper;
use AppModel\Useracc;
use DatabaseConnectivity;


class UserGroupLogMapper
{
    protected $_entityTable = 'USERGROUPLOGS';
    protected $_entityClass = 'UserGroupLog';

    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter)
    {
        parent::__construct($adapter, array(
            'entityTable' => $this->_entityTable,
            'entityClass' => $this->_entityClass
        ));
    }

    /**
     * Create an usergrouplog entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $grouprelation = array(
            'uGLogId'      => $data['UGLOGID'] ? $data['UGLOGID'] : '',
            'uGLogDescription'    => $data['UGLOGDESCRIPTION'] ? $data['UGLOGDESCRIPTION'] : '',
            'uGLogSourceIP'    => $data['UGLOGSOURCEIP'] ? $data['UGLOGSOURCEIP'] : '',
            'uGLogCreatedAt'  => $data['UGLOGCREATEDAT'] ? $data['UGLOGCREATEDAT'] : ''
        );
        return $grouprelation;
    }
}
