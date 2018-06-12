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
            'relationId'      => array_key_exists('RELATIONID', $data) ? $data['RELATIONID'] : '',
            'userId'    => array_key_exists('USERID', $data) ? $data['USERID'] : '',
            'uGroupId'    => array_key_exists('UGROUPID', $data) ? $data['UGROUPID'] : '',
            'canUpdItm'  => array_key_exists('CANUPDITM', $data) ? $data['CANUPDITM'] : '',
            'canMngMbs'    => array_key_exists('CANMNGMBS', $data) ? $data['CANMNGMBS'] : '',
            'grpRelCreatedAt'    => array_key_exists('GRPRELCREATEDAT', $data) ? $data['GRPRELCREATEDAT'] : '',
            'grpRelUpdatedAt'    => array_key_exists('GRPRELUPDATEDAT', $data) ? $data['GRPRELUPDATEDAT'] : ''
        );
        return $grouprelation;
    }
}
