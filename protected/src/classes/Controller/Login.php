<?php
namespace Controller;
class Login {
    public function show($f3, $params) {
        echo \View::instance()->render('login.php');
    }

    public function login($f3, $params) {

    }
}