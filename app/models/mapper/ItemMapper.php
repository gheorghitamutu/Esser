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
        $useracc = array(
            'itemId'            => $data['ITEMID'] ? $data['ITEMID'] : '',
            'itemDescription'   => $data['ITEMDESCRIPTION'] ? $data['ITEMDESCRIPTION'] : '',
            'itemQuantity'      => $data['ITEMQUANTITY'] ? $data['ITEMQUANTITY'] : '',
            'iGroupId'          => $data['IGROUPID'] ? $data['IGROUPID'] : '',
            'iWarnQnty'         => $data['IWARNQNTY'] ? $data['IWARNQNTY'] : '',
            'itemImage'         => $data['ITEMIMAGE'] ? $data['ITEMIMAGE'] : '',
            'itemCreatedAt'     => $data['ITEMCREATEDAT'] ? $data['ITEMCREATEDAT'] : '',
            'itemUpdatedAt'     => $data['ITEMUPDATEDAT'] ? $data['ITEMUPDATEDAT'] : ''
        );
        return $useracc;
    }
}
