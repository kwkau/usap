<?php

class errors extends Controller
{

    public function __construct()
    {
        parent::__construct("errors");
    }

    public static $recent_date;

    public function index()
    {
        $this->viewBag["title"] = "Error Logs";

        $this->view();
    }

    public function load_errors()
    {
        $error = new error_mdl();
        echo json_encode($error->get_errors());
        self::$recent_date = $error->recent;
    }

    public function refresh_errors()
    {
        $error = new error_mdl();
        $error->recent = self::$recent_date;
        echo json_encode($error->refresh_error());
    }
}