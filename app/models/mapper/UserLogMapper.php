<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:15
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class UserLogMapper extends AbstractMapper
{
    protected $_entityTable = 'USERLOGS';
    protected $_entityClass = 'UserLog';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $userlog = new $this->_entityClass(array(
            'ULOGID'     => $data['uLogId'],
            'ULOGDESCRIPTION'   => $data['uLogDescription'],
            'ULOGSOURCEIP'   => $data['uLogSourceIP'],
            'ULOGCREATEDAT'   => $data['uLogCreatedAt']
        ));
        return $userlog;
    }
}