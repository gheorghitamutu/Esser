<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 18:34
 */

namespace ModelMapper;
use AppModel\Useracc;
use DatabaseConnectivity;

class ItemMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMS';
    protected $_entityClass = 'Item';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $useracc = array(
            'itemId'            => $data['ITEMID'],
            'itemDescription'   => $data['ITEMDESCRIPTION'],
            'itemQuantity'      => $data['ITEMQUANTITY'],
            'iGroupId'          => $data['IGROUPID'],
            'iWarnQnty'         => $data['IWARNQNTY'],
            'itemImage'         => $data['ITEMIMAGE'],
            'itemCreatedAt'     => $data['ITEMCREATEDAT'],
            'itemUpdatedAt'     => $data['ITEMUPDATEDAT'],
            'userUpdatedAt'     => $data['USERUPDATEDAT']
        );
        return $useracc;
    }
}
