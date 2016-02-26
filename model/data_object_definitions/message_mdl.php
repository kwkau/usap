<?php

class message_mdl extends Model{

    public function __construct()
    {
        parent::__construct();

    }

    public $id;
    public $text;
    public $created_at;
    /**
     * @var $user user_mdl the user who sent the message
     */
    public $user;
    public $status;

}