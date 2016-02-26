<?php

class profile_sckt extends wsserver {

    public function __construct($address, $port)
    {
        parent::__construct($address,$port);
        $this->channel = "profileSocket";
    }

    protected function onopen(Socket $sckt, $data)
    {
        /*---------------------------------------------------------------------------------------
         * a user who connects to a socket will have to provide their user_id for identification
         * on the server. this will be our first rule in our socket protocol
         *--------------------------------------------------------------------------------------*/
        $packet = $this->check_data($data);

        if(is_array($packet)){//we have a valid packet
            //we are creating our socket user object to hold information about the user who just connected
            $sckt->user = new user_mdl($packet["payload"]);

                //send the users entire profile
                $profile = new profile_data();
                $profile->get_all($sckt->user->id);
                $this->post($sckt,"profile_load",$profile);

        }
    }

    protected function onmessage(Socket $sckt, $data)
    {
        $packet = $this->check_data($data);

        if(is_array($packet)){//we have a valid packet
            $payload = $this->decode($packet["payload"]);
            if($payload["type"] == "edit"){
                //we are about to edit a profile field
                $this->profile_edit($sckt,$payload);
            }
        }
    }

    protected function onclose(Socket $sckt)
    {

    }

    private function profile_edit(Socket $sckt,$profile_data)
    {
        $profile = new profile_data();
        $profile->user_id = $sckt->user->id;
        $profile->edit_profile($profile_data["field_name"],$profile_data["value"]);
    }
}