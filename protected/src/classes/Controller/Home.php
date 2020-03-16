<?php
namespace Controller;
class Home {
    public function show($f3, $params) {
        echo \View::instance()->render('home.php');
    }
}