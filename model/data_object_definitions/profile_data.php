<?php


class profile_data extends Model
{

    public function __construct($user_id = null)
    {
        parent::__construct();
        !empty($user_id) ? $this->get_profile($user_id) : null;
    }

    public $id;
    public $profile_pic;
    public $date_of_birth;
    public $phone;
    /**
     * @var $department department_mdl the users department
     */
    public $department;
    public $created_at;
    public $gender;
    public $email_address;
    public $user_id;
    public $type;
    public $first_name;
    public $last_name;
    public $full_name;
    public $about_me;
    public $year_complete;
    public $year_start;
    public $programme;
    public $hobbies;
    public $index_number;
    public $profile_pic_thumb;
    public $black_list;
    public $exists = false;
    public $error_code = 0;


    private function get_profile($user_id)
    {
        $prof = $this->pdo_fetch("CALL ussap.fetch_profile(?)", array($user_id), PDO::FETCH_ASSOC);

        if ($prof["count"] > 0) {
            $this->id = $prof["data"]["profile_id"];
            $this->profile_pic = !empty($prof["data"]["profile_pic"]) ? $prof["data"]["profile_pic"] : DEFAULT_PROFILE_PIC;
            $this->profile_pic_thumb = !empty($prof["data"]["profile_pic_thumb"]) ? $prof["data"]["profile_pic_thumb"] : DEFAULT_PROFILE_PIC;
            $this->department = new department_mdl($prof["data"]["department_id"]);
            $this->user_id = $prof["data"]["user_id"];
            $this->type = $prof["data"]["category"];
            $this->first_name = $prof["data"]["first_name"];
            $this->last_name = $prof["data"]["last_name"];
            $this->full_name = $this->first_name . " " . $this->last_name;
            $this->index_number = $prof["data"]["index_number"];
            $this->exists = true;
        } else {
            $this->exists = false;
            $this->error_code = 12;
        }

    }

    public function get_all($id)
    {
        $prof = $this->pdo_fetch("CALL ussap.fetch_profile(?)", array($id), PDO::FETCH_ASSOC);

        if ($prof["count"] > 0) {
            $this->id = $prof["data"]["profile_id"];
            $this->profile_pic = !empty($prof["data"]["profile_pic"]) ? $prof["data"]["profile_pic"] : DEFAULT_PROFILE_PIC;
            $this->profile_pic_thumb = !empty($prof["data"]["profile_pic_thumb"]) ? $prof["data"]["profile_pic_thumb"] : DEFAULT_PROFILE_PIC;
            $this->phone = $prof["data"]["phonenumber"];
            $this->department = new department_mdl($prof["data"]["department_id"]);
            $this->created_at = $prof["data"]["created_at"];
            $this->gender = $prof["data"]["gender"];
            $this->email_address = $prof["data"]["email_address"];
            $this->user_id = $prof["data"]["user_id"];
            $this->type = $prof["data"]["category"];
            $this->first_name = $prof["data"]["first_name"];
            $this->last_name = $prof["data"]["last_name"];
            $this->full_name = $this->first_name . " " . $this->last_name;
            $this->about_me = $prof["data"]["about_me"];
            $this->year_complete = $prof["data"]["year_complete"];
            $this->year_start = $prof["data"]["year_start"];
            $this->hobbies = $prof["data"]["hobbies"];
            $this->programme = $prof["data"]["programme"];
            $this->index_number = $prof["data"]["index_number"];
            $this->black_list = $prof["data"]["black_list"];
            $this->exists = true;
        } else {
            $this->exists = false;
            $this->error_code = 12;
        }
    }

    public function insert_profile_data($user_id, $profile_fields)
    {
        /*------------------------------------------------------
             * next on the list is to create a profile for the user
             *-----------------------------------------------------*/
        $this->pdo_insert("CALL ussap.insert_profile(:user_id,:emailAddress,:userGender,:userDepartment,:userCategory,:userFirstName,:userLastName,:index_number)",
            array(
                ':user_id' => $user_id,
                ':emailAddress' => $profile_fields["email"],
                ':userGender' => $profile_fields["gender"],
                ':userDepartment' => $profile_fields["department"],
                ':userFirstName' => $profile_fields["first_name"],
                ':userLastName' => $profile_fields["last_name"],
                ':userCategory' => $profile_fields["category"],
                ":index_number" => $profile_fields["index_number"]
            ));
    }

    public function  edit_profile($prop, $val)
    {
        $query = "UPDATE ussap.profile as prof SET prof.{$prop}  = :val WHERE prof.user_id = :user_id;";

        $this->pdo_update($query, array(":val" => $val, ":user_id" => $this->user_id));
    }


}

