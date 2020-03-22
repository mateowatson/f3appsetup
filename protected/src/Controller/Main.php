<?php
namespace F3AppSetup\Controller;

class Main {
    protected $f3;
    protected $params;
    protected $request;
    protected $db;
    protected $csrf_fail_redirect;

    public function __construct($f3, $params, $csrf_fail_redirect = '/') {
        $this->f3 = $f3;
        $this->params = $params;
        $this->request = $f3->get('REQUEST');
        $this->db = $f3->get('DB');
        $this->csrf_fail_redirect = $csrf_fail_redirect;
    }

    public function beforeRoute() {
        $this->preventCSRF();
        // Get previous session errors/confirmations
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

    public function preventCSRF() {
        $this->generateCSRF();
        $request_method = isset($this->f3->get('SERVER')['REQUEST_METHOD']) ?
            $this->f3->get('SERVER')['REQUEST_METHOD'] : '';
        if(strcasecmp($request_method, 'POST') !== 0)
            return;
        if($this->f3->get('SESSION.csrf') !== $this->request['csrf']) {
            $this->f3->merge('session_errors', array(_(
                'Form entry was not successful. Try again or contact site administrator.'
            )), true);
            $this->reroute($this->csrf_fail_redirect);
        }
    }

    public function generateCSRF() {
        $request_method = isset($this->f3->get('SERVER')['REQUEST_METHOD']) ?
            $this->f3->get('SERVER')['REQUEST_METHOD'] : '';
        if(strcasecmp($request_method, 'GET') !== 0)
            return;
        $session_csrf = $this->f3->get('SESSION.csrf');
        if($session_csrf)
            return;
        $this->f3->set('SESSION.csrf', bin2hex( random_bytes(24) ));
    }
}