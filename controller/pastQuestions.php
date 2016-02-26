<?php

class pastQuestions extends Controller{

    public function __construct()
    {
        parent::__construct("pastQuestions");
    }

    public function index()
    {
        $this->viewBag["title"] = "USSAP Past Questions";

        $this->view("Layout");
    }

}