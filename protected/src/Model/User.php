<?php
namespace F3AppSetup\Model;

class User {
    private $username;
    private $password;
    private $email;
    private $db;
    private $validate_new_user_errors = array();
    private $signup_errors = array();
    private $send_email_verification_code_errors = array();
    private $email_verification_hash;
    private $f3;

    public function __construct($username, $password, $email) {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->f3 = \Base::instance();
        $this->db = $this->f3->get('DB');
    }

    public function validateNewUser() {
        $is_email_required = $this->f3->get('USERSIGNUP') === 'email'
            ? true : false;
        $users = $this->db->exec('SHOW TABLES LIKE \'users\'');
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
        if(count($users)) {
            $db_users = new \DB\SQL\Mapper($this->db, 'users');
            $user = $db_users->load(array('username=?', $this->username));
        } else {
            $user = FALSE;
        }
        if($user !== FALSE) {
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

    public function getValidateNewUserErrors() {
        return $this->validate_new_user_errors;
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

        $site_name = $f3->get('SITE_NAME');
        $site_url = $f3->get('SITE_URL');

        $smtp->set('From', '<'.$this->username.'>');
        $smtp->set('To', '<'.$this->email.'>');
        $smtp->set('Subject', $site_name.' email verification');

        // creates 12 digit random string
        $email_verification = bin2hex( random_bytes(6) );

        $email_verification_hash = password_hash($email_verification, PASSWORD_DEFAULT);


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
            $this->email_verification_hash = $email_verification_hash;
            $user = new \DB\SQL\Mapper($this->db, 'users');
            $user->load('username=?', $this->username);
            $user->email_verification_hash = $this->email_verification_hash;
            $user->save();
            return true;
        }

        array_push($this->validate_new_user_errors, _(
            'Could not send email verification.'
        ));
        return false;
    }

    public function getSendEmailVerificationCodeErrors() {
        return $this->email_verification_hash;
    }

    public function getEmailVerificationHash() {
        return $this->email_verification_hash;
    }

    public function signup() {
        if(count($this->validate_new_user_errors)) {
            array_push($this->signup_errors, _(
                'The new user did not pass validation.'
            ));
            return false;
        }
        $user = new \DB\SQL\Mapper($this->db, 'users');
        $user->username = $this->username;
        $user->password = password_hash($this->password, PASSWORD_DEFAULT);
        if($this->email) {
            $user->email = $this->email;
        }
        $user->save();
        return true;
    }

    public function getSignupErrors() {
        return $this->signup_errors;
    }

    public function delete() {
        $user = new \DB\SQL\Mapper($this->db, 'users');
        $user->load('username=?', $this->username);
        $user->erase();
    }
}