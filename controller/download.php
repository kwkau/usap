<?php

class download extends Controller{

    public function __construct()
    {
        parent::__construct("download");
        $this->login_verify();
    }

    public function index()
    {
        // block any attempt to explore the filesystem
        if (isset($_GET['file']) && basename($_GET['file']) == $_GET['file']) {
            $getfile = $_GET['file'];
        } else {
            $getfile = NULL;
        }


        if (!$getfile) {
            // go no further if filename not set
            session::set('download_error_code',0);
            $this->redirect('dropzone');
            exit();
        } else {
            // define the pathname to the file
            $filepath = UPLOAD_DIR . $getfile;
            // check that it exists and is readable
            if (file_exists($filepath) && is_readable($filepath)) {
                //get the file's size and send the appropriate headers
                $size = filesize($filepath);
                header('Content-Type: application/octet-stream');
                header('Content-Length: ' . $size);
                header('Content-Disposition: attachment; filename=' . $getfile);
                header('Content-Transfer-Encoding: binary');
                //open the file in binary read-only mode
                //suppress error messages if the file can't be opened
                $file = @fopen($filepath, 'rb');
                if ($file) {
                    //stream the file and exit the script when complete
                    fpassthru($file);
                    exit();
                } else {
                    session::set('download_error_code',0);
                    $this->redirect('dropzone');
                    exit();
                }
            } else {
                session::set('download_error_code',0);
                $this->redirect('dropzone');
                exit();
            }
        }


    }
}