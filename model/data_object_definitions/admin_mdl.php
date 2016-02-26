<?php

class admin_mdl extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    public $id;
    public $username;
    public $password;
    public $salt;

    /**
     * function to check the credentials of the admin trying to login into the dashboard
     * @param string $username the username of the admin
     * @param string $password the password of the admin
     * @return boolean true if the credentials are successfully verified false otherwise
     */
    public function login_verf($username, $password)
    {
        //check if the username exist in our database
        $rslt = $this->pdo_fetch("select * FROM ussap.usap_admin as admn where admn.username = ?",array($username),PDO::FETCH_ASSOC,true);

        if($rslt["count"] == 1){
            $user = $rslt["data"][0];
            $hashPass = $this->hash($password.$user["salt"]);
            if($hashPass == $user["password"]){
                return true;
            }
        }

        return false;
    }

    /**
     * function to register a new admin for the platform
     * @param $fields array array list containing the data required to register a new admin
     * @return boolean returns true if the admin is successfully registered
     */
    public function register($fields)
    {
        $salt = $this->salt();
        $hashPass = $this->hash($fields["password"].$salt);
        $this->pdo_insert("insert into ussap.usap_admin (first_name,last_name,username, password, salt) VALUES (?,?,?,?,?)",
            array($fields["first_name"],$fields["last_name"],$fields["username"],$hashPass,$salt));
        return true;
    }


    /**
     * function to check if the admin table is empty
     * @return bool return true if the table is not empty false otherwise
     */
    public function is_empty()
    {
        $rslt = $this->pdo_fetch("select admn.admin_id from ussap.usap_admin as admn limit 50",array(),PDO::FETCH_NUM);
        return $rslt["count"] == 0;
    }
}