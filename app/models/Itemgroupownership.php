<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use ModelMapper;
use InvalidArgumentException;

class Itemgroupownership extends AbstractEntity
{
    protected $_allowedFields = array('iGOwnershipId', 'iGOwnerId', 'iGId');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\ItemGroupOwnerShipMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }

//    public function setId($id  = false) {
//        if (!$id) {
//            $this->_values['iGOwnershipId'] = null;
//        }
//        else {
//            $this->_values['iGOwnershipId'] = null;
//        }
//    }
//
//    public function setOwnerId($id) {
//        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
//            throw new InvalidArgumentException('The owner user group id should be a int value between 1 and 999999999');
//        }
//        $this->_values['iGOwnershipId'] = $id;
//    }
//
//    public function setGrouId($id) {
//        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
//            throw new InvalidArgumentException('The owned item group id should be a int value between 1 and 999999999');
//        }
//        $this->_values['iGOwnershipId'] = $id;
//    }
}
