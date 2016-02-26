<?php



class hcmsdashboard extends Controller{
    public function __construct(){
        parent::__construct("hcmsdashboard");
    }

    public function index(){
        $this->viewBag["title"] = "HCMS DASH BOARD";
        /*
         * check if the incoming request is for a form submission or it is a page request
         * */
        if(count($_POST) > 0){
            //the request is a form submission request
            $username = $_POST['username'];
            $password = $_POST["password"];
            $this->viewBag["username"] = $username;
            $this->viewBag["password"] = $password;
            /*if($this->model->hcms_login_verify($username,$password)){
                $this->view("hcmsdashboard","hcmsLayout");
            }*/
            $this->view("hcmsLayout");
            return;
        }


        $this->viewBag["hcms_data"] = $this->model->page_data_fetch();
        $this->view("hcmsLayout");
    }

    public function upload(){
        /* * * * * * * * * * * * * * *
         * handling multimedia upload (pictures and videos)
         * */


           foreach($_FILES as $key => $file){
               if(isset($_FILES) && !empty($_FILES) && $_FILES[$key]["error"] == 0 && $_FILES[$key]["size"] > 0){

                   $upld_handler = new UploadHandler(array(
                       'upload_dir' => UPLOAD_DIR,
                       'upload_url' => UPLOAD_URL,
                       'user_dirs' => false,
                       'mkdir_mode' => 0755,
                       'param_name' => $key
                   ));

                   $upload_param = (array)json_decode($upld_handler->upload_data);
                   $upload_data = (array)$upload_param[$key];
                   $uploaded_file = (array)$upload_data[0];


                   $this->model->page_content_insert($uploaded_file["url"],$key,"multimedia",$_POST["page_name"],$_POST["{$key}_container_type"]);

               }

           }


        /* * * * * * * * * * * * *
         * handling text uploads
         * */
        foreach($_POST as $key => $fld_val){

            if(!empty($fld_val) && $key != "upload-submit" && $key != "page_name" && !strstr($key,"_container_type")){
                $this->model->page_content_insert($fld_val,$key,"text",$_POST["page_name"],$_POST["{$key}_container_type"]);
            }

        }

        $this->redirect("hcmsdashboard");
    }

    /**
     * function to register users for HcmsDashBoard
     */
    public function register()
    {

    }
}