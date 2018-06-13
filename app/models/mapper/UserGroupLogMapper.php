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


class UserGroupLogMapper extends AbstractMapper
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
            'uGLogId'      => array_key_exists('UGLOGID', $data) ? $data['UGLOGID'] : '',
            'uGLogDescription'    => array_key_exists('UGLOGDESCRIPTION', $data) ? $data['UGLOGDESCRIPTION'] : '',
            'uGLogSourceIP'    => array_key_exists('UGLOGSOURCEIP', $data) ? $data['UGLOGSOURCEIP'] : '',
            'uGLogCreatedAt'  => array_key_exists('UGLOGCREATEDAT', $data) ? $data['UGLOGCREATEDAT'] : ''
        );
        return $grouprelation;
    }
}
