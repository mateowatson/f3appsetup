<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Domain\SMTP;
use F3AppSetup\Domain\Validator;

class ForgotPassword extends Middleware\Guest {
    public function beforeRoute() {
        parent::beforeRoute();
        if(!$this->f3->get('EMAIL_ENABLED'))
            $this->f3->error(404);
    }
    
    public function show() {
        echo \View::instance()->render('forgot-password.php');
    }

    public function startReset() {
        $this->sendEmailWithResetOptions();
    }

    public function sendEmailWithResetOptions() {
        $smtp = new SMTP();
        $validator = new Validator();
        if(!$validator->validateEmail($this->request['email'])) {
            $this->f3->merge('session_errors', $validator->getErrors(), true);
            $this->reroute('/forgot-password');
        }
        if(!$smtp->sendEmailWithResetOptions($this->request['email'])) {
            $this->f3->merge('session_errors', $smtp->getErrors(), true);
            $this->reroute('/forgot-password');
        }
        $this->f3->merge('session_confirmations', array(_(
            'You should get an email within a few minutes with password reset options. You may close this tab.'
        )), true);
        $this->reroute('/');
    }
}