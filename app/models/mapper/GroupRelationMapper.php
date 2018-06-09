<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:12
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class GroupRelationMapper extends AbstractMapper
{
    protected $_entityTable = 'GROUPRELATIONS';
    protected $_entityClass = 'Grouprelation';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $grouprelation = new $this->_entityClass(array(
            'relationId'      => $data['relationId'],
            'userId'    => $data['userId'],
            'uGroupId'    => $data['uGroupId'],
            'canUpdItm'  => $data['canUpdItm'],
            'canMngMbs'    => $data['canMngMbs'],
            'grpRelCreatedAt'    => $data['grpRelCreatedAt'],
            'grpRelUpdatedAt'    => $data['grpRelUpdatedAt']
        ));
        return $grouprelation;
    }

}