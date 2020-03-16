<?php
namespace Controller;
class Signup {
    public function show($f3, $params) {
        echo \View::instance()->render('signup.php');
    }

    public function signup($f3, $params) {
        
    }
}