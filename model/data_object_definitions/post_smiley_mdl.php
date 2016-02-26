<?php


class post_smiley_mdl extends Model{
    public function __construct()
    {
        parent::__construct();
    }

    public $id;
    public $post_magic_id;
    /**
     * @var $user_prof profile_data the user who liked the post comment
     */
    public $user_prof;

    public function get_smileys($post_magic_id)
    {
        $smileys = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_post_smiley(?)",array($post_magic_id),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach($result["data"] as $val){
                $smiley = new post_smiley_mdl();
                $smiley->id = $val["post_smiley_id"];
                $smiley->user_prof = new profile_data($val["user_id"]);
                $smileys[] = $smiley;
            }
        }
        return $smileys;
    }

    public function set_post_smiley()
    {
        $this->pdo_insert("CALL ussap.insert_post_smiley(:user_id,:magic_id)",
            array(
                ":user_id" => $this->user_prof->user_id,
                ":magic_id" => $this->post_magic_id
            ));
    }

    public function del_post_smiley()
    {
        $this->pdo_insert("CALL ussap.delete_post_smiley(:user_id,:magic_id)",
            array(
                ":user_id" => $this->user_prof->user_id,
                ":magic_id" => $this->post_magic_id
            ));
    }

    public function smiley_count($post_id)
    {
        $result = $this->pdo_fetch("CALL ussap.post_smiley_count(?)",array($post_id),PDO::FETCH_ASSOC,true);
        return $result["count"];
    }

}