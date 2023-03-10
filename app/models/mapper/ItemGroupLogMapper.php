<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:13
 */

namespace ModelMapper;
use AppModel;
use DatabaseConnectivity;

class ItemGroupLogMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMGROUPLOGS';
    protected $_entityClass = 'Itemgrouplog';

    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter)
    {
        parent::__construct($adapter, array(
            'entityTable' => $this->_entityTable,
            'entityClass' => $this->_entityClass
        ));
    }
    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $grouprelation = array(
            'iGLogId'   => array_key_exists('IGLOGID', $data) ? $data['IGLOGID'] : '',
            'IGLogDescription'   => array_key_exists('IGLOGDESCRIPTION', $data) ? $data['IGLOGDESCRIPTION'] : '',
            'iGLogSourceIP'   => array_key_exists('IGLOGSOURCEIP', $data) ? $data['IGLOGSOURCEIP'] : '',
            'iGLogCreatedAt' => array_key_exists('IGLOGCREATEDAT', $data) ? $data['IGLOGCREATEDAT'] : ''
        );
        return $grouprelation;
    }
}
