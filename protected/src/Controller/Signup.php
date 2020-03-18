<?php
namespace F3StarterApp\Controller;

use F3StarterApp\Model\User;

class Signup extends Middleware\Guest {
    public function show() {
        echo \View::instance()->render('signup.php');
    }

    public function signup() {
        $user = new User(
            $this->request['username'],
            $this->request['password'],
            $this->request['email']
        );

        if(!$user->validateNewUser()) {
            $this->f3->push('view_errors', $user->getValidationErrors());
        }
    }
}