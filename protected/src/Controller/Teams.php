<?php
namespace F3AppSetup\Controller;

class Teams extends Middleware\User {
    public function show() {
        echo \View::instance()->render('teams.php');
    }
}