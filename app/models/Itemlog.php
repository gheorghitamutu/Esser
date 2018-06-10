<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use ModelMapper;
use InvalidArgumentException;

class Itemlog extends AbstractEntity
{
    protected $_allowedFields = array('iLogId', 'iLogDescription', 'iLogSourceIP', 'iLogCreatedAt');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\ItemLogMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }

    public function setId($id = false) {
        if (!$id) {
            $this->_values['iLogId'] = null;
        }
        else {
            $this->_values['iLogId'] = null;
        }
    }

    public function setDescription($description) {
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        if (!is_string($description) ) {
            throw new InvalidArgumentException('Item log description should only be a string format!');
        }
        if (strlen($description) < 4 ) {
            throw new InvalidArgumentException('Item log description length should be greater than 4! Current length is: ' . strlen($description) .' !');
        }
        if (strlen($description) > 2000) {
            throw new InvalidArgumentException('Item log description length shouldn\'t be greater than 2000! Current length is: ' . strlen($description) .' !');
        }
        $this->_values['iLogDescription'] = $description;
    }

    public function setSourceIP($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP) || !is_string($ip)) {
            throw new InvalidArgumentException('Item log source IP input is in a bad format!');
        }
        $this->_values['iLogSourceIP'] = $ip;
    }
    public function setCreatedAt($date = false) {
        if (!$date) {
            $this->_values['iLogCreatedAt'] = null;
        }
        else {
            $this->_values['iLogCreatedAt'] = null;
        }
    }

}