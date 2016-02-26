<?php


class group_data extends Model{
    public function __construct()
    {
        parent::__construct();
    }

    public $id;
    public $name;
    /**
     * @var $admin profile_data the profile of the user who created the group
     */
    public $admin;
    public $created_at;
    public $privacy_type;
    public $description;
    public $exists = false;

    public function get_grps($user_id)
    {
        $groups = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_groups(?)",array($user_id),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach($result["data"] as $val){
                $group = new group_data();
                $group->id = $val["group_id"];
                $group->name = $val["group_name"];
                $group->admin = new profile_data($val["admin"]);
                $group->created_at = $val["created_at"];
                $group->privacy_type = $val["type"];
                $group->description = $val["group_description"];
                $groups[] = $group;
            }
        }
        return $groups;
    }

    public function fetch_group($name)
    {
        $result = $this->pdo_fetch("CALL ussap.fetch_group(?)",array($name),PDO::FETCH_ASSOC);
        if($result["count"] > 0){
            $this->id = $result["data"]["group_id"];
            $this->name = $result["data"]["group_name"];
            $this->admin = new profile_data($result["data"]["admin"]);
            $this->created_at = $result["data"]["created_at"];
            $this->description = $result["data"]["group_description"];
            $this->privacy_type = $result["data"]["type"];
            $this->exists = true;
        }

    }

    public function set_group()
    {
        $this->pdo_insert("CALL ussap.insert_group(:admin_user_id,:group_name,:created_at,:privacy_type)",
            array(
                ":admin_user_id" => $this->admin->user_id,
                ":group_name" => $this->name,
                ":created_at" => $this->dt->get_date("date.timezone"),
                ":privacy_type" => $this->privacy_type
            ));
    }

    public function grp_count($id)
    {
        $result = $this->pdo_fetch("CALL ussap.fetch_groups(?)",array($id),PDO::FETCH_ASSOC,true);
        return $result["count"];
    }

    public function grp_exists($name)
    {
        $result = $this->pdo_fetch("SELECT grp.group_id FROM ussap.groups AS grp WHERE grp.group_name = ?",array($name),PDO::FETCH_NUM);
        if($result["count"] == 0){
            return false;
        }
        return true;
    }

    public function is_member($user_id)
    {
        $result = $this->pdo_fetch("CALL ussap.is_group_member(?,?)",array($user_id,$this->id),PDO::FETCH_ASSOC);
        return $result["count"] == 1;
    }

    public function grp_forum_count()
    {
        $result = $this->pdo_fetch("CALL ussap.fetch_count_department_forums(?)",array($this->id),PDO::FETCH_ASSOC,true);
        return $result["count"];
    }
}