<?php


class noti_sckt extends wsserver{

    public function __construct($address, $port)
    {
        parent::__construct($address,$port);
        $this->channel="notiSocket";
    }


    public function onopen(Socket $sckt, $data)
    {
        try{
            $packet = $this->check_data($data);

            if(is_array($packet)){

                    $sckt->user = new user_mdl($packet["payload"]);
                    $this->add_user($sckt)?$this->post($sckt,"noti_login","valid"):$this->post($sckt,"noti_login","invalid");

            }
        }catch (Exception $ex){
            $this->log_err($ex);
        }
    }

    protected function onmessage(Socket $sckt, $data)
    {
        try{
            $packet = $this->check_data($data);
            if(is_array($packet)){
                $payload = $this->decode($packet["payload"]);

                if($payload["type"] == "noti_load"){
                    /*-------------------------------------------------------------
                     * send the user notifications that belong to this socket user
                     *-----------------------------------------------------------*/
                    $this->post($sckt,"noti_load",$this->noti_load($sckt));

                }elseif($payload["type"] == "department_noti_load"){
                    /*-------------------------------------------------------------------
                     * send the department notifications that belong to this socket user
                     *-----------------------------------------------------------------*/
                    $this->post($sckt,"department_noti_load",$this->department_noti($sckt));

                }elseif($payload["type"] == "group_noti_load"){
                    /*--------------------------------------------------------------
                     * send the group notifications that belong to this socket user
                     *------------------------------------------------------------*/
                    $this->post($sckt,"group_noti_load", $this->group_noti($sckt));

                }elseif($payload["type"] == "insert_noti"){
                    $noti_data = $this->decode($payload["noti_data"]);
                    /*------------------------------------------------------------------------------------------------
                     * send the notification to the user socket which has the same target id if the user is connected
                     *----------------------------------------------------------------------------------------------*/
                    if($target_sckt = $this->find_socket($noti_data["target_id"])){
                        $this->post($target_sckt,"insert_noti",$noti_data);
                    }
                    /*-------------------------------------------
                     * insert the notification into the database
                     *-----------------------------------------*/
                    $this->insert_noti($sckt,$noti_data);

                }elseif($payload["type"] == "delete_noti"){
                    $noti_data = $this->decode($payload["noti_data"]);
                    /*-------------------------------------------
                     * delete the notification from the database
                     *-----------------------------------------*/
                    $this->delete_noti($sckt,$noti_data);

                }elseif($payload["type"] == "fetch_post"){
                    /*--------------------------------
                     * fetch a post for a user socket
                     *------------------------------*/
                    $this->post($sckt,"fetch_post",$this->fetch_post($payload["post_mgcid"]));

                }elseif($payload["type"] == "fetch_forum"){
                    /*---------------------------------
                     * fetch a forum for a user socket
                     *-------------------------------*/
                    $this->post($sckt,"fetch_forum",$this->fetch_forum($payload["forum_mgcid"]));

                }elseif($payload["type"] == "forum_comment_load"){
                    /*------------------------
                     * fetch a forums comment
                     *-----------------------*/
                    $comments = array("forum_magic_id"=>$payload["magic_id"],"comments"=>$this->load_forum_comments($payload["magic_id"]));
                    $this->post($sckt,"noti_forum_comment_load",$comments);

                }elseif($payload["type"] == "forum_comment"){
                    /*--------------------------------------
                     * we have a forum comment to deal with
                     *------------------------------------*/
                    $forum_comment_data = $this->decode($payload["forum_comment_data"]);


                    /*--------------------------------------------
                     * insert the forum comment into the database
                     *-------------------------------------------*/
                    $this->insert_forum_comment($sckt,$forum_comment_data);

                }elseif($payload["type"] == "post_comment"){
                    /*--------------------------------------
                     * we have a post comment to deal with
                     *-------------------------------------*/
                    $post_comment_data = $this->decode($payload["post_comment_data"]);


                    /*--------------------------------------------
                     * insert the post comment into the database
                     *-------------------------------------------*/
                    $this->insert_post_comment($sckt,$post_comment_data);

                }elseif($payload["type"] == "post_comment_load"){
                    $comments = array("post_magic_id"=>$payload["magic_id"],"comments"=>$this->load_post_comments($payload["magic_id"]));
                    $this->post($sckt,"noti_post_comment_load",$comments);

                }

            }
        }catch (Exception $ex){
            $this->log_err($ex);
        }
    }

