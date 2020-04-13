<?php
namespace F3AppSetup\Controller;

use F3AppSetup\Model\User;
use F3AppSetup\Domain\SMTP;
use F3AppSetup\Domain\Validator;

class Signup extends Middleware\Guest {
    private $identifier;
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
        $this->identifier = $this->f3->get('USERSIGNUP') === 'email' ?
            $this->request['email'] : $this->request['username'];
        $this->username = $this->request['username'];
        $this->password = $this->request['password'];
        $this->email = $this->request['email'];
        $this->user = new User();
    }

    public function show() {
        echo \View::instance()->render('signup.php');
    }

    public function signup() {
        if(!$this->validateNewUser()) {
            $this->f3->merge('session_errors', $this->validate_new_user_errors, true);
            $this->reroute('/signup');
        }

        if(!$this->userCreate()) {
            $this->f3->merge('session_errors', $this->user_create_errors, true);
            $this->reroute('/signup');
        }

        if($this->f3->get('USERSIGNUP') === 'email' || $this->email) {
            $smtp = new SMTP();
            if(!$smtp->sendEmailVerificationCode($this->identifier, $this->email)) {
                $this->f3->merge('session_errors', $smtp->getErrors(), true);
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

    public function getIdentifier() {
        return $this->identifier;
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
        $this->validatePassword();
        $this->validateUsername();
        $this->validateEmail();

        if($this->f3->get('USERSIGNUP') === 'email') {
            $this->validateNewSignupEmailUser();
        } else if($this->f3->get('USERSIGNUP') === 'anonymous') {
            $this->validateNewSignupAnonymousUser();
        } else if($this->f3->get('USERSIGNUP') === 'optional') {
            $this->validateNewSignupOptionalUser();
        }

        
        if(count($this->validate_new_user_errors))
            return false;
        return true;
    }

    public function validateNewSignupEmailUser() {
        if(!$this->email) {
            array_push($this->validate_new_user_errors, _(
                'An email address is required for signing up.'
            ));
        }
        if($this->user->userExists($this->email) !== false) {
            array_push($this->validate_new_user_errors, _(
                'The email '.$this->email.' is already taken.'
            ));
        }
    }

    public function validateNewSignupOptionalUser() {
        if(!$this->username) {
            array_push($this->validate_new_user_errors, _(
                'A username is required for signing up.'
            ));
        }
        if($this->email) {
            $this->validateEmail();
        }
        if($this->user->userExists($this->username) !== false) {
            array_push($this->validate_new_user_errors, _(
                'The username '.$this->username.' is already taken.'
            ));
        }
    }

    public function validateNewSignupAnonymousUser() {
        if(!$this->username) {
            array_push($this->validate_new_user_errors, _(
                'A username is required for signing up.'
            ));
        }
        if($this->user->userExists($this->username) !== false) {
            array_push($this->validate_new_user_errors, _(
                'The username '.$this->username.' is already taken.'
            ));
        }
        
    }

    public function validateUsername() {
        $validator = new Validator();
        $validator->validateUsername($this->username);
        $this->validate_new_user_errors = array_merge(
            $this->validate_new_user_errors, $validator->getErrors()
        );
    }

    public function validatePassword() {
        $validator = new Validator();
        $validator->validatePassword($this->password);
        $this->validate_new_user_errors = array_merge(
            $this->validate_new_user_errors, $validator->getErrors()
        );
    }

    public function validateEmail() {
        $validator = new Validator();
        $validator->validateEmail($this->email);
        $this->validate_new_user_errors = array_merge(
            $this->validate_new_user_errors, $validator->getErrors()
        );
    }

    public function userCreate() {
        if($this->user->userCreate($this)) return true;
        array_push($this->user_create_errors, _(
            'Failed to create the account.'
        ));
        return false;
    }

    public function userErase() {
        if($this->user->userErase($this->identifier)) return true;
        array_push($this->user_erase_errors, _(
            'Signed invalid user up and failed to delete the record.'
        ));
        return false;
    }
}