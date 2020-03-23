<?php
namespace F3AppSetup\Controller;

class Logout extends Middleware\User {
    public function logout() {
        $this->f3->set('SESSION.username', '');
        $this->f3->merge('session_confirmations', array(_(
            'You have successfully logged out.'
        )), true);
        $this->reroute('/');
    }
}