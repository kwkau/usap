<?php

class upload extends Controller{
    public function __construct()
    {
        parent::__construct("upload");
        $this->login_verify();
        $this->max_user_space = 150 * 1024 * 1024;
        $this->max_group_space = 300 * 1024 * 1024;
    }
    private $mimes = array (
        "jpeg" => "image/jpeg",
        "png" => "image/png",
        "gif" => "image/gif",
        "pdf" => "application/pdf",
        "x" => "application/xdoc",
        "w" => "application/msword",
        "pp" => "application/vnd.ms-powerpoint"
    );


    /**
     *
     */
    public function index(){
        /*-------------------------------------------------------------------------------------------------------------
         * we will have to check the remaining space that the user has left, if the remaining space is enough to store
         * the uploading file the we upload otherwise we abort the upload
         * first we need to decide the space each user must be allocated
         * 150Mb
         *------------------------------------------------------------------------------------------------------------*/
        $maxed = true;
        $tag = filter_input(INPUT_POST,"tag");
        $magcid = filter_input(INPUT_POST,"magic_id");
        $upld = new upload_mdl();
        $upld->user_id = session::get("user_id");

        if($tag == "user"){
            //obtain all the files the user has currently uploaded
            $upld->get_uploads($tag,$upld->user_id);

            //check if the user the user has reached the maximum upload size
            $maxed = $upld->total_size >= $this->max_user_space;
        }elseif($tag == "department"){
            $usr = new profile_data($upld->user_id);
            $upld->department_id = $usr->department->id;
            $maxed = false;
        }elseif($tag == "group"){
            //obtain the total size of the files the group has currently uploaded
            $group = new group_data();
            $group->fetch_group(session::get("group_name"));
            $upld->get_uploads($tag,$group->id);
            $upld->group_id = $group->id;

            //check if the group has reached the maximum upload size
            $maxed = $upld->total_size >= $this->max_group_space;
        }

        if(!$maxed) {
            try{
                $upld_handler = new UploadHandler(array(
                    'upload_dir' => UPLOAD_DIR,
                    'upload_url' => UPLOAD_URL,
                    'user_dirs' => false,
                    'param_name' => "file",
                    'max_file_size' => 50 * 1024 * 1024
                ));

            }catch (Exception $ex){

            }

            $upload_param = (array)json_decode($upld_handler->upload_data);
            $upload_data = (array)$upload_param["file"];
            $uploaded_file = (array)$upload_data[0];

            $type = strstr($uploaded_file["type"],'image/') ? "img": array_search($uploaded_file["type"], $this->mimes);

            print_r($upload_param);

            /*------------------------
             * set upload parameters
             *----------------------*/
            $upld->file_type = $type;
            $upld->type = $tag;
            $upld->file_size =  $uploaded_file["size"];
            $upld->file_name = $uploaded_file["name"];
            $upld->file_url = HOST_URL."/download?file=".$uploaded_file["name"];
            $upld->file_mime = $uploaded_file["type"];
            $upld->magic_id = $magcid;
            /*---------------------------------------------
             * insert upload information into the database
             *-------------------------------------------*/

            print_r($upld);
            $upld->set_upload();
            if($tag == "user"){
                //redirect to the dropzone page
                $this->redirect("dropzone");
            }elseif($tag == "department"){
                //redirect the user to their department page
                $this->redirect("department",$usr->department->name);
            }elseif($tag == "group"){
                //redirect the user to their group page
                $this->redirect("group",$group->name);
            }
        }else{
            echo "full";
            session::set("space_maxed",true);
        }



        /*
         * $uploaded_file =Array
(
    [name] => Chinese-Wok-Tea-Smoked-Fish-2.jpg
    [size] => 108603
    [type] => image/jpeg
    [url] => http://localhost/usap/public/uploads/profile_pics/Chinese-Wok-Tea-Smoked-Fish-2.jpg
    [thumbnailUrl] => http://localhost/usap/public/uploads/profile_pics/thumbnail/Chinese-Wok-Tea-Smoked-Fish-2.jpg
    [deleteUrl] => http://localhost/usap/?file=Chinese-Wok-Tea-Smoked-Fish-2.jpg
    [deleteType] => DELETE
)
         * */

    }

}