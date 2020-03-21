<?php
namespace F3AppSetup\Controller;

class Home extends Main {
    public function show() {
        echo \View::instance()->render('home.php');
    }
}