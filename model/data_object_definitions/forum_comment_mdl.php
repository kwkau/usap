<?php


class forum_comment_mdl extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public $id;
    public $text;
    public $created_at;
    public $forum_id;
    /**
     * @var $user_prof profile_data the profile of the user who posted the comment
     */
    public $user_prof;
    /**
     * @var $smileys array the smileys for this comment
     */
    public $smileys;
    public $magic_id;
    public $forum_magic_id;
    public $error_code = 0;

    /**
     * @param $id string|int
     * @param $type string
     * @return array the list of comments that belong to the forum with magic id that was specified
     */
    public function get_forum_comments($id, $type)
    {
        $magic_id = $type == "magic" ? $id : 0;
        $forum_id = $type == "forum" ? $id : 0;
        $type = $type == "magic" ? 1 : 0;

        $comments = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_forum_comments(:forum_id,:magic_id,:type)",
            array(
                ":magic_id" => $magic_id,
                ":forum_id" => $forum_id,
                ":type" => $type
            ), PDO::FETCH_ASSOC, true);

        if ($result["count"] > 0) {
            foreach ($result["data"] as $val) {
                $comment = new forum_comment_mdl();
                $smiley = new forum_smiley_mdl();
                $comment->id = $val["forum_comment_id"];
                $comment->text = $val["content"];
                $comment->created_at = $val["created_at"];
                $comment->forum_id = $val["forum_id"];
                $comment->magic_id = $val["magic_id"];
                $comment->user_prof = new profile_data($val["user_id"]);
                $comment->smileys = $smiley->get_smileys($comment->magic_id);
                $comments[] = $comment;
            }
        }
        return $comments;
    }

    public function set_forum_comment()
    {
        $this->pdo_insert("CALL ussap.insert_forum_comments(:forum_magic_id,:user_id,:comment_text,:created_at,:magic_id)",
            array(
                ":forum_magic_id" => $this->forum_magic_id,
                ":user_id" => $this->user_prof->user_id,
                ":comment_text" => $this->text,
                ":created_at" => $this->created_at,
                ":magic_id" => $this->magic_id
            ));
    }

    public function comment_count($forum_id)
    {
        $result = $this->pdo_fetch("Call ussap.forum_comment_count(?)", array($forum_id), PDO::FETCH_NUM, true);
        return $result["count"];
    }

}