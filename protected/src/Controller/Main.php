<?php
namespace F3AppSetup\Controller;

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
        $this->f3->set(
            'view_errors',
            json_decode($this->f3->get('SESSION.errors'))
        );
        $this->f3->set(
            'view_confirmations',
            json_decode($this->f3->get('SESSION.confirmations'))
        );
        // Reset errors in db
        $this->f3->set('SESSION.errors', '');
        $this->f3->set('SESSION.confirmations', '');
        // Reset errors to put in session after route
        $this->f3->set('session_errors', array());
        $this->f3->set('session_confirmations', array());
    }

    public function afterRoute() {
        
    }

    public function reroute($path) {
        $this->f3->set('SESSION.errors',
            json_encode($this->f3->get('session_errors'))
        );
        $this->f3->set('SESSION.confirmations',
            json_encode($this->f3->get('session_confirmations'))
        );
        $this->f3->reroute($path);
    }
}