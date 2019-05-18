<?php

/**
 * @package    Controller
 * @subpackage  BrandController
 */
class BrandController extends APP_Controller
{
    function BrandController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista todas las marcas existentes
     */
    function get ()
    {
        $brandTable = Doctrine_Core::getTable ( 'Brand' );
        $text_autocomplete = $this->input->post ( 'query' );
        $brand = $brandTable->retrieveAll ( $text_autocomplete );

        if ( $brand->count () )
        {
            echo '({"total":"' . $brand->count () . '", "results":' . $this->json->encode ( $brand->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega nueva Marca
     * 
     * @post string $brand_name
     */
    function add ()
    {
        //Recibimos los parametros
        $brand_name = $this->input->post ( 'brand_name' );

        try
        {
            $brand = new Brand();
            $brand->brand_name = $brand_name;
            $brand->save ();
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * update
     * 
     * Modifica la Marca
     * 
     * @post int brand_id  
     * @post string brand_name
     */
    function update ()
    {
        $brand = Doctrine_Core::getTable ( 'Brand' )->find ( $this->input->post ( 'brand_id' ) );
        $brand[ 'brand_name' ] = $this->input->post ( 'brand_name' );
        $brand->save ();
        echo '{"success": true}';
    }

    /**
     * delete
     * 
     * Elimina la Marca del Activo si no esta asociado a un Activo en caso contrario no elimina.
     * 
     * @post int brand_id
     */
    function delete ()
    {
        $brand_id = $this->input->post ( 'brand_id' );
        $brandIsFK = Doctrine::getTable ( 'Brand' )->brandIsFK ( $brand_id );
        if ( $brandIsFK === false )
        {
            $brand = Doctrine::getTable ( 'Brand' )->find ( $brand_id );
            if ( $brand->delete () )
            {
                $exito = true;
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $exito = false;
                $msg = $this->translateTag ( 'General' , 'error' );
            }
        }
        else
        {
            $exito = false;
            $msg = $this->translateTag ( 'Asset' , 'not_delete_mark_for_associated_an_active' );
        }

        $json_data = $this->json->encode ( array ( 'success' => $exito , 'msg' => $msg ) );

        echo $json_data;
    }

}