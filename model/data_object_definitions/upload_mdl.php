<?php

class upload_mdl extends Model{
    public function __construct()
    {
        parent::__construct();
    }

    public $id;
    public $file_name;
    public $file_size;
    public $file_type;
    public $file_mime;
    public $file_url;
    public $type;
    public $magic_id;
    public $user_prof;
    public $group_id;
    public $department_id;
    public $user_id;
    public $total_size = 0;
    public $created_at;


    public function get_uploads($type,$id)
    {

        //array_search();
        $uploads = array();$result=array();
        switch($type){
            case "department":
                $result = $this->pdo_fetch("CALL ussap.fetch_department_uploads(:department_id)",array(":department_id" => $id),PDO::FETCH_ASSOC,true);
                break;
            case "user":
                $result = $this->pdo_fetch("CALL ussap.fetch_user_uploads(:user_id)",array(":user_id" => $id),PDO::FETCH_ASSOC,true);
                break;
            case "group":
                $result = $this->pdo_fetch("CALL ussap.fetch_grp_uploads(:group_id)",array(":group_id" => $id),PDO::FETCH_ASSOC,true);
                break;
        }

        if($result["count"] > 0){
            foreach ($result["data"] as $var) {
                $upload = new upload_mdl();
                $upload->id = $var["upload_id"];
                $upload->file_name = $var["file_name"];
                $upload->file_size = $var["size"];
                $upload->file_type = $var["file_type"];
                $upload->magic_id = $var["magic_id"];
                $upload->file_url = $var["upload_url"];
                $upload->created_at = $var["created_at"];
                $upload->type = $type;
                if($type == "group" || $type == "department"){
                    $upload->user_prof = new profile_data($var["user_id"]);
                }
                $uploads[] = $upload;
                $this->total_size += intval($var["size"]);
            }

        }
        return $uploads;
    }


    public function set_upload()
    {
        switch($this->type){
            case "department":
                $this->pdo_insert("CALL ussap.insert_department_upload(:depid,:userid,:fsize,:fname,:ftype,:createdat,:url,:magicid)",
                    array(
                        ":depid" => $this->department_id,
                        ":userid" => $this->user_id,
                        ":fsize" => $this->file_size,
                        ":fname" => $this->file_name,
                        ":ftype" => $this->file_type,
                        ":createdat" => $this->dt->get_date("date.timezone"),
                        ":url" => $this->file_url,
                        ":magicid" => $this->magic_id
                    ));
                break;
            case "user":
                $ret = $this->pdo_insert("CALL ussap.insert_user_upload(:user_id,:f_size,:f_name,:f_type,:created_at,:url,:magic_id)",
                    array(
                        ":user_id" => $this->user_id,
                        ":f_size" => $this->file_size,
                        ":f_name" => $this->file_name,
                        ":f_type" => $this->file_type,
                        ":created_at" => $this->dt->get_date("date.timezone"),
                        ":url" => $this->file_url,
                        ":magic_id" => $this->magic_id
                    ));
                break;
            case "group":
                $this->pdo_insert("CALL ussap.insert_group_upload(:grpid,:userid,:fsize,:fname,:ftype,:createdat,:url,:magicid)",
                    array(
                        ":grpid" => $this->group_id,
                        ":userid" => $this->user_id,
                        ":fsize" => $this->file_size,
                        ":fname" => $this->file_name,
                        ":ftype" => $this->file_type,
                        ":createdat" => $this->dt->get_date("date.timezone"),
                        ":url" => $this->file_url,
                        ":magicid" => $this->magic_id
                    ));
                break;
        }
    }

    public function upld_del()
    {
        switch($this->type){
            case "user":
                $ret = $this->pdo_insert("CALL ussap.delete_user_upload(:magic_id)",
                    array(
                        ":magic_id" => $this->magic_id
                    ));
                break;
            case "group":
                $this->pdo_insert("CALL ussap.delete_group_upload(:magicid)",
                    array(
                        ":magicid" => $this->magic_id
                    ));
                break;
        }
    }


}