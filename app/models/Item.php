<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use InvalidArgumentException;
use ModelMapper;

class Item extends AbstractEntity
{
    protected $_allowedFields = array('itemId','itemName','itemDescription','itemQuantity','iGroupId','iWarnQnty','itemImage','itemCreatedAt','itemUpdatedAt');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\Item($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }

    /**
     * Set the entry ID
     * @param bool $id = false Method implemented but should not be used. Id's are generated automatically and should remain the value generated!
     */
    public function setId($id = false)
    {
        if (!$id) {
            $this->_values['itemId'] = null;
        }
        else {
            if(!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
                throw new InvalidArgumentException('Item id can only be between 1 and 999999999!');
            }
            $this->_values['itemId'] = null;
        }
    }

    public function setName($name) {
        if (!is_string($name) ) {
            throw new InvalidArgumentException('Item name input should only be in string format!');
        }
        if (preg_match('/a-zA-Z0-9._ -]+/', $name) !== 0){
            throw new InvalidArgumentException('The name format is not correct! Only alpha-numeric, \'-\', \'_\', \'.\' and whitespace characters are allowed!');
        }
        if (strlen($name) < 4 ) {
            throw new InvalidArgumentException('Item name length should be greater than 4! Current length is: ' . strlen($name) .' !');
        }
        if (strlen($name) > 16) {
            throw new InvalidArgumentException('Item name length shouldn\'t be greater than 16! Current length is: ' . strlen($name) .' !');
        }
        $this->_values['itemName'] = $name;
    }

    public function setDescription($description) {
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        if (!is_string($description) ) {
            throw new InvalidArgumentException('Description should only be a string format!');
        }
        if (strlen($description) < 4 ) {
            throw new InvalidArgumentException('Description length should be greater than 4! Current length is: ' . strlen($description) .' !');
        }
        if (strlen($description) > 2000) {
            throw new InvalidArgumentException('Description length shouldn\'t be greater than 2000! Current length is: ' . strlen($description) .' !');
        }
        $this->_values['itemDescription'] = $description;
    }

    public function setQuantity($quantity) {
        if (!filter_var($quantity, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('Item id can only be between 0 and 999999999!');
        }
        $this->_values['itemQuantity'] = $quantity;
    }

    public function setGroupId($groupid) {
        if (!filter_var($groupid, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('Item group id can only be between 1 and 999999999!');
        }
        $this->_values['iGroupId'] = $groupid;
    }

    public function setWarnQnty($warnqnty = false) {
        if ($warnqnty && !filter_var($warnqnty, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('Item warn quantity can only be null or between 0 and 999999999!');
        }
        $this->_values['iWarnQnty'] = $warnqnty;
    }

    public function setImage($image) {
        if (!is_string($image)) {
            throw new InvalidArgumentException('Item image must be either \'undefined\' or a path towards an existing image file!');
        }
        $this->_values['itemImage'] = $image;
    }

    public function setCreatedAt($date = false) {
        if (!$date) {
            $this->_values['itemCreatedAt'] = null;
        }
        else {
            $this->_values['itemId'] = null;
        }
    }

    public function setUpdatedAt($date = false) {
        if (!$date) {
            $this->_values['itemUpdatedAt'] = null;
        }
        else {
            $this->_values['itemUpdatedAt'] = null;
        }
    }
}
