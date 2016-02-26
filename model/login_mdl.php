<?php

class login_mdl extends Model{

    public function __construct(){
        parent::__construct();
    }

    /**
     * this model function will be responsible for verifying the login credentials
     * of the user by querying it against the database
     * @param $username string the username of the user
     * @param $password string the password of the user
     * @return array|mixed will return login parameters array id the user is successfully verified or false if
     * otherwise
     */
    public function login_check($username,$password)
    {
        //we are creating our user instance
        $usr = new user_mdl($username);

        /*-----------------------------------------------
         * there is a user that has the username entered
         *----------------------------------------------*/
        if ($usr->exists) {
            //check for the occurrence of brute force
            if (!($this->check_brute($usr->id))) {

                //user name verified and brute force checked, now time to verify the password

                $hashPass = $this->hash($password . $usr->salt);


                if ($usr->password  == $hashPass) {
                    /*-------------------------------------------
                     * set the user's log on status to logged on
                     *-------------------------------------------*/
                    $usr->logon();


                    return array(
                        "friend_id" => $usr->friend_id,
                        "user_id" => $usr->id,
                        "login_string" => $this->hash($hashPass.$_SERVER['HTTP_USER_AGENT']),
                        "department" => $usr->profile->department->name,
                        "logged_in" => true
                    );
                } else {
                    //password not verified
                    //we need to take a log of this in the login_attempts table
                    $this->pdo_insert ("insert into ussap.login_attempts (user_id, login_attempt_time)
                                  values(:userid,:current_time)",array(':userid'=> $usr->id,':current_time'=>time()));
                    return array ("logged_in" => false, "error_type"=> LOGIN_PASSWORD_ERR);
                }
            } else {
                //account has been locked so therefore cannot enter anymore passwords
                session::set ('account_locked', true);
                return array("logged_in" => false, "error_type"=> ACCOUNT_LOCK);
            }

        } else {
            //username not verified
            session::set ('login_err', LOGIN_USERNAME_ERR);
            return array ("logged_in" => false, "error_type"=> LOGIN_USERNAME_ERR);
        }

    }


    public function verify_token($username, $token, $series_identifier)
    {
        $user = new user_mdl();
        //obtain the token for the specified username from the database
        $db_token = $user->get_token($username);
        if($db_token){
            //we have a valid user
            if($user->series_identifier == $series_identifier){
                //we have user with a valid series identifier
                if($user->token == $token){
                    //token fully verified regenerate new token for user
                    $user->set_token($user->id);
                    return array("status" => 0,"user" => $user);
                } else {
                    // the user presented an invalid token
                    return 3;
                }
            } else {
                //the user presented an invalid series identifier
                return 2;
            }
        } else {
            //the user does not exist
            return 1;
        }
    }


    /**
     * this function will be used to register new users into the system by entering
     * the users into the database
     * @param $profile_fields array array containing the profile data of the user
     * @return bool true if the user is successfully registered and false if
     * otherwise
     */
    public function register_user($profile_fields)
    {
        $user = new user_mdl();

        return $user->register($profile_fields);
    }

    public function get_departments()
    {
        return $this->pdo_fetch("CALL ussap.fetch_departments()",array(),PDO::FETCH_ASSOC,true);
    }

    /**
     * function to check the number of times the user has tried to login, this is a measure to prevent
     * a brute force attack on the system
     * @param $id int the id of the user
     * @return bool returns true
     */
    private function check_brute($id){
        // All login attempts are counted from the past 2 hours
        $valid_time = time() - (2 * 60 * 60);
        $result = $this->pdo_fetch('select ussap.login_attempts.login_attempt_time from ussap.login_attempts
                  where ussap.login_attempts.user_id = ? and ussap.login_attempts.login_attempt_time > ? ',array($id,$valid_time),PDO::FETCH_NUM);
        if($result['count'] < 5){
            session::set('login_count', $result['count']);
            return false;
        }else if($result['count'] > 5 && $result['count'] > 10){
            //todo: log brute force incident, suspicious activity

            return true;
        }else{

            return true;
        }
    }



}