<?php

class ServerDat{
    private $server_vals= array();
    public function __construct(){
        $this->load_config();
        $this->set_constants();
    }

   private function load_config(){
        try{
            $config_json = fread(fopen( __DIR__.'/config.json','r'),4*1024);
        }catch (Exception $ex){
            die("We are sorry for the inconvenience caused but we are having problems with our server, please try again later");
        }
        $obj = (array)json_decode($config_json);
        $this->server_vals = (array) $obj['server'];
   }

    private function set_constants () {
        foreach ($this->server_vals as $key => $val) {
            defined($key) ? null : define($key, $val);
        }
    }


}