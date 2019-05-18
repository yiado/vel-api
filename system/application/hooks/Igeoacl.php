<?php
/**
 * Clase para adminsitrar las ACL
 * @author manuteko
 *
 */
class IgeoAcl {

	function isAllowedAccess()
    {

        $CI =& get_instance();
        
	return true;
        
        if (substr_count($CI->uri->uri_string(), 'cron') != 1) {

	        //Obtenemos la URI del request
	        $module = $CI->uri->segment(1);
	        $controller = $CI->uri->segment(2);
	        $action = $CI->uri->segment(3);
	
	        //Por defecto, siempre tiene acceso el usuario
	        $isAllowed = true;
	
	        //Datos de la sesión
	        $data_session = $CI->auth->get_user_data();
	
	        //Verificamos si el user está logeadola, de lo contrario redireccionamos al login
	        if( $CI->auth->is_logged_in() ) {
	
		        //Tipo de usuario
	            $user_type = $data_session['user_type'];
	            //Si no es un usuario del tipo A (administrador) se validan sus permisos
		        if ($user_type != 'A') {
	
	                //Si no es una url publica se continua la validación
	                $public_actions = $data_session['public_actions'];
	
	                if (empty($public_actions[$module][$controller][$action])) {
	
	                    //Accesos del usuario
	                    $user_actions = $data_session['user_actions'];
	
	                    if (empty($user_actions[$module][$controller][$action])) {
	
	                    	$isAllowed = false;
	
	                    }
	
	                }
		        }
		    }
		    else { //No está logeado
	
		    	$isAllowed = false;
	
		    }
	
		   //Redirecciona al /
		   if ( ($isAllowed === false && $module != 'core' && $controller != 'auth' && !in_array($action, array('login', 'logout'))) && $CI->uri->total_segments() > 0 ) {
	
		   	    $uri = $CI->uri->uri_string();
		    	//Registramos el log
		    	$CI->syslog->register('acl', array($data_session['user_username'], $uri));
		    	//redireccionar al login
	            redirect();
	
		    }
		    
        }
        
    }
}
?>