<?php


class token_mdl extends Model{

    public function __construct($token_type=null)
    {
        parent::__construct();
        if(!empty($token_type))$this->get_token($token_type);
    }

    public $id;
    public $token;
    public $token_salt;
    public $token_hash;
    public $token_type;
    public $token_count;

    public function get_token($token_type)
    {
        $result = $this->pdo_fetch("CALL ussap.fetch_token(:token_type)",array(":token_type" => $token_type),PDO::FETCH_ASSOC);
        $this->token_count = $result["count"];
        if($result["count"]>0){
            $token = $result["data"];
            $this->id = $token["token_id"];
            $this->token = $token["token"];
            $this->token_salt = $token["token_salt"];
            $this->token_hash = $token["token_hash"];
            $this->token_type = $token["token_type"];
        }

    }

    public function set_token($token_type,$token_value)
    {
        $this->token_salt = $this->salt();//generate and has the token salt
        $this->token = $this->hash($token_value);//hash the the token value to obtain the token
        //insert our token into the database
        $this->pdo_insert("CALL ussap.insert_token(:token_type,:token,:token_salt,:token_hash)",
            array(
                ":token_type" => $token_type,
                ":token" => $this->token,
                ":token_salt" => $this->token_salt,
                ":token_hash" => $this->hash($this->token.$this->token_salt)//generate the the token hash by hashing the token and the token_salt
            ));
    }
}