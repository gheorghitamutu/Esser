<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use InvalidArgumentException;
use ModelMapper;

class Grouprelation extends AbstractEntity
{
    protected $_allowedFields = array('relationId','userId','uGroupId','canUpdItm','canMngMbs','grpRelCreatedAt','grpRelUpdatedAt');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\GroupRelationMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }
    public function setId($id = false) {
        if (!$id) {
            $this->_values['relationId'] = null;
        }
        else {
            if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
                throw new InvalidArgumentException('Group relation id can only be between 1 and 999999999!');
            }
            $this->_values['relationId'] = null;
        }
    }

    public function setUserId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('The user id referenced in this group relation can only be between 1 and 999999999!');
        }
        $this->_values['userId'] = $id;
    }

    public function setGroupId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('The usergroup id referenced in this group relation can only be between 1 and 99999999!');
        }
        $this->_values['uGroupId'] = $id;
    }

    public function setCanUpdItem($can) {
        if (!filter_var($can, FILTER_VALIDATE_BOOLEAN)) {
            throw new InvalidArgumentException('The canUpdItem flag can only be true of false!');
        }
        $this->_values['canUpdItem'] = $can ? 1 : 0;
    }

    public function setCanMngMbs($can) {
        if (!filter_var($can, FILTER_VALIDATE_BOOLEAN)) {
            throw new InvalidArgumentException('The canMngMbs flag can only be true of false!');
        }
        $this->_values['canMngMbs'] = $can ? 1 : 0;
    }

    public function setCreatedAt($date = false) {
        if (!$date) {
            $this->_values['grpRelCreatedAt'] = null;
        }
        else {
            $this->_values['grpRelCreatedAt'] = null;
        }
    }

    public function setUpdatedAt($date = false) {
        if (!$date) {
            $this->_values['grpRelUpdatedAt'] = null;
        }
        else {
            $this->_values['grpRelUpdatedAt'] = null;
        }
    }
}

