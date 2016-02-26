<?php


class post_mdl extends Model{
    public function __construct($post_id=null)
    {
        parent::__construct();
        if(!empty($post_id))$this->get_post($post_id);
    }

    public $id;
    public $department_id;
    public $created_at;
    /**
     * @var $comments int the number of comments a post has
     */
    public $comments;
    /**
     * @var $user_prof profile_data the profile of the user who created the post
     */
    public $user_prof;
    /**
     * @var $smileys int the number of smileys the post has
     */
    public $smileys;
    public $post_text;
    /**
     * @var $target string states target audience of a post (public or friends)
     */
    public $target;
    public $pic_url;
    /**
     * @var $content_type string the data format for the content of a post(text, multi, pic)
     */
    public $content_type;
    /**
     * @var $group_id group_data the id of the group the post belongs to
     */
    public $group_id;
    public $post_type;
    public $magic_id;

    /**
     * function to fetch all three posts namely
     * <ul>
     *  <li>Departmental posts ($type = "department")</li>
     *  <li>User posts, posts that belong to the user ($type = "user")</li>
     *  <li>Group posts, ($type = "group")</li>
     * </ul>
     * @param $type string the type of post you want to fetch
     * @param $id mixed the of the user id, the department name or the friend id of the user
     * @return array|bool an array of post_mdl objects
     */
    public function get_posts($id,$type)
    {
        $posts = array();$result= "";
        switch($type){
            case "department":
                $result = $this->pdo_fetch("CALL ussap.fetch_department_posts(:department_id)",array(":department_id" => $id),PDO::FETCH_ASSOC,true);
                break;
            case "user":
                $result = $this->pdo_fetch("CALL ussap.fetch_all_user_posts(:user_id)",array(":user_id" => $id),PDO::FETCH_ASSOC,true);
                break;
            case "group":
                $result = $this->pdo_fetch("CALL ussap.fetch_group_post(:group_id)",array(":group_id" => $id),PDO::FETCH_ASSOC,true);
                break;
        }

        if($result["count"] > 0){
            foreach($result["data"] as $val){
                if($val["flag"] > 0)continue;
                $post = new post_mdl();
                $smiley = new post_smiley_mdl();
                $comment = new post_comment_mdl();
                $post->id = $val["post_id"];
                $post->created_at = $val["created_at"];
                $post->user_prof = new profile_data($val["user_id"]);
                $post->pic_url = $val["pic_url"];
                $post->comments = $comment->comment_count($post->id);
                $post->smileys = $smiley->get_smileys($val["magic_id"]);
                $post->magic_id = $val["magic_id"];
                $post->content_type = $val["content_type"];
                $post->post_text = $val["post_text"];
                $post->post_type = $val["post_type"];
                $post->target = $val["post_type"] == "user" ? $val["target"]:$val["post_type"];
                $posts[] = $post;
            }
        }
        return $posts;
    }

    public function set_post($type)
    {
        if($type == "department"){
            $this->pdo_insert("CALL ussap.insert_department_post(:user_id,:post_text,:pic_url,:content_type,:created_at,:department_id,:magic_id)",
                array(
                    ":user_id" => $this->user_prof->user_id,
                    ":post_text" => $this->post_text,
                    ":pic_url" => $this->pic_url,
                    ":content_type" => $this->content_type,
                    ":created_at" => $this->created_at,
                    ":department_id" => $this->department_id,
                    ":magic_id" => $this->magic_id
                ));
        }elseif($type == "general"){
            $this->pdo_insert("CALL ussap.insert_general_post(:user_id,:post_text,:pic_url,:content_type,:created_at,:magic_id)",
                array(
                    ":user_id" => $this->user_prof->user_id,
                    ":post_text" => $this->post_text,
                    ":pic_url" => $this->pic_url,
                    ":content_type" => $this->content_type,
                    ":created_at" => $this->created_at,
                    ":magic_id" => $this->magic_id
                ));

        }elseif($type == "friend"){
            $this->pdo_insert("CALL ussap.insert_friend_post(:user_id,:post_text,:pic_url,:content_type,:created_at,:magic_id)",
                array(
                    ":user_id" => $this->user_prof->user_id,
                    ":post_text" => $this->post_text,
                    ":pic_url" => $this->pic_url,
                    ":content_type" => $this->content_type,
                    ":created_at" => $this->created_at,
                    ":magic_id" => $this->magic_id
                ));
        }elseif($type == "group"){
            $this->pdo_insert("CALL ussap.insert_group_post(:user_id,:group_id,:post_text,:pic_url,:content_type,:created_at,:magic_id)",
                array(
                    ":user_id" => $this->user_prof->user_id,
                    ":group_id" => $this->group_id,
                    ":post_text" => $this->post_text,
                    ":pic_url" => $this->pic_url,
                    ":content_type" => $this->content_type,
                    ":created_at" => $this->created_at,
                    ":magic_id" => $this->magic_id
                ));
        }
    }

    public function get_post($magic_id)
    {
        $result = $this->pdo_fetch("CALL ussap.fetch_post(?)",array($magic_id),PDO::FETCH_ASSOC);
        if($result["count"] > 0 && $result["data"]["flag"] == 0){
            $comment = new post_comment_mdl();
            $smiley = new post_smiley_mdl();
            $this->id = $result["data"]["post_id"];
            $this->created_at = $result["data"]["created_at"];
            $this->user_prof = new profile_data($result["data"]["user_id"]);
            $this->comments = $comment->comment_count($this->id);
            $this->pic_url = $result["data"]["pic_url"];
            $this->magic_id = $result["data"]["magic_id"];
            $this->smileys = $smiley->get_smileys($this->magic_id);
            $this->content_type = $result["data"]["content_type"];
            $this->post_text = $result["data"]["post_text"];
            $this->target = "singular";
            $this->post_type = $result["data"]["post_type"];
        }
    }

    public function flag($magcId)
    {
        $result = $this->pdo_update("Call ussap.post_flag(?)",array($magcId));
    }

    public function fetch_flagged()
    {
        $posts = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_flagged_posts(?)",array(FLAG_LIMIT),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach ($result["data"] as $val) {
                $post = new post_mdl();
                $smiley = new post_smiley_mdl();
                $comment = new post_comment_mdl();
                $post->id = $val["post_id"];
                $post->created_at = $val["created_at"];
                $post->user_prof = new profile_data($val["user_id"]);
                $post->pic_url = $val["pic_url"];
                $post->comments = $comment->comment_count($post->id);
                $post->smileys = $smiley->get_smileys($val["magic_id"]);
                $post->magic_id = $val["magic_id"];
                $post->content_type = $val["content_type"];
                $post->post_text = $val["post_text"];
                $post->post_type = $val["post_type"];
                $posts[] = $post;
            }
        }
        return $posts;
    }
}