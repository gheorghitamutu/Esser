<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 21:09
 */

namespace ModelMapper;
use AppModel\Useracc;
use DatabaseConnectivity;

class ItemLogMapper
{
    protected $_entityTable = 'ITEMLOGS';
    protected $_entityClass = 'ItemLogs';

    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter)
    {
        parent::__construct($adapter, array(
            'entityTable' => $this->_entityTable,
            'entityClass' => $this->_entityClass
        ));
    }

    /**
     * Create an itemlog entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $grouprelation = array(
            'iLogId'      => $data['ILOGID'] ? $data['ILOGID'] : '',
            'iLogDescription'    => $data['ILOGDESCRIPTION'] ? $data['ILOGDESCRIPTION'] : '',
            'iLogSourceIP'    => $data['ILOGSOURCEIP'] ? $data['ILOGSOURCEIP'] : '',
            'iLogCreatedAt'  => $data['ILOGCREATEDAT'] ? $data['ILOGCREATEDAT'] : ''
        );
        return $grouprelation;
    }
}

