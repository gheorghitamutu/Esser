<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use ModelMapper;
use InvalidArgumentException;

class UserGroupLog extends AbstractEntity
{
    protected $_allowedFields = array('uGLogId', 'uGLogDescription', 'uGLogSourceIP', 'uGLogCreatedAt');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\UserGroupLogMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }


    public function setId($id = false) {
        if (!$id) {
            $this->_values['uGLogId'] = null;
        }
        else {
            $this->_values['uGLogId'] = null;
        }
    }

    public function setDescription($description) {
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        if (!is_string($description) ) {
            throw new InvalidArgumentException('User group log description should only be a string format!');
        }
        if (strlen($description) < 4 ) {
            throw new InvalidArgumentException('User group log description length should be greater than 4! Current length is: ' . strlen($description) .' !');
        }
        if (strlen($description) > 2000) {
            throw new InvalidArgumentException('User group log description length shouldn\'t be greater than 2000! Current length is: ' . strlen($description) .' !');
        }
        $this->_values['uGLogDescription'] = $description;
    }

    public function setSourceIP($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP) || !is_string($ip)) {
            throw new InvalidArgumentException('IP input is in a bad format!');
        }
        $this->_values['uGLogSourceIP'] = $ip;
    }
    public function setCreatedAt($date = false) {
        if (!$date) {
            $this->_values['uGLogCreatedAt'] = null;
        }
        else {
            $this->_values['uGLogCreatedAt'] = null;
        }
    }
}