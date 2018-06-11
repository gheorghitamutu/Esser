<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 17:05
 */

namespace ModelMapper;

use AppModel\Useracc;
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
        if(!isset($this->_entitytable)) {
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
        if(!is_string($entitytable) || empty($entitytable)){
            throw new \InvalidArgumentException('The entity table is invalid!');
        }
        $this->_entitytable = $entitytable;
        return $this;
    }

    public function getEntityTable(){
        return $this->_entitytable;
    }

    public function setEntityClass($entityclass) {

        //if(!is_subclass_of((string)$entityclass,'AbstractEntity')){
        //    throw new \InvalidArgumentException('The entity class is invalid!');
        //}
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
     * @return array|OCI_Collection(not available - yet)findAll
     */
    public function findAll($criteria = '', $fields = false, $order=false, $limit=false)
    {
        $selectstmt = $this->_adapter->select($this->_entitytable, $criteria, $fields ? $fields : '*', $order ? $order : '', $limit ? $limit : '');

        $collection = array();

        if(($data = $this->_adapter->fetchAll($selectstmt)) !== false)
        {
            for ($i = 0; $i < count($data); ++$i){
                $collection[$i] = $this->_createEntity($data[$i]);
            }
        }
        $this->_adapter->disconnect();
        return $collection;
    }

    public function countAll($criteria = '')
    {
        $selectstmt = $this->_adapter->selectCount($this->_entitytable, $criteria);
        if (($data = $this->_adapter->fetch($selectstmt)) !== false) {
            $result = $this->_adapter->getResult($selectstmt,1);
        }
        else {
            $result = 0;
        }
        $this->_adapter->disconnect();
        return $result;
    }

    public function insert($entity)
    {
        if (!$entity instanceof $this->_entityclass) {
            throw new \InvalidArgumentException('The entity that needs to be inserted must be an instance of ' . $this->_entityclass . '!');
        }
        $result = $this->_adapter->insert($this->_entitytable, $entity->toArray());
        $this->_adapter->disconnect();
        return $result;
    }

    public function update($entity)
    {
        if (!$entity instanceof $this->_entityclass) {
            throw new \InvalidArgumentException('The entity that needs to be updated must be an instance of ' . $this->_entityclass . '!');
        }
        $id = $entity->id;
        $data = $entity->toArray();
        unset($data['id']);
        $result = $this->_adapter->update($this->_entitytable, $data, "id = $id");
        $this->_adapter->disconnect();
        return $result;
    }

    public function delete($id, $col = 'id')
    {
        if ($id instanceof $this->_entityclass) {
            $id = $id->id;
        }
        $result = $this->_adapter->delete($this->_entitytable, "$col = $id");
        $this->_adapter->disconnect();
        return $result;
    }
}
