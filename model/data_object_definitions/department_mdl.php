<?php


class department_mdl extends Model{

    public function __construct($id=null)
    {
        parent::__construct();
        if(!empty($id))$this->get_dep($id);
    }

    public $id;
    public $name;
    public $school;
    public $head;
    public $info;
    public $valid = false;
    public $error_code = 0;

    private function get_dep($id)
    {
        $dep = $this->pdo_fetch("CALL ussap.fetch_dep(?)",array($id),PDO::FETCH_ASSOC);
        if($dep["count"] == 1){
            $this->id = $dep["data"]["department_id"];
            $this->name = $dep["data"]["department_name"];
            $this->school = $dep["data"]["department_type"];
            $this->head = $dep["data"]["department_head"];
            $this->info = $dep["data"]["department_info"];
            $this->valid = true;
        }else{
            $this->error_code = 3;
        }
    }


    public function fetch_dep($depname)
    {
        $dep = $this->pdo_fetch("CALL ussap.fetch_byname_dep(?)",array($depname),PDO::FETCH_ASSOC);
        if($dep["count"] == 1){
            $this->id = $dep["data"]["department_id"];
            $this->name = $dep["data"]["department_name"];
            $this->school = $dep["data"]["department_type"];
            $this->head = $dep["data"]["department_head"];
            $this->info = $dep["data"]["department_info"];
            $this->valid = true;
        }else{
            $this->error_code = 3;
        }
    }

    public function get_all_dep(){
        $dep = $this->pdo_fetch("CALL ussap.fetch_all_dep()", array(), PDO::FETCH_ASSOC,true);
        $departments = array();
        if($dep["count"] >= 1){
            foreach($dep["data"] as $val){
                $department = new department_mdl();
                $department->id = $val["department_id"];
                $department->name = $val["department_name"];
                $department->school = $val["department_type"];
                $department->head = $val["department_head"];
                $department->info = $val["department_info"];
                $department->valid = true;
                $departments[] = $department;
            }
        }
        return $departments;
    }

}