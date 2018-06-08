<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 17:05
 */

namespace ModelMapper;
use DatabaseConnectivity, ModelProxy, OCI_Collection,
    http\Exception\InvalidArgumentException,
    http\Exception\RuntimeException;

abstract class AbstractMapper implements MapperInterface
{
    protected $_adapter;
    protected $_entityTable;
    protected $_entityClass;

    /**
     * Constructor
     */
    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter, array $entityOptions = array())
    {
        $this->_adapter = $adapter;
        //setting the entity table to the specified option
        if(isset($entityOptions['entityTable'])) {
            $this->setEntityTable($entityOptions['entityTable']);
        }
        //checking entity options
        $this->_checkEntityOptions();
    }


    /**
     * method to check the entity options
     */
    protected function _checkEntityOptions()
    {
        if(!isset($this->_entityTable)) {
            throw new RuntimeException('The entity table has been not set yet!');
        }
        if (!isset($this->_entityClass)) {
            throw new RuntimeException('The entity class has been not set yet!');
        }
    }

    /**
     * Get DB adapter
     */
    public function getAdapter() {
        return $this->_adapter;
    }

    public function setEntityTable($entityTable) {
        if(!is_string($entityTable) || empty($entityTable)){
            throw new InvalidArgumentException('The entity table is invalid!');
        }
        $this->_entityTable = $entityTable;
        return $this;
    }

    public function getEntityTable(){
        return $this->_entityTable;
    }

    public function setEntityClass($entityClass) {
        if(!is_subclass_of($entityClass,'ModelAbstractEntity')){
            throw new InvalidArgumentException('The entity class is invalid!');
        }
        $this->_entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass() {
        return $this->_entityClass;
    }

    /**
     * Reconstitute an entity with the data retrieved from the storage (implementation delegated to concrete mappers)
     */
    abstract protected function _createEntity($data);

    /**
     * Find an entity by its ID
     */
    public function findById($id)
    {
        $selectStatement = $this->_adapter->select($this->_entityTable, "id = $id");
        if ($data = $this->_adapter->fetch($selectStatement)) {
            return $this->_createEntity($data);
        }
        return null;
    }

    /**
     * Find entities according to the given criteria (all entities will be fetched if no criteria are specified)
     */
    public function find($criteria = '')
    {
        $collection = new OCI_Collection();
        $selectStatement = $this->_adapter->select($this->_entityTable, $criteria);
        while ($data = $this->_adapter->fetch($selectStatement)) {
            $collection[] = $this->_createEntity($data);
        }
        return $collection;
    }

    public function insert($entity)
    {
        if (!$entity instanceof $this->_entityClass) {
            throw new InvalidArgumentException('The entity that needs to be inserted must be an instance of ' . $this->_entityClass . '!');
        }
        return $this->_adapter->insert($this->_entityTable, $entity->toArray());
    }

    public function update($entity)
    {
        if (!$entity instanceof $this->_entityClass) {
            throw new InvalidArgumentException('The entity that needs to be updated must be an instance of ' . $this->_entityClass . '!');
        }
        $id = $entity->id;
        $data = $entity->toArray();
        unset($data['id']);
        return $this->_adapter->update($this->_entityTable, $data, "id = $id");
    }

    public function delete($id, $col = 'id')
    {
        if ($id instanceof $this->_entityClass) {
            $id = $id->id;
        }
        return $this->_adapter->delete($this->_entityTable, "$col = $id");
    }

}