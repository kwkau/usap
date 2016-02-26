<?php


class friend_mdl extends Model{
    public function __construct($user_id=null)
    {
        parent::__construct();

        if(!empty($user_id))return $this->get_frnds($user_id);
    }

    public $id;
    /**
     * @var $user_prof profile_data the user
     */
    public $user_prof;
    public $target;
    public $perp;
    public $magic_id;
    public $created_at;

    public function get_frnds($user_id)
    {
        $frnds = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_friend_list(?)", array($user_id),PDO::FETCH_ASSOC,true);
        foreach($result["data"] as $val){
            $frnd = new friend_mdl();
            $frnd->id = $val["friend_id1"];
            $frnd->user_prof = new profile_data($val["user_id"]);
            $frnds[] = $frnd;
        }
        return $frnds;
    }

    public function mke_frnd($frnd_id1,$frnd_id2)
    {
        $this->pdo_insert("CALL ussap.insert_friends(:frnd1,:frnd2)",
            array(
                ":frnd1" => $frnd_id1,
                ":frnd2" => $frnd_id2
            ));
    }

    public function frnd_count($id)
    {
        $result = $this->pdo_fetch("CALL ussap.fetch_friend_list(?)", array($id),PDO::FETCH_ASSOC,true);
        return $result["count"];
    }

    /**
     * this is a function to check whether if one user is the friend of another user
     * @param $user int the friend_id of the user who we are checking against
     * @param $indi int friend_id of the individual who we want to check if is a friend of the user
     * @return bool true if the $indi is a friend to $user
     */
    public function chck_frnd($user,$indi)
    {
        $result = $this->pdo_fetch("CALL ussap.chck_frnd(?,?)",array($user,$indi),PDO::FETCH_ASSOC);
        return $result["count"] > 0;
    }

    public function insert_friend_request()
    {
        $this->pdo_insert("CALL ussap.insert_friend_request(:t_id,:p_id,:m_id)",
            array(
                ":t_id" => $this->target,
                ":p_id" => $this->perp,
                ":m_id" => $this->magic_id
            ));
    }

    public function delete_friend_request()
    {
        $this->pdo_delete("DELETE FROM ussap.friend_request WHERE ussap.friend_request.magic_id = ?",
            array(
                $this->magic_id
            ));
    }
}