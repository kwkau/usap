<?php

abstract class wsserver extends WebSocketServer{
    /**
     * @var $channel string the name of the socket
     */
    public $channel = "default";

    /**
     * @var $server_sockets array a list of sockets that are connected to the socket server
     */
    public $server_sockets = array();

    public function __construct($address, $port)
    {
         parent::__construct($address,$port);
    }

    public function connected(Socket $sckt,$msg)
    {
        $this->onopen($sckt,$msg);
    }

    protected function process(Socket $sckt, $msg)
    {
        $this->onmessage($sckt,$msg);
    }

    public function closed(Socket $sckt)
    {
        $this->onclose($sckt);
    }

    //Web Socket server events
    abstract protected function onopen(Socket $sckt, $data);//executed whenever the socket server establishes a new connection
    abstract protected function onmessage(Socket $sckt, $data);//executed when the socket sever receives data from a socket
    abstract protected function onclose(Socket $sckt);//executed when a socket closes the connection to the socket server
    /*abstract protected function onerror(Socket $sckt);*/

    protected function broadcast (Socket $main,array $sckts,$pckt_type,$message)
    {
        foreach($sckts as $id => $sckt){
            if($main->id != $sckt->id){
                $this->post($sckt,$pckt_type,$message,$this->channel);
            }
        }
    }

    /**
     * checks if the incoming data contains a valid packet
     * @param $data string a json string
     * @return array|bool returns an associative  array of the json object if the packet is valid and false otherwise
     */
    public function check_data($data)
    {
        $packet = $this->decode(str_replace("\u0000","",$data));
        if(isset($packet["channel"]) && $packet["channel"] == $this->channel){
            return $packet;
        }
        return false;
    }

    /**
     * parses a json string into an associative array
     * @param $data string|Object json string or json object
     * @return array|bool returns an associative array if the json string or object provided is valid and false if otherwise
     */
    protected function decode($data)
    {
        if(is_object($data)){
            return (Array)$data;
        }elseif(is_string($data)){
            return (Array)json_decode($data);
        }
        return false;
    }

    /**
     * function to send data to the socket provided
     * @param Socket $sckt the socket to receive the data
     * @param $pckt_type string a description for the data being sent
     * @param $data mixed the data to be sent, could either be a string, Object or array
     */
    protected function post(Socket $sckt,$pckt_type,$data)
    {
        $packet = json_encode(array("channel" =>$this->channel, "packet_type" => $pckt_type,"payload" => $data));
        $this->send($sckt,$packet);
    }

    /**
     * checks if user1 is a friend to user2
     * @param user_mdl $user1 the user model for the first user
     * @param user_mdl $user2 the user model for the second user
     * @return bool returns true if the two users are indeed friends and false if otherwise
     */
    protected function is_frnd(user_mdl $user1,user_mdl $user2)
    {
        $frnd = new friend_mdl();
        return $frnd->chck_frnd($user1->friend_id,$user2->friend_id);
    }


    protected function group_mem_sckts(Socket $u_sckt,$name)
    {
        $mem_sckts = array();
        foreach ($this->server_sockets as $sckt) {
            if(($sckt->user->group->name == $name) && ($u_sckt->user->id != $sckt->user->id)){
                $mem_sckts[] = $sckt;
            }
        }
        return $mem_sckts;
    }

    protected function dep_mem_sckts(Socket $u_sckt,$name)
    {
        $mem_sckts = array();
        foreach ($this->server_sockets as $sckt) {
            if(($sckt->user->department->name == $name) && ($u_sckt->user->id != $sckt->user->id)){
                $mem_sckts[] = $sckt;
            }
        }
        return $mem_sckts;
    }

    /**
     * finds the sockets that belong to the friends of the user together with the users, these friends must be connected to the socket
     * @param $id int the user id of the user who's friends sockets you want to find
     * @return array returns an array of sockets that belong to the friends of the user together with the users socket if the user is online
     */
    protected function find_user_frnd_sockets($id)
    {
        //check if user is online
        if($this->online($id)){
            //find the users socket
            $user = $this->find_socket($id);
            $sckts = $this->frnd_sockets($user);
             $sckts[] = $user;
            return $sckts;
        }else{
            //this user is not online and hence does not have a socket connection, so we go the hard way
            $user = new user_mdl($id);
            $frnd_sckts = array();
            foreach($this->server_sockets as $sckt){
                if($this->is_frnd($user,$sckt->user)){
                    $frnd_sckts[] = $sckt;
                }
            }
            return $frnd_sckts;
        }
    }

    /**
     * finds a particular socket when provided the user id of the user of that socket
     * @param $id int the user id of the user the socket belongs to
     * @return Socket|bool returns a socket if the user id provided belongs to a valid socket that is connected to the socket
     */
    protected function find_socket($id)
    {
        $socket = false;
        foreach($this->server_sockets as $sckt){
            if($sckt->user->id == $id){
                $socket = $sckt;
            }
        }
        return $socket;
    }

    /**
     * function to check if a user is online
     * @param $id int the user id of the user
     * @return bool returns true if the user is online and false if otherwise
     */
    protected function online($id)
    {
        $online = false;
        foreach($this->server_sockets as $sckt){
            if($sckt->user->id == $id){
                $online = true;
            }
        }
        return $online;
    }


    /**
     * function to find the sockets that belong to the friends of the user provided, these friends must be connected to the socket
     * @param Socket $user_sckt the socket of the user
     * @return array returns an array of sockets that belong to the friends of the user
     */
    protected function frnd_sockets(Socket $user_sckt)
    {
        $frnd_sckts = array();
        foreach($this->server_sockets as $sckt){
            if($this->is_frnd($user_sckt->user,$sckt->user)){
                $frnd_sckts[] = $sckt;
            }
        }
        return $frnd_sckts;
    }

    /**
     * adds  a socket to the list of sockets that are connected to this socket server, the socket to be added must be a new socket
     * @param Socket $sckt the socket to be added to the server
     * @return bool returns true if the socket is successfully added to the server and false if otherwise
     */
    protected function add_user(Socket $sckt)
    {
        $socket_exists = false;
        /*---------------------------------------------------------------------------------------
         * a user who connects to a socket will be added to the server sockets array to keep
         * record of them
         *--------------------------------------------------------------------------------------*/
         foreach ($this->server_sockets as $socket) {
            if($socket->user->id == $sckt->user->id){
               $socket_exists = true;
            }
         }
        if(!$socket_exists){
            $this->server_sockets[$sckt->id] = $sckt;
            return true;
        }
        return false;
    }

    /**
     * removes a socket from the list of sockets that are connected to the socket server
     * @param Socket $sckt the socket to be removed
     */
    protected function remove_user(Socket $sckt)
    {
        unset($this->server_sockets[$sckt->id]);
    }

    public function log_err(Exception $ex)
    {
        $err = new error_mdl();
        $err->message = $ex->getMessage();
        $err->socket_name = $this->channel;
        $err->line_number = $ex->getLine();
        $err->stack_trace = $ex->getTraceAsString();
        $err->code = $ex->getCode();
        $err->set_error();
    }

}