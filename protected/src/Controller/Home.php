<?php
namespace F3StarterApp\Controller;

class Home extends Main {
    public function show() {
        echo \View::instance()->render('home.php');
    }
}