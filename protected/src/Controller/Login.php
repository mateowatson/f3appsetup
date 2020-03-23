<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Model\User;

class Login extends Middleware\Guest {
    private $identifier;
    private $identifier_field;
    private $username;
    private $password;
    private $user;
    private $submit_login_errors = array();

    public function __construct($f3, $params, $csrf_fail_redirect = '/') {
        parent::__construct($f3, $params, $csrf_fail_redirect);
        $this->csrf_fail_redirect = '/login';
        $this->identifier = $this->f3->get('USERSIGNUP') === 'email' ?
            $this->request['email'] : $this->request['username'];
        $this->identifier_field = $this->f3->get('USERSIGNUP') === 'email' ?
            _('email') : _('username');
        $this->username = $this->request['username'];
        $this->password = $this->request['password'];
        $this->email = $this->request['email'];
        $this->user = new User();
    }

    public function show() {
        echo \View::instance()->render('login.php');
    }

    public function login() {
        if(!$this->submitLogin()) {
            $this->f3->merge('session_errors', $this->submit_login_errors, true);
            $this->reroute('/login');
        }

        $this->f3->merge('session_confirmations', array(_(
            'Welcome '.$this->request['username'].'!'
        )), true);
        $this->reroute('/dashboard');
    }

    public function submitLogin() {
        if(!$this->identifier || !$this->password) {
            array_push($this->submit_login_errors, _(
                ucwords($this->identifier_field) . 
                ' and password are required.'
            ));
            return false;
        }

        $this->user->load(array('username=?', $this->identifier));

        if(
            $this->f3->get('USERSIGNUP') === 'email' &&
            !$this->user->dry() &&
            $this->user->email_verified === 0
        ) {
            $site_url = $this->f3->get('SITE_URL');
            array_push($this->submit_login_errors, _(
                "You must confirm your email address before logging in.
                Check your email. If you are missing an email confirmation
                message, go to <a href=\"$site_url/confirm\">$site_url/confirm</a>."
            ));
            return false;
        }

        if($this->user->dry()) {
            array_push($this->submit_login_errors, _(
                'The '.$this->identifier_field.' and password you entered are incorrect.'
            ));
            return false;
        }

        if(!password_verify($this->password, $this->user->password)) {
            array_push($this->submit_login_errors, _(
                'The '.$this->identifier_field.' and password you entered are incorrect.'
            ));
            return false;
        }

        $this->f3->set('SESSION.username', $this->identifier);
        return true;
    }
}