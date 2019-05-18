<?php

/**
 * @package    Controller
 * @subpackage AssetTypeController
 */
class AssetTypeController extends APP_Controller
{
    function AssetTypeController ()
    {

        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista todos los tipos de equipos existentes
     */
    function get ()
    {
        $assetTypeTable = Doctrine_Core::getTable ( 'AssetType' );
        $text_autocomplete = $this->input->post ( 'query' );
        $assetType = $assetTypeTable->retrieveAll ( $text_autocomplete );

        if ( $assetType->count () )
        {
            echo '({"total":"' . $assetType->count () . '", "results":' . $this->json->encode ( $assetType->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega nuevo tipo de equipo
     * 
     * @post string $asset_type_name
     */
    function add ()
    {
        //Recibimos los parametros
        $asset_type_name = $this->input->post ( 'asset_type_name' );
        try
        {
            $assetType = new AssetType();
            $assetType->asset_type_name = $asset_type_name;
            $assetType->save ();
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
     * Modifica tipo de equipo
     * 
     * @post int asset_type_id  
     * @post string asset_type_name
     */
    function update ()
    {
        try
        {
            $assetType = Doctrine_Core::getTable ( 'AssetType' )->find ( $this->input->post ( 'asset_type_id' ) );
            $assetType[ 'asset_type_name' ] = $this->input->post ( 'asset_type_name' );
            $assetType->save ();
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
     * Elimina Tipo de Activo si no esta asociado a un Activo en caso contrario no elimina.
     * 
     * @post int asset_type_id
     */
    function delete ()
    {
        try
        {
            $asset_type_id = $this->input->post ( 'asset_type_id' );
            $assetTypeInAsset = Doctrine::getTable ( 'AssetType' )->assetTypeInAsset ( $asset_type_id );
            if ( $assetTypeInAsset === false )
            {
                $assetType = Doctrine::getTable ( 'AssetType' )->find ( $asset_type_id );
                if ( $assetType->delete () )
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
                $msg = $this->translateTag ( 'Asset' , 'type_assets_not_eliminated_associated_assets' );
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