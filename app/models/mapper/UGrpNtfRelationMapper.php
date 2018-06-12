<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:14
 */

namespace ModelMapper;

use AppModel\Useracc;
use DatabaseConnectivity;

class UGrpNtfRelationMapper extends AbstractMapper
{
    protected $_entityTable = 'UGRPNTFRELATIONS';
    protected $_entityClass = 'Ugrpntfrelation';

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
        $ugrpntfrelation = array(
            'usrGNRelationId'     => array_key_exists('USRGNRELATIONID', $data) ? $data['USRGNRELATIONID'] : '',
            'usrGNNotificationId'   => array_key_exists('USRGNNOTIFICATIONID', $data) ? $data['USRGNNOTIFICATIONID'] : '',
            'usrGNNotifiedGroupId'   => array_key_exists('USRGNNOTIFIEDGROUPID', $data) ? $data['USRGNNOTIFIEDGROUPID'] : ''
        );
        return $ugrpntfrelation;
    }
}
