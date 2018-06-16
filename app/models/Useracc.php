<?php

namespace AppModel;

use DatabaseConnectivity\DatabaseAdapterInterface;
use ModelMapper;

class Useracc extends AbstractEntity
{
    protected $_allowedFields =
        array
        (
            'userId',
            'userName',
            'userEmail',
            'userPass',
            'userType',
            'userState',
            'userImage',
            'userCreatedAt',
            'userUpdatedAt'
        );

    public $mapper = null;

    public function __construct(DatabaseAdapterInterface $adapter)
    {
        parent::__construct($this->_allowedFields);
        $this->mapper = new ModelMapper\UseraccMapper($adapter);
    }

    public function get_mapper()
    {
        return $this->mapper;
    }
}
