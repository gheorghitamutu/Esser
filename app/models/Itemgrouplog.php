<?php

namespace AppModel;

use InvalidArgumentException;

class Itemgrouplog extends AbstractEntity
{
    protected $_allowedFields = array('iGLogId', 'iGLogDescription', 'iGLogSourceIP', 'iGLogCreatedAt');

    public function setId($id = false) {
        if (!$id) {
            $this->_values['iGLogId'] = null;
        }
        else {
            $this->_values['iGLogId'] = null;
        }
    }

    public function setDescription($description) {
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        if (!is_string($description) ) {
            throw new InvalidArgumentException('Group log description should only be a string format!');
        }
        if (strlen($description) < 4 ) {
            throw new InvalidArgumentException('Group log description length should be greater than 4! Current length is: ' . strlen($description) .' !');
        }
        if (strlen($description) > 2000) {
            throw new InvalidArgumentException('Group log description length shouldn\'t be greater than 2000! Current length is: ' . strlen($description) .' !');
        }
        $this->_values['iGroupDescription'] = $description;
    }

    public function setSourceIP($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP) || !is_string($ip)) {
            throw new InvalidArgumentException('IP input is in a bad format!');
        }
        $this->_values['iGLogSourceIP'] = $ip;
    }
    public function setCreatedAt($date = false) {
        if (!$date) {
            $this->_values['iGLogCreatedAt'] = null;
        }
        else {
            $this->_values['iGLogCreatedAt'] = null;
        }
    }
}
