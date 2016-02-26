<?php

class error_mdl extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    public $id;
    public $message;
    public $socket_name;
    public $code;
    public $line_number;
    public $stack_trace;
    public $created_at;
    public $recent;

    public function get_errors()
    {
        $x = 0;
        $errors = array();
        $result = $this->pdo_fetch("SELECT * FROM ussap.error_log as er ORDER BY er.created_at DESC;",array(),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach ($result["data"] as $val) {
                if($x == 0){
                    $this->recent = $val["created_at"];
                    $x++;
                }
                $error = new error_mdl();
                $error->id = $val["log_id"];
                $error->message = $val["message"];
                $error->socket_name = $val["socket_name"];
                $error->line_number = $val["line_number"];
                $error->stack_trace = $val["stack_trace"];
                $error->created_at = $val["created_at"];
                $error->code = $val["code"];
                $errors[] = $error;
            }
        }
        return $errors;
    }

    public function set_error()
    {
        $this->pdo_insert("INSERT INTO ussap.error_log(message, code,socket_name, line_number, stack_trace,created_at) VALUES (:msg,:cde,:sckt_name,:ln_num,:stck_trace,:dt)",
            array(
                ":msg" => $this->message,
                ":cde" => $this->code,
                ":sckt_name" => $this->socket_name,
                ":ln_num" => $this->line_number,
                ":stck_trace" => $this->stack_trace,
                ":dt" => $this->dt->get_date("date.timezone")
            ));
    }


    public function del_error()
    {
        $this->pdo_delete("DELETE * FROM ussap.error_log as er WHERE er.log_id = ?",array($this->id));
        echo 1;
    }

    public function refresh_error()
    {
        $errors = array();
        $result = $this->pdo_fetch("SELECT * FROM ussap.error_log as er WHERE er.created_at > ?",array($this->recent),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach ($result["data"] as $val) {
                $error = new error_mdl();
                $error->id = $val["log_id"];
                $error->message = $val["message"];
                $error->socket_name = $val["socket_name"];
                $error->line_number = $val["line_number"];
                $error->stack_trace = $val["stack_trace"];
                $error->created_at = $val["created_at"];
                $error->code = $val["code"];
                $errors[] = $error;
            }

        }
        return $errors;
    }
}