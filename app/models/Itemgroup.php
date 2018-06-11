<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use InvalidArgumentException;
use ModelMapper;

class Itemgroup extends AbstractEntity
{
    protected $_allowedFields = array('iGroupId','iGroupName','iGroupDescription','iGroupCreatedAt','iGroupUpdatedAt');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\ItemGroupMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }

    public function setId($id = false) {
        if (!$id) {
            $this->_values['iGroupId'] = null;
        }
        else {
            $this->_values['iGroupId'] = null;
        }
    }

    public function setName($name) {
        if (!is_string($name) ) {
            throw new InvalidArgumentException('Group name input should only be in string format!');
        }
        if (preg_match('/[^a-zA-Z0-9._ -]+/', $name) !== 0){
            throw new InvalidArgumentException('The name format is not correct! Only alpha-numeric, \'-\', \'_\', \'.\' and whitespace characters are allowed!');
        }
        if (strlen($name) < 4 ) {
            throw new InvalidArgumentException('Group name length should be greater than 4! Current length is: ' . strlen($name) .' !');
        }
        if (strlen($name) > 48) {
            throw new InvalidArgumentException('Group name length shouldn\'t be greater than 48! Current length is: ' . strlen($name) .' !');
        }
        $this->_values['iGroupName'] = $name;
    }

    public function setDescription($description) {
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        if (!is_string($description) ) {
            throw new InvalidArgumentException('Group description should only be a string format!');
        }
        if (strlen($description) < 4 ) {
            throw new InvalidArgumentException('Group description length should be greater than 4! Current length is: ' . strlen($description) .' !');
        }
        if (strlen($description) > 2000) {
            throw new InvalidArgumentException('Group description length shouldn\'t be greater than 2000! Current length is: ' . strlen($description) .' !');
        }
        $this->_values['iGroupDescription'] = $description;
    }

    public function setCreatedAt($date = false) {
        if (!$date) {
            $this->_values['iGroupCreatedAt'] = null;
        }
        else {
            $this->_values['iGroupCreatedAt'] = null;
        }
    }

    public function setUpdatedAt($date = false) {
        if (!$date) {
            $this->_values['iGroupUpdatedAt'] = null;
        }
        else {
            $this->_values['iGroupUpdatedAt'] = null;
        }
    }
}
