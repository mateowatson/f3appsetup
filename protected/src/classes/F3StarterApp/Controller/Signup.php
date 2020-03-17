<?php
namespace F3StarterApp\Controller;
class Signup extends Middleware\Guest {
    public function show() {
        echo \View::instance()->render('signup.php');
    }

    public function signup() {
        
    }
}