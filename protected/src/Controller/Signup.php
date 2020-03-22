<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Model\User;

class Signup extends Middleware\Guest {
    private $username;
    private $password;
    private $email;
    private $user;
    private $validate_new_user_errors = array();
    private $user_create_errors = array();
    private $user_erase_errors = array();
    private $send_email_verification_code_errors = array();
    private $email_verification_hash;

    public function __construct($f3, $params, $csrf_fail_redirect = '/') {
        parent::__construct($f3, $params, $csrf_fail_redirect);
        $this->csrf_fail_redirect = '/signup';
        $this->username = $this->request['username'];
        $this->password = $this->request['password'];
        $this->email = $this->request['email'] ? $this->request['email'] : '';
        $this->user = new User();
    }

    public function show() {
        echo \View::instance()->render('signup.php');
    }

    public function signup() {
        if(!$this->validateNewUser()) {
            $this->f3->merge('session_errors', $user->validate_new_user_errors(), true);
            $this->reroute('/signup');
        }

        if(!$this->userCreate()) {
            $this->f3->merge('session_errors', $this->user_errors, true);
            $this->reroute('/signup');
        }

        if($this->f3->get('USERSIGNUP') === 'email' || $this->request['email']) {
            if(!$this->sendEmailVerificationCode()) {
                $this->f3->merge('session_errors', $this->send_email_verification_code_errors, true);
                if(!$this->userErase()) {
                     $this->f3->merge('session_errors', $this->user_erase_errors, true);
                }
                $this->reroute('/signup');
            }
        }

        $this->f3->merge('session_confirmations', array(_(
            'Thank you for signing up! You may now login.'
        )), true);
        $this->reroute('/login');
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function validateNewUser() {
        $is_email_required = $this->f3->get('USERSIGNUP') === 'email'
            ? true : false;
        if(!$this->username) {
            array_push($this->validate_new_user_errors, _(
                'A username is required for signing up.'
            ));
        }
        if(!$this->password) {
            array_push($this->validate_new_user_errors, _(
                'A password is required for signing up.'
            ));
        }
        if($is_email_required && !$this->email) {
            array_push($this->validate_new_user_errors, _(
                'An email address is required for signing up.'
            ));
        }
        if($this->user->userExists($this->username) !== false) {
            array_push($this->validate_new_user_errors, _(
                'The username '.$this->username.' is already taken.'
            ));
        }
        if($this->username && !ctype_alnum($this->username)) {
            array_push($this->validate_new_user_errors, _(
                'The username must use only alphanumeric characters. You provided '.$username.'.'
            ));
        }
        if($this->username && strlen($this->username) > 25) {
            array_push($this->validate_new_user_errors, _(
                'The username must not be more than 25 characters long.'
            ));
        }
        if($this->password && strlen($this->password) > 75) {
            array_push($this->validate_new_user_errors, _(
                'The password must not be more than 75 characters long.'
            ));
        }
        if($this->password && strlen($this->password) < 8) {
            array_push($this->validate_new_user_errors, _(
                'The password must be at least 8 characters long.'
            ));
        }
        if($this->email &&
            filter_var($this->email, FILTER_VALIDATE_EMAIL) === FALSE) {
            array_push($this->validate_new_user_errors, _(
                'Not a valid email address.'
            ));
        }
        if(count($this->validate_new_user_errors))
            return false;
        return true;
    }

    public function userCreate() {
        if($this->user->userCreate($this)) return true;
        array_push($this->user_create_errors, _(
            'Failed to create the account.'
        ));
        return false;
    }

    public function userErase() {
        if($this->user->userErase($this->username)) return true;
        array_push($this->validate_new_user_errors, _(
            'Signed invalid user up and failed to delete the record.'
        ));
        return false;
    }

    public function sendEmailVerificationCode() {
        if(!$this->f3->get('EMAIL_ENABLED')) {
            array_push($this->validate_new_user_errors, _(
                'Email not enabled on this site.'
            ));
            return false;
        }
        if($this->f3->get('SMTP_SCHEME') !== 'tls' ||
            $this->f3->get('SMTP_SCHEME') !== 'ssl') {
            $scheme = null;
        } else {
            $scheme = $this->f3->get('SMTP_SCHEME');
        }

        $host = $this->f3->get('SMTP_HOST');
        $port = $this->f3->get('SMTP_PORT');
        $username = $this->f3->get('SMTP_USERNAME');
        $password = $this->f3->get('SMTP_PASSWORD');

        $smtp = new \SMTP (
            $host,
            $port,
            $scheme,
            $username,
            $password
        );

        $site_name = $this->f3->get('SITE_NAME');
        $site_url = $this->f3->get('SITE_URL');

        $smtp->set('From', '<'.$username.'>');
        $smtp->set('To', '<'.$this->email.'>');
        $smtp->set('Subject', $site_name.' email verification');

        // creates 12 digit random string
        $email_verification = bin2hex( random_bytes(6) );

        $message = <<<MESSAGE
Hello $this->username,

Your email verification code is:
    
    $email_verification

Log in at $site_url/login with the username

    $this->username

and the password you signed up with.

Go to $site_url/verify-email and enter the verification code. This will allow
you to reset your password if you ever forget it.

Sincerely,
The $site_name Team
MESSAGE;

        if($smtp->send($message)) {
            $this->email_verification_hash = password_hash($email_verification, PASSWORD_DEFAULT);
            $user = new \DB\SQL\Mapper($this->db, 'users');
            $user->load(array('username=?', $this->username));
            $user->email_verification_hash = $this->email_verification_hash;
            $user->save();
            return true;
        }

        array_push($this->send_email_verification_code_errors, _(
            'Could not send email verification.'
        ));
        return false;
    }
}