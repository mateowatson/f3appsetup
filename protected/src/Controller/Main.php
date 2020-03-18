<?php
namespace F3StarterApp\Controller;

class Main {
    protected $f3;
    protected $params;
    protected $request;

    public function __construct($f3, $params) {
        $this->f3 = $f3;
        $this->params = $params;
        $this->request = $f3->get('REQUEST');
    }

    public function beforeRoute() {
        
    }

    public function afterRoute() {
        
    }
}