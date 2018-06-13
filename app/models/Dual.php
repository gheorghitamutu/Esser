<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/11/2018
 * Time: 19:50
 */

namespace AppModel;
use DatabaseConnectivity\DatabaseAdapterInterface;
use ModelMapper;
use InvalidArgumentException;

class Dual extends AbstractEntity
{
    protected $_allowedFields = array('TIMEZONESTAMP', 'TIMEZONENAME');
    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\DualMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }

//    public function getTimezone() {
//        return $this->_allowedFields['TIMEZONE'];
//    }
//
//    public function getSysDateTime() {
//        return $this->_allowedFields['SYSDATETIME'];
//    }
}