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

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $ugrpntfrelation = array(
            'usrGNRelationId'     => $data['USRGNRELATIONID'],
            'usrGNNotificationId'   => $data['USRGNNOTIFICATIONID'],
            'usrGNNotifiedGroupId'   => $data['USRGNNOTIFIEDGROUPID']
        );
        return $ugrpntfrelation;
    }
}
