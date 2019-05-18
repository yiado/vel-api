<?php

/**
 * @package Controller
 * @subpackage userController
 */
class UserController extends APP_Controller
{
    function UserController ()
    {
        parent :: APP_Controller ();
    }

    /**
     * Lista todos los usuarios del sistema
     * @param string $query (opcional)
     */
    function get ()
    {
        $user_name = $this->input->post ( 'user_name' );
        $user_email = $this->input->post ( 'user_email' );
        $user_username = $this->input->post ( 'user_username' );
        $start_date = $this->input->post ( 'start_date' );
        $end_date = $this->input->post ( 'end_date' );

        $filters = array (
            'user_id = ?' => $this->input->post ( 'user_id'
            ) , 'user_name LIKE ?' => ( ! empty ( $user_name ) ? '%' . $user_name . '%' : NULL) , 'user_email LIKE ?' => ( ! empty ( $user_email ) ? '%' . $user_email . '%' : NULL) , 'user_username LIKE ?' => ( ! empty ( $user_username ) ? $user_username . '%' : NULL) , 'user_expiration >= ?' => ( ! empty ( $start_date ) ? $start_date . ' 00:00:00' : NULL) , 'user_expiration <= ?' => ( ! empty ( $end_date ) ? $end_date . ' 23:59:59' : NULL) , 'ug.user_group_id = ?' => $this->input->post ( 'user_group_id' ) );

        $text_autocomplete = $this->input->post ( 'query' );
        $show_admin_user = $this->input->post ( 'show_admin_user' );
        $display_the_user_system = $this->input->post ( 'display_the_user_system' );

        $users = Doctrine_Core :: getTable ( 'User' )->retrieveAll ( $text_autocomplete , ( ! empty ( $show_admin_user ) ? true : false ) , ( ! empty ( $display_the_user_system ) ? true : false ) , $filters );

        echo '({"total":"' . $users->count () . '", "results":' . $this->json->encode ( $users->toArray () ) . '})';
    }
    
 /**
     * Lista todos los usuarios del sistema con email
     * @param string $query (opcional)
     */
    function getNotification ()
    {
        $user_name = $this->input->post ( 'user_name' );
        $user_email = $this->input->post ( 'user_email' );
        $user_username = $this->input->post ( 'user_username' );
        $start_date = $this->input->post ( 'start_date' );
        $end_date = $this->input->post ( 'end_date' );

        $filters = array (
            'user_id = ?' => $this->input->post ( 'user_id'
            ) , 'user_name LIKE ?' => ( ! empty ( $user_name ) ? $user_name . '%' : NULL) , 'user_email LIKE ?' => ( ! empty ( $user_email ) ? $user_email . '%' : NULL) , 'user_username LIKE ?' => ( ! empty ( $user_username ) ? $user_username . '%' : NULL) , 'user_expiration >= ?' => ( ! empty ( $start_date ) ? $start_date . ' 00:00:00' : NULL) , 'user_expiration <= ?' => ( ! empty ( $end_date ) ? $end_date . ' 23:59:59' : NULL) , 'ug.user_group_id = ?' => $this->input->post ( 'user_group_id' ) );

        $text_autocomplete = $this->input->post ( 'query' );
        $show_admin_user = $this->input->post ( 'show_admin_user' );
        $display_the_user_system = $this->input->post ( 'display_the_user_system' );

        $users = Doctrine_Core :: getTable ( 'User' )->retrieveAllNotification ( $text_autocomplete , ( ! empty ( $show_admin_user ) ? true : false ) , ( ! empty ( $display_the_user_system ) ? true : false ) , $filters );

        echo '({"total":"' . $users->count () . '", "results":' . $this->json->encode ( $users->toArray () ) . '})';
    }

