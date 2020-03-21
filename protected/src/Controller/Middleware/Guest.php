<?php
namespace F3AppSetup\Controller\Middleware;

class Guest extends \F3AppSetup\Controller\Main {
    public function beforeRoute() {
        parent::beforeRoute();
    }

    public function afterRoute() {
        parent::afterRoute();
    }
}