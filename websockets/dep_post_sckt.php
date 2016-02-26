<?php

class dep_post_sckt extends wsserver{

    public function __construct($addr, $port)
    {
        parent::__construct($addr,$port);
        $this->channel = "depPostSocket";
    }

    protected function onopen(Socket $sckt, $data)
    {
        //we will receive the user id of the user connecting to this socket
        $packet = $this->check_data($data);

        //check if we have a valid packet
        if(is_array($packet)) {
            $pckt_data = $this->decode($packet["payload"]);
            /*-----------------------------------------------------------------------------------------------
             * evey user that connects to this socket with purpose of viewing information about a particular
             * group, the information about that group must fetched and added to the users data to allow us
             * to uniquely identify each user to their respective groups
             *---------------------------------------------------------------------------------------------*/
            $sckt->user = new user_mdl($pckt_data["user_id"]);
            $dep = new department_mdl($pckt_data["dep_id"]);

            $sckt->user->department = $dep;

            //store the users socket and if the user is successfully added fetch the first eight posts for the user
            $this->add_user($sckt) ? $this->post($sckt, "post_login", "valid") : $this->post($sckt, "post_login", "invalid");
        }
    }

    protected function onmessage(Socket $sckt, $data)
    {
        $packet = $this->check_data($data);

        if(is_array($packet)) {
            $payload = $this->decode($packet["payload"]);

            if($payload["type"] == "post"){
                /*----------------------------------------------------
                 * we have a post on our hands. time to create a post
                 *---------------------------------------------------*/
                $post_data = $this->decode($payload["post_data"]);

                /*------------------------------------------------------------------------
                 * broadcast the just created post to every user connected to this server
                 *----------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_post_creation",$post_data);

                /*------------------------------------
                 * insert the post into the database
                 *----------------------------------*/
                $this->create_post($sckt,$post_data);

            }elseif($payload["type"] == "post_load"){
                /*-------------------------------------------------------------------
                 * load the first 8 post for the user when they open their post tab
                 *-----------------------------------------------------------------*/
                $this->post($sckt,"dep_post_load",$this->load_posts($payload["dep_id"]));

            }elseif($payload["type"] == "post_smiley"){
                /*------------------------------
                 * we are coming to like a post
                 *----------------------------*/
                $post_smiley_data = $this->decode($payload["post_smiley_data"]);

                /*--------------------------------------------------------------------------------------
                 * broadcast the post like to all members of the group that are connected to the server
                 *------------------------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_post_smiley_creation",$post_smiley_data);

                /*------------------------------------------
                 * insert the post smiley into the database
                 *----------------------------------------*/
                $this->insert_post_smiley($sckt,$post_smiley_data);

            }elseif($payload["type"] == "post_smiley_del"){
                /*--------------------------------
                 * we are coming to unlike a post
                 *-----------------------------*/
                $post_smiley_data = $this->decode($payload["post_smiley_data"]);

                /*---------------------------------------------------------------------------
                 * broadcast the post like to all users that are connected to the server
                 *--------------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_post_smiley_del",$post_smiley_data);

                /*------------------------------------------
                 * insert the post smiley into the database
                 *----------------------------------------*/
                $this->delete_post_smiley($sckt,$post_smiley_data);

            }elseif($payload["type"] == "post_comment"){
                /*--------------------------------------
                 * we have a post comment to deal with
                 *-------------------------------------*/
                $post_comment_data = $this->decode($payload["post_comment_data"]);

                /*---------------------------------------------------------------------------
                 * broadcast the forum comment to all the members that are connected to the server
                 *--------------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_post_comment_creation",$post_comment_data);

                /*--------------------------------------------
                 * insert the post comment into the database
                 *-------------------------------------------*/
                $this->insert_post_comment($sckt,$post_comment_data);

            }elseif($payload["type"] == "post_comment_load"){
                $comments = array("post_magic_id"=>$payload["magic_id"],"comments"=>$this->load_comments($payload["magic_id"]));
                $this->post($sckt,"dep_post_comment_load",$comments);

            }elseif($payload["type"] == "post_comment_smiley"){
                /*--------------------------------------------
                 * we have a post comment like to respond to
                 *-------------------------------------------*/
                $post_comment_smiley_data = $this->decode($payload["post_comment_smiley_data"]);

                /*-------------------------------------------------------------------------------
                 * broadcast the post comment like to all users that are connected to the server
                 *-----------------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_post_comment_smiley_creation",$post_comment_smiley_data);

                /*--------------------------------------------------e
                 * insert the post comment smiley into the database
                 *------------------------------------------------*/
                $this->insert_post_comment_smiley($sckt,$post_comment_smiley_data);

            }elseif($payload["type"] == "post_comment_smiley_del"){
                /*--------------------------------------------
                 * we have a post comment like to respond to
                 *-------------------------------------------*/
                $post_comment_smiley_data = $this->decode($payload["post_comment_smiley_data"]);

                /*-------------------------------------------------------------------------------
                 * broadcast the post comment like to all users that are connected to the server
                 *-----------------------------------------------------------------------------*/
                $this->broadcast($sckt,$this->dep_mem_sckts($sckt,$sckt->user->department->name),"dep_post_comment_smiley_del",$post_comment_smiley_data);

                /*--------------------------------------------------
                 * insert the post comment smiley into the database
                 *------------------------------------------------*/
                $this->delete_post_comment_smiley($sckt,$post_comment_smiley_data);
            }
        }
    }

    protected function onclose(Socket $sckt)
    {
        $this->remove_user($sckt);
    }


    private function load_posts($department_id)
    {

        $post = new post_mdl();
        return $post->get_posts($department_id,"department");
    }

    private function create_post(Socket $sckt, $post_data)
    {
        $post = new post_mdl();
        $post->magic_id = $post_data["magic_id"];
        $post->content_type = $post_data["content_type"];
        $post->created_at = $post_data["created_at"];
        $post->department_id = $post_data["dep_id"];
        //check for content type and set the appropriate post properties
        if($post->content_type == "text"){
            $post->post_text = $post_data["post_text"];
        }elseif($post->content_type == "multimedia"){
            $post->post_text = $post_data["post_text"];
            $post->pic_url = $post_data["pic_url"];
        }
        $post->user_prof = $sckt->user->profile;

        $post->set_post($post_data["target"]);
    }

    private function insert_post_smiley(Socket $sckt, $smiley_data)
    {
        $post_smiley = new post_smiley_mdl();
        $post_smiley->user_prof = $sckt->user->profile;
        $post_smiley->post_magic_id = $smiley_data["post_magic_id"];

        $post_smiley->set_post_smiley();
    }

    private function delete_post_smiley(Socket $sckt, $smiley_data)
    {
        $post_smiley = new post_smiley_mdl();
        $post_smiley->user_prof = $sckt->user->profile;
        $post_smiley->post_magic_id = $smiley_data["post_magic_id"];

        $post_smiley->del_post_smiley();
    }

    private function insert_post_comment(Socket $sckt,$comment_data)
    {
        /*-----------------------------------
         * we are creating our post comment
         *---------------------------------*/
        $comment = new post_comment_mdl();
        $comment->user_prof = $sckt->user->profile;
        $comment->created_at = $comment_data["created_at"];
        $comment->text = $comment_data["text"];
        $comment->magic_id = $comment_data["magic_id"];
        $comment->post_magic_id = $comment_data["post_magic_id"];

        $comment->set_post_comment();
    }

    private function load_comments($post_magic_id)
    {
        $comments = new post_comment_mdl();
        return $comments->get_post_comments($post_magic_id,"magic");
    }

    private function insert_post_comment_smiley(Socket $sckt, $smiley_data)
    {
        $comment_smiley = new post_comment_smiley_mdl();
        $comment_smiley->user_prof = $sckt->user->profile;
        $comment_smiley->comment_magic_id = $smiley_data["comment_magic_id"];

        $comment_smiley->set_smiley();
    }

    private function delete_post_comment_smiley(Socket $sckt, $smiley_data)
    {
        $comment_smiley = new post_comment_smiley_mdl();
        $comment_smiley->user_prof = $sckt->user->profile;
        $comment_smiley->comment_magic_id = $smiley_data["comment_magic_id"];

        $comment_smiley->delete_smiley();
    }
}