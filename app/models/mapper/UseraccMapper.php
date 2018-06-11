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
            'entityTable' => 'USERACCS',
            'entityClass' => 'Useracc'
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
            'userId'    => $data['USERID'],
            'userName'  => $data['USERNAME'],
            'userEmail' => $data['USEREMAIL'],
            'userPass'  => $data['USERPASS'],
            'userType'  => $data['USERTYPE'],
            'userState' => $data['USERSTATE'],
            'userImage' => $data['USERIMAGE'],
            'userCreatedAt' => $data['USERCREATEDAT'],
            'userUpdatedAt' => $data['USERUPDATEDAT']
        );
        return $useracc;
    }
}