<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Model\User;

class Signup extends Middleware\Guest {
    public function show() {
        echo \View::instance()->render('signup.php');
    }

    public function signup() {
        $user = new User(
            $this->request['username'],
            $this->request['password'],
            isset($this->request['email']) ? $this->request['email'] : null
        );

        if(!$user->validateNewUser()) {
            $this->f3->merge('session_errors', $user->getValidateNewUserErrors(), true);
            $this->reroute('/signup');
        }

        if(!$user->signup()) {
            $this->f3->merge('session_errors', $user->getSignupErrors(), true);
            $this->reroute('/signup');
        }

        if($this->f3->get('USERSIGNUP') === 'email' || $this->request['email']) {
            if(!$user->sendEmailVerificationCode()) {
                $this->f3->merge('session_errors', $user->getSendEmailVerificationCodeErrors(), true);
                $user->delete();
                $this->reroute('/signup');
            }
        }

        $this->f3->merge('session_confirmations', array(_(
            'Thank you for signing up! You may now login.'
        )), true);
        $this->reroute('/login');
    }
}