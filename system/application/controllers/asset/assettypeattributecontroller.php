<?php

/**
 * @package    Controller
 * @subpackage AssetTypeAttributeController
 */
class AssetTypeAttributeController extends APP_Controller
{
    function AssetTypeAttributeController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista los tipos de atributos de equipos
     * 
     */
    function get ()
    {
        $assetTypeAttTable = Doctrine_Core::getTable ( 'AssetTypeAttribute' );
        $assetTypeAtt = $assetTypeAttTable->retrieveAll ();

        if ( $assetTypeAtt->count () )
        {
            echo '({"total":"' . $assetTypeAtt->count () . '", "results":' . $this->json->encode ( $assetTypeAtt->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega un nuevo tipo de atributo para equipos
     * 
     * @post int asset_type_id
     * @post string asset_type_attribute_name
     * @post string asset_type_attribute_description
     * @post string asset_type_attribute_type
     */
    function add ()
    {
        $assetTypeAtt = new AssetTypeAttribute();
        $assetTypeAtt[ 'asset_type_id' ] = $this->input->post ( 'asset_type_id' );
        $assetTypeAtt[ 'asset_type_attribute_name' ] = $this->input->post ( 'asset_type_attribute_name' );
        $assetTypeAtt[ 'asset_type_attribute_description' ] = $this->input->post ( 'asset_type_attribute_description' );
        $assetTypeAtt[ 'asset_type_attribute_type' ] = $this->input->post ( 'asset_type_attribute_type' );
        $assetTypeAtt->save ();
        echo '{"success": true}';
    }

    /**
     * update
     * 
     * Modifica el tipo de atributo de los equipos 
     * 
     * @post int asset_type_attribute_id 
     * @post int asset_type_id
     * @post string asset_type_attribute_name 
     * @post string asset_type_attribute_description
     * @post string asset_type_attribute_type
     */
    function update ()
    {
        $assetTypeAtt = Doctrine_Core::getTable ( 'AssetTypeAttribute' )->find ( $this->input->post ( 'asset_type_attribute_id' ) );
        $assetTypeAtt[ 'asset_type_id' ] = $this->input->post ( 'asset_type_id' );
        $assetTypeAtt[ 'asset_type_attribute_name' ] = $this->input->post ( 'asset_type_attribute_name' );
        $assetTypeAtt[ 'asset_type_attribute_description' ] = $this->input->post ( 'asset_type_attribute_description' );
        $assetTypeAtt[ 'asset_type_attribute_type' ] = $this->input->post ( 'asset_type_attribute_type' );
        $assetTypeAtt->save ();
        echo '{"success": true}';
    }

    /**
     * delete
     * 
     * Elimina un tipo de atributo de equipo
     * 
     * @post int asset_type_attribute_id
     */
    function delete ()
    {
        $assetTypeAtt = Doctrine::getTable ( 'AssetTypeAttribute' )->find ( $this->input->post ( 'asset_type_attribute_id' ) );
        if ( $assetTypeAtt->delete () )
        {
            echo '{"success": true}';
        }
        else
        {
            echo '{"success": false}';
        }
    }

}