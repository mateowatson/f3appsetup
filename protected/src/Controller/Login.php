<?php
namespace F3AppSetup\Controller;

class Login extends Middleware\Guest {
    public function show() {
        echo \View::instance()->render('login.php');
    }

    public function login() {

    }
}