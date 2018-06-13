<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:14
 */

namespace ModelMapper;
use AppModel;
use DatabaseConnectivity;

class NotificationMapper extends AbstractMapper
{
    protected $_entityTable = 'NOTIFICATIONS';
    protected $_entityClass = 'Notification';

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
        $notification = array(
            'ntfId'    => array_key_exists('NTFID', $data) ? $data['NTFID'] : '',
            'nItemId'  => array_key_exists('NITEMID', $data) ? $data['NITEMID'] : '',
            'ntfType'  => array_key_exists('NTFTYPE', $data) ? $data['NTFTYPE'] : '',
            'ntfDscrp' => array_key_exists('NTFDSCRP', $data) ? $data['NTFDSCRP'] : '',
            'ntfCreatedAt' => array_key_exists('NTFCREATEDAT', $data) ? $data['NTFCREATEDAT'] : ''
        );
        return $notification;
    }
}
