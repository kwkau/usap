<?php


class post_comment_smiley_mdl extends Model{
    public function __construct()
    {
        parent::__construct();

    }

    public $id;
    public $comment_magic_id;
    /**
     * @var $user_prof profile_data the user who liked the post comment
     */
    public $user_prof;
    public $error_code = 0;

    public function get_smileys($post_comment_magic_id)
    {
        $smileys = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_post_comment_smiley(?)",array($post_comment_magic_id),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach($result["data"] as $val){
                $smiley = new post_comment_smiley_mdl();
                $smiley->id = $val["post_comment_smiley_id"];
                $smiley->user_prof = new profile_data($val["user_id"]);
                $smileys[] = $smiley;
            }
        }
        return $smileys;
    }

    public function set_smiley()
    {
        $this->pdo_insert("CALL ussap.insert_post_comment_smiley(:user_id,:magic_id)",
            array(
                ":user_id" => $this->user_prof->user_id,
                ":magic_id" => $this->comment_magic_id
            ));
    }

    public function delete_smiley()
    {
        $this->pdo_insert("CALL ussap.delete_post_comment_smiley(:user_id,:magic_id)",
            array(
                ":user_id" => $this->user_prof->user_id,
                ":magic_id" => $this->comment_magic_id
            ));
    }

}