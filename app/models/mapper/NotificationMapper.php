<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:14
 */

namespace ModelMapper;
use DatabaseConnectivity, AppModel, OCI_Collection;

class NotificationMapper extends AbstractMapper
{
    protected $_entityTable = 'NOTIFICATIONS';
    protected $_entityClass = 'Notification';

    /**
     * Create an useracc entity with the supplied data
     */
    protected function _createEntity(array $data)
    {
        $notification = new $this->_entityClass(array(
            'NTFID'     => $data['ntfId'],
            'NITEMID'   => $data['nItemId'],
            'NTFTYPE'   => $data['ntfType'],
            'NTFDSCRP'  => $data['ntfDscrp'],
            'NTFCREATEDAT' => $data['ntfCreatedAt']
        ));
        return $notification;
    }
}