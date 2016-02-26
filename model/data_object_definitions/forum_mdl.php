<?php


class forum_mdl extends Model{

    public function __construct($forum_id=null)
    {
        parent::__construct();
    }

    /**
     * @var $id int
     */
    public $id;
    public $tag;
    /*
     * @var $created_at DateTime
     */
    public $created_at;
    /**
     * @var $comments int the number of comments which belong to this forum
     */
    public $comments;
    /**
     * @var $user_prof profile_data the user who created the forum
     */
    public $user_prof;
    public $topic;
    public $type;
    public $magic_id;
    public $group_id;
    public $department_id;
    private $timezone;


    /**
     * function to fetch all four forums namely
     * <ul>
     *  <li>Departmental forum ($type = "department")</li>
     *  <li>User forum ($type = "user")</li>
     *  <li>General forum ($type = "general")</li>
     *  <li>Friends forum ($type = "friend")</li>
     * </ul>
     * @param $type string the type of forum you want to fetch
     * @param $id mixed the of the user id, the department name or the friend id of the user
     * @return array|bool an array of forum_mdl objects
     */
    public function get_forums($id,$type)
    {

        $forums = array();$result= "";
        switch($type){
            case "department":
                $result = $this->pdo_fetch("CALL ussap.fetch_department_forums(:dep_id)",array(":dep_id" => $id),PDO::FETCH_ASSOC,true);
                break;
            case "user":
                $result = $this->pdo_fetch("CALL ussap.fetch_all_user_forums(:user_id)",array(":user_id" => $id),PDO::FETCH_ASSOC,true);
                break;
            case "group":
                $result = $this->pdo_fetch("CALL ussap.fetch_group_forums(:group_id)",array(":group_id" => $id),PDO::FETCH_ASSOC,true);
                break;
        }

        if(isset($result["count"]) && $result["count"] > 0){
            foreach($result["data"] as $val){
                if($val["flag"] > 0)continue;
                $forum = new forum_mdl();
                $comment = new forum_comment_mdl();
                $forum->id = $val["forum_id"];
                $forum->tag = $val["category"];
                $forum->topic = $val["forum_topic"];
                $forum->created_at = $val["created_at"];
                $forum->type = $type == "user" ? $val["target"] : $type;
                $forum->magic_id = $val["magic_id"];
                $forum->user_prof = new profile_data($val["user_id"]);
                $forum->comments = $comment->comment_count($forum->id);
                $forums[] = $forum;
            }
        }
            return $forums;
    }

    public function get_forum($magic_id)
    {
        $result = $this->pdo_fetch("CALL ussap.fetch_forum(?)",array($magic_id),PDO::FETCH_ASSOC);
        if($result["count"] > 0 && $result["data"]["flag"] == 0){
            $comment = new forum_comment_mdl();
            $this->id = $result["data"]["forum_id"];
            $this->tag = $result["data"]["category"];
            $this->topic = $result["data"]["forum_topic"];
            $this->created_at = $result["data"]["created_at"];
            $this->user_prof = new profile_data($result["data"]["user_id"]);
            $this->type =$result["data"]["forum_type"];
            $this->magic_id = $result["data"]["magic_id"];
            $this->comments = $comment->comment_count($this->id);
        }
    }

    public function set_forum()
    {
        if($this->type == "department"){
            $this->pdo_insert("CALL ussap.insert_department_forum(:user_id,:forum_cat,:forum_topic,:created_at,:magic_id,:department_id)",
                array(
                    ":user_id" => $this->user_prof->user_id,
                    ":forum_cat" => $this->tag,
                    ":forum_topic" => $this->topic,
                    ":magic_id" => $this->magic_id,
                    ":created_at" => $this->created_at,
                    ":department_id" => $this->department_id
                ));
        }elseif($this->type == "general"){
            $this->pdo_insert("CALL ussap.insert_general_forum(:user_id,:forum_cat,:forum_topic,:magic_id,:created_at)",
                array(
                    ":user_id" => $this->user_prof->user_id,
                    ":forum_cat" => $this->tag,
                    ":forum_topic" => $this->topic,
                    ":magic_id" => $this->magic_id,
                    ":created_at" => $this->created_at
                ));
        }elseif($this->type == "friend"){
            $this->pdo_insert("CALL ussap.insert_friend_forum(:user_id,:forum_cat,:forum_topic,:magic_id,:created_at)",
                array(
                    ":user_id" => $this->user_prof->user_id,
                    ":forum_cat" => $this->tag,
                    ":forum_topic" => $this->topic,
                    ":magic_id" => $this->magic_id,
                    ":created_at" => $this->created_at
                ));
        }elseif($this->type == "group"){
            $this->pdo_insert("CALL ussap.insert_group_forum(:user_id,:forum_cat,:forum_topic,:created_at,:group_id,:magic_id)",
                array(
                    ":user_id" => $this->user_prof->user_id,
                    ":forum_cat" => $this->tag,
                    ":forum_topic" => $this->topic,
                    ":group_id" => $this->group_id,
                    ":magic_id" => $this->magic_id,
                    ":created_at" => $this->created_at
                ));
        }
    }

    public function flag($magcId)
    {
        $result = $this->pdo_update("Call ussap.forum_flag(?)",array($magcId));
    }

    public function fetch_flagged()
    {
        $forums = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_flagged_forums(?)",array(FLAG_LIMIT),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach ($result["data"] as $val) {
                $forum = new forum_mdl();
                $comment = new forum_comment_mdl();
                $forum->id = $val["forum_id"];
                $forum->tag = $val["category"];
                $forum->topic = $val["forum_topic"];
                $forum->created_at = $val["created_at"];
                $forum->magic_id = $val["magic_id"];
                $forum->user_prof = new profile_data($val["user_id"]);
                $forum->comments = $comment->comment_count($forum->id);
                $forums[] = $forum;
            }
        }
        return $forums;
    }

    /**
     * function to get the timezone for the specific forum
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

}