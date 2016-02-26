<?php

 class dropzone extends Controller{

     public function __construct()
     {
         parent::__construct("dropzone");
         $this->login_verify();
         /*quora.com*/
     }

     public function index()
     {
         $this->viewBag["title"] = "usap";
         $this->viewBag["tag"] = "user";

         //display the dropzone page
         $this->view("Layout");
     }


     public function get_data()
     {
         $user = new user_mdl(session::get("user_id"));
         echo json_encode($user->profile);
     }


     public function upload_post_pic()
     {
         $file_name = $_SERVER["HTTP_X_FILE_NAME"];
         $file_size = $_SERVER["HTTP_X_FILE_SIZE"];
         $file_type = $_SERVER["HTTP_X_FILE_TYPE"];

         $upld_handler = new UploadHandler(array(
             'upload_dir' => "C:/wamp/www/usap/public/uploads/tmp/",
             'upload_url' => "",
             'user_dirs' => false,
             'mkdir_mode' => 0755,
             'param_name' => "post-pic",
             'thumbnail' => array()
         ));

         $upload_param = (array)json_decode($upld_handler->upload_data);
         $upload_data = (array)$upload_param["post-pic"];
         $uploaded_file = (array)$upload_data[0];

         $tmp_file_dir = "C:/wamp/www/usap/public/uploads/tmp/".$uploaded_file["name"];

         //open the temporary file
         $hndl = fopen($tmp_file_dir,"r");

         //read the contents of the temporary file in 4mb chunks
         $tmp_file = fread(fopen($tmp_file_dir,"r"),4*1024*1024);
         fclose($hndl);

         //delete temporary file
         unlink($tmp_file_dir);



         if(stristr($file_type,"png")){
             imagepng(imagecreatefromstring($tmp_file),POST_PIC_UPLOAD_DIR.$file_name,100);
         }elseif(stristr($file_type,"jpeg")){
             imagejpeg(imagecreatefromstring($tmp_file),POST_PIC_UPLOAD_DIR.$file_name,100);
         }elseif(stristr($file_type,"gif")){
             imagegif(imagecreatefromstring($tmp_file),POST_PIC_UPLOAD_DIR.$file_name,100);
         }
        echo POST_PIC_UPLOAD_URL.$file_name;
     }

     public function get_notifications()
     {
         $user_id  = filter_input(INPUT_POST,"user");
         $user_noti = new notification_mdl($user_id,"user");
         $dep_noti = new notification_mdl($user_id,"department");
         $grp_noti = new notification_mdl($user_id, "group");

         echo json_encode(
             array(
                 "user_noti_num" => count($user_noti),
                 "dep_noti_num" => count($dep_noti),
                 "grp_noti_num" => count($grp_noti)
             ));
     }



     public function get_friends()
     {
         $user_id  = filter_input(INPUT_POST,"user");
         $friends = new friend_mdl($user_id);
         echo json_encode(array("friend_num" => count($friends)));
     }

     public function load_friends()
     {
         $user_id  = filter_input(INPUT_POST,"user");
         $friend = new friend_mdl();
         echo json_encode($friend->get_frnds($user_id));
     }

     public function load_groups()
     {
         $user_id  = filter_input(INPUT_POST,"user");
         $group = new group_data();
         echo json_encode($group->get_grps($user_id));
     }

     public function load_bookmarks()
     {
         $bkmrk = new bookmark_mdl();
         echo json_encode($bkmrk->get_bkmks(filter_input(INPUT_POST,"type"),session::get("user_id")));
     }




     public function set_bookmark()
     {
         $bkmrk = new bookmark_mdl();
         $bkmrk->bookmark_type = filter_input(INPUT_POST,"bkmrk_type");
         $bkmrk->user_id = session::get("user_id");
         $bkmrk->magic_id = filter_input(INPUT_POST,"magic_id");
         $bkmrk->created_at = filter_input(INPUT_POST,"created_at");
         $bkmrk->set_bkmk();
     }


     public function flag()
     {
         $flag_type = filter_input(INPUT_POST,"flag_type");
         $magic_id = filter_input(INPUT_POST,"magic_id");
         if($flag_type == "post"){
             $post = new post_mdl();
             $post->flag($magic_id);
         }elseif($flag_type == "forum"){
             $forum = new forum_mdl();
             $forum->flag($magic_id);
         }
     }

     public function search()
     {
         $search = new search_mdl();
         $search->query = filter_input(INPUT_POST,"query");
         echo json_encode($search->search_run());
     }

     public function fetch_upload()
     {
         $tag = filter_input(INPUT_POST,"tag");
         $t_id = filter_input(INPUT_POST,"t_id");
         $upld = new  upload_mdl();
         echo json_encode($upld->get_uploads($tag,$t_id));
     }


}