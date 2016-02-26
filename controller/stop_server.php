<?php


class stop_server extends Controller{

    public function __construct()
    {
      parent::__construct("stop_server");
    }

    public function index()
    {
        WebSocketServer::$stop = true;
    }

}