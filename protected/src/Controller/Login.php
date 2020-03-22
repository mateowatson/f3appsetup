<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Model\User;

class Login extends Middleware\Guest {
    public function __construct($f3, $params, $csrf_fail_redirect = '/') {
        parent::__construct($f3, $params, $csrf_fail_redirect);
        $this->csrf_fail_redirect = '/login';
    }
    
    public function show() {
        echo \View::instance()->render('login.php');
    }

    public function login() {
        $user = new User(
            $this->request['username'],
            $this->request['password'],
            isset($this->request['email']) ? $this->request['email'] : null
        );

        if(!$user->login()) {
            $this->f3->merge('session_errors', $user->getLoginErrors(), true);
            $this->reroute('/login');
        }

        $this->f3->merge('session_confirmations', array(_(
            'Welcome '.$this->request['username'].'!'
        )), true);
        $this->reroute('/dashboard');
    }
}