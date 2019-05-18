<?php

class principal extends APP_Controller {

    function index() {
        
        switch ($this->auth->get_user_data('user_type')) {
            case 'P':
                $this->interface_provider();
                break;

            default:
                $this->interface_normal();
                break;
        }
        return;
    }

    public function interface_provider($id = null) {
        $data_session = $this->auth->get_user_data();
        $user = Doctrine_Core::getTable('User')->find($data_session['user_id']);
        $language = Doctrine_Core::getTable('LanguageTag')->findByLanguage($data_session['language_id']);

        $gui_files = array();
        $gui_cfgs = array();

        foreach (array('mtn', 'request') as $module) {
            $this->load->config('module/' . $module);
            $gui_files[$module] = $this->config->item($module . '_prov_gui_files');

            if ($this->config->item($module . '_gui_confgs')) {
                foreach ($this->config->item($module . '_gui_confgs') as $confg => $confg_value) {
                    $gui_cfgs[$confg] = $confg_value;
                }
            }
        }
        $data['session'] = $data_session;
        $data['language'] = $language;
        $data['user_modules'] = DoctrineObjectToArray($user->getUserModules()->toArray(), 'module_abbreviation');
        $data['user_actions'] = $user->getUserActions()->toArray();
        $data['gui_files'] = $gui_files;
        $data['gui_cfgs'] = $gui_cfgs;
        $this->load->view('gui/interface_provider', $data);
    }

    public function interface_normal($id = null) {
       
        $data_session = $this->auth->get_user_data();
        $user = Doctrine_Core::getTable('User')->find($data_session['user_id']);
        $language = Doctrine_Core::getTable('LanguageTag')->findByLanguage($data_session['language_id']);
        $gui_files = array();
        $gui_cfgs = array();

        foreach ($user->getUserModules() as $module) {
         
            $this->load->config('module/' . $module->module_abbreviation);
            $gui_files[$module->module_abbreviation] = $this->config->item($module->module_abbreviation . '_gui_files');

            if ($this->config->item($module->module_abbreviation . '_gui_confgs')) {
                foreach ($this->config->item($module->module_abbreviation . '_gui_confgs') as $confg => $confg_value) {
                    $gui_cfgs[$confg] = $confg_value;
                }
            }
        }
   
        $data['session'] = $data_session;
        $data['language'] = $language;
        $data['user_modules'] = DoctrineObjectToArray($user->getUserModules()->toArray(), 'module_abbreviation');
        $data['user_actions'] = $user->getUserActions()->toArray();
        $data['gui_files'] = $gui_files;
        $data['gui_cfgs'] = $gui_cfgs;
        
        $this->load->view('gui/interface' . $user->user_preference, $data);
    }

}
