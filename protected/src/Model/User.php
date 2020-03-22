<?php
namespace F3AppSetup\Model;

use DB\SQL\Mapper;
use F3AppSetup\Controller\Signup;

class User extends Mapper {
   public function __construct() {
        parent::__construct(\Base::instance()->get('DB'), 'users');
    }

    public function userCreate(Signup $signup) {
        if($this->userExists($signup->getUsername())) return false;
        $this->reset();
        $this->username = $signup->getUsername();
        $this->password = password_hash($signup->getPassword(), PASSWORD_DEFAULT);
        $this->email = $signup->getEmail();
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
}