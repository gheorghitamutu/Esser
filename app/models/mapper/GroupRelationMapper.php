<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:12
 */

namespace ModelMapper;
use AppModel\Useracc;
use DatabaseConnectivity;

class GroupRelationMapper extends AbstractMapper
{
    protected $_entityTable = 'GROUPRELATIONS';
    protected $_entityClass = 'Grouprelation';

    /**
     * Create an grouprelation entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $grouprelation = array(
            'relationId'      => $data['RELATIONID'],
            'userId'    => $data['USERID'],
            'uGroupId'    => $data['UGROUPID'],
            'canUpdItm'  => $data['CANUPDITM'],
            'canMngMbs'    => $data['CANMNGMBS'],
            'grpRelCreatedAt'    => $data['GRPRELCREATEDAT'],
            'grpRelUpdatedAt'    => $data['GRPRELUPDATEDAT']
        );
        return $grouprelation;
    }
}
