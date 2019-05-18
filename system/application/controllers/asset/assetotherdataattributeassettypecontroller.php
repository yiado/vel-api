<?php

/** @package    Controller
 *  @subpackage AssetOtherDataAttributeAssetTypeController
 */
class AssetOtherDataAttributeAssetTypeController extends APP_Controller
{
    function AssetOtherDataAttributeAssetTypeController ()
    {

        parent::APP_Controller ();
    }

    /**
     * getList
     *
     * Lista los atributos de info segun tipo de activo
     *
     * @post asset_other_data_attribute_asset_type_id
     */
    function get ()
    {
        $asset_other_data_attribute_asset_type_id = $this->input->post ( 'asset_other_data_attribute_asset_type_id' );
        $infoAttAssetTypeTable = Doctrine_Core::getTable ( 'AssetOtherDataAttributeAssetType' );
        $infoAttAssetType = $infoAttAssetTypeTable->retrieveByAssetType ( $asset_other_data_attribute_asset_type_id );

        if ( $infoAttAssetType->count () )
        {
            echo '({"total":"' . $infoAttAssetType->count () . '", "results":' . $this->json->encode ( $infoAttAssetType->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     *
     * Agrega un nuevo atributo para info segun el tipo de activo
     *
     * @post int asset_other_data_attribute_id
     * @post int asset_type_id
     */
    function add ()
    {
        $asset_type_id = $this->input->post ( 'asset_type_id' );
        $infoSelectedFields = explode ( ',' , $this->input->post ( 'itemselector' ) );
        try
        {
            //Obtenemos la conexi�n actual
            $conn = Doctrine_Manager::getInstance ()->getCurrentConnection ();

            //	Iniciar transacci�n
            $conn->beginTransaction ();

            //Eliminamos la config actual
            Doctrine_Core::getTable ( 'AssetOtherDataAttributeAssetType' )->deleteInfoAttributeAssetType ( $asset_type_id );

            //Insert de los fields en la configuraci�n para el tipo de activo
            if ( ! empty ( $infoSelectedFields[ 0 ] ) )
            {
                foreach ( $infoSelectedFields as $key => $field )
                {

                    $assetOtherDataAttributeAssetType = new AssetOtherDataAttributeAssetType();
                    $assetOtherDataAttributeAssetType->asset_other_data_attribute_id = $field;
                    $assetOtherDataAttributeAssetType->asset_type_id = $asset_type_id;
                    $assetOtherDataAttributeAssetType->asset_other_data_attribute_asset_type_order = $key;

                    $assetOtherDataAttributeAssetType->save ();
                }
            }

            //Commit de la transacci�n
            $conn->commit ();
            $success = true;

            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            //Rollback de la transacci�n
            $conn->rollback ();
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * update
     *
     * Modifica atributo de info de activo tipo
     *
     * @post int asset_other_data_attribute_asset_type_id
     * @post int asset_other_data_attribute_id
     * @post int asset_type_id
     */
    function update ()
    {
        $infoAttAssetType = Doctrine_Core::getTable ( 'AssetOtherDataAttributeAssetType' )->find ( $this->input->post ( 'asset_other_data_attribute_asset_type_id' ) );
        $infoAttAssetType[ 'asset_other_data_attribute_id' ] = $this->input->post ( 'asset_other_data_attribute_id ' );
        $infoAttAssetType[ 'asset_type_id' ] = $this->input->post ( 'asset_type_id' );

        try
        {
            $infoAttAssetType->save ();
            $success = true;

            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'Infrastructure' , 'category_entered_successfully' );
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
     * Elimina atributo de info segun tipo de activo
     *
     * @post int asset_other_data_attribute_asset_type_id
     */
    function delete ()
    {
        $infoAttAssetType = Doctrine::getTable ( 'AssetOtherDataAttributeAssetType' )->find ( $this->input->post ( 'asset_other_data_attribute_asset_type_id' ) );

        if ( $infoAttAssetType->delete () )
        {
            echo '{"success": true}';
        }
        else
        {
            echo '{"success": false}';
        }
    }

}

