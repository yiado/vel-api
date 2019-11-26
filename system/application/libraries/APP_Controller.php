<?php

/*
 * APP_Controller
 */
class APP_Controller extends Controller {

    function APP_Controller() {
        parent::Controller();

        if ($this->config->item('auth_require_login') && !$this->auth->is_logged_in()) {
            if (substr_count($this->uri->uri_string(), 'core/auth') != 1 && substr_count($this->uri->uri_string(), 'cron') != 1) {
                redirect('core/auth');
            }
        }

        $this->loadModuleConfigs();
    }

    private function loadModuleConfigs() {
        $this->load->helper('directory');

        $map = directory_map('./system/application/config/module/');

        foreach ($map as $module) {
            $this->load->config('module/' . $module);
        }
    }

    function translateTag($module, $tag, $language_id = NULL) {
        if (is_null($language_id)) {

            $data_session = $this->auth->get_user_data();
            $language_id = $data_session['language_id'];
            $language_id = 1;
        }
        return $this->language->translate($language_id, $module, $tag);
    }

}
