<?php

/**
 * @package    Controller
 * @subpackage AssetAttributeController
 */
class AssetAttributeController extends APP_Controller
{
    function AssetAttributeController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista los atributos de equipo
     * 
     * @post int asset_id
     */
    function get ()
    {
        $asset_id = $this->input->post ( 'asset_id' );
        $assetAttTable = Doctrine_Core::getTable ( 'AssetAttribute' );
        $assetAtt = $assetAttTable->retrieveById ( $asset_id );

        if ( $assetAtt->count () )
        {
            echo '({"total":"' . $assetAtt->count () . '", "results":' . $this->json->encode ( $assetAtt->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega un nuevo atributo para equipo
     * 
     * @post int asset_id
     * @post int asset_type_attribute_id
     * @post string asset_attribute_value
     */
    function add ()
    {
        try
        {
            $assetAtt = new AssetAttribute();
            $assetAtt[ 'asset_id' ] = $this->input->post ( 'asset_id' );
            $assetAtt[ 'asset_type_attribute_id' ] = $this->input->post ( 'asset_type_attribute_id' );
            $assetAtt[ 'asset_attribute_value' ] = $this->input->post ( 'asset_attribute_value' );
            $assetAtt->save ();
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
     * Modifica atributo de equipo
     * 
     * @post int asset_attribute_id
     * @post int asset_id
     * @post int asset_type_attribute_id
     * @post string asset_attribute_value
     */
    function update ()
    {
        try
        {
            $assetAtt = Doctrine_Core::getTable ( 'AssetAttribute' )->find ( $this->input->post ( 'asset_attribute_id' ) );
            $assetAtt[ 'asset_id' ] = $this->input->post ( 'asset_id' );
            $assetAtt[ 'asset_type_attribute_id' ] = $this->input->post ( 'asset_type_attribute_id' );
            $assetAtt[ 'asset_attribute_value' ] = $this->input->post ( 'asset_attribute_value' );
            $assetAtt->save ();
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
     * Elimina atributo de equipo
     * 
     * @post int asset_attribute_id
     */
    function delete ()
    {
        try
        {
            $assetAtt = Doctrine::getTable ( 'AssetAttribute' )->find ( $this->input->post ( 'asset_attribute_id' ) );
            $assetAtt->delete ();
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