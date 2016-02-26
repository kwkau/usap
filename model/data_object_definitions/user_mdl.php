<?php

class user_mdl extends Model{

    /**
     * the user_mdl fetches information about the user when given either the users email or
     * the users id
     * @param $user
     */
    public function __construct($user=null)
    {
        parent::__construct();
        if(is_numeric($user)){
            //the user_id is given
            $this->get_user_by_id($user);
        }else if(!is_numeric($user) and !empty($user)){
            //the user_email is given
            $this->get_user($user);
        }

    }

    public $id;
    public $email;
    public $password;
    public $salt;
    public $token;
    public $series_identifier;
    public $created_at;
    public $status;
    /**
     * @var $profile profile_data the users profile data
     */
    public $profile;
    public $friend_id;
    /**
     * @var $group group_data the groups the user is involved in
     */
    public $group;
    public $department;
    public $count;
    public $user_error_code = 0;
    public $exists = false;

    private function get_user($username)
    {
       $user = $this->pdo_fetch("CALL ussap.user_verify(:username)", array(':username' => $username),PDO::FETCH_ASSOC);
        if($user["count"] == 1 and !isset($user["data"]["error_status"])){
            $this->id = isset($user["data"]["user_id"])? $user["data"]["user_id"]:null;

            $this->email = isset($user["data"]["user_email"])? $user["data"]["user_email"]:null;

            $this->password = isset($user["data"]["password"])? $user["data"]["password"]:null;

            $this->salt = isset($user["data"]["salt"])? $user["data"]["salt"]:null;

            $this->created_at = isset($user["data"]["created_at"])? $user["data"]["created_at"]:null;

            $this->status = isset($user["data"]["user_status"])? $user["data"]["user_status"]:null;

            /*---------------------------------------------------
             * load the profile data into the profile_data class
             *--------------------------------------------------*/
            $this->profile = isset($user["data"]["user_id"])? new profile_data($user["data"]["user_id"]):null;

            /*---------------------------------
             * fetch the friend_id of the user
             *--------------------------------*/
            $this->friend_id = isset($user["data"]["user_id"])? $this->get_friend_id($user["data"]["user_id"]):null;

            /*---------------------------------
             * fetch the groups of the user
             *--------------------------------*/
            /*$this->groups = new group_data($this->id);*/

            $this->exists = true;
        }else if($user["data"]["error_status"] = 10){
            //the user does not exist
            $this->exists = false;
            $this->user_error_code = 10;
        }elseif ($user["data"]["error_status"] = 11){
            //we have multiple users
            $this->exists = false;
            $this->user_error_code = 11;
        }

        $this->count = $user["count"];
    }

    private function get_user_by_id($id)
    {
        $user = $this->pdo_fetch("CALL ussap.fetch_user(?)",array($id),PDO::FETCH_ASSOC);
        if($user["count"] == 1){
            $group = new group_data();
            $this->id = isset($user["data"]["user_id"])? $user["data"]["user_id"]:null;

            $this->email = isset($user["data"]["user_email"])? $user["data"]["user_email"]:null;

            $this->created_at = isset($user["data"]["created_at"])? $user["data"]["created_at"]:null;

            $this->status = isset($user["data"]["user_status"])? $user["data"]["user_status"]:null;

            /*-----------------------------------
             * load the profile data of the user
             *---------------------------------*/
            $this->profile = isset($user["data"]["user_id"])? new profile_data($user["data"]["user_id"]):null;

            /*---------------------------------
             * fetch the friend_id of the user
             *--------------------------------*/
            $this->friend_id = isset($user["data"]["user_id"])? $this->get_friend_id($user["data"]["user_id"]):null;

            /*------------------------------
             * fetch the groups of the user
             *----------------------------*/
            $this->group = $group->get_grps($this->id);

            $this->exists = true;
        }else{
            //the user does not exist
            $this->exists = false;
            $this->user_error_code = 10;
        }
    }

    public function get_password($id)
    {
        $result = $this->pdo_fetch("SELECT ussap.user.password FROM ussap.user WHERE ussap.user.user_id = ?",array($id),PDO::FETCH_ASSOC);
        return $result["data"]["password"];
    }

    public function get_friend_id($user_id)
    {
        $user_frnd = $this->pdo_fetch("select ussap.friends.friend_id from ussap.friends where ussap.friends.user_id = ? ", array($user_id),PDO::FETCH_ASSOC);
        if($user_frnd["count"] == 1){
            return $user_frnd["data"]["friend_id"];
        }else{
            return null;
        }
    }

