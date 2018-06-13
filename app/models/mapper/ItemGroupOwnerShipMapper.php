<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:14
 */

namespace ModelMapper;
use AppModel;
use DatabaseConnectivity;

class ItemGroupOwnerShipMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMGROUPOWNERSHIPS';
    protected $_entityClass = 'Itemgroupownership';

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
        $itmgrpown = array(
            'iGOwnershipId'     =>  array_key_exists('IGOWNERSHIPID', $data) ? $data['IGOWNERSHIPID'] : '',
            'iGOwnerId'         =>  array_key_exists('IGOWNERID', $data) ? $data['IGOWNERID'] : '',
            'iGId'              =>  array_key_exists('IGID', $data) ? $data['IGID'] : ''
        );
        return $itmgrpown;
    }
}
