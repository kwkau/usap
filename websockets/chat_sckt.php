<?php


 class chat_sckt extends wsserver{

    public function __construct($address, $port)
    {
        parent::__construct($address,$port);
    }

    private $chat_sckts = array();

    public function connecting(Socket $sckt)
    {
        $sckt->user = new user_mdl(session::get("user_id"));
    }

    public function connected(Socket $sckt)
    {
        $this->chat_sckts[$sckt->id] = $sckt;
    }

    public function process(Socket $sckt, $msg)
    {

    }

    private function check_msg($msg)
    {

    }

}