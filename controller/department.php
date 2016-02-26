<?php


class department extends Controller{

    public function __construct()
    {
        parent::__construct("department");
        $this->login_verify();
    }

    public function index($name)
    {
        $dep = new department_mdl();
        $dep->fetch_dep($name);

        /*------------------------------------------------------
         * check if the name provided is for a valid department
         *----------------------------------------------------*/
        if($dep->valid){
            $this->viewBag["title"] = $dep->name;
            $this->viewBag["department"] = $dep;
            $this->viewBag["tag"] = "department";

            session::set("dep_id",$dep->id);
            $this->view("Layout");
        }else{
            $this->redirect("dropzone");
        }
    }

    public function get_dep_data()
    {
        $dep = new department_mdl(session::get("dep_id"));

        $prof = new profile_data(session::get("user_id"));
        echo json_encode(array("department" => $dep, "mem_prof" => $prof));
    }
}