    protected function onclose(Socket $sckt)
    {
        try{
            $this->remove_user($sckt);
        }catch (Exception $ex){
            $this->log_err($ex);
        }
    }


    private function noti_load(Socket $sckt)
    {
        try{
            $noti = new notification_mdl();
            /*---------------------------------------------------------
             * we will first fetch the user notifications for the user
             *-------------------------------------------------------*/
            return $noti->fetch_user_noti($sckt->user->id);
        }catch (Exception $ex){
            $this->log_err($ex);
        }
    }

    private function department_noti(Socket $sckt)
    {
        try{
            $noti = new notification_mdl();
            return $noti->fetch_dep_noti($sckt->user->id);
        }catch (Exception $ex){
            $this->log_err($ex);
        }
    }

    private function group_noti(Socket $sckt)
    {
        try{
            $noti = new notification_mdl();
            return $noti->fetch_grp_noti($sckt->user->id);
        }catch (Exception $ex){
            $this->log_err($ex);
        }

    }

    private function insert_noti(Socket $sckt, array $noti_data)
    {
        try{
            $noti = new notification_mdl();
            $noti->type = $noti_data["type"];
            $noti->perp_prof = $sckt->user->profile;
            $noti->target_id = $noti->type == "user"? $noti_data["target_id"]:null;
            $noti->target_object_magic_id = $noti_data["target_object_magic_id"];
            $noti->target_object_type = $noti_data["target_object_type"];
            $noti->department_id = $noti->type == "department"? $noti_data["department_id"]:null;
            $noti->group_id = $noti->type == "group"? $noti_data["group_id"]:null;
            $noti->created_at = $noti_data["created_at"];
            $noti->noti_text = $noti_data["noti_text"];
            $noti->magic_id = $noti_data["magic_id"];

            $noti->create_noti();
        }catch (Exception $ex){
            $this->log_err($ex);
        }

    }

    private function delete_noti(Socket $sckt, array $noti_data)
    {
        try{
            $noti = new notification_mdl();
            $noti->magic_id = $noti_data["magic_id"];
            $noti->type = $noti_data["type"];

            $noti->delete_noti();
        }catch (Exception $ex){
            $this->log_err($ex);
        }

    }

    private function fetch_forum($forum_mgcid)
    {
        try{
            $forum = new forum_mdl();
            $forum->get_forum($forum_mgcid);
            return $forum;
        }catch (Exception $ex){
            $this->log_err($ex);
        }
    }

    private function fetch_post($post_mgcid)
    {
        try{
            $post = new post_mdl();
            $post->get_post($post_mgcid);
            return $post;
        }catch (Exception $ex){
            $this->log_err($ex);
        }

    }

    private function load_forum_comments($forum_mgc_id)
    {
        try{
            $comment = new forum_comment_mdl();
            return $comment->get_forum_comments($forum_mgc_id,"magic");
        }catch (Exception $ex){
            $this->log_err($ex);
        }

    }

    private function load_post_comments($post_magic_id)
    {
        try{
            $comments = new post_comment_mdl();
            return $comments->get_post_comments($post_magic_id,"magic");
        }catch (Exception $ex){
            $this->log_err($ex);
        }

    }

    private function insert_forum_comment(Socket $sckt, array $forum_comment_data)
    {
        try{
            /*-----------------------------------
             * we are creating our forum comment
             *---------------------------------*/
            $comment = new forum_comment_mdl();
            $comment->user_prof = $sckt->user->profile;
            $comment->created_at = $forum_comment_data["created_at"];
            $comment->text = $forum_comment_data["text"];
            $comment->magic_id = $forum_comment_data["magic_id"];
            $comment->forum_magic_id = $forum_comment_data["forum_magic_id"];
            $comment->set_forum_comment();
        }catch (Exception $ex){
            $this->log_err($ex);
        }
    }



    private function insert_post_comment(Socket $sckt,$comment_data)
    {
        try{
            /*----------------------------------
             * we are creating our post comment
             *--------------------------------*/
            $comment = new post_comment_mdl();
            $comment->user_prof = $sckt->user->profile;
            $comment->created_at = $comment_data["created_at"];
            $comment->text = $comment_data["text"];
            $comment->magic_id = $comment_data["magic_id"];
            $comment->post_magic_id = $comment_data["post_magic_id"];
            $comment->set_post_comment();
        }catch (Exception $ex){
            $this->log_err($ex);
        }

    }
}