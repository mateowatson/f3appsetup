<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Model\User;
use F3AppSetup\Domain\SMTP;

class Confirm extends Main {
    public function show() {
        $r_user = urldecode($this->request['user']);
        $r_token = urldecode($this->request['token']);
        $email_or_account_verbiage = $this->f3->get('USERSIGNUP') === 'email' ?
            _('account') : _('email');
        if($r_user && $r_token) {
            $user = new User();
            $user->load(array('username = ?', $r_user));
            if($user->dry()) {
                $this->f3->merge('session_errors', array(_(
                    'Could not find the account. Try signing up if you haven\'t
                    already.'
                )), true);
                $this->reroute('/confirm');
            }
            if(!password_verify($r_token, $user->email_verification_hash)) {
                $this->f3->merge('session_errors', array(_(
                    'Could not confirm the '.$email_or_account_verbiage.'. Try
                    resending the email verification code.'
                )), true);
                $this->reroute('/confirm');
            }

            $user->email_verified = true;
            $user->save();

            $username = $this->f3->get('SESSION.username');
            $db_users = new \DB\SQL\Mapper($this->f3->get('DB'), 'users');
            $session_user = $username ? $db_users->load(array('username=?', $username)) : '';
            if(!$username || !$session_user) {
                $this->f3->set('SESSION.username', '');
                $this->f3->merge('session_confirmations', array(_(
                    'We have successfully confirmed your email! Log in below.'
                )), true);
                $this->reroute('/login');
            }

            $this->f3->merge('session_confirmations', array(_(
                'We have successfully confirmed your email!'
            )), true);
            $this->reroute('/dashboard');
        }
        echo \View::instance()->render('confirm.php');
    }

    public function resend() {
        $identifier = $this->f3->get('USERSIGNUP') === 'email' ?
            $this->request['email'] : $this->request['username'];
        $email = $this->request['email'];

        if(!$identifier || !$email) {
            $this->f3->merge('session_errors', array(_(
                'All fields required.'
            )), true);
            $this->reroute('/confirm');
        }

        $user = new User();
        $user->load(array('username = ?', $identifier));
        if($user->dry()) {
            $this->f3->merge('session_errors', array(_(
                'Could not find the account. Try signing up if you haven\'t
                already.'
            )), true);
            $this->reroute('/confirm');
        }

        $smtp = new SMTP();
        if(!$smtp->sendEmailVerificationCode($identifier, $email)) {
            $this->f3->merge('session_errors', $smtp->getErrors(), true);
            $this->reroute('/confirm');
        }

        $this->f3->merge('session_confirmations', array(_(
            'We\'ve sent an email confirmation link to your email address.'
        )), true);
        $this->reroute('/login');
        $this->reroute('/');
    }
}