<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Domain\SMTP;
use F3AppSetup\Domain\Validator;

class ForgotPassword extends Middleware\Guest {
    public function show() {
        echo \View::instance()->render('forgot-password.php');
    }

    public function startReset() {
        error_log('line-0');
        $this->sendEmailWithResetOptions();
        error_log('line-8');
    }

    public function sendEmailWithResetOptions() {
        $smtp = new SMTP();
        $validator = new Validator();
        error_log('line-1');
        if(!$validator->validateEmail($this->request['email'])) {
            error_log('line-2');
            $this->f3->merge('session_errors', $validator->getErrors(), true);
            error_log('line-3');
            $this->reroute('/forgot-password');
        }
        if(!$smtp->sendEmailWithResetOptions($this->request['email'])) {
            error_log('line-4');
            $this->f3->merge('session_errors', $smtp->getErrors(), true);
            error_log('line-5');
            $this->reroute('/forgot-password');
        }
        error_log('line-6');
        $this->f3->merge('session_confirmations', array(_(
            'You should get an email within a few minutes with password reset options. You may close this tab.'
        )), true);
        error_log('line-7');
        $this->reroute('/');
    }
}