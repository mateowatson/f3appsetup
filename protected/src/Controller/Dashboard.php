<?php
namespace F3AppSetup\Controller;

class Dashboard extends Middleware\User {
    public function show() {
        echo \View::instance()->render('dashboard.php');
    }
}