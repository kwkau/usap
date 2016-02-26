<?php

class dashboard extends Controller{
    public function __construct()
    {
        parent::__construct("dashboard");
    }

    public function index()
    {
        $this->viewBag["title"] = "USAP Dashboard";
        $admin = new admin_mdl();

        if($admin->is_empty()){//no admin registered
            $this->select("admin_reg");
            return false;
        }elseif(!session::get("logged")){
            $this->select("admin_login");
            return false;
        }

        $this->view();
    }

    public function login()
    {
        $admin = new admin_mdl();
        if($admin->login_verf(filter_input(INPUT_POST,'username'),filter_input(INPUT_POST,"password-hash"))){
            session::set("logged",true);
            $this->redirect("dashboard");
        }else{
            $this->redirect("dashboard");
        }
    }

    public function register()
    {
        $admin = new admin_mdl();
        $admin->register(array(
            "first_name" => filter_input(INPUT_POST,'firstname'),
            "last_name" => filter_input(INPUT_POST,'lastname'),
            "username" => filter_input(INPUT_POST,'username'),
            "password" => filter_input(INPUT_POST,'password-hash')
        ));
        session::set("logged",true);
        $this->redirect("dashboard");
    }

    public function search()
    {
        $search = new search_mdl();
        $search->query = filter_input(INPUT_POST,"query");
        echo json_encode($search->search_run());
    }

    public function del_user()
    {
        $user_id = filter_input(INPUT_POST,"id");
        $user = new user_mdl();
        $user->delete_user($user_id);
        echo 1;
    }

    public function res_user()
    {
        $user_id = filter_input(INPUT_POST,"id");
        $user = new user_mdl();
        $user->restore_user($user_id);
        echo 1;
    }

    public function flagged_posts()
    {
        $post = new post_mdl();
        echo json_encode($post->fetch_flagged());
    }


    public function flagged_forums()
    {
        $forum = new forum_mdl();
        echo json_encode($forum->fetch_flagged());
    }
}