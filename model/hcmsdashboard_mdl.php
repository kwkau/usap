<?php

class hcmsdashboard_mdl extends Model{
    public function __construct(){
        parent::__construct();
    }

    private $page_data = array();
    private $container_pckg = array();
    private static $sharedDate;


    /**
     * Fetch page and page container data from the database
     * @return array
     * @throws Exception
     */
    public  function page_data_fetch(){

       $pages = $this->pdo_fetch("CALL HCMS.fetch_page()",array(),PDO::FETCH_ASSOC);

        foreach($pages["data"] as $page){
            //now we fetch the containers
            $main_containers = $this->pdo_fetch("CALL HCMS.fetch_main_container(:page_id)",array(":page_id"=> $page["page_id"]),PDO::FETCH_ASSOC);

            if($main_containers["count"] > 0){
                //we now have our main containers so we see if they have sub_containers and then we fetch them
                foreach($main_containers["data"] as $main_container){
                    $sub_containers = $this->pdo_fetch("CALL HCMS.fetch_sub_container(:main_container_id)",array(":main_container_id"=> $main_container["page_main_container_id"]),PDO::FETCH_ASSOC);
                    /*
                     * so now that we have all that we need how do we package it and make it controller friendly
                     * simple we use a two dimensional table to store both main and sub container data for every iteration
                     * */
                    $this->container_pckg[] = array(
                        "page" => $page["page_name"],
                        "main_container_tag"=> $main_container["page_main_container_tag"],
                        "main_content_type"=> $main_container["main_content_type"],
                        "sub_containers" => $sub_containers["data"]
                    );

                }
            }else{
                /*
                 * todo: we will need to throw an exception if there are no containers for a page
                 * */
                /*throw(new Exception("fatal error: {$page["page_name"]} does not have a container"));*/
                die("fatal error: {$page["page_name"]} does not have a container");
            }


            $this->page_data["pages"][] =  $page["page_name"]
            ;

            $this->page_data["containers"][$page["page_name"]] = $this->container_pckg;



            /*
                 * reset our container package array variable to prevent previous container data from spilling over into other pages
                 * */
            $this->container_pckg = array();
        }
        return $this->page_data;
    }

    public function hcms_login_verify($username,$password){
       $login_result =  $this->pdo_fetch("CALL hcms.hcms_login_check(:username)",array(":username" => $username),PDO::FETCH_NUM);
        if($login_result["count"] == 1){
            /*
             * user is valid, now we check the salt and password
             * */
            $hashPass = $this->hash($password . $login_result["data"][1]);
            if($login_result["data"][0] == $hashPass ){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function page_content_insert ($content,$container_tag,$content_type,$page_name,$container_type){
        /*
         * insert content parameters (in content text, in main_cont_tag varchar (45), in contnt_type varchar (45), in pge_name varchar (45),in containerType varchar(45))
         * */
        if($container_type == "main_container"){
            $timestamp = $this->dt->get_date("date.timezone");
            $this->pdo_insert("CALL hcms.insert_content(:content,:main_cont_tag,:contnt_type,:pge_name,:containerType,:created_at)",array(
                ":content" => $content,
                ":main_cont_tag" => $container_tag,
                ":contnt_type" => $content_type,
                ":pge_name" => $page_name,
                ":containerType" => $container_type,
                ":created_at" => $timestamp
            ));
            self::$sharedDate = $timestamp;
        }else if ($container_type == "sub_container") {
            $this->pdo_insert("CALL hcms.insert_content(:content,:main_cont_tag,:contnt_type,:pge_name,:containerType,:created_at)",array(
                ":content" => $content,
                ":main_cont_tag" => $container_tag,
                ":contnt_type" => $content_type,
                ":pge_name" => $page_name,
                ":containerType" => $container_type,
                ":created_at" => self::$sharedDate
            ));
        }


    }


}