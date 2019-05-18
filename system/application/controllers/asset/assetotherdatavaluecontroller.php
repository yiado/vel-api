<?php

/** @package    Controller
 *  @subpackage AssetOtherDataValueController
 */
class AssetOtherDataValueController extends APP_Controller
{
    function AssetOtherDataValueController ()
    {
        parent::APP_Controller ();
    }

    /**
     * get
     * 
     * Lista los valores de info
     * 
     * @post int asset_id
     */
    function get ()
    {
        $asset_id = $this->input->post ( 'asset_id' );
        if ( is_numeric ( $asset_id ) )
        {
            $assetType = Doctrine_Core::getTable ( 'Asset' )->find ( $asset_id )->AssetType;
            $attributes = Doctrine_Core::getTable ( 'AssetOtherDataAttributeAssetType' )->retrieveByAssetType ( $assetType->asset_type_id );
            $result = array ( );
            $cont = 0;

            foreach ( $attributes as $att )
            {
                $value = Doctrine_Core::getTable ( 'AssetOtherDataValue' )->retrieveByAttributeAsset ( $asset_id , $att->asset_other_data_attribute_id );
                $result[ $cont ] = array ( );
                $result[ $cont ][ 'asset_other_data_attribute_id' ] = $att->asset_other_data_attribute_id;
                $result[ $cont ][ 'value' ] = ($value) ? $value->asset_other_data_value_value : NULL;
                $result[ $cont ][ 'label' ] = $att->AssetOtherDataAttribute->asset_other_data_attribute_name;
                $cont ++;
            }
            $output = array ( 'total' => $attributes->count () , 'results' => $result );
        }
        else
        {

            $output = array ( 'total' => 0 , 'results' => array ( ) );
        }

        echo $this->json->encode ( $output );
    }

    /**
     * 
     * Guarda la informacion de un nodo
     * 
     * @post int node_id
     * @post misc
     * 
     */
    function add ()
    {
        $asset_id = $this->input->post ( 'asset_id' );
        try
        {
            //Obtenemos la conexión actual
            $conn = Doctrine_Manager::getInstance ()->getCurrentConnection ();

            //Iniciar transacción
            $conn->beginTransaction ();

            foreach ( $this->input->postall () as $att => $val )
            {
                if ( ! is_numeric ( $att ) )
                    continue;

                $value = Doctrine_Core::getTable ( 'AssetOtherDataValue' )->retrieveByAttributeAsset ( $asset_id , $att );
                $attr = Doctrine_Core::getTable ( 'AssetOtherDataAttribute' )->find ( $att );

                if ( $value === false )
                {
                    $value = new AssetOtherDataValue();
                }

                $value->asset_other_data_attribute_id = $att;
                $value->asset_id = $asset_id;
                $value->asset_other_data_value_value = $val;

                $value->save ();
            }
            //Commit de la transacción
            $conn->commit ();
            $success = true;

            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'Asset' , 'with_success_save_asset' );
        }
        catch ( Exception $e )
        {
            //Rollback de la transacción
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
     * Modifica un valor de info
     * 
     * @post int asset_other_data_value_id
     * @post int asset_other_data_attribute_id
     * @post int asset_id
     * @post int asset_other_data_value_value
     */
    function update ()
    {
        $assetOtherDataOption = Doctrine_core::getTable ( 'AssetOtherDataValue' )->find ( $this->input->post ( 'asset_other_data_value_id' ) );
        $assetOtherDataValue[ 'asset_other_data_attribute_id' ] = $this->input->post ( 'asset_other_data_attribute_id' );
        $assetOtherDataValue[ 'asset_id' ] = $this->input->post ( 'asset_id' );
        $assetOtherDataValue[ 'asset_other_data_value_value' ] = $this->input->post ( 'asset_other_data_value_value' );

        try
        {
            $assetOtherDataValue->save ();
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
     * Elimina un valor de info
     * 
     * @post int asset_other_data_value_id
     */
    function delete ()
    {
        $assetOtherDataValue = Doctrine::getTable ( 'AssetOtherDataValue' )->find ( $this->input->post ( 'asset_other_data_value_id' ) );
        if ( $assetOtherDataValue->delete () )
        {
            echo '{"success": true}';
        }
        else
        {
            echo '{"success": false}';
        }
    }

}

