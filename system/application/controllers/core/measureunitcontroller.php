<?php

/**
 * @package Controller
 * @subpackage MeasureUnitController
 */
class MeasureUnitController extends APP_Controller
{
    function MeasureUnitController ()
    {
        parent::APP_Controller ();
    }

    /**
     * 
     * Lista todas las unidades de medida 
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $measureUnitTable = Doctrine_Core::getTable ( 'MeasureUnit' );
        $measureUnit = $measureUnitTable->retrieveAll ($text_autocomplete);
        
        //Output 
        $json_data = $this->json->encode ( array ( 'total' => $measureUnit->count () , 'results' => $measureUnit->toArray () ) );
        echo $json_data;
    }

    /**
     * 
     * Crea una nueva unidad de medida
     * 
     * @param string measure_unit_name
     * @param string measure_unit_description
     * @method POST
     */
    function add ()
    {
        $measureUnit = new measureUnit();
        $measure_unit_name = $this->input->post ( 'measure_unit_name' );
        $measure_unit_description = $this->input->post ( 'measure_unit_description' );
        $measureUnit->measure_unit_name = $measure_unit_name;
        $measureUnit->measure_unit_description = $measure_unit_description;

        try
        {
            $measureUnit->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }

        //Output 
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * update
     * 
     * Modifica una unidad de medida
     * 
     * @post int measure_unit_id
     * @post string measure_unit_name
     * @post string measure_unit_description
     */
    function update ()
    {
        $measure_unit_id = $this->input->post ( 'measure_unit_id' );
        $measureUnit = Doctrine_Core::getTable ( 'MeasureUnit' )->retrieveById ( $measure_unit_id );
        $measure_unit_name = $this->input->post ( 'measure_unit_name' );
        $measure_unit_description = $this->input->post ( 'measure_unit_description' );
        $measureUnit->measure_unit_name = $measure_unit_name;
        $measureUnit->measure_unit_description = $measure_unit_description;

        try
        {
            $measureUnit->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }

        //Output 
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * 
     * Elimina una unidad de medida
     * 
     * @param integer measure_unit_id
     */
    function delete ()
    {
        $measure_unit_id = $this->input->post ( 'measure_unit_id' );
        $checkMeasureUnitInAsset = Doctrine::getTable ( 'MeasureUnit' )->checkMeasureUnitInAsset ( $measure_unit_id );

        if ( $checkMeasureUnitInAsset === false )
        {
            $measureUnit = Doctrine_Core::getTable ( 'MeasureUnit' )->retrieveById ( $measure_unit_id );
            
            if ( $measureUnit->delete () )
            {
                $success = 'true';

                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $success = 'false';
                $msg = 'Error';
            }
        }
        else
        {
            $success = 'false';
            $msg = $this->translateTag ( 'General' , 'type_measurement_associated_assets' );
        }

        //Output 
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

}