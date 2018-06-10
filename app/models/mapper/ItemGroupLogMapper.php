<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:13
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class ItemGroupLogMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMGROUPLOGS';
    protected $_entityClass = 'Itemgrouplog';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $grouprelation = new $this->_entityClass(array(
            'IGLOGID'      => $data['iGLogId'],
            'IGLOGDESCRIPTION'    => $data['IGLogDescription'],
            'IGLOGSOURCEIP'    => $data['iGLogSourceIP'],
            'IGLOGCREATEDAT'  => $data['iGLogCreatedAt']
        ));
        return $grouprelation;
    }
}