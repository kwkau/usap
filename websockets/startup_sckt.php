<?php

class startup_sckt extends wsserver{

    public function __construct($address, $port)
    {
        parent::__construct($address, $port);
        $this->channel = "startUpSocket";
    }

    public function onopen(Socket $sckt,$data)
    {
        /*---------------------------------------------------------------------------------------
         * a user who connects to a socket will have to provide their user_id for identification
         * on the server. this will be our first rule in our socket protocol
         *--------------------------------------------------------------------------------------*/
        $packet = $this->check_data($data);
        if($packet){
            $sckt->user = new user_mdl($packet["payload"]);
            $this->server_sockets[$sckt->id] = $sckt;

            $this->post($sckt,"user_load",$this->load($sckt->user->id));
        }
    }

    public function onmessage(Socket $sckt, $data)
    {
        $packet = $this->check_data($data);
        if(is_array($packet)){
            $payload = $this->decode($packet["payload"]);

            if($payload["type"] == "friend-request"){
                /*---------------------------------------
                 * we have a friend request on out hands
                 *-------------------------------------*/
                $req_data = $this->decode($payload["request_data"]);
                $this->insert_request($sckt,$req_data);
            }elseif($payload["type"] == "friend-accept"){
                /*----------------------------------------
                 * some one has accepted a friend request
                 *--------------------------------------*/
                $data = $this->decode($payload["accept_data"]);
                $this->mke_frnd($sckt->user,new user_mdl($data["uid"]));
                $this->rmv_request($data);
            }elseif($payload["type"] == "friend-reject"){
                /*----------------------------------------
                 * some one has rejected a friend request
                 *--------------------------------------*/
                $data = $this->decode($payload["reject_data"]);

                $this->rmv_request($data);
            }
        }
    }

    public function onclose(Socket $sckt)
    {
        unset($this->server_sockets[$sckt->id]);
    }

    private function load($id)
    {
        //the number of friends that the user has
        $frnd = new friend_mdl();

        //the number of notifications
        $notis = new notification_mdl();

        //the number of groups
        $grps = new group_data();

        return array(
            "noti_count" => array("num" => $notis->noti_count($id)),
            "frnd_count" => array("num" => $frnd->frnd_count($id)),
            "grp_count" => array("num" => $grps->grp_count($id))
        );

    }

    private function insert_request(Socket $sckt, $req_data)
    {
        $friend = new friend_mdl();

        $friend->magic_id = $req_data["magic_id"];
        $friend->target = $req_data["target_id"];
        $friend->perp = $sckt->user->id;

        $friend->insert_friend_request();
    }

    private function rmv_request($req_data)
    {
        $friend = new friend_mdl();

        $friend->magic_id = $req_data["magic_id"];

        $friend->delete_friend_request();
    }

    private function mke_frnd(user_mdl $user1, user_mdl $user2)
    {
        $friend = new friend_mdl();
        $friend->mke_frnd($user1->friend_id,$user2->friend_id);
    }

}