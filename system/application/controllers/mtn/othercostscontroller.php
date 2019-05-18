<?php

/** @package    Controller
 *  @subpackage Othercostscontroller
 */
class Othercostscontroller extends APP_Controller
{
    function Othercostscontroller ()
    {
        parent::APP_Controller ();
    }

    /*
     * Devuelve todos los otros costos (maestro)
     */

    function get ()
    {
        $otherCostsTable = Doctrine_Core::getTable ( 'MtnOtherCosts' );
        $text_autocomplete = $this->input->post ( 'query' );
        $maintainer_type=1; //asset
        $otherCosts = $otherCostsTable->retrieveAll ( $text_autocomplete ,$maintainer_type );

        if ( $otherCosts->count () )
        {
            echo '({"total":"' . $otherCosts->count () . '", "results":' . $this->json->encode ( $otherCosts->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    

    function getByNode ()
    {
        $otherCostsTable = Doctrine_Core::getTable ( 'MtnOtherCosts' );
        $text_autocomplete = $this->input->post ( 'query' );
        $maintainer_type=2;//node
        $filters = array();
        $otherCosts = $otherCostsTable->retrieveAll ( $text_autocomplete,$filters,$maintainer_type );

        if ( $otherCosts->count () )
        {
            echo '({"total":"' . $otherCosts->count () . '", "results":' . $this->json->encode ( $otherCosts->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * Agrega un nuevo tipo de Orden de Trabajo
     * @post string mtn_other_costs_name
     */
    function add ()
    {
        //Recibimos los Parametros
        $mtn_other_costs_name = $this->input->post ( 'mtn_other_costs_name' );

        try
        {
            $mtnWorkOrderType = new MtnOtherCosts();
            $mtnWorkOrderType->mtn_other_costs_name = $mtn_other_costs_name;
            $mtnWorkOrderType->mtn_maintainer_type_id = 1;
            $mtnWorkOrderType->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $mtn_work_order_task_id = NULL;
            $success = 'false';
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
    function addByNode ()
    {
        //Recibimos los Parametros
        $mtn_other_costs_name = $this->input->post ( 'mtn_other_costs_name' );

        try
        {
            $mtnWorkOrderType = new MtnOtherCosts();
            $mtnWorkOrderType->mtn_other_costs_name = $mtn_other_costs_name;
            $mtnWorkOrderType->mtn_maintainer_type_id = 2;
            $mtnWorkOrderType->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $mtn_work_order_task_id = NULL;
            $success = 'false';
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * update
     * Actualiza Otros Costos
     * @post string mtn_other_costs_name
     */
    function update ()
    {
        $mtnOtherCosts = Doctrine_Core::getTable ( 'MtnOtherCosts' )->find ( $this->input->post ( 'mtn_other_costs_id' ) );
        $mtnOtherCosts[ 'mtn_other_costs_name' ] = $this->input->post ( 'mtn_other_costs_name' );
        $mtnOtherCosts->save ();
        echo '{"success": true}';
    }

    /**
     * delete
     * Elimina un otros costos  si no esta asociado al componente
     * @param integer mtn_other_costs_id
     */
    function delete ()
    {
        $mtn_other_costs_id = $this->input->post ( 'mtn_other_costs_id' );
        $OtherCosts = Doctrine::getTable ( 'MtnOtherCosts' )->checkDataInOtherCosts ( $mtn_other_costs_id );
        if ( $OtherCosts === false )
        {
            $MtnOtherCosts = Doctrine::getTable ( 'MtnOtherCosts' )->find ( $mtn_other_costs_id );
            if ( $MtnOtherCosts->delete () )
            {
                $exito = true;
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $exito = false;
                $msg = "Error";
            }
        }
        else
        {
            $exito = false;
            $msg = $this->translateTag ( 'Maintenance' , 'you_can_not_delete_because_it_is_associated' );
        }
        $json_data = $this->json->encode ( array ( 'success' => $exito , 'msg' => $msg ) );
        echo $json_data;
    }
}