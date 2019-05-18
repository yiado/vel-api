<?php

/**
 * @package Controller
 * @subpackage AssetStatusController 
 */
class AssetStatusController extends APP_Controller
{
    function AssetStatusController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista todos los estados de los equipos 
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $assetStatusTable = Doctrine_Core::getTable ( 'AssetStatus' );
        $assetStatus = $assetStatusTable->retrieveAll ( $text_autocomplete );
        if ( $assetStatus->count () )
        {
            echo '({"total":"' . $assetStatus->count () . '", "results":' . $this->json->encode ( $assetStatus->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega un nuevo estado para los equipos
     * 
     * @post string asset_status_name
     * @post string asset_status_description 
     */
    function add ()
    {
        //Recibimos los parametros
        $asset_status_name = $this->input->post ( 'asset_status_name' );
        $asset_status_description = $this->input->post ( 'asset_status_description' );

        try
        {
            $assetStatus = new AssetStatus();
            $assetStatus->asset_status_name = $asset_status_name;
            $assetStatus->asset_status_description = $asset_status_description;
            $assetStatus->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
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
     * Modifica un estado de equipos
     * 
     * @post int asset_status_id  
     * @post string asset_status_name
     * @post string asset_status_description 
     */
    function update ()
    {
        try
        {
            $assetStatus = Doctrine_Core::getTable ( 'AssetStatus' )->find ( $this->input->post ( 'asset_status_id' ) );
            $assetStatus[ 'asset_status_name' ] = $this->input->post ( 'asset_status_name' );
            $assetStatus[ 'asset_status_description' ] = $this->input->post ( 'asset_status_description' );
            $assetStatus->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
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
     * Elimina Estado si no esta asociado a un Activo en caso contrario no elimina.
     * 
     * @post int asset_status_id
     */
    function delete ()
    {
        try
        {
            $asset_status_id = $this->input->post ( 'asset_status_id' );
            $assetStatusInAsset = Doctrine::getTable ( 'AssetStatus' )->assetStatusInAsset ( $asset_status_id );
            if ( $assetStatusInAsset === false )
            {
                $assetStatus = Doctrine::getTable ( 'AssetStatus' )->find ( $asset_status_id );
                if ( $assetStatus->delete () )
                {
                    $success = true;
                    $msg = $this->translateTag ( 'General' , 'operation_successful' );
                }
                else
                {
                    $success = false;
                    $msg = $this->translateTag ( 'General' , 'error' );
                }
            }
            else
            {
                $success = false;
                $msg = $this->translateTag ( 'Asset' , 'state_not_delete_associated' );
            }
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

}