    /**
     * Lista todos los usuarios del sistema incluso el usuario system
     */
    function getAll ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $users = Doctrine_Core :: getTable ( 'User' )->retrieveAllFull ($text_autocomplete);
        echo '({"total":"' . $users->count () . '", "results":' . $this->json->encode ( $users->toArray () ) . '})';
    }
    
    function export() {
        
        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');
               
        $text_autocomplete = null;
        $users = Doctrine_Core :: getTable ( 'User' )->retrieveAllFull ($text_autocomplete);

        $sheet->setCellValue('A1', $this->translateTag('Core', 'username'))
                ->setCellValue('B1', $this->translateTag('Core', 'english_username'))
                ->setCellValue('C1', $this->translateTag('Core', 'email'))
                ->setCellValue('D1', $this->translateTag('General', 'user_type'))
                ->setCellValue('E1', $this->translateTag('Core', 'groups'))
                ->setCellValue('F1', $this->translateTag('Core', 'full_access'))
                ->setCellValue('G1', $this->translateTag('General', 'expiration_date'))
                ->setCellValue('H1', $this->translateTag('General', 'state'));

        $rcount = 1;
        foreach ($users as $user) 
        {
            if ($user->user_tree_full == 0){
                $user_tree_full = "NO";
                
            } else {
                $user_tree_full = "SI";
            }
            
            if ($user->user_status == 0){
                $user_status = "ACTIVO";
                
            } else {
                $user_status = "INACTIVO";
            }
            
            if ($user->user_expiration){
               $date = date_create($user->user_expiration);
               $date = date_format($date, 'd/m/Y '); 
            } else {
               $date = "";
            }
            
            
            $user_group_name_grupos = "";
            $user_id = Doctrine_Core :: getTable ( 'User' )->retrieveByID ($user->user_id);
                    
            if($user_id){
                foreach ($user_id as $use) 
                {
                    $user_g= Doctrine_Core :: getTable ( 'UserGroup' )->retrieveById ($use->user_group_id);
                    if ($user_g){
                        $user_group_name = $user_g->user_group_name;
                        $user_group_name_grupos =  $user_group_name . ", " . $user_group_name_grupos;
                    }
                }
                
            } 
            
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $user->user_name)
                    ->setCellValueExplicit('B' . $rcount, $user->user_username)
                    ->setCellValueExplicit('C' . $rcount, $user->user_email)
                    ->setCellValueExplicit('D' . $rcount, $user->user_type_name)
                    ->setCellValueExplicit('E' . $rcount, $user_group_name_grupos)
                    ->setCellValueExplicit('F' . $rcount, $user_tree_full)
                    ->setCellValueExplicit('G' . $rcount, $date)
                    ->setCellValueExplicit('H' . $rcount, $user_status);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        $sheet->getStyle('A1:H1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:H1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:H' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));

        echo '{"success": true, "file": "' . $this->input->post('file_name') . '.xls"}';

    }

    /**
     * add
     *
     * Agrega un nuevo usuario al sistema
     *
     * @post string user_name
     * @post string user_username
     * @post string user_password
     * @post string user_email
     * @post string user_status
     */
    function add ()
    {
        $this->load->helper ( 'string' );
        $user = new User();
        $user->user_name = $this->input->post ( 'user_name' );
        $user->user_username = $this->input->post ( 'user_username' );
        $user->user_preference = 1;
        $user_username = $this->input->post ( 'user_username' );
        $user_passwd = $this->input->post ( 'user_password' );
        $user_type = $this->input->post ( 'user_type' );
        $user_tree_full = $this->input->post ( 'user_tree_full' );
        $user_provider = $this->input->post ( 'user_provider' );
        $user->user_tree_full = ($user_tree_full == 'true' ? '1' : '0');

        if ( $user_provider == 'true' )
        {
            $user->user_type = 'P';
        }
        else
        {
            $user->user_type = ($user_type == 'true' ? 'A' : 'N');
        }

        //Si el user_passwd es vacio, se setea una passwd por defecto
        $clean_user_passwd = (empty ( $user_passwd ) ? random_string ( 'alnum' , 8 ) : $user_passwd);
        $user->user_password = md5 ( $clean_user_passwd );

        $user->user_email = $this->input->post ( 'user_email' );

        $user_expiration = $this->input->post ( 'user_expiration' );
        $user->user_expiration = (empty ( $user_expiration ) ? NULL : $user_expiration);

        //Trae el lenguaje por defecto
        $dataDefaultLanguage = Doctrine_Core :: getTable ( 'Language' )->defaultLanguage ();
        $user->language_id = $dataDefaultLanguage->language_id;

        //Validar si un usiario existe en el sistema
        $check = Doctrine_Core :: getTable ( 'User' )->checkUser ( $user_username );

        if ( ! empty ( $check ) )
        {

            $success = false;
            $msg = $this->translateTag ( 'General' , 'the_user_name_already_exists_in_the_database' );
            $user_id = '';
        }
        else
        {
            try
            {
                $user->save ();
                $user_id = $user->user_id;
                $user_type = $user->user_type;
                $success = true;
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            catch ( Exception $e )
            {
                $success = false;
                $user_id = NULL;
                $clean_user_passwd = NULL;
                $msg = $e->getMessage ();
            }
        }

        $json_data = $this->json->encode ( array (
                    'success' => $success ,
                    'msg' => $msg ,
                    'password' => $clean_user_passwd ,
                    'user_id' => $user_id ,
                    'user_type' => $user_type
                ) );
        echo $json_data;
    }

    /**
     * update
     *
     * Modifica los datos de un usuario del sistema
     *
     * @param int user_id
     * @post string user_name
     * @post string user_username
     * @post string user_password
     * @post string user_email
     * @post int user_status
     */
    function update ()
    {
        $user = Doctrine_Core :: getTable ( 'User' )->find ( $this->input->post ( 'user_id' ) );
        $user->user_name = $this->input->post ( 'user_name' );
        $user->user_username = $this->input->post ( 'user_username' );
        $user_password = $this->input->post ( 'user_password' );
        $user_type = $this->input->post ( 'user_type' );
        $user_tree_full = $this->input->post ( 'user_tree_full' );
        $user->user_tree_full = ($user_tree_full == 'true' ? '1' : '0');
        $user->user_type = ($user_type == 'true' ? 'A' : 'N');

        if ( ! empty ( $user_password ) )
        {
            $user->user_password = md5 ( $user_password );
        }
        $user_expiration = $this->input->post ( 'user_expiration' );
        $user->user_expiration = (empty ( $user_expiration ) ? NULL : $user_expiration);
        $user->user_email = $this->input->post ( 'user_email' );

        try
        {
            $user->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array (
                    'success' => $success ,
                    'msg' => $msg
                ) );
        echo $json_data;
    }

    /**
     * status
     *
     * Habilita y Deshabilita usuarios dependiendo del parametro ingresado 0 Habilita 1 Deshabilita.
     *
     * @param int user_id
     */
    function status ()
    {
        $user_id = $this->input->post ( 'user_id' );
        $user_status = $this->input->post ( 'user_status' );
        $user = Doctrine :: getTable ( 'User' )->find ( $user_id );
        $user->user_status = $user_status;

        try
        {
            $user->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array (
                    'success' => $success ,
                    'msg' => $msg
                ) );
        echo $json_data;
    }

    /**
     * Agrega grupos al usuario
     * @param integer $user_id
     * @param string $groups_to_user
     */
    function addGroup ()
    {
        $user_id = $this->input->post ( 'user_id' );
        $groups_to_user = explode ( ',' , $this->input->post ( 'groups_to_user' ) );

        try
        {
            //Obtenemos la conexión actual
            $conn = Doctrine_Manager :: getInstance ()->getCurrentConnection ();

            //Iniciar transacción
            $conn->beginTransaction ();

            //Eliminamos la config actual
            Doctrine_Core :: getTable ( 'UserGroupUser' )->deleteCurrentGroupsUser ( $user_id );

            //Insert de los usuarios al grupo
            if ( ! empty ( $groups_to_user[ 0 ] ) )
            {

                foreach ( $groups_to_user as $user_group_id )
                {
                    $userGroupUser = new UserGroupUser();
                    $userGroupUser->user_group_id = $user_group_id;
                    $userGroupUser->user_id = $user_id;
                    $userGroupUser->save ();
                }
            }

            //Commit de la transacción
            $conn->commit ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            //Rollback de la transacción
            $conn->rollback ();
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array (
                    'success' => $success ,
                    'msg' => $msg
                ) );
        echo $json_data;
    }

    /**
     * Retorna los grupos de usuario
     * @param integer $user_id
     */
    function groups ()
    {
        $user_id = $this->input->post ( 'user_id' );
        $userGroupUser = Doctrine_Core :: getTable ( 'UserGroupUser' )->retrieveGroupsByUserId ( $user_id );
        $json_data = $this->json->encode ( array (
                    'total' => $userGroupUser->count () , 'results' => $userGroupUser->toArray () ) );
        echo $json_data;
    }

    /**
     * Retorna los usuarios que no están asociados al grupo especicado por parametro.
     * @param integer $user_group_id
     */
    function groupOutsideUser ()
    {
        $user_id = $this->input->post ( 'user_id' );
        $userGroupUser = Doctrine_Core :: getTable ( 'UserGroupUser' )->retrieveGroupOutsideUsers ( $user_id );
        $json_data = $this->json->encode ( array (
                    'total' => $userGroupUser->count () , 'results' => $userGroupUser->toArray () ) );
        echo $json_data;
    }

    /**
     * preferences
     *
     * Modifica los datos de un usuario del sistema
     *
     * @param int user_id
     * @post string user_name
     * @post string user_email
     * @post string user_password
     * @post string pass
     * @post string pass_cfrm
     */
    function preferences ()
    {
        //Obtenemos los datos de la sesión del usuario.
        $data_session = $this->auth->get_user_data ();
        $user_username = $data_session[ 'user_username' ];
        $user_password = $this->input->post ( 'user_password' );
        $new_password = trim ( $this->input->post ( 'new_password' ) );
        $new_password_cfrm = trim ( $this->input->post ( 'new_password_cfrm' ) );
        $user_preference = $this->input->post ( 'user_preference' );
         

        try
        {
            //Actualizar los datos personales
            $user = Doctrine_Core :: getTable ( 'User' )->find ( $data_session[ 'user_id' ] );
            $user_prefernce_entes = $user->user_preference;
            $user->user_name = $this->input->post ( 'user_name' );
            $user->user_email = $this->input->post ( 'user_email' );
            $user->language_id = $this->input->post ( 'language_id' );
            $user->user_default_module = $this->input->post ( 'user_default_module' );
            $user->user_preference = $this->input->post ( 'user_preference' );
            
            if ($user_prefernce_entes != $user_preference){
                $preference = true;
                
            } else {
                $preference = false;
            }

            if ( ! empty ( $new_password ) && ! empty ( $new_password_cfrm ) )
            {

                //	Comparar la coincidencia de la contraseña actual con la ingresada por el usuario
                $credendital_user = Doctrine_Core :: getTable ( 'User' )->validate ( $user_username , $user_password );

                if ( ! empty ( $credendital_user->user_username ) )
                {

                    // Comparar la coincidencia de la contraseña nueva con la confirmada
                    if ( $new_password == $new_password_cfrm )
                    {
                        $user->user_password = md5 ( $new_password );
                    }
                    else
                    {
                        throw new Exception ( $this->translateTag ( 'General' , 'password_does_not_match_its_confirmation' ) );
                    }
                }
                else
                {
                    throw new Exception ( $this->translateTag ( 'General' , 'incorrect_password' ) );
                }
            }

            $user->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array (
                    'success' => $success ,
                    'msg' => $msg,
                    'preference' => $preference
                ) );
        echo $json_data;
    }
    
    function setDefaultView () {
    	
    	$data_session = $this->auth->get_user_data ();
    	
	    $user = Doctrine_Core :: getTable ( 'User' )->find ( $data_session[ 'user_id' ] );
	    $user->user_preference = 1;
    	$user->save ();
    	
    	redirect(site_url());
    	
    }

    function getModules ()
    {
        $data_session = $this->auth->get_user_data ();
        $user = Doctrine_Core::getTable ( 'User' )->find ( $data_session[ 'user_id' ] );
        $modules = $user->getUserModules ();

        if ( $modules->count () )
        {
            echo '({"total":"' . $modules->count () . '", "results":' . $this->json->encode ( $modules->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * Verifica el acceso de un usuario al node
     * @param integer $node_id
     * @return JSON
     */
    function checkAccessNode ()
    {
        $data_session = $this->auth->get_user_data ();
        $node_id = $this->input->post ( 'node_id' );

        if ( ($this->auth->get_user_data ( 'user_type' ) != 'A' && $this->auth->get_user_data ( 'user_tree_full' ) != 1) && $node_id != 'root' )
        {
            $checkAccessNode = Doctrine_Core :: getTable ( 'User' )->checkAccessNode ( $data_session[ 'user_id' ] , $node_id );
        }
        else
        {
            $checkAccessNode = true;
        }

        $json_data = $this->json->encode ( array (
                    'success' => $checkAccessNode
                ) );
        echo $json_data;
    }

}