<?php

namespace F3AppSetup\Domain;

use F3AppSetup\Model\User;

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

    public function sendEmailWithResetOptions($email) {
        $site_name = $this->f3->get('SITE_NAME');
        $site_url = $this->f3->get('SITE_URL');
        $user_model = new User();
        $users = $user_model->getUsersByEmail($email);
        $usernames = array();
        $password_reset_verification = bin2hex( random_bytes(6) );
        $password_reset_verification_hash = password_hash($password_reset_verification, PASSWORD_DEFAULT);
        foreach ($users as $user) {
            array_push($usernames, $user->username);
            $user->password_reset_verification_hash = $password_reset_verification_hash;
            $user->save();
        }
        if(count($usernames) > 1) {
            $email_message = $this->getResetMessageForOneEmailManyUsers(
                $usernames,
                $email,
                $password_reset_verification
            );
        } else if(count($usernames) === 1) {
            $email_message = $this->getResetMessageForOneEmailOneUser(
                $usernames,
                $email,
                $password_reset_verification
            );
        }
        if(!$email_message) {
            array_push($this->errors, _(
                'Could not generate email message. No message sent.'
            ));
            return false;
        }

        $site_name = $this->f3->get('SITE_NAME');
        $site_url = $this->f3->get('SITE_URL');

        $this->set('From', '<'.$this->user.'>');
        $this->set('To', '<'.$email.'>');
        $this->set('Subject', $site_name.' password reset verification');

        if($this->send($email_message)) {
            return true;
        }

        array_push($this->errors, _(
            'Could not send reset password email message.'
        ));
        return false;
    }

    public function getResetMessageForOneEmailManyUsers(
        $usernames,
        $email,
        $password_reset_verification
    ) {
        $site_name = $this->f3->get('SITE_NAME');
        $site_url = $this->f3->get('SITE_URL');

        $userlist = '';
        foreach ($usernames as $idx => $username) {
            $userlist .= $username.' '.$site_url.'/reset-password/'.urlencode($username).
                '/'.urlencode($password_reset_verification);
            if($idx+1 !== count($usernames)) {
                $userlist .= "\n\n";
            }
        }
        if(!$userlist) return false;

        return <<<MANYUSERS
Hello $email!

You may reset your password for one of the following accounts by clicking on the
appropriate link below:

$userlist

Sincerely,
The $site_name Team
MANYUSERS;
    }

    public function getResetMessageForOneEmailOneUser(
        $usernames,
        $email,
        $password_reset_verification
    ) {
        $site_name = $this->f3->get('SITE_NAME');
        $site_url = $this->f3->get('SITE_URL');
        $username = $usernames[0];
        if(!$username) return false;

        $pw_reset_url = $site_url.'/reset-password/'.urlencode($username).
            '/'.urlencode($password_reset_verification);

        return <<<MANYUSERS
Hello $username!

You may reset your password by going to the following URL:

$pw_reset_url

Sincerely,
The $site_name Team
MANYUSERS;
    }
}