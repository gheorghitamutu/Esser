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

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $itemgroup = array(
            'iGroupId'      => $data['IGROUPID'],
            'iGroupName'    => $data['IGROUPNAME'],
            'iGroupDescription'    => $data['IGROUPDESCRIPTION'],
            'iGroupCreatedAt'  => $data['IGROUPCREATEDAT'],
            'iGroupUpdatedAt'  => $data['IGROUPUPDATEDAT'],
        );
        return $itemgroup;
    }
}
