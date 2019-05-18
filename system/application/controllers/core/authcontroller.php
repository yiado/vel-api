<?php

/**
 * @package Controller
 * @subpackage AuthController
 */
class AuthController extends APP_Controller {

    function AuthController() {
        parent :: APP_Controller();
    }

    /**
     * index
     *
     * Carga la vista de login
     */
    function index() {
        $dataDefaultLanguage = Doctrine_Core :: getTable('Language')->defaultLanguage();
        $language_id = $dataDefaultLanguage->language_id;
        $language = Doctrine_Core::getTable('LanguageTag')->findByLanguage($language_id, array('General', 'Core'));
        $data['language'] = $language;
        $this->load->view('auth/auth/login', $data);
    }

    /**
     * autentication
     *
     * Captura datos de ingreso al sistema del usuario y los envía al método validate
     *
     * @post string user_username
     * @post string user_password
     */
    function autentication() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $auth_engine_config = $this->config->item('auth_engine');
        $this->load->library($auth_engine_config);

        //Trae el lenguaje por defecto
        $dataDefaultLanguage = Doctrine_Core :: getTable('Language')->defaultLanguage();
        $language_id = $dataDefaultLanguage->language_id;

        //Validar Usuario y Password
        $validation = $this->$auth_engine_config->isValid($username, $password);
        if ($validation === TRUE) {
            $success = true;
            $user_data = $this->$auth_engine_config->getUser();
            $user_type = $user_data->user_type;
            $user_expiration = $user_data->user_expiration;
            $user_status = $user_data->user_status;

            //Fecha de expiración del usuario
            $user_expiration = strtotime($user_expiration);
            //Fecha del día de hoy
            $now = strtotime(date('Y-m-d'));

            //Validar si expiro la fecha del usuario
            if ($user_expiration < $now && $user_expiration > 0) {
                $success = false;
                $msg = $this->translateTag('General', 'its_validity_expired_user', $language_id);
            }

            //Validar si es un usuario inhabilitado
            if ($user_status == 1) {
                $success = false;
                $msg = $this->translateTag('General', 'disabled_users', $language_id);
            }
        } else {
            $success = false;
            if ($validation === FALSE) {
                $msg = $this->translateTag('General', 'invalid_authentication_data', $language_id);
            } else {
                $msg = $validation;
            }
        }

        if ($success === true) {
            $this->auth->session_start($this->$auth_engine_config->getUser());
            $data_to_json = array(
                'success' => true,
                'base_url' => base_url(), 'user_type' => $user_type);

            $this->syslog->register('auth', array(
                $this->auth->get_user_data('user_name'
        ))); // registering auth log
        } else {

            $data_to_json = array(
                'success' => false,
                'message' => $msg
            );
        }

        $json_data = $this->json->encode($data_to_json);
        echo $json_data;
    }

    /*
     * Genera el archivo XML
     */

    function permissions() {

        if ($this->auth->get_user_data('user_tree_full') == 1) {

            $json_data = $this->json->encode(array(
                'success' => true,
                'msg' => 'XML creado'
                    ));
            echo $json_data;

            return;
        }

        $this->load->helper('file');

        //Ruta y nombre del archivo
        $user_id = $this->auth->get_user_data('user_id');
        $file_name = $user_id . '.xml';
        $xml_name = $this->app->getTempFileDir('treexml/' . $file_name);

        if (!read_file($xml_name)) {
            $aux = array();
            $path_to_icons = $this->config->item('node_icon_url');
            $doc = new DOMDocument('1.0', 'ISO-8859-1');
            $doc->formatOutput = true;

            $root_node = $doc->createElement("nodes");
            $doc->appendChild($root_node);

            $treeObject = Doctrine_Core :: getTable('Node')->getTree();

            $q = Doctrine_Query :: create()
                    ->select('n.node_name, n.node_id, n.node_type_id')
                    ->from('Node n')
                    ->innerJoin('n.UserGroupNode ugn')
                    ->innerJoin('ugn.UserGroup ug')
                    ->innerJoin('ug.UserGroupUser ugu')
                    ->where('ugu.user_id = ?', $user_id);

            foreach ($treeObject->fetchRoots() as $root) {
                $treeObject->setBaseQuery($q);
                $tree = $treeObject->fetchBranch($root->node_id);
                $treeObject->resetBaseQuery();
                foreach ($tree as $node) {
                    $parent = $root_node;
                    $ancestors = $node->getNode()->getAncestors();
                    if ($ancestors) {
                        foreach ($ancestors as $ancestor) {
                            if (array_key_exists($ancestor->node_id, $aux)) {
                                $parent = $aux[$ancestor->node_id];
                            } else {
                                $n = $doc->createElement("node");
                                $a = $doc->createAttribute('name');
                                $a->appendChild($doc->createTextNode($ancestor->node_name));
                                $n->appendChild($a);
                                $i = $doc->createAttribute('id');
                                $i->appendChild($doc->createTextNode($ancestor->node_id));
                                $n->appendChild($i);

                                //Se agrega el attr del icono para el nodo
                                $icon = $doc->createAttribute('icon');
                                $icon_with_path = $path_to_icons . $ancestor->node_type_id . '.gif';
                                $icon->appendChild($doc->createTextNode($icon_with_path));
                                $n->appendChild($icon);
                                $aux[$ancestor->node_id] = $n;
                                $parent->appendChild($n);
                                $parent = $n;
                            }
                        }
                    }
                    
                    
                    $n = $doc->createElement("node");
                    $a = $doc->createAttribute('name');
                    $a->appendChild($doc->createTextNode($node->node_name));
                    $n->appendChild($a);
                    $i = $doc->createAttribute('id');
                    $i->appendChild($doc->createTextNode($node->node_id));
                    $n->appendChild($i);

                    //Se agrega el attr del icono para el nodo
                    $icon = $doc->createAttribute('icon');
                    $icon_with_path = $path_to_icons . $node->node_type_id . '.gif';
                    $icon->appendChild($doc->createTextNode($icon_with_path));
                    $n->appendChild($icon);
                    $aux[$node->node_id] = $n;
                    $parent->appendChild($n);
                }
            }
            try {
                $doc->save($xml_name);
                $this->auth->set_user_data('xml_permissions_file', $this->app->getTempFilePath('treexml/' . $file_name));
                $success = true;
                $msg = 'XML creado';
            } catch (Exception $e) {
                $success = false;
                $msg = $e->getMessage();
            }
        } else {
            $this->auth->set_user_data('xml_permissions_file', $this->app->getTempFilePath('treexml/' . $file_name));
            $success = true;
            $msg = 'XML creado';
        }
        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));

        echo $json_data;
    }

    /**
     * logout
     *
     * Cierra sesión
     */
    function logout() {
        $this->auth->session_destroy();
        redirect();
    }

    /**
     * isLoggedIn
     *
     * Verifica que el usuario está logeado o tiene iniciada la sesión
     * @return JSON (true|false, date)
     */
    function isLoggedIn() {
        if ($this->auth->is_logged_in()) {
            $success = true;
        } else {
            $success = false;
        }
        $json_data = $this->json->encode(array(
            'success' => $success,
            'system_server_date' => date('Y-m-d'
            )));
        echo $json_data;
    }

}
