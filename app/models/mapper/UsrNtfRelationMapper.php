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

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $usrntfrelation = new $this->_entityClass(array(
            'usrNtfRelationId'     => $data['USRNRELATIONID'],
            'usrNNotifiedAccId'   => $data['USRNNOTIFIEDACCID'],
            'usrNNotificationId'   => $data['USRNNOTIFICATIONID']
        ));
        return $usrntfrelation;
    }
}
