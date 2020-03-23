<?php

namespace F3AppSetup\Domain;

class SMTP extends \SMTP {
    private $f3;
    private $email_enabled;
    private $errors = array();

    public function __construct() {
        parent::__construct();
        $this->f3 = \Base::instance();
        if($this->f3->get('SMTP_SCHEME') !== 'tls' ||
            $this->f3->get('SMTP_SCHEME') !== 'ssl') {
            $scheme = null;
        } else {
            $scheme = $this->f3->get('SMTP_SCHEME');
        }

        $this->host = $this->f3->get('SMTP_HOST');
        $this->port = $this->f3->get('SMTP_PORT');
        $this->user = $this->f3->get('SMTP_USERNAME');
        $this->pw = $this->f3->get('SMTP_PASSWORD');
        $this->scheme = $scheme;

        $this->email_enabled = $this->f3->get('EMAIL_ENABLED');
        if(!$this->email_enabled) {
            array_push($this->errors, _('Email not enabled on this site.'));
        }
    }

    public function getErrors() {
        return $this->errors;
    }

    public function sendEmailVerificationCode($username, $email) {
        if(count($this->errors)) {
            return false;
        }

        $site_name = $this->f3->get('SITE_NAME');
        $site_url = $this->f3->get('SITE_URL');

        $this->set('From', '<'.$this->user.'>');
        $this->set('To', '<'.$email.'>');
        $this->set('Subject', $site_name.' email verification');
        // creates 12 digit random string
        $email_verification = bin2hex( random_bytes(6) );
        $email_verification_link = $site_url.'/confirm?user='.
        urlencode($username).'&token='.urlencode($email_verification);

        if($this->f3->get('USERSIGNUP') === 'email') {
            $message = <<<MESSAGE
Hello $username,

Confirm your account by going to this link:

    $email_verification_link

Sincerely,
The $site_name Team
MESSAGE;
        } else {
            $message = <<<MESSAGE2
Hello $username,

Confirm your email by going to this link:

    $email_verification_link

Without confirming your email, you will not be able to use Forgot Password.

Sincerely,
The $site_name Team
MESSAGE2;
        }

        $email_verification_hash = password_hash($email_verification, PASSWORD_DEFAULT);
        $user = new \F3AppSetup\Model\User();
        if(!$user->updateEmailVerificationHash($username, $email_verification_hash)) {
            array_push($this->errors, _(
                'Could not create email verification.'
            ));
            return false;
        }
        
        if($this->send($message)) {
            return true;
        }

        array_push($this->errors, _(
            'Could not send email verification.'
        ));
        return false;
    }
}