<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:13
 */

namespace ModelMapper;
use AppModel\Useracc;
use DatabaseConnectivity;

class ItemGroupLogMapper extends AbstractMapper
{
    protected $_entityTable = 'ITEMGROUPLOGS';
    protected $_entityClass = 'Itemgrouplog';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $grouprelation = array(
            'iGLogId'   =>  $data['IGLOGID'],
            'IGLogDescription'    => $data['IGLOGDESCRIPTION'],
            'iGLogSourceIP'    => $data['IGLOGSOURCEIP'],
            'iGLogCreatedAt'  => $data['IGLOGCREATEDAT']
        );
        return $grouprelation;
    }
}
