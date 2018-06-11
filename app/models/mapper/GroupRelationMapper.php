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

    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter)
    {
        parent::__construct($adapter, array(
            'entityTable' => $this->_entityTable,
            'entityClass' => $this->_entityClass
        ));
    }

    /**
     * Create an grouprelation entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $grouprelation = array(
            'relationId'      => $data['RELATIONID'] ? $data['RELATIONID'] : '',
            'userId'    => $data['USERID'] ? $data['USERID'] : '',
            'uGroupId'    => $data['UGROUPID'] ? $data['UGROUPID'] : '',
            'canUpdItm'  => $data['CANUPDITM'] ? $data['CANUPDITM'] : '',
            'canMngMbs'    => $data['CANMNGMBS'] ? $data['CANMNGMBS'] : '',
            'grpRelCreatedAt'    => $data['GRPRELCREATEDAT'] ? $data['GRPRELCREATEDAT'] : '',
            'grpRelUpdatedAt'    => $data['GRPRELUPDATEDAT'] ? $data['GRPRELUPDATEDAT'] : ''
        );
        return $grouprelation;
    }
}
