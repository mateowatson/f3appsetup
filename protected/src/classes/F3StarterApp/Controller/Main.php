<?php
namespace F3StarterApp\Controller;
class Main {
    protected $f3;
    protected $params;

    function __construct($f3, $params) {
        $this->f3 = $f3;
        $this->params = $params;
    }

    public function beforeRoute() {
        var_dump($this->params);
    }

    public function afterRoute() {
        
    }
}