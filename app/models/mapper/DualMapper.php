<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/11/2018
 * Time: 19:47
 */

namespace ModelMapper;
use AppModel;
use DatabaseConnectivity;


class DualMapper extends AbstractMapper
{
    protected $_entityTable = 'DUAL';
    protected $_entityClass = 'Dual';

    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter)
    {
        parent::__construct($adapter, array(
            'entityTable' => $this->_entityTable,
            'entityClass' => $this->_entityClass
        ));
    }

    public function findAll($criteria = '', $fields = false, $order = false, $limit = false)
    {
        return self::find();
    }

    public function findById($id)
    {
        return self::find();
    }

    protected function find() {
        $selectstmt = $this->_adapter->select($this->_entitytable,'',
            $fields = "TO_CHAR(SYSDATE, 'DD-MM-YYYY HH24:MI:SS') , TZ_OFFSET(SESSIONTIMEZONE)");
        if (($data = $this->_adapter->fetchRow($selectstmt)) !== false) {
            $result = $this->_createEntity($data);
            $this->_adapter->disconnect();
            return $result;
        }
        $this->_adapter->disconnect();
        return null;
    }
    /**
     * Create an useracc entity with the supplied data
     * @param array $data
     * @return array
     */
    protected function _createEntity(array $data)
    {
        $dual = array(
            'sysdatetime' => $data['0'] ? $data['0'] : '',
            'timezonestamp' => $data['1'] ? $data['1'] : ''
        );
        return $dual;
    }

}