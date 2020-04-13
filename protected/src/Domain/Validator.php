<?php

namespace F3AppSetup\Domain;

class Validator {
    private $errors = array();

    public function getErrors() {
        return $this->errors;
    }

    public function validateUsername($username) {
        if($username && !ctype_alnum($username)) {
            array_push($this->errors, _(
                'The username must use only alphanumeric characters. You provided '.$username.'.'
            ));
        }
        if($username && strlen($username) > 25) {
            array_push($this->errors, _(
                'The username must not be more than 25 characters long.'
            ));
        }
        return count($this->errors) ? false : true;
    }

    public function validatePassword($password) {
        if(!$password) {
            array_push($this->errors, _(
                'A password is required for signing up.'
            ));
        }
        if($password && strlen($password) > 75) {
            array_push($this->errors, _(
                'The password must not be more than 75 characters long.'
            ));
        }
        if($password && strlen($password) < 8) {
            array_push($this->errors, _(
                'The password must be at least 8 characters long.'
            ));
        }
        return count($this->errors) ? false : true;
    }

    public function validateEmail($email) {
        if($email && filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
            array_push($this->errors, _(
                'Not a valid email address.'
            ));
        }
        return count($this->errors) ? false : true;
    }
}