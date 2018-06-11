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

class ItemGroupOwnerShipMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMGROUPOWNERSHIPS';
    protected $_entityClass = 'Itemgroupownership';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $itmgrpown = array(
            'iGOwnershipId'     => $data['IGOWNERSHIPID'],
            'iGOwnerId'         => $data['IGOWNERID'],
            'iGId'              => $data['IGID']
        );
        return $itmgrpown;
    }
}
