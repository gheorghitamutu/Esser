<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use ModelMapper;
use InvalidArgumentException;

class Usergroup extends AbstractEntity
{
    protected $_allowedFields =
        array('uGroupId', 'uGroupName', 'uGroupDescription', 'nrOfMembers',
        'nrOfManagers', 'uGroupCreatedAt', 'uGroupUpdatedAt');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\UserGroupMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }


    public function setId($id = false) {
        if (!$id) {
            $this->_values['uGroupId'] = null;
        }
        else {
            $this->_values['uGroupId'] = null;
        }
    }

    public function setName($name) {
        if (!is_string($name) ) {
            throw new InvalidArgumentException('Group name input should only be in string format!');
        }
        if (preg_match('/[^a-zA-Z0-9._ -]+/', $name) !== 0){
            throw new InvalidArgumentException('The group name format is not correct! Only alpha-numeric, \'-\', \'_\', \'.\' and whitespace characters are allowed!');
        }
        if (strlen($name) < 4 ) {
            throw new InvalidArgumentException('Group name length should be greater than 4! Current length is: ' . strlen($name) .' !');
        }
        if (strlen($name) > 48) {
            throw new InvalidArgumentException('Group name length shouldn\'t be greater than 48! Current length is: ' . strlen($name) .' !');
        }
        $this->_values['uGroupName'] = $name;
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
        $this->_values['uGroupDescription'] = $description;
    }

    public function setNrOfMembers($nr) {
        if (!filter_var($nr, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('The nrOfMembers field should be a value only between 0 and 999999999!');
        }
        $this->_values['nrOfMembers'] = $nr;
    }

    public function setNrOfManagers($nr) {
        if (!filter_var($nr, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('The nrOfManagers field should be a value only between 0 and 999999999!');
        }
        $this->_values['nrOfManagers'] = $nr;
    }

    public function setCreatedAt($date = false) {
        if(!$date) {
            $this->_values['uGroupCreatedAt'] = null;
        }
        else {
            $this->_values['uGroupCreatedAt'] = null;
        }
    }

    public function setUpdatedAt($date = false) {
        if (!$date) {
            $this->_values['uGroupUpdatedAt'] = null;
        } else {
            $this->_values['uGroupUpdatedAt'] = null;

        }
    }
}