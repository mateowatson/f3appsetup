<?php
namespace F3AppSetup\Controller\Middleware;

class User extends \F3AppSetup\Controller\Main {
    public function beforeRoute() {
        parent::beforeRoute();
        $username = $this->f3->get('SESSION.username');
        if(!$username)
            return $this->f3->reroute('/login');
        $db_users = new \DB\SQL\Mapper($this->f3->get('DB'), 'users');
        $session_user = $db_users->load(array('username=?', $username));
        if(!$session_user)
            return $this->f3->reroute('/login');
    }

    public function afterRoute() {
        parent::afterRoute();
    }
}