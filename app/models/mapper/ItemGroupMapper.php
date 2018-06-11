<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:13
 */

namespace ModelMapper;
use AppModel\Useracc;
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
            'iGroupId'      => $data['IGROUPID'] ? $data['IGROUPID'] : '',
            'iGroupName'    => $data['IGROUPNAME'] ? $data['IGROUPNAME'] : '',
            'iGroupDescription'    => $data['IGROUPDESCRIPTION'] ? $data['IGROUPDESCRIPTION'] : '',
            'iGroupCreatedAt'  => $data['IGROUPCREATEDAT'] ? $data['IGROUPCREATEDAT'] : '',
            'iGroupUpdatedAt'  => $data['IGROUPUPDATEDAT'] ? $data['IGROUPUPDATEDAT'] : ''
        );
        return $itemgroup;
    }
}
