<?php

namespace AppModel;

use InvalidArgumentException;

class Useracc extends AbstractEntity
{
    protected $_allowedFields = array('userId','userName','userEmail','userPass','userType','userState','userImage','userCreatedAt','userUpdatedAt');

    /**
     * Set the entry ID
     * @param bool $id = false Method implemented but should not be used. Id's are generated automatically and should remain the value generated!
     */
    public function setId($userid = false)
    {
        if (!$userid) {
            $this->_values['userId'] = null;
        }
        else {
            if (!filter_var($userid, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 999999999)))) {
                throw new InvalidArgumentException('User id can only be between 1 and 999999999!');
            }
            $this->_values['userId'] = $userid;
        }
    }

    public function setName($username) {
        if (!is_string($username) ) {
            throw new InvalidArgumentException('Username input should only be in string format!');
        }
        if (preg_match('/[^a-zA-Z0-9._ -]+/', $username) !== 0){
            throw new InvalidArgumentException('The name format is not correct! Only alpha-numeric, \'-\', \'_\', \'.\' and whitespace characters are allowed!');
        }
        if (strlen($username) < 4 ) {
            throw new InvalidArgumentException('Username length should be greater than 4! Current length is: ' . strlen($username) .' !');
        }
        if (strlen($username) > 16) {
            throw new InvalidArgumentException('Username length shouldn\'t be greater than 16! Current length is: ' . strlen($username) .' !');
        }
        $this->_values['userName'] = $username;
    }

    public function setEmail($useremail) {
        if (!is_string($useremail) ) {
            throw new InvalidArgumentException('Email input should only be in string format!');
        }
        if (preg_match('/[^a-zA-Z0-9@._]+/', $useremail) !== 0){
            throw new InvalidArgumentException('The email format is not correct! Only alpha-numeric, \'_\', \'.\' and \'@\' characters are allowed!');
        }
        if(!filter_var($useremail, FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException('The email format is not correct!');
        }
        if (strlen($useremail) < 6 ) {
            throw new InvalidArgumentException('Email length should be greater than 6! Current length is: ' . strlen($useremail) .' !');
        }
        if (strlen($useremail) > 48) {
            throw new InvalidArgumentException('Email length shouldn\'t be greater than 48! Current length is: ' . strlen($useremail) .' !');
        }
        $this->_values['userEmail'] = $useremail;
    }

    public function setPass($userpass) {
        if (!is_string($userpass)) {
            throw new InvalidArgumentException('Password input needs to be in string format always!');
        }
        if(strlen($userpass) < 4) {
            throw new InvalidArgumentException('Passwords should be at least 4 character long! Yours has only ' . strlen($userpass) . ' !');
        }
        if(strlen($userpass) > 64) {
            throw new InvalidArgumentException('Dude, 64 is a big enough password length! Do not go over it like now: ' . strlen($userpass). ' !');
        }
        $salt = '$1_2jlh83#@J^Q';
        $this->_values['userPass'] = hash('sha512', $this->_values['userName']. $salt . $userpass);
    }

    public function setType($usertype) {
        if (!filter_var($usertype, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 3)))) {
            throw new InvalidArgumentException('User type can only be 0 (for not-accepted yet users), 1 (for normal, accepted users), 2 (for root managers) or 3 (for root admins)!');
        }
        $this->_values['userType'] = $usertype;
    }

    public function setState($userstate) {
        if (!filter_var($userstate, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 1)))) {
            throw new InvalidArgumentException('User state can only be 0 (for suspended users) or 1 (for active users)!');
        }
        $this->_values['userState'] = $userstate;
    }

    public function setImage($userimage) {
        if (!is_string($userimage)) {
            throw new InvalidArgumentException('User image must be either \'undefined\' or a path towards an existing image file!');
        }
        $this->_values['userImage'] = $userimage;
    }

    public function setCreatedAt($usercreatedat = false) {
        if(!$usercreatedat) {
            $this->_values['userCreatedAt'] = null;
        }
        else {
            $this->_values['userCreatedAt'] = $usercreatedat;
        }
    }

    public function setUpdatedAt($userupdatedat = false) {
        if(!$userupdatedat) {
            $this->_values['userUpdatedAt'] = null;
        }
        else {
            $this->_values['userUpdatedAt'] = $userupdatedat;
        }
    }
}