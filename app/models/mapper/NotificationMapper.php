<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/10/2018
 * Time: 01:14
 */

namespace ModelMapper;
use AppModel\Useracc;
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
            'ntfId'     => $data['NTFID'] ? $data['NTFID'] : '',
            'nItemId'   => $data['NITEMID'] ? $data['NITEMID'] : '',
            'ntfType'   => $data['NTFTYPE'] ? $data['NTFTYPE'] : '',
            'ntfDscrp'  => $data['NTFDSCRP'] ? $data['NTFDSCRP'] : '',
            'ntfCreatedAt' => $data['NTFCREATEDAT'] ? $data['NTFCREATEDAT'] : ''
        );
        return $notification;
    }
}
