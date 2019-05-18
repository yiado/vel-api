<?php

/** @package    Controller
 *  @subpackage InfraOtherDataOptionController
 */
class InfraOtherDataOptionController extends APP_Controller
{
    function InfraOtherDataOptionController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista las opciones de info
     * 
     * @post int infra_other_data_option_id
     */
    function get ()
    {
        $infra_other_data_attribute_id = $this->input->post ( 'infra_other_data_attribute_id' );
        $text_autocomplete = $this->input->post ( 'query' );
        $options = Doctrine_Core::getTable ( 'InfraOtherDataOption' )->retrieveByAttribute ( $infra_other_data_attribute_id, $text_autocomplete );

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
     * add
     * 
     * Agrega una nueva opción a información
     * 
     * @post int infra_other_data_attribute_id
     * 
     * @post int infra_other_data_option_name
     */
    function add ()
    {
        $InfraOtherDataOption = new InfraOtherDataOption();
        $InfraOtherDataOption[ 'infra_other_data_attribute_id' ] = $this->input->post ( 'infra_other_data_attribute_id' );
        $InfraOtherDataOption[ 'infra_other_data_option_name' ] = $this->input->post ( 'infra_other_data_option_name' );

        try
        {
            $InfraOtherDataOption->save ();
            $success = true;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'General' , 'record_added_successfully' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * update
     * 
     * Modifica una opci�n de info
     * 
     * @post int infra_other_data_option_id
     * @post int infra_other_data_attribute_id
     * @post int infra_other_data_option_name
     * 
     */
    function update ()
    {
        $InfraOtherDataOption = Doctrine_core::getTable ( 'InfraOtherDataOption' )->find ( $this->input->post ( 'infra_other_data_option_id' ) );
        $InfraOtherDataOption[ 'infra_other_data_attribute_id' ] = $this->input->post ( 'infra_other_data_attribute_id' );
        $InfraOtherDataOption[ 'infra_other_data_option_name' ] = $this->input->post ( 'infra_other_data_option_name' );

        try
        {
            $InfraOtherDataOption->save ();
            $success = true;
            
            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'General' , 'record_updated_successfully' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * delete
     * 
     * Elimina una opci�n de info
     * 
     * post int infra_other_data_option_id
     */
    function delete ()
    {
        $InfraOtherDataOption = Doctrine::getTable ( 'InfraOtherDataOption' )->find ( $this->input->post ( 'infra_other_data_option_id' ) );

        if ( $InfraOtherDataOption->delete () )
        {
            echo '{"success": true}';
        }
        else
        {
            echo '{"success": false}';
        }
    }
}