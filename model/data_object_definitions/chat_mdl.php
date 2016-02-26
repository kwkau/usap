<?php

class chat_mdl extends Model{

    public function __construct()
    {
        parent::__construct();

    }

    public $id;
    public $created_at;
    public $friend_id;
    /**
     * @var $messages message_mdl the messages that belong to the chat
     */
    public $messages;
}