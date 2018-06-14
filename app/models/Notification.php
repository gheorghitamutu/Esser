<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use ModelMapper;
use InvalidArgumentException;

class Notification extends AbstractEntity
{
    protected $_allowedFields = array('ntfId', 'nItemId', 'ntfType', 'ntfDscrp', 'ntfCreatedAt');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\NotificationMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }


//    public function setId($id = false) {
//        if (!$id) {
//            $this->_values['ntfId'] = null;
//        }
//        else {
//            $this->_values['ntfId'] = null;
//        }
//    }
//
//    public function setItemId($id) {
//        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
//            throw new InvalidArgumentException('Specified item id for this notification must be between 1 and 999999999!');
//        }
//            $this->_values['nItemId'] = $id;
//    }
//
//    public function setType($type) {
//        if (!is_string($type) ||
//            (strlen($type) === 4 && preg_match('/(user)/', $type) === 0) ||
//            (strlen($type) === 5 && preg_match('/(group)/', $type) === 0) ||
//            (strlen($type) < 4 || strlen($type) > 5)) {
//            throw new InvalidArgumentException('Notification type can only be either "group" or "user" !');
//        }
//        $this->_values['ntfType'] = $type;
//    }
//
//    public function setDescription($description) {
//        $description = filter_var($description, FILTER_SANITIZE_STRING);
//        if (!is_string($description) ) {
//            throw new InvalidArgumentException('Notification description should only be a string format!');
//        }
//        if (strlen($description) < 4 ) {
//            throw new InvalidArgumentException('Notification description length should be greater than 4! Current length is: ' . strlen($description) .' !');
//        }
//        if (strlen($description) > 2000) {
//            throw new InvalidArgumentException('Notification description length shouldn\'t be greater than 2000! Current length is: ' . strlen($description) .' !');
//        }
//        $this->_values['ntfDscrp'] = $description;
//    }
//
//    public function setCreatedAt($date = false) {
//        if (!$date) {
//            $this->_values['ntfCreatedAt'] = null;
//        }
//        else {
//            $this->_values['ntfCreatedAt'] = null;
//        }
//    }
}