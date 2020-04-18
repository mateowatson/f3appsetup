<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Domain\SMTP;
use F3AppSetup\Domain\Validator;
use F3AppSetup\Model\User;

class ResetPassword extends Middleware\Guest {
    public function beforeRoute() {
        parent::beforeRoute();
        if(!$this->f3->get('EMAIL_ENABLED'))
            $this->f3->error(404);
    }

    // Saving the previous request values is moot and does not work because
    // we are using reroute in error handler in order to display errors.
    // Don't feel like fixing this. ¯\_(ツ)_/¯
    public function show() {
        $username = $this->params['username'] ? $this->params['username'] : $this->request['username'];
        $username = $username ? $username : '';

        $resetcode = $this->params['resetcode'] ? $this->params['resetcode'] : $this->request['resetcode'];
        $resetcode = $resetcode ? $resetcode : '';

        $this->f3->set('default_username_value', $username);
        $this->f3->set('default_resetcode_value', $resetcode);
        echo \View::instance()->render('reset-password.php');
    }

    public function reset() {
        $errors = array();
        if(!$this->request['username']) {
            array_push($errors, _(
                'Username required.'
            ));
        }
        if(!$this->request['resetcode']) {
            array_push($errors, _(
                'Reset code required.'
            ));
        }
        if(!$this->request['password']) {
            array_push($errors, _(
                'New password required.'
            ));
        }
        if($this->resetHandleErrors($errors)) return;
        $user = new User();
        $user->load(array('username = ?', $this->request['username']));
        if($user->dry()) {
            array_push($errors, _(
                'User does not exist.'
            ));
        }
        if($this->resetHandleErrors($errors)) return;
        if(!password_verify(
            $this->request['resetcode'], $user->password_reset_verification_hash
        )) {
            array_push($errors, _(
                'Incorrect reset code.'
            ));
        }
        if($this->resetHandleErrors($errors)) return;
        $validator = new Validator();
        if(!$validator->validatePassword($this->request['password'])) {
            $errors = array_merge($errors, $validator->getErrors());
        }
        if($this->resetHandleErrors($errors)) return;
        if(!$user->userResetPassword($this->request['password'])) {
            array_push($errors, _(
                'Password reset failed to save.'
            ));
        }
        if($this->resetHandleErrors($errors)) return;
        $this->f3->merge('session_confirmations', array(_(
            'Your password has been reset! You may now login.'
        )), true);
        $this->reroute('/login');
    }

    public function resetHandleErrors($errors) {
        if(count($errors)) {
            $this->f3->merge('session_errors', $errors, true);
            // Mediocre attempt to "save" form values.
            if($this->request['username'] && $this->request['resetcode']) {
                $this->reroute(
                    '/reset-password/'.
                    urlencode($this->request['username']).'/'.
                    urlencode($this->request['resetcode'])
                );
                return true;
            }
            $this->reroute('/reset-password');
            return true;
        }
        return false;
    }
}