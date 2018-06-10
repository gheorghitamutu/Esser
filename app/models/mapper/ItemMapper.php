<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 18:34
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class ItemMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMS';
    protected $_entityClass = 'Item';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $useracc = new $this->_entityClass(array(
            'itemId'            => $data['itemId'],
            'itemDescription'   => $data['itemDescription'],
            'itemQuantity'      => $data['itemQuantity'],
            'iGroupId'          => $data['iGroupId'],
            'iWarnQnty'         => $data['iWarnQnty'],
            'itemImage'         => $data['itemImage'],
            'itemCreatedAt'     => $data['itemCreatedAt'],
            'itemUpdatedAt'     => $data['itemUpdatedAt'],
            'userUpdatedAt'     => $data['userUpdatedAt']
        ));
        return $useracc;
    }
}