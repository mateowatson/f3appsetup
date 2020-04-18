<?php
namespace F3AppSetup\Controller\Middleware;

class User extends \F3AppSetup\Controller\Main {
    public function beforeRoute() {
        parent::beforeRoute();
        $username = $this->f3->get('SESSION.username');
        if(!$username)
            $this->reroute('/login');
        $db_users = new \DB\SQL\Mapper($this->f3->get('DB'), 'users');
        $session_user = $db_users->load(array('username=?', $username));
        if(!$session_user) {
            $this->f3->set('SESSION.username', '');
            $this->reroute('/login');
        }
        $this->session_user = $session_user;
    }

    public function afterRoute() {
        parent::afterRoute();
    }
}