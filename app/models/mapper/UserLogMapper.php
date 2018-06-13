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
     * @param array $data
     * @return array
     */
    protected function _createEntity(array $data)
    {
        $userlog =
            array
            (
                'uLogId'            => array_key_exists ('ULOGID',          $data) ? $data['ULOGID'] : '',
                'uLogDescription'   => array_key_exists ('ULOGDESCRIPTION', $data) ? $data['ULOGDESCRIPTION'] : '',
                'uLogSourceIP'      => array_key_exists ('ULOGSOURCEIP',    $data) ? $data['ULOGSOURCEIP'] : '',
                'uLogCreatedAt'     => array_key_exists ('ULOGCREATEDAT',   $data) ? $data['ULOGCREATEDAT'] : ''
            );

        return $userlog;
    }
}
