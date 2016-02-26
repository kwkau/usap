<?php


class bookmark_mdl extends Model{
    public function __construct($type=null)
    {
        parent::__construct();
    }

    public $id;
    public $created_at;
    public $bookmark_type;
    /**
     * @var $user_id int the id of user who created the bookmark
     */
    public $user_id;
    /**
     * @var $forum forum_mdl the bookmarked forum
     */
    public $forum;
    /**
     * @var $post post_mdl the bookmarked post
     */
    public $post;
    public $magic_id;
    public $error_code = 0;
    public $exists =false;


    public function get_bkmks($type, $user_id)
    {
        $bkmks = array();
        if($type == "forum"){
            $result = $this->pdo_fetch("CALL ussap.fetch_forum_bkmk(?)",array($user_id),PDO::FETCH_ASSOC,true);
            foreach($result["data"] as $val){
                $frm = new forum_mdl();
                $frm->get_forum($val["forum_magic_id"]);
                $bkmk = new bookmark_mdl();
                $bkmk->id = $val["bookmark_id"];
                $bkmk->created_at = $val["created_at"];
                $bkmk->user_id = $val["user_id"];
                $bkmk->bookmark_type = $val["bookmark_type"];
                $bkmk->forum = $frm;
                $bkmks[] = $bkmk;
            }
        }elseif ($type == "post"){
            $result = $this->pdo_fetch("CALL ussap.fetch_post_bkmk(?)",array($user_id),PDO::FETCH_ASSOC,true);
            foreach($result["data"] as $val){
                $bkmk = new bookmark_mdl();
                $post = new post_mdl();
                $post->get_post($val["post_magic_id"]);
                $bkmk->id = $val["bookmark_id"];
                $bkmk->created_at = $val["created_at"];
                $bkmk->user_id = $val["user_id"];
                $bkmk->bookmark_type = $val["bookmark_type"];
                $bkmk->post = $post;
                $bkmks[] = $bkmk;
            }
        }
        return $bkmks;
    }

    public function set_bkmk()
    {
        if($this->bookmark_type == "post"){
            $this->pdo_insert("CALL ussap.insert_post_bkmk(:user_id,:book_mark_type,:post_magic_id,:created_at)",
                array(
                    ":user_id" => $this->user_id,
                    ":book_mark_type" => "post",
                    ":post_magic_id" => $this->magic_id,
                    ":created_at" => $this->created_at
                ));
        }elseif($this->bookmark_type == "forum"){
            $this->pdo_insert("CALL ussap.insert_forum_bkmk(:user_id,:book_mark_type,:forum_magic_id,:created_at)",
                array(
                    ":user_id" => $this->user_id,
                    ":book_mark_type" => "forum",
                    ":forum_magic_id" => $this->magic_id,
                    ":created_at" => $this->created_at
                ));
        }

    }
}