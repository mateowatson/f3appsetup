<?php
namespace F3AppSetup\Model;

use DB\SQL\Mapper;
use F3AppSetup\Controller\Signup;

class User extends Mapper {
   public function __construct() {
        parent::__construct(\Base::instance()->get('DB'), 'users');
    }

    public function userCreate(Signup $signup) {
        if($this->userExists($signup->getIdentifier())) return false;
        $this->reset();
        $this->username = $signup->getIdentifier();
        $this->password = password_hash($signup->getPassword(), PASSWORD_DEFAULT);
        $this->email = $signup->getEmail();
        $this->save();
        return true;
    }

    public function userResetPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->password_reset_verification_hash = null;
        $this->save();
        return true;
    }

    public function userErase($username) {
        $this->load(array('username = ?', $username));
        if($this->dry()) return false;
        $this->erase();
        return true;
    }

    public function userExists($username) {
        $this->load(array('username = ?', $username));
        return $this->dry() ? false : true;
    }

    public function updateEmailVerificationHash($username, $email_verification_hash) {
        if(!$this->userExists($username)) return false;
        $this->load(array('username = ?', $username));
        $this->email_verification_hash = $email_verification_hash;
        $this->save();
        return true;
    }

    public function getUsersByEmail($email) {
        return $this->find(array('email = ?', $email));
    }
}