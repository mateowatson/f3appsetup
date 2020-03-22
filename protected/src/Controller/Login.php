<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Model\User;

class Login extends Middleware\Guest {
    private $username;
    private $password;
    private $user;
    private $submit_login_errors = array();

    public function __construct($f3, $params, $csrf_fail_redirect = '/') {
        parent::__construct($f3, $params, $csrf_fail_redirect);
        $this->csrf_fail_redirect = '/login';
        $this->username = $this->request['username'];
        $this->password = $this->request['password'];
        $this->email = $this->request['email'] ? $this->request['email'] : '';
        $this->user = new User();
    }

    public function show() {
        echo \View::instance()->render('login.php');
    }

    public function login() {
        if(!$this->submitLogin()) {
            $this->f3->merge('session_errors', $this->submit_login_errors(), true);
            $this->reroute('/login');
        }

        $this->f3->merge('session_confirmations', array(_(
            'Welcome '.$this->request['username'].'!'
        )), true);
        $this->reroute('/dashboard');
    }

    public function submitLogin() {
        if(!$this->username || !$this->password) {
            array_push($this->submit_login_errors, _(
                'Username and password are required.'
            ));
            return false;
        }

        $this->user->load(array('username=?', $this->username));

        if($this->user->dry()) {
            array_push($this->submit_login_errors, _(
                'The username and password you entered are incorrect.'
            ));
            return false;
        }

        if(!password_verify($this->password, $this->user->password)) {
            array_push($this->submit_login_errors, _(
                'The username and password you entered are incorrect.'
            ));
            return false;
        }

        $this->f3->set('SESSION.username', $this->username);
        return true;
    }
}