<?php

class logout extends Controller{

    public function __construct()
    {
        parent::__construct("logout");
        $user = new user_mdl(session::get("user_id"));
        $user->logout();
        $user->delete_token(session::get("user_id"));
        session::end();
        session::set("logged_out",true);
        setcookie("user_token","",time() - 60,"/");
        $this->redirect();

    }
}