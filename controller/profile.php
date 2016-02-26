<?php

class profile extends Controller{
    public function __construct(){
        parent::__construct("profile");
        $this->login_verify();
    }

    public function index($user_id){
        $this->viewBag["title"] = "Profile Page";
        /*--------------------------------------------------------------------
         * check if a user id has been provided, when we receive a user id it
         * means we are fetching a particular users profile
         *------------------------------------------------------------------*/
        if(!empty($user_id)){
            //obtain the profile of the user who's user id has been provided
            $user = new user_mdl($user_id);
            $profile  = new profile_data();
            $profile->get_all($user_id);
            $this->viewBag["user_prof"] = $profile;

            //obtain the number of friends which the user has
            $friend =  new friend_mdl();
            $this->viewBag["friends"] = $friend->get_frnds($user_id);
            $this->viewBag["is_friend"] = $friend->chck_frnd(session::get("friend_id"),$user->get_friend_id($profile->user_id));

            //obtain information on all the groups the user is part of
            $grps = new group_data();
            $this->viewBag["groups"] = $grps->get_grps($user_id);

            $this->viewBag["title"] = $profile->full_name;

            //display a view for displaying the profile of a particular user
            $this->select("user_prof","Layout");
            return false;
        }

        $this->view("Layout");
    }

    public function uploads()
    {

        $upld_handler = new UploadHandler(array(
            'upload_dir' => PROFILE_PIC_UPLOAD_DIR,
            'upload_url' => PROFILE_PIC_UPLOAD_URL,
            'user_dirs' => false,
            'mkdir_mode' => 0755,
            'param_name' => "profile_pic"
        ));

        $upload_param = (array)json_decode($upld_handler->upload_data);
        $upload_data = (array)$upload_param["profile_pic"];
        $uploaded_file = (array)$upload_data[0];

        /*------------------------------------------------------------------------------------------------------------------------
         * todo: we have to log the picture which the user is trying to upload into the upload table for archives/later reference
         *----------------------------------------------------------------------------------------------------------------------*/


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

        $profile = new profile_data();
        $profile->user_id = $_SESSION["user_id"];
        $profile->edit_profile("profile_pic_thumb",$uploaded_file["thumbnailUrl"]);
        $profile->edit_profile("profile_pic",$uploaded_file["url"]);
        $this->redirect("profile");
    }
}