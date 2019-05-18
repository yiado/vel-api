<?php

/**
 * @package    Controller
 * @subpackage infraInfoOptionController
 */
class infraInfoOptionController extends APP_Controller
{
    function infraInfoOptionController ()
    {
        parent::APP_Controller ();
    }

    /** get
     *
     * obtiene las opciones respecto a una option
     *
     * @post int type
     *
     */
    function get ()
    {
        $infra_info_option_parent_id = $this->input->post ( 'infra_info_option_parent_id' );
        $query = $this->input->post ( 'query' );
        
        $options = Doctrine_Core::getTable ( 'InfraInfoOption' )->findByParent ( $infra_info_option_parent_id, $query );

        if ( $options->count () )
        {
            echo '({"total":"' . $options->count () . '", "results":' . $this->json->encode ( $options->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * Agrega una opción a los campos de selección
     */
    function add ()
    {
        $infra_info_option_name = $this->input->post ( 'infra_info_option_name' );
        $infra_info_option_parent_id = $this->input->post ( 'infra_info_option_parent_id' );

        try
        {
            $infraInfoOption = new InfraInfoOption();
            $infraInfoOption->infra_info_option_name = $infra_info_option_name;
            $infraInfoOption->infra_info_option_parent_id = $infra_info_option_parent_id;
            $infraInfoOption->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'Infrastructure' , 'option_selection_recorded_successfully' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg , 'infra_info_option_parent_id' => $infra_info_option_parent_id ) );
        echo $json_data;
    }
    
    function update ()
    {
        $infra_info_option_id = $this->input->post ( 'infra_info_option_id' );
        $infraInfoOption = Doctrine::getTable ( 'InfraInfoOption' )->find ( $infra_info_option_id );
        $infraInfoOption->fromArray($this->input->postall());
        

        try
        {
            $infraInfoOption->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }
        
        //Output
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * Elimina un campo de opción
     * @param integer $infra_info_option_id
     */
    function delete ()
    {
        $infra_info_option_id = $this->input->post ( 'infra_info_option_id' );
        $infraInfoOption = Doctrine::getTable ( 'InfraInfoOption' )->retrieveById ( $infra_info_option_id );

        if ( $infraInfoOption->delete () )
        {
            $success = 'true';
            $msg = $this->translateTag ( 'Infrastructure' , 'option_deleted_successfully' );
        }
        else
        {
            $msg = $this->translateTag ( 'Infrastructure' , 'problems_eliminating_option' );
            $success = 'false';
        }
        
        //Output
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
}