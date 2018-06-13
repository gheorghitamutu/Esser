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
     * @param array $data
     * @return array
     */
    protected function _createEntity(array $data)
    {
        $usrntfrelation =
            array
            (
                'usrNtfRelationId'     => array_key_exists('USRNRELATIONID',        $data) ? $data['USRNRELATIONID'] : '',
                'usrNNotifiedAccId'    => array_key_exists('USRNNOTIFIEDACCID',     $data) ? $data['USRNNOTIFIEDACCID'] : '',
                'usrNNotificationId'   => array_key_exists('USRNNOTIFICATIONID',    $data) ? $data['USRNNOTIFICATIONID'] : ''
            );
        return $usrntfrelation;
    }
}
