<?php
class Controller_mdl extends Model {

    function __construct() {
        parent::__construct();
    }


    /**
     * function to check if a user has logged in by checking their login strings
     * @param $uid int the unique id of the user (user id)
     * @param $lg_strng string unique hash value for every legitimately logged on user
     * @param $uagnt string the name of the user agent of the user
     * @return bool returns true of the login string is successfully verified and false if otherwise
     */
    public function l_verf($uid, $lg_strng, $uagnt) {
        $user = $this->pdo_fetch('select ussap.user.password from ussap.user where ussap.user.user_id = ?',array($uid),PDO::FETCH_ASSOC);

        if ($user["count"] == 1) {
            $login_check = $this->hash($user["data"]['password'] . $uagnt);
            if ($lg_strng == $login_check) { return true; } else return false;
        } else return false;
    }
}
