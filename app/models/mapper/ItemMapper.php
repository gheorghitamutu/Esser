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
            'itemId'            =>  array_key_exists('ITEMID', $data) ? $data['ITEMID'] : '',
            'itemDescription'   =>  array_key_exists('ITEMDESCRIPTION', $data) ? $data['ITEMDESCRIPTION'] : '',
            'itemQuantity'      =>  array_key_exists('ITEMQUANTITY', $data) ? $data['ITEMQUANTITY'] : '',
            'iGroupId'          =>  array_key_exists('IGROUPID', $data) ? $data['IGROUPID'] : '',
            'iWarnQnty'         =>  array_key_exists('IWARNQNTY', $data) ? $data['IWARNQNTY'] : '',
            'itemImage'         =>  array_key_exists('ITEMIMAGE', $data) ? $data['ITEMIMAGE'] : '',
            'itemCreatedAt'     =>  array_key_exists('ITEMCREATEDAT', $data) ? $data['ITEMCREATEDAT'] : '',
            'itemUpdatedAt'     =>  array_key_exists('ITEMUPDATEDAT', $data) ? $data['ITEMUPDATEDAT'] : ''
        );
        return $useracc;
    }
}
