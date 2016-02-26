<?php


class forum_smiley_mdl extends Model{

    public function __construct($magic_id = null)
    {
        parent::__construct();
    }

    public $id;
    public $comment_id;
    public $comment_magic_id;
    /**
     * @var $user_prof profile_data the profile of the user who liked the forum comment
     */
    public $user_prof;
    public $error_code = 0;

    public function get_smileys($comment_magic_id)
    {
        $smileys = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_forum_smiley(?)",array($comment_magic_id),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach($result["data"] as $val){
                $smiley = new forum_smiley_mdl();
                $smiley->id = $val["forum_comment_smiley_id"];
                $smiley->user_prof = new profile_data($val["user_id"]);
                $smileys[] = $smiley;
            }
        }else{
            $this->error_code = 1;
        }
        return $smileys;
    }

    public function set_smiley()
    {
        $this->pdo_insert("CALL ussap.insert_forum_smiley(?,?)",
            array(
                $this->user_prof->user_id,
                $this->comment_magic_id
            ));
    }

    public function del_smiley()
    {
        $this->pdo_delete("CALL ussap.delete_forum_comment_smiley(?,?)",
            array(
                $this->user_prof->user_id,
                $this->comment_magic_id
            ));
    }

}