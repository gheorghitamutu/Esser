<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 08-Jun-18
 * Time: 18:34
 */

namespace ModelMapper;
use AppModel\Useracc;
use DatabaseConnectivity;

class UseraccMapper extends AbstractMapper
{
    protected $_entityTable = 'USERACCS';
    protected $_entityClass = 'Useracc';

    public function __construct(DatabaseConnectivity\DatabaseAdapterInterface $adapter)
    {
        parent::__construct($adapter, array(
            'entityTable' => $this->_entityTable,
            'entityClass' => $this->_entityClass
        ));
    }

    /**
     * Create an useracc entity with the supplied data
     * @param array $data
     * @return array
     */
    protected function _createEntity(array $data)
    {
        $useracc = array(
            'userId'        => array_key_exists('USERID',           $data) ? $data['USERID'] : '',
            'userName'      => array_key_exists('USERNAME',         $data) ? $data['USERNAME'] : '',
            'userEmail'     => array_key_exists('USEREMAIL',        $data) ? $data['USEREMAIL'] : '',
            'userPass'      => array_key_exists('USERPASS',         $data) ? $data['USERPASS'] : '',
            'userType'      => array_key_exists('USERTYPE',         $data) ? $data['USERTYPE'] : '',
            'userState'     => array_key_exists('USERSTATE',        $data) ? $data['USERSTATE'] : '',
            'userImage'     => array_key_exists('USERIMAGE',        $data) ? $data['USERIMAGE'] : '',
            'userCreatedAt' => array_key_exists('USERCREATEDAT',    $data) ? $data['USERCREATEDAT'] : '',
            'userUpdatedAt' => array_key_exists('USERUPDATEDAT',    $data) ? $data['USERUPDATEDAT'] : ''
        );

        return $useracc;
    }
}
