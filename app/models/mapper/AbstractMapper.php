<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 17:05
 */

namespace ModelMapper;

use AppModel;
use DatabaseConnectivity;


abstract class AbstractMapper implements MapperInterface
{
    protected $_adapter;
    protected $_entitytable;
    protected $_entityclass;

    /**
     * Constructor
     */
    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter, array $entityOptions = array())
    {
        $this->_adapter = $adapter;
        if(isset($entityOptions['entityTable'])) {
            $this->setEntityTable($entityOptions['entityTable']);
        }
        if(isset($entityOptions['entityClass'])) {
            $this->setEntityClass($entityOptions['entityClass']);
        }
        //checking entity options
        $this->_checkEntityOptions();
    }


    /**
     * method to check the entity options
     */
    protected function _checkEntityOptions()
    {
        if (!isset($this->_entitytable)) {
            throw new \RuntimeException('The entity table has been not set yet!');
        }
        if (!isset($this->_entityclass)) {
            throw new \RuntimeException('The entity class has been not set yet!');
        }
    }

    /**
     * Get DB adapter
     */
    public function getAdapter() {
        return $this->_adapter;
    }

    public function setEntityTable($entitytable) {
        if (!is_string($entitytable) || empty($entitytable))
        {
            new \InternalServerErrorController();
            throw new \InvalidArgumentException('The entity table is invalid!');
        }
        $this->_entitytable = $entitytable;
        return $this;
    }

    public function getEntityTable() {
        return $this->_entitytable;
    }

    public function setEntityClass($entityclass) {

//        if (!is_subclass_of((string)$entityclass,'AbstractEntity')) {
//            throw new \InvalidArgumentException('The entity class is invalid!');
//        }
        $this->_entityclass = $entityclass;
        return $this;
    }

    public function getEntityClass() {
        return $this->_entityclass;
    }

    /**
     * Reconstitute an entity with the data retrieved from the storage (implementation delegated to concrete mappers)
     */
    abstract protected function _createEntity(array $data);

    /**
     * Find an entity by its ID
     */
    public function findById($id)
    {
        $selectstmt = $this->_adapter->select($this->_entitytable, "id = $id");
        if (($data = $this->_adapter->fetchRow($selectstmt)) !== false) {
            $result = $this->_createEntity($data);
            $this->_adapter->disconnect();
            return $result;
        }
        $this->_adapter->disconnect();
        return null;
    }

    /**
     * Find entities according to the given criteria (all entities will be fetched if no criteria are specified)
     * @param string $criteria
     * @param bool $fields
     * @param bool $order
     * @param null $limit
     * @return array|OCI_Collection(not available - yet)findAll
     */
    public function findAll($criteria = '', $fields = false, $order = false, $limit = null)
    {
        $selectstmt =
            $this->_adapter->select(
                $this->_entitytable,
                $criteria,
                ($fields) ? $fields : '*',
                ($order) ? $order : '',
                ($limit) ? $limit : null);
        $collection = array();

        foreach($this->_adapter->fetchAll($selectstmt) as $row)
        {
            array_push($collection, $this->_createEntity($row));
        }

        $this->_adapter->disconnect();

        return $collection;
    }

    public function countAll($criteria = '')
    {
        $result = 0;
        $selectstmt = $this->_adapter->selectCount($this->_entitytable, $criteria);
        if (($data = $this->_adapter->fetch($selectstmt)) !== false)
        {
            $result = $this->_adapter->getResult($selectstmt,1);
        }
        $this->_adapter->disconnect();
        return $result;
    }

    public function insert($table, array $fields)
    {
        if (empty($fields))
        {
            new \InternalServerErrorController();
            throw new \RuntimeException('You\'re calling an insert without anything to insert!');
        }
        $result = $this->_adapter->insert($table, $fields);
        $this->_adapter->disconnect();
        return $result;
    }

    public function update($table, array $fields, $criteria = false)
    {
        if (empty($fields))
        {
            new \InternalServerErrorController();
            throw new \RuntimeException('You\'re calling an update without anything to update!');
        }
        $result = $this->_adapter->update($table, $fields, $criteria);
        $this->_adapter->disconnect();
        return $result;
    }

    public function delete($table, $criteria)
    {
        $result = $this->_adapter->delete($table, $criteria);
        $this->_adapter->disconnect();
        return $result;
    }
}
