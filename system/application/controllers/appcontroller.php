<?php

class AppController extends APP_Controller {

    function AppController() {
        parent::APP_Controller();
    }

    function download($file) {
        $this->load->helper('download');
        $data = file_get_contents($this->app->getTempFileDir($file)); // Read the file's contents
        force_download($file, $data);
    }

    function downloaddir($path) {
        $aux = explode('~', $path);
        $this->load->helper('download');
        $data = file_get_contents(implode('/', $aux)); // Read the file's contents
        force_download(end($aux), $data);
    }

}
