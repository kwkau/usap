<?php


class search_mdl extends Model{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @var $forums forum_mdl forums that match the search query
     */
    public $forums;

    /**
     * @var $users user_mdl users that match the search query
     */
    public $users;

    /**
     * @var $groups group_mdl groups that match the search query
     */
    public $groups;

    public $query;

    public function search_run()
    {
        /*---------------------------
         * first we search for users
         *-------------------------*/
        $users = array();
        $grps = array();
        $user_result = $this->pdo_fetch("SELECT ussap.profile.user_id FROM ussap.profile WHERE profile.first_name LIKE '%{$this->query}%' OR profile.last_name LIKE '%{$this->query}%'",
            array(), PDO::FETCH_ASSOC,true);
        foreach ($user_result["data"] as $rslt) {
            $user = new profile_data();
            $user->get_all($rslt["user_id"]);
            $users[] = $user;
        }

        /*---------------------------
         * next we search for groups
         *-------------------------*/
        $grp_result = $this->pdo_fetch("SELECT ussap.groups.group_name FROM ussap.groups WHERE ussap.groups.group_name LIKE '%{$this->query}%' ORDER BY ussap.groups.group_name ASC",
            array(), PDO::FETCH_ASSOC,true);

        return array("users" => $users, "groups" => $grp_result["data"]);

    }
}