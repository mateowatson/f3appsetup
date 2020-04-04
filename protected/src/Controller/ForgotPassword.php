<?php
namespace F3AppSetup\Controller;

class ForgotPassword extends Middleware\Guest {
    public function show() {
        echo \View::instance()->render('forgot-password.php');
    }
}