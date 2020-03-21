<?php
namespace F3StarterApp\Model;

class User {
    private $username;
    private $password;
    private $email;
    private $db;
    private $validate_new_user_errors = array();
    private $signup_errors = array();
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
        if(count($this->validate_new_user_errors))
            return false;
        return true;
    }

    public function getValidateNewUserErrors() {
        return $this->validate_new_user_errors;
    }

    public function signup() {
        if(count($this->validate_new_user_errors)) {
            array_push($this->signup_errors, _(
                'The new user did not pass validation.'
            ));
        }

    }

    public function getSignupErrors() {

    }
}