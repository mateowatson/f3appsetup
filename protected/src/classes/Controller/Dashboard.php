<?php
namespace Controller;
class Dashboard {
    public function show($f3, $params) {
        echo \View::instance()->render('dashboard.php');
    }
}