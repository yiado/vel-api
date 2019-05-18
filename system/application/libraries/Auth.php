<?php

/**
 * Interface de conexión para realizar login de los usuarios en IGEO y en sistemas externos.
 * @package IGEOv3
 * @subpackage User
 * @author manuteko
 *
 */
class Auth {


        /**
         * Setea la información de la sesión del usuario
         * @param string $key
         * @param string $value
         */

        function set_user_data($key, $value){

            $CI = & get_instance();

            $CI->session->set_userdata($key, $value);

        }
		/**
		 * Retorna la información de la sesión del usuario
		 * @param string $key
		 * @return mix array
		 * @todo falta implementar el proceso
		 */

		function get_user_data($key = NULL){

			$CI = & get_instance();

			if (!is_null($key)) {
				//Implementar obtener el value del indice correspondiente de la clave en la data del usuario
				$data = $CI->session->userdata($key);
			} else {
				$data = $CI->session->alluserdata();
			}

			return $data;
		}

		function is_logged_in () {

			return $this->get_user_data('is_logged_in');

		}

		function session_start ( $user ) {

			$CI = & get_instance();

			$CI->session->set_userdata('is_logged_in',   true);
			$CI->session->set_userdata('user_id', 		 		$user->user_id);
			$CI->session->set_userdata('user_type', 	 		$user->user_type);
			$CI->session->set_userdata('language_id', 	 		$user->language_id);
                        $CI->session->set_userdata('user_username',  		$user->user_username);
			$CI->session->set_userdata('user_email',  		$user->user_email);
			$CI->session->set_userdata('user_tree_full', 		$user->user_tree_full);
			$CI->session->set_userdata('user_path', 		$user->user_path);
                        $CI->session->set_userdata('user_preference', 		$user->user_preference);
            
			if ($user->user_default_module) { 
				$module = Doctrine_Core::getTable('Module')->find($user->user_default_module);
				$CI->session->set_userdata('user_default_module', 	$module->module_abbreviation);
			}	
			

			//Setemos los accesos del usuario
			$userTable = Doctrine_Core::getTable('User');
			$user_actions = $userTable->getPermissionsActionsUser($user->user_id);
            $CI->session->set_userdata('user_actions', $user_actions);

			//Setemos los accesos publicos
			$moduleActionTable = Doctrine_Core::getTable('ModuleAction');
			$public_actions = $moduleActionTable->getPublicActions();
			$CI->session->set_userdata('public_actions', $public_actions);

		}

		function session_destroy () {

			$CI = & get_instance();
			$CI->session->destroy();

		}

}