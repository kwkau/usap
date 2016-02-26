<?php

class dep_forum_sckt extends wsserver{

    public function __construct($addr, $port)
    {
        parent::__construct($addr,$port);
        $this->channel = "depForumSocket";
    }

    protected function onopen(Socket $sckt, $data)
    {
        /*---------------------------------------------------------------------------------------
         * a user who connects to a socket will have to provide their user_id for identification
         * on the server.
         *--------------------------------------------------------------------------------------*/
        $packet = $this->check_data($data);

        if(is_array($packet)){
            $pckt_data = $this->decode($packet["payload"]);

            /*-----------------------------------------------------------------------------------------------
             * evey user that connects to this socket with purpose of viewing information about a particular
             * group, the information about that group must fetched and added to the users data to allow us
             * to uniquely identify each user to their respective groups
             *---------------------------------------------------------------------------------------------*/
            $sckt->user = new user_mdl($pckt_data["user_id"]);
            $dep = new department_mdl($pckt_data["dep_id"]);

            $sckt->user->department = $dep;

            //we adding users who have not logged on already then we send the eight most recent forums of the group to the user who just got connected
            $this->add_user($sckt)?$this->post($sckt,"dep_forum_load",$this->load_forums($pckt_data["dep_id"])):$this->post($sckt,"dep_forum_load","invalid");

        }
    }

    protected function onmessage(Socket $sckt, $data)
    {
        $packet = $this->check_data($data);

        if(is_array($packet)) {

            $payload = $this->decode($packet["payload"]);

            if($payload["type"] == "forum"){
                /*------------------------------
                 * we have a forum on our hands
                 *------------------------------*/

                $forum_data = $this->decode($payload["forum_data"]);
                //obtain target
                /*-------------------------------------------------------------------------
                 * broadcast the just created forum to every user connected to this server
                 *------------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_forum_creation",$forum_data);

                /*-------------------------------------------------
                 * insert the just created forum into the database
                 *------------------------------------------------*/
                $this->insert_forum($sckt,$forum_data);

            }elseif($payload["type"] == "forum_comment"){
                /*--------------------------------------
                 * we have a forum comment to deal with
                 *-------------------------------------*/
                $forum_comment_data = $this->decode($payload["forum_comment_data"]);

                /*---------------------------------------------------------------------------
                 * broadcast the forum comment to all users that are connected to the server
                 *--------------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_forum_comment_creation",$forum_comment_data);

                /*--------------------------------------------
                 * insert the forum comment into the database
                 *-------------------------------------------*/
                $this->insert_forum_comment($sckt,$forum_comment_data);

            }elseif($payload["type"] == "forum_comment_load"){
                $comments = array("forum_magic_id"=>$payload["magic_id"],"comments"=>$this->load_comments($payload["magic_id"]));
                $this->post($sckt,"dep_forum_comment_load",$comments);
            }elseif($payload["type"] == "forum_comment_smiley"){
                /*--------------------------------------------
                 * we have a forum comment like to respond to
                 *-------------------------------------------*/
                $forum_comment_smiley_data = $this->decode($payload["forum_comment_smiley_data"]);

                /*--------------------------------------------------------------------------------
                 * broadcast the forum comment like to all users that are connected to the server
                 *------------------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_forum_comment_smiley_creation",$forum_comment_smiley_data);

                //create forum_comment_smiley
                $this->insert_forum_comment_smiley($sckt,$forum_comment_smiley_data);

            }elseif($payload["type"] == "del_forum_comment_smiley"){
                /*-------------------------------------
                 * a user wants to unsmiley a comment
                 *------------------------------------*/
                $forum_comment_smiley_data = $this->decode($payload["forum_comment_smiley_data"]);

                /*---------------------------------------------------------------------------
                 * broadcast the forum comment like to all users that are connected to the server
                 *--------------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_forum_comment_smiley_del",$forum_comment_smiley_data);

                //delete smiley
                $this->delete_forum_comment_smiley($sckt,$forum_comment_smiley_data);

            }elseif($payload["type"] == "scroll_forum_load"){

            }
        }
    }

    protected function onclose(Socket $sckt)
    {
        $this->remove_user($sckt);
    }

    /**
     * functions to fetch the last eight forums for the user who just connected
     * @param $id
     * @return array|bool
     */
    private function load_forums($id)
    {
        /*--------------------------------------------------------------------------
         * send the 8 most recent forums to the user who just connected to the group
         *-------------------------------------------------------------------------*/
        $forum_obj = new forum_mdl();
        return $forum_obj->get_forums($id,"department");
    }

    /**
     * @param Socket $sckt
     * @param array $forum_data
     */
    protected function insert_forum(Socket $sckt, array $forum_data)
    {
        /*--------------------------
         * we are creating our forum
         *------------------------*/
        $forum = new forum_mdl();
        $forum->user_prof = $sckt->user->profile;
        $forum->tag = $forum_data["tag"];
        $forum->topic = $forum_data["topic"];
        $forum->type = $forum_data["type"];
        $forum->department_id = $forum_data["dep_id"];
        $forum->magic_id = $forum_data["magic_id"];
        $forum->created_at = $forum_data["created_at"];
        $forum->set_forum();
        //send feed back to the user to certify that the forum has been inserted into the database
        $this->post($sckt,"forum_creation_feedback",1);
    }

    private function insert_forum_comment(Socket $sckt, array $forum_comment_data)
    {
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
        //send feed back to the user to certify that the forum comment has been inserted into the database
        $this->post($sckt,"forum_comment_creation_feedback",1);
    }

    private function load_comments($forum_mgc_id)
    {
        $comment = new forum_comment_mdl();
        return $comment->get_forum_comments($forum_mgc_id,"magic");
    }

    private function insert_forum_comment_smiley(Socket $sckt, array $forum_comment_smiley_data)
    {
        /*------------------------------------------
         * we are creating our forum comment smiley
         *----------------------------------------*/
        $smiley = new forum_smiley_mdl();
        $smiley->user_prof = $sckt->user->profile;
        $smiley->comment_magic_id = $forum_comment_smiley_data["comment_magic_id"];
        $smiley->set_smiley();
        //send feed back to the user to certify that the forum comment smiley has been inserted into the database
        $this->post($sckt,"forum_comment_smiley_feedback",1);
    }

    private function delete_forum_comment_smiley(Socket $sckt, array $forum_comment_smiley_data)
    {

        $smiley = new forum_smiley_mdl();
        $smiley->user_prof = $sckt->user->profile;
        $smiley->comment_magic_id = $forum_comment_smiley_data["comment_magic_id"];

        //delete the smiley for the forum comment with the specified magic id
        $smiley->del_smiley();
    }
}