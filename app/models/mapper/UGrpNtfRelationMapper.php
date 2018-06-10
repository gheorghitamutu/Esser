<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:14
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class UGrpNtfRelationMapper extends AbstractMapper
{
    protected $_entityTable = 'UGRPNTFRELATIONS';
    protected $_entityClass = 'Ugrpntfrelation';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $ugrpntfrelation = new $this->_entityClass(array(
            'USRGNRELATIONID'     => $data['usrGNRelationId'],
            'USRGNNOTIFICATIONID'   => $data['usrGNNotificationId'],
            'USRGNNOTIFIEDGROUPID'   => $data['usrGNNotifiedGroupId']
        ));
        return $ugrpntfrelation;
    }
}