    public function set_token($id)
    {
        $result = $this->pdo_fetch("CALL ussap.fetch_user(?)",array($id),PDO::FETCH_ASSOC);
        if($result["count"] > 0) {
            $this->id = $id;
            $user = $result["data"];
            //generate the token for the user
            $this->token = $this->hash($this->salt().$user["series_identifier"]);
            $this->series_identifier = $user["series_identifier"];
            //insert the token into the database for the particular user
            $this->insert_user_token();
        }
    }

    public function delete_token($id)
    {
        $this->pdo_update("UPDATE ussap.user SET ussap.user.token = NULL WHERE ussap.user.user_id = ?",array($id));
    }

    public function get_token($username)
    {
        $result = $this->pdo_fetch("CALL ussap.fetch_user_token(?)",array($username),PDO::FETCH_ASSOC);
        if($result["count"] == 1){
            $this->id = $result["data"]["user_id"];
            $this->token = $result["data"]["token"];
            $this->series_identifier = $result["data"]["series_identifier"];
            $this->get_user_by_id($this->id);
            return true;
        }else{
            return false;
        }
    }



    private function insert_user_token()
    {
        $this->pdo_update("UPDATE ussap.user SET ussap.user.token = :token WHERE ussap.user.user_id = :user_id",
            array(
                ":token" => $this->token,
                ":user_id" => $this->id
            ));
    }

    public function logon()
    {
        if($this->exists)
        {
            $this->pdo_update("update ussap.user set ussap.user.user_status = :log where ussap.user.user_id = :userid", array(':log' => 1,':userid' => $this->id));
        }
    }

    public function logout()
    {
        if($this->exists)
        {
            echo $this->id;
            $this->pdo_update("update ussap.user set ussap.user.user_status = :log where ussap.user.user_id = :userid", array(':log' => 0,':userid' => $this->id));
        }
    }


    public function register($profile_fields)
    {
        //check if the user exists
        $user = new user_mdl($profile_fields["email"]);
        if(!$user->exists){
            /*--------------------------------------------
             * we generate our salt for our password hash
             *-------------------------------------------*/
            $salt = $this->salt();//salt to make our password tasty
            $password = $this->hash($profile_fields["password"].$salt);

            /*---------------------------------------------------------
             * create our user by inserting the user into the database
             * the stored procedure insert_will also return the user id
             * of the user we just created
             *--------------------------------------------------------*/
            $usr_insrt = $this->pdo_fetch("CALL ussap.insert_user(:username,:password,:salt,:timestamp,:series_identifier)",
                array(
                    ':username' => $profile_fields["email"],
                    ':password' => $password,
                    ':salt' => $salt,
                    ':timestamp' => $this->dt->get_date("date.timezone"),
                    ":series_identifier" => $this->salt()
                ),PDO::FETCH_ASSOC);


            /*------------------------------------------------------
             * next on the list is to create a profile for the user
             *-----------------------------------------------------*/
            $profile = new profile_data();
            $profile->insert_profile_data($usr_insrt["data"]["user_id"],$profile_fields);


            /*-------------------------------------------------------------
             * we are now going to create a friend id for the user so that
             * our user can make friends
             * the insert_friend procedure will return the friend id of
             * the user
             *-----------------------------------------------------------*/
            $frnd_insrt = $this->pdo_fetch("CALL ussap.insert_friend(:user_id)",
                array(
                    ":user_id" => $usr_insrt["data"]["user_id"]
                ),PDO::FETCH_ASSOC);

            //we create our user instance
            $user = new user_mdl($usr_insrt["data"]["user_id"]);
            $user->logon();

            if($usr_insrt["state"] && $frnd_insrt["state"]){
                return array(
                    "user_id" =>$user->id,
                    "login_string" => $this->hash($password.$_SERVER['HTTP_USER_AGENT']),
                    "friend_id" => $user->friend_id,
                    "department" => $user->profile->department->name,
                    "registered" => true);
            } else {
                return array("registered" => false, "error_type" => REG_USER_EXIST);
            }
        }
        return false;
    }

    public function mke_admin()
    {

    }

    public function delete_user($id)
    {
        $this->pdo_delete("CALL ussap.delete_user(?)",array($id));
    }

    public function restore_user($id)
    {
        $this->pdo_update("UPDATE ussap.profile SET ussap.profile.black_list = 0 WHERE ussap.profile.user_id = ?",array($id));
    }

}