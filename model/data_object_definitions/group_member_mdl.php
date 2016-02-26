<?php

class group_member_mdl extends Model
{
    public function __construct()
    {
        parent::__construct();

    }

    public $id;
    public $status;
    /**
     * @var $mem_prof profile_data the profile of the member of the group
     */
    public $mem_prof;
    public $group_id;

    public function get_grp_members($grp_id)
    {
        $grp_mems = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_group_members(?)",array($grp_id),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach ($result["data"] as $cur) {
                $grp_mem = new group_member_mdl();
                $grp_mem->id = $cur["group_member_id"];
                $grp_mem->status = $cur["group_member_status"];
                $grp_mem->mem_prof = new profile_data($cur["user_id"]);
                $grp_mem->group_id = $cur["group_id"];
                $grp_mems[] = $grp_mem;
            }
        }
        return $grp_mems;
    }

    public function set_grp_member($user_id,$grp_id)
    {
        $this->pdo_insert("CALL ussap.insert_group_member(?,?)",array($user_id,$grp_id));
        return true;
    }

}