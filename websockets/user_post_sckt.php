<?php


class user_post_sckt extends wsserver{

    public function __construct($address,$port)
    {
        parent::__construct($address,$port);
        $this->channel = "postSocket";
    }

    public function onopen(Socket $sckt, $data)
    {
        //we will receive the user id the user
        $packet = $this->check_data($data);

        //check if we have a valid packet
        if(is_array($packet)){
            //load the users data
            $sckt->user = new user_mdl($packet["payload"]);

            //store the users socket and if the user is successfully added fetch the first eight posts for the user
            $this->add_user($sckt)?$this->post($sckt,"post_login","valid"):$this->post($sckt,"post_login","invalid");
        }


    }

    protected function onmessage(Socket $sckt, $data)
    {
        $packet = $this->check_data($data);

        if(is_array($packet)){
            $payload = $this->decode($packet["payload"]);
            if($payload["type"] == "post"){
                /*----------------------------------------------------
                 * we have a post on our hands. time to create a post
                 *---------------------------------------------------*/
                $post_data = $this->decode($payload["post_data"]);

                //check for the content type of the post
                if($post_data["target"] == "friend"){
                    /*-------------------------------------------------------------------------------------
                     * broadcast the just created post to all the friends of the user who created the post
                     *-----------------------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->frnd_sockets($sckt),"post_creation",$post_data);

                }elseif($post_data["target"] == "general"){
                    /*------------------------------------------------------------------------
                     * broadcast the just created post to every user connected to this server
                     *----------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->server_sockets,"post_creation",$post_data);
                }

                /*------------------------------------
                 * insert the post into the database
                 *----------------------------------*/
                $this->create_post($sckt,$post_data);

            }elseif($payload["type"] == "post_load"){
                /*-------------------------------------------------------------------
                 * load the first 8 post for the user when they open their post tab
                 *-----------------------------------------------------------------*/
                $this->post($sckt,"post_load",$this->load_posts($sckt->user->id));

            }elseif($payload["type"] == "post_smiley"){
                /*------------------------------
                 * we are coming to like a post
                 *----------------------------*/
                $post_smiley_data = $this->decode($payload["post_smiley_data"]);
                if($post_smiley_data["target"] == "friend"){
                    /*-----------------------------------------------------------------------------
                     * broadcast the post like to all the friends of the user who created the post
                     *---------------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->find_user_frnd_sockets($post_smiley_data["user_id"]),"post_smiley_creation",$post_smiley_data);

                }elseif($post_smiley_data["target"] == "general"){
                    /*---------------------------------------------------------------------------
                     * broadcast the post like to all users that are connected to the server
                     *--------------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->server_sockets,"post_smiley_creation",$post_smiley_data);
                }

                /*------------------------------------------
                 * insert the post smiley into the database
                 *----------------------------------------*/
                $this->insert_post_smiley($sckt,$post_smiley_data);

            }elseif($payload["type"] == "post_smiley_del"){
                /*--------------------------------
                 * we are coming to unlike a post
                 *-----------------------------*/
                $post_smiley_data = $this->decode($payload["post_smiley_data"]);
                if($post_smiley_data["target"] == "friend"){
                    /*-----------------------------------------------------------------------------
                     * broadcast the post like to all the friends of the user who created the post
                     *---------------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->find_user_frnd_sockets($post_smiley_data["user_id"]),"post_smiley_del",$post_smiley_data);

                }elseif($post_smiley_data["target"] == "general"){
                    /*---------------------------------------------------------------------------
                     * broadcast the post like to all users that are connected to the server
                     *--------------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->server_sockets,"post_smiley_del",$post_smiley_data);
                }

                /*------------------------------------------
                 * insert the post smiley into the database
                 *----------------------------------------*/
                $this->delete_post_smiley($sckt,$post_smiley_data);

            }
            elseif($payload["type"] == "post_comment"){
                /*--------------------------------------
                 * we have a post comment to deal with
                 *-------------------------------------*/
                $post_comment_data = $this->decode($payload["post_comment_data"]);
                /*
                 * what should we do when we receive a forum comment
                 * */
                if($post_comment_data["target"] == "friend"){
                    /*----------------------------------------------------------------------------------
                     * broadcast the forum comment to all the friends of the user who created the forum
                     *--------------------------------------------------------------------------------*/
                    //we need to find the socket of the user who created the forum

                    $this->broadcast($sckt,$this->find_user_frnd_sockets($post_comment_data["user_id"]),"post_comment_creation",$post_comment_data);
                }elseif($post_comment_data["target"] == "general"){
                    /*---------------------------------------------------------------------------
                     * broadcast the forum comment to all users that are connected to the server
                     *--------------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->server_sockets,"post_comment_creation",$post_comment_data);
                }

                /*--------------------------------------------
                 * insert the post comment into the database
                 *-------------------------------------------*/
                $this->insert_post_comment($sckt,$post_comment_data);

            }elseif($payload["type"] == "post_comment_load"){
                $comments = array("post_magic_id"=>$payload["magic_id"],"comments"=>$this->load_comments($payload["magic_id"]));
                $this->post($sckt,"post_comment_load",$comments);

            }elseif($payload["type"] == "post_comment_smiley"){
                /*--------------------------------------------
                 * we have a post comment like to respond to
                 *-------------------------------------------*/
                $post_comment_smiley_data = $this->decode($payload["post_comment_smiley_data"]);
                if($post_comment_smiley_data["target"] == "friend"){
                    /*---------------------------------------------------------------------------------------
                     * broadcast the post comment like to all the friends of the user who created the forum
                     *-------------------------------------------------------------------------------------*/

                    $this->broadcast($sckt,$this->find_user_frnd_sockets($post_comment_smiley_data["user_id"]),"post_comment_smiley_creation",$post_comment_smiley_data);

                }elseif($post_comment_smiley_data["target"] == "general"){
                    /*-------------------------------------------------------------------------------
                     * broadcast the post comment like to all users that are connected to the server
                     *-----------------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->server_sockets,"post_comment_smiley_creation",$post_comment_smiley_data);
                }

                /*--------------------------------------------------
                 * insert the post comment smiley into the database
                 *------------------------------------------------*/
                $this->insert_post_comment_smiley($sckt,$post_comment_smiley_data);

            }elseif($payload["type"] == "post_comment_smiley_del"){
                /*--------------------------------------------
                 * we have a post comment like to respond to
                 *-------------------------------------------*/
                $post_comment_smiley_data = $this->decode($payload["post_comment_smiley_data"]);
                if($post_comment_smiley_data["target"] == "friend"){
                    /*---------------------------------------------------------------------------------------
                     * broadcast the post comment like to all the friends of the user who created the forum
                     *-------------------------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->find_user_frnd_sockets($post_comment_smiley_data["user_id"]),"post_comment_smiley_del",$post_comment_smiley_data);

                }elseif($post_comment_smiley_data["target"] == "general"){
                    /*-------------------------------------------------------------------------------
                     * broadcast the post comment like to all users that are connected to the server
                     *-----------------------------------------------------------------------------*/
                    $this->broadcast($sckt,$this->server_sockets,"post_comment_smiley_del",$post_comment_smiley_data);
                }

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


    private function load_posts($user_id)
    {

        $post = new post_mdl();
        return $post->get_posts($user_id,"user");
    }

    private function create_post(Socket $sckt, $post_data)
    {
        $post = new post_mdl();
        $post->magic_id = $post_data["magic_id"];
        $post->content_type = $post_data["content_type"];
        $post->created_at = $post_data["created_at"];
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


    private function load_comments($post_magic_id)
    {
        $comments = new post_comment_mdl();
        return $comments->get_post_comments($post_magic_id,"magic");
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


    private function process_multimedia($post_data)
    {
        //check the details of the file that we are about to upload (size and type)
        if($post_data["file_size"] <= 1000000){
            $post_pic_upload_path = POST_PIC_UPLOAD_DIR.$post_data["file_name"];
            try{
                file_put_contents($post_pic_upload_path,$post_data["file"]);
            }catch(Exception $er){
                $this->stdout($er->getMessage());
            }

            //set the url for the uploaded picture
            $post_data["pic_url"] = POST_PIC_UPLOAD_URL.$post_data["file_name"];
            try{
                unset($post_data["file_size"]);
                unset($post_data["file_name"]);
                unset($post_data["file_type"]);
            }catch(Exception $er){
                $this->stdout($er->getMessage());
            }

            return $post_data;
        }else{
            return false;
        }

    }

}