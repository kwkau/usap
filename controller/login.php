<?php

class login extends Controller{

    public function __construct(){
        $number_of_days = 30;
        $number_of_hours = 24;
        $date_of_expiry = time() + 60 * 60 * 24 * $number_of_days;
        $date_of_expiry = time() + 60 * 60 * $number_of_hours;
        parent::__construct("login");
    }

    /**
     * index will be the first function to be called when the user
     * requests for home page
     */
    public function index(){
        $this->viewBag["title"] = "usap";
        $token = new token_mdl("lecturer_token");
        if($token->token_count < 1){
            $token->set_token("lecturer_token",TOKEN);
        }
        /*----------------------------------------------------------------------------------------
         * check if the user has a valid token, if this is true redirect the user to the dropzone
         *---------------------------------------------------------------------------------------*/

        if(isset($_COOKIE["user_token"])){
            //we have a user with a user_token
            //obtain the username and token from the cookie
            @list($username,$token,$series_identifier) = @explode(":",$_COOKIE["user_token"]);

            //verify that the token obtained is a valid token
            $res = $this->model->verify_token($username, $token, $series_identifier);

            if(is_array($res) && $res["status"] == 0){
                //the user presented a correct token
                $user = new user_mdl($username);
                $ret = $res["user"];

                //issue the user a new token
                setcookie("user_token","{$username}:{$ret->token}:{$series_identifier}",time() + 60 * 60 * 24,"/",null,null,true);

                //set session variables
                session::set("user_id",$user->id);
                session::set("friend_id", $user->friend_id);
                session::set("login_string",$this->hash($user->password.$_SERVER['HTTP_USER_AGENT']));
                session::set("department",$user->profile->department->name);

                //send the user to their dropzone
                $this->redirect("dropzone");
            }else if($res == 3){
                //invalid token todo: notify the user that they have been attacked
                session::set("invalid_token",true);
            }else if($res == 2){
                //invalid series identifier, we have caught an attacker todo: decide on what to do with the attacker
                session::set("invalid_series_identifier",true);
            }else if($res == 1){
                //invalid user
                session::set("cookie_invalid",true);
            }
        }



        $dep = new department_mdl();
        $this->viewBag["departments"] = $dep->get_all_dep();

        //we check to see if there was an error with the login process
        if(session::get('username_err')){
            $this->viewBag["username_err"] = true;
        }elseif(session::get('password_err')){
            $this->viewBag["password_err"] = true;
        }

        session::set("username_err", false);
        session::set("password_err", false);

        $this->view("Layout");
    }


    /**
     * this function will perform the login functions when a user tries
     * to login into their account usap
     */
    public function user_login(){
        /*-----------------------------------
         * check if the user is a valid user
         *----------------------------------*/
            $username = filter_input(INPUT_POST, "username");
            $password = filter_input(INPUT_POST, "password-hash");
        $login = $this->model->login_check($username,$password);

        if($login["logged_in"]){
            $user = new user_mdl();
            if(filter_input(INPUT_POST, "remember_me") == "checked"){
                //create a token for the user
                $user->set_token($login["user_id"]);

                //send the token together with the user's email to user as a cookie
                $number_of_days = 30;
                setcookie("user_token","{$username}:{$user->token}:{$user->series_identifier}",time() + 60 * 60 * 24 * $number_of_days,"/",null,null,true);
            }else{
                //clear any stored tokens for this user
                $user->delete_token($login["user_id"]);
            }

            /*------------------------
             * user has been verified
             * set session variables
             *----------------------*/
            session::set("user_id",$login["user_id"]);
            session::set("friend_id", $login["friend_id"]);
            session::set("login_string",$login["login_string"]);
            session::set("department",$login["department"]);


            /*-----------------------------------------------------
             * redirect the user to their dropzone
             * todo: add profile home url to the redirect function
             *---------------------------------------------------*/
            $this->redirect("dropzone");
        }else{
            /*------------------------
             * check for login error
             *----------------------*/
            if($login["error_type"] == LOGIN_USERNAME_ERR){
                session::set("username_err", true);

                /*-------------------------------------
                 * redirect the user back to login page
                 *------------------------------------*/
                $this->redirect();
            }elseif ($login["error_type"] == LOGIN_PASSWORD_ERR){
                session::set("password_err", true);

                /*-------------------------------------
                 * redirect the user back to login page
                 *------------------------------------*/
                $this->redirect();
            }
        }
    }

    public function register(){
        //registration values
        $profile_fields['password'] = filter_input(INPUT_POST,'reg-password-hash');
        $profile_fields['email'] = filter_input(INPUT_POST,'user_email');
        $profile_fields["category"] = filter_input(INPUT_POST,'category');
        $profile_fields['gender'] = filter_input(INPUT_POST,'gender');
        $profile_fields['department'] = filter_input(INPUT_POST,'department');
        $profile_fields['first_name'] = filter_input(INPUT_POST,'first_name');
        $profile_fields['last_name'] = filter_input(INPUT_POST,'last_name');
        $profile_fields['token'] = filter_input(INPUT_POST,'token');
        $profile_fields['index_number'] = filter_input(INPUT_POST,'index_number');

        if($profile_fields["category"] == "lecturer"){
            //todo: validate lecturer token
            //obtain the lecturer_token
            $lect_token = new token_mdl("lecturer_token");

            $chck_token = $this->hash($profile_fields['token']);

            //check if the token provided is the same as the token in the database
            if($chck_token == $lect_token->token){
                //check if the the hash value in the database is the same as the hash value of the provided token

                if($this->hash($chck_token.$lect_token->token_salt) != $lect_token->token_hash){
                    //token hash is not a match redirect the user back to the landing page
                    $this->redirect();
                }
            }else{
                //wrong token redirect the user back to the landing page
                $this->redirect();
            }
        }elseif($profile_fields["category"] == "student" || $profile_fields["category"] == "alumni"){
            //todo: validate index number
            if(!preg_match("/[a-zA-Z]{2,3}\/[a-zA-Z]{3}\/[0-9]{2,3}\/[0-9]/i",$profile_fields['index_number'])){
                //incorrect index number
                $this->redirect();
            }
        }

        /*-------------------------------------
         * insert the user into the database
         *------------------------------------*/
        $register_user = $this->model->register_user($profile_fields);

        if($register_user["registered"]){
            /*----------------------------------------
             * redirect the user to the dropzone page
             *--------------------------------------*/
            session::set("user_id",$register_user["user_id"]);
            session::set("friend_id", $register_user["friend_id"]);
            session::set("login_string",$register_user["login_string"]);
            session::set("department",$register_user["department"]);
            $this->redirect("dropzone");
        }else{
            /*-----------------------------------------
             * redirect the user back to the home page
             * todo: create friend request table
             *----------------------------------------*/
            $this->redirect();
        }
    }
}