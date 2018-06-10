<?php

namespace AppModel;
use InvalidArgumentException;

class Usrntfrelation extends AbstractEntity
{
    protected $_allowedFields = array('usrNRelationId', 'usrNNotifiedAccId', 'usrNNotificationId');

    public function setId($id = false) {
        if (!$id) {
            $this->_values['usrNRelationId'] = null;
        }
        else {
            $this->_values['usrNRelationId'] = null;
        }
    }

    public function setNotificationId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('Specified notification id for user-notification relation must be between 1 and 999999999!');
        }
        $this->_values['usrNNotificationId'] = $id;
    }

    public function setNotifiedId($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
            throw new InvalidArgumentException('Specified item id for user-notification relation for this notification must be between 1 and 999999999!');
        }
        $this->_values['usrNNotifiedAccId'] = $id;
    }
}