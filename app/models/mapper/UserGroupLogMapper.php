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

    /**
     * Create an usergrouplog entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $grouprelation = array(
            'uGLogId'      => $data['UGLOGID'],
            'uGLogDescription'    => $data['UGLOGDESCRIPTION'],
            'uGLogSourceIP'    => $data['UGLOGSOURCEIP'],
            'uGLogCreatedAt'  => $data['UGLOGCREATEDAT']
        );
        return $grouprelation;
    }
}
