<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use ModelMapper;

class UserLog extends AbstractEntity
{
    protected $_allowedFields =
        array
        (
            'uLogId',
            'uLogDescription',
            'uLogSourceIP',
            'uLogCreatedAt'
        );

    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\UserLogMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }


//    public function setId($id = false) {
//        if (!$id) {
//            $this->_values['uLogId'] = null;
//        }
//        else {
//            $this->_values['uLogId'] = null;
//        }
//    }
//
//    public function setDescription($description) {
//        $description = filter_var($description, FILTER_SANITIZE_STRING);
//        if (!is_string($description) ) {
//            throw new InvalidArgumentException('User group log description should only be a string format!');
//        }
//        if (strlen($description) < 4 ) {
//            throw new InvalidArgumentException('User group log description length should be greater than 4! Current length is: ' . strlen($description) .' !');
//        }
//        if (strlen($description) > 2000) {
//            throw new InvalidArgumentException('User group log description length shouldn\'t be greater than 2000! Current length is: ' . strlen($description) .' !');
//        }
//        $this->_values['uLogDescription'] = $description;
//    }
//
//    public function setSourceIP($ip) {
//        if (!filter_var($ip, FILTER_VALIDATE_IP) || !is_string($ip)) {
//            throw new InvalidArgumentException('IP input is in a bad format!');
//        }
//        $this->_values['uLogSourceIP'] = $ip;
//    }
//    public function setCreatedAt($date = false) {
//        if (!$date) {
//            $this->_values['uLogCreatedAt'] = null;
//        }
//        else {
//            $this->_values['uLogCreatedAt'] = null;
//        }
//    }
}