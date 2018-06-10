<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 18:34
 */

namespace ModelMapper;
use DatabaseConnectivity, ModelProxy, OCI_Collection;

class UseraccMapper extends AbstractMapper
{
    protected $_somethingThatMadeTheUser;//(Exists???)
    protected $_entityTable = 'USERACCS';
    protected $_entityClass = 'UserAcc';

    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter, SomethingThatMadeTheUser $somethingThatMadeTheUser)
    {
        $this->_somethingThatMadeTheUser = $somethingThatMadeTheUser;
        parent::__construct($adapter);
    }

    /**
     * Reconstitute an entity with the data retrieved from the storage (implementation delegated to concrete mappers)
     */
    protected function _createEntity($data)
    {
        // TODO: Implement _createEntity() method.
    }
}