<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:14
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class ItemGroupOwnerShipMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMGROUPOWNERSHIPS';
    protected $_entityClass = 'Itemgroupownership';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $itmgrpown = new $this->_entityClass(array(
            'IGOWNERSHIPID'     => $data['iGOwnershipId'],
            'IGOWNERID'         => $data['iGOwnerId'],
            'IGID'              => $data['iGId']
        ));
        return $itmgrpown;
    }
}