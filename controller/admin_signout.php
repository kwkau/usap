<?php

class admin_signout extends Controller{
    public function __construct()
    {
        parent::__construct("admin_signout");
        session::end();
        session::set("admin_logged_out",true);
        $this->redirect("dashboard");
    }


}