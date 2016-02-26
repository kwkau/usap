<?php

class group extends Controller{
    public function __construct(){
        parent::__construct("group");
        $this->login_verify();
    }

    public function index($grp_name)
    {
        /*------------------------------------
         * obtain information about the group
         *-----------------------------------*/
        $group = new group_data();
        $group->fetch_group($grp_name);

        if($group->exists){
            $this->viewBag["title"] =  $group->name;
            $this->viewBag["group"] = $group;
            $this->viewBag["tag"] = "group";
            session::set("group_name",$group->name);

            /*--------------------------------------------------------------------
             * check if the user with the current session is a member of the group
             * send the user to the group profile page if the user is not a member
             * of the group or to the main group page if the user is a member of the
             * group
             *--------------------------------------------------------------------*/
            $mem = $group->is_member(session::get("user_id"));
            if($mem){
                $this->view("Layout");
            }else{
                $grp_mem = new group_member_mdl();
                $this->viewBag["group_members"] = $grp_mem->get_grp_members($group->id);
                $this->viewBag["forum_count"] =  $group->grp_forum_count();
                $this->viewBag["is_mem"] = $mem;
                $this->select("group_profile","Layout");
            }

        }else{
            $this->redirect("dropzone");
        }

    }

    public function creation()
    {

        $name = filter_input(INPUT_POST,"grp_name");
        $group = new group_data();
        $group->fetch_group($name);
        if(!$group->exists){
            $group->name = $name;
            $group->admin = new profile_data(session::get("user_id"));
            $group->privacy_type = "open";
            $group->set_group();

            $this->redirect("group",$group->name);
        }
        $this->redirect("dropzone");
    }

    public function check_grp_name()
    {
        $grp_name = filter_input(INPUT_POST,"grp_name");
        $group = new group_data();
        echo json_encode(array("grp_exists" => $group->grp_exists($grp_name)));
    }

    public function get_grp_data()
    {
        $group = new group_data();
        $group->fetch_group(session::get("group_name"));

        $prof = new profile_data(session::get("user_id"));
        echo json_encode(array("group" => $group, "mem_prof" => $prof));
    }

    public function grp_mem_join()
    {
        $user_id = filter_input(INPUT_POST,"user_id");
        $grp_id = filter_input(INPUT_POST,"grp_id");
        $mem = new group_member_mdl();
        echo json_encode(array("state" => $mem->set_grp_member($user_id,$grp_id)));
    }
}