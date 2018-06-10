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
            'uLogId'     => $data['ULOGID'],
            'uLogDescription'   => $data['ULOGDESCRIPTION'],
            'uLogSourceIP'   => $data['ULOGSOURCEIP'],
            'uLogCreatedAt'   => $data['ULOGCREATEDAT']
        ));
        return $userlog;
    }
}
