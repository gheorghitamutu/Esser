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

class UsrNtfRelationMapper extends AbstractMapper
{
    protected $_entityTable = 'USRNTFRELATIONS';
    protected $_entityClass = 'Usrntfrelation';

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
        $usrntfrelation = new $this->_entityClass(array(
            'usrNtfRelationId'     => $data['USRNRELATIONID'] ? $data['USRNRELATIONID'] : '',
            'usrNNotifiedAccId'   => $data['USRNNOTIFIEDACCID'] ? $data['USRNNOTIFIEDACCID'] : '',
            'usrNNotificationId'   => $data['USRNNOTIFICATIONID'] ? $data['USRNNOTIFICATIONID'] : ''
        ));
        return $usrntfrelation;
    }
}
