<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:13
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class ItemGroupMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMGROUPS';
    protected $_entityClass = 'Itemgroup';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $itemgroup = new $this->_entityClass(array(
            'IGROUPID'      => $data['iGroupId'],
            'IGROUPNAME'    => $data['iGroupName'],
            'IGROUPDESCRIPTION'    => $data['iGroupDescription'],
            'IGROUPCREATEDAT'  => $data['iGroupCreatedAt'],
            'IGROUPUPDATEDAT'  => $data['iGroupUpdatedAt'],
        ));
        return $itemgroup;
    }
}