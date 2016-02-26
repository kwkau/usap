<?php


class dropzone_mdl extends Model {
    public function __construct(){
        parent::__construct();
    }

    public function get_forums($id,$type){
        $forum = new forum_mdl();
        return $forum->get_forums($id,$type);
    }
}