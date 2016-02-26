<?php


class post_comment_mdl extends Model{
    public function __construct()
    {
        parent::__construct();
    }

    public $id;
    public $magic_id;
    public $text;
    public $created_at;
    public $post_magic_id;
    public $post_id;
    /**
     * @var $user_prof profile_data the user who posted the comment
     */
    public $user_prof;
    /**
     * @var $smileys post_comment_smiley_mdl the smileys for this comment
     */
    public $smileys;
    public $pic_url;
    public $content_type;
    public $error_code = 0;

    public function get_post_comments($id,$type)
    {
        $magic_id = $type == "magic"? $id : 0;
        $post_id = $type == "post"? $id : 0;
        $type = $type == "magic"? 1 : 0;

        $comments = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_post_comments(:post_id,:magic_id,:type)",
            array(
                ":post_id" =>$post_id,
                ":magic_id" => $magic_id,
                ":type" => $type
            ),PDO::FETCH_ASSOC,true);

        if($result["count"] > 0){
            foreach($result["data"] as $val){
                $comment = new post_comment_mdl();
                $smiley = new post_comment_smiley_mdl();
                $comment->id = $val["post_comment_id"];
                $comment->magic_id = $val["magic_id"];
                $comment->text = $val["content"];
                $comment->created_at = $val["created_at"];
                $comment->post_id = $val["post_id"];
                $comment->user_prof = new profile_data($val["user_id"]);
                $comment->smileys = $smiley->get_smileys($comment->magic_id);
                $comments[] = $comment;
            }
        }
        return $comments;
    }

    public function comment_count($post_id)
    {
        $result = $this->pdo_fetch("CALL ussap.post_comment_count(?)",array($post_id),PDO::FETCH_ASSOC,true);
        return $result["count"];
    }

    public function set_post_comment()
    {
        $this->pdo_insert("CALL ussap.insert_post_comment(:forum_magic_id,:user_id,:comment_text,:created_at,:magic_id)",
            array(
                ":forum_magic_id" => $this->post_magic_id,
                ":user_id" => $this->user_prof->user_id,
                ":comment_text" => $this->text,
                ":created_at" => $this->created_at,
                ":magic_id" => $this->magic_id
            ));
    }

}