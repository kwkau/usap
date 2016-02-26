<?php

class socketStart extends Controller {

    public function __construct()
    {
        parent::__construct("socketStart");

    }

    public function usrForumSckt()
    {
        set_time_limit(0);
        $sckt = new usr_forum_sckt(HOST_NAME,FORUM_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }

    public function chatSckt()
    {
        set_time_limit(0);
        $sckt = new chat_sckt(HOST_NAME,CHAT_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }

    public function notiSckt()
    {
        set_time_limit(0);
        $sckt = new noti_sckt(HOST_NAME,NOTI_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }

    public function userPostSckt()
    {
        set_time_limit(0);
        $sckt = new user_post_sckt(HOST_NAME,POST_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }

    public function startUp()
    {
        set_time_limit(0);
        $sckt = new startup_sckt(HOST_NAME,STARTUP_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }

    public function profileSckt()
    {
        set_time_limit(0);
        $sckt = new profile_sckt(HOST_NAME,PROFILE_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }

    public function grpForumSckt()
    {
        set_time_limit(0);
        $sckt = new grp_forum_sckt(HOST_NAME,GROUP_FORUM_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }


    public function grpPostSckt()
    {
        set_time_limit(0);
        $sckt = new grp_post_sckt(HOST_NAME,GROUP_POST_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }

    public function depPostSckt()
    {
        set_time_limit(0);
        $sckt = new dep_post_sckt(HOST_NAME,DEPARTMENT_POST_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }

    public function depForumSckt()
    {
        set_time_limit(0);
        $sckt = new dep_forum_sckt(HOST_NAME,DEPARTMENT_FORUM_SOCKET_PORT);
        try{
            $sckt->run();
        }catch(Exception $ex){
            die($ex->getMessage());
        }
    }

    public function stop($server_name)
    {

    }
}