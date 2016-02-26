<?php


class notification_mdl extends Model{
    public function __construct()
    {
        parent::__construct();
    }


    public $id;
    /**
     *@var $type string the type of notification e.g. user,department,group
     */
    public $type;
    public $created_at;
    /**
     *@var $perp_prof profile_data the profile of user who performed the action to cause the notification
     */
    public $perp_prof;

    public $magic_id;
    /**
     *@var $noti_text string the message that will be displayed to the user when the noti is viewed
     */
    public $noti_text;
    /**
     *@var $target_id int the user_id user who will receive the notification (user_notifications)
     */
    public $target_id;
    /**
     *@var $department_id int the id of the department that the notification is meant for (department notifications)
     */
    public $department_id;
    /**
     *@var $group_id int the id if the group the notification is meant for (group notification)
     */
    public $group_id;
    /**
     *@var $chat_id int the id of the chat the notification is about (chat notification)
     */
    public $chat_id;
    /**
     *@var $target_object_magic_id int the the unique id of the object the perp interacted with
     */
    public $target_object_magic_id;
    /**
     *@var $target_object_type string the name of the object the perp interacted with e.g. forum, post, comment, upload
     */
    public $target_object_type;
    /**
     *@var $perp_object_type string the name of the object the perp created whiles interacting with the target object
     */
    public $perp_object_type;
    /**
     *@var $perp_object_id string the unique id of the object the perp created while interacting with the target object
     */
    public $perp_object_id;


    /**
     *function to create a notification after setting the respective notification properties
     */
    public function create_noti()
    {
        switch($this->type){
            case "user":
                //user notification
                $this->pdo_insert("CALL ussap.insert_user_noti(:perp_id,:target_id,:noti_text,:target_object_magic_id,:target_object_type,:created_at,:magic_id)",
                    array(
                        ":perp_id" => $this->perp_prof->user_id,
                        ":target_id" => $this->target_id,
                        ":noti_text" => $this->noti_text,
                        ":target_object_magic_id" => $this->target_object_magic_id,
                        ":target_object_type" => $this->target_object_type,
                        ":created_at" => $this->created_at,
                        ":magic_id" => $this->magic_id
                    ));
                break;
            case "department":
                //departmental notification
                $this->pdo_insert("CALL ussap.insert_department_noti(:perp_id,:dep_id,:noti_text,:target_object_magic_id,:target_object_type,:created_at,:magic_id)",
                    array(
                        ":perp_id" => $this->perp_prof->user_id,
                        ":dep_id" => $this->department_id,
                        ":noti_text" => $this->noti_text,
                        ":target_object_magic_id" => $this->target_object_magic_id,
                        ":target_object_type" => $this->target_object_type,
                        ":created_at" => $this->created_at,
                        ":magic_id" => $this->magic_id
                    ));
                break;
            case "group":
                //group notification
                $this->pdo_insert("CALL ussap.insert_group_noti(:perp_id,:group_id,:noti_text,:target_object_magic_id,:target_object_type,:created_at,:magic_id)",
                    array(
                        ":perp_id" => $this->perp_prof->user_id,
                        ":group_id" => $this->group_id,
                        ":noti_text" => $this->noti_text,
                        ":target_object_magic_id" => $this->target_object_magic_id,
                        ":target_object_type" => $this->target_object_type,
                        ":created_at" => $this->created_at,
                        ":magic_id" => $this->magic_id
                    ));
                break;
            case "chat":
                //chat notification
                $this->pdo_insert("CALL ussap.insert_chat_noti(:perp_id,:target_id,:noti_text,:target_object_magic_id,:target_object_type,:noti_type,:perp_object_type,:perp_object_id,:created_at)",
                    array(
                        ":perp_id" => $this->perp_prof->user_id,
                        ":target_id" => $this->target_id,
                        ":noti_text" => $this->noti_text,
                        ":target_object_magic_id" => $this->target_object_magic_id,
                        ":target_object_type" => $this->target_object_type,
                        ":created_at" => $this->created_at
                    ));
                break;
            default:
                return false;
                break;
        }
    }

    public function delete_noti()
    {
        $this->pdo_delete("CALL ussap.delete_noti(:noti_magic_id,:noti_type)",
            array(
                ":noti_magic_id" => $this->magic_id,
                ":noti_type" => $this->type
            ));
    }

    public function fetch_all($id)
    {
        return array(
            "user_noti" => $this->fetch_user_noti($id),
            "department_noti" => $this->fetch_dep_noti($id),
            "group_noti" => $this->fetch_grp_noti($id)
        );

    }

    public function fetch_user_noti($id)
    {
        $notifications = array();
        $result = $this->pdo_fetch("CALL ussap.fetch_user_noti(?)",array($id),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach($result["data"] as $val)
            {
                //parent properties
                $noti = new notification_mdl();

                $this->set_parents($noti,$val);

                //child properties
                $noti->target_id = $val["user_id"];
                $notifications[] = $noti;
            }
        }

        return $notifications;
    }

    public function fetch_dep_noti($id)
    {
        $notifications = array();
        $result =$this->pdo_fetch("CALL ussap.fetch_department_noti(?)",array($id),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach($result["data"] as $val)
            {
                $noti = new notification_mdl();
                //parent properties
                $this->set_parents($noti,$val);

                //child properties
                $noti->department_id = $val["department_id"];
                $notifications[]=$noti;
            }
        }

        return $notifications;
    }


    public function fetch_grp_noti($id)
    {
        $notifications = array();
        $result =$this->pdo_fetch("CALL ussap.fetch_group_noti(?)",array($id),PDO::FETCH_ASSOC,true);
        if($result["count"] > 0){
            foreach($result["data"] as $val)
            {
                $noti = new notification_mdl();

                //parent properties
                $this->set_parents($noti,$val);

                //child properties
                $noti->group_id = $val["group_id"];
                $notifications[]=$noti;
            }
        }

        return $notifications;
    }

    private function fetch_chat_noti($id)
    {
        $notifications = array();
        $result =$this->pdo_fetch("CALL ussap.fetch_chat_noti(?)",array($id),PDO::FETCH_ASSOC,true);
        foreach($result["data"] as $val)
        {
            $noti = new notification_mdl();

            //parent properties
            $this->set_parents($noti,$val);

            //child properties
            $noti->target_id = $val["target_id"];
            $noti->chat_id = $val["chat_id"];
            $notifications[] = $noti;
        }
        return $notifications;
    }

    public function noti_count ($id)
    {
        $grp_result =$this->pdo_fetch("CALL ussap.fetch_group_noti(?)",array($id),PDO::FETCH_ASSOC,true);
        $dep_result =$this->pdo_fetch("CALL ussap.fetch_department_noti(?)",array($id),PDO::FETCH_ASSOC,true);
        $usr_result = $this->pdo_fetch("CALL ussap.fetch_user_noti(?)",array($id),PDO::FETCH_ASSOC,true);
        return $grp_result["count"] + $dep_result["count"] + $usr_result["count"];
    }




    private function set_parents(notification_mdl &$noti, array $val){
        $noti->id = $val["notification_id"];
        $noti->type = $val["notification_type"];
        $noti->created_at = $val["created_at"];
        $noti->perp_prof = new profile_data($val["perp"]);
        $noti->noti_text = $val["notification_text"];
        $noti->target_object_magic_id = $val["target_object_magic_id"];
        $noti->target_object_type = $val["target_object_type"];
        $noti->magic_id = $val["magic_id"];
    }

}