<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use ModelMapper;
use InvalidArgumentException;

class Ugrpntfrelation extends AbstractEntity
{
    protected $_allowedFields = array('usrGrpRelationId', 'usrGNNotificationId', 'usrGNNotifiedGroupId');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\UGrpNtfRelationMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }


//    public function setId($id = false) {
//        if (!$id) {
//            $this->_values['usrGrpRelationId'] = null;
//        }
//        else {
//            $this->_values['usrGrpRelationId'] = null;
//        }
//    }
//
//    public function setNotificationId($id) {
//        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
//            throw new InvalidArgumentException('Specified notification id for user-group-notification relation must be between 1 and 999999999!');
//        }
//        $this->_values['usrGNNotificationId'] = $id;
//    }
//
//    public function setNotifiedId($id) {
//        if (!filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
//            throw new InvalidArgumentException('Specified item id for user-group-notification relation for this notification must be between 1 and 999999999!');
//        }
//        $this->_values['usrGNNotifiedGroupId'] = $id;
//    }
}