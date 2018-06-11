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
        $userlog = array(
            'uLogId'     => $data['ULOGID'] ? $data['ULOGID'] : '',
            'uLogDescription'   => $data['ULOGDESCRIPTION'] ? $data['ULOGDESCRIPTION'] : '',
            'uLogSourceIP'   => $data['ULOGSOURCEIP'] ? $data['ULOGSOURCEIP'] : '',
            'uLogCreatedAt'   => $data['ULOGCREATEDAT'] ? $data['ULOGCREATEDAT'] : ''
        );
        return $userlog;
    }
}
