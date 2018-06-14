<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:13
 */

namespace ModelMapper;
use AppModel;
use DatabaseConnectivity;

class ItemGroupMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMGROUPS';
    protected $_entityClass = 'Itemgroup';

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
        $itemgroup = array(
            'iGroupId'      => array_key_exists('IGROUPID', $data) ? $data['IGROUPID'] : '',
            'iGroupName'    => array_key_exists('IGROUPNAME', $data) ? $data['IGROUPNAME'] : '',
            'iGroupDescription'    => array_key_exists('IGROUPDESCRIPTION', $data) ? $data['IGROUPDESCRIPTION'] : '',
            'iGroupCreatedAt'  => array_key_exists('IGROUPCREATEDAT', $data) ? $data['IGROUPCREATEDAT'] : '',
            'iGroupUpdatedAt'  => array_key_exists('IGROUPUPDATEDAT', $data) ? $data['IGROUPUPDATEDAT'] : ''
        );
        return $itemgroup;
    }
}
