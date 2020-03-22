<?php
namespace F3AppSetup\Controller\Middleware;

class Guest extends \F3AppSetup\Controller\Main {
    public function beforeRoute() {
        parent::beforeRoute();
        $username = $this->f3->get('SESSION.username');
        if($username)
            return $this->f3->reroute('/dashboard');
    }

    public function afterRoute() {
        parent::afterRoute();
    }
}