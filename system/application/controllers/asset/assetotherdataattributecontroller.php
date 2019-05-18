<?php

/** @package    Controller
 *  @subpackage AssetOtherDataAttributeController
 */
class AssetOtherDataAttributeController extends APP_Controller 
{
    function AssetOtherDataAttributeController() 
    {
        parent::APP_Controller();
    }

    /**
     * 
     * Lista los atributos disponibles para el tipo de activo
     * @param integer $asset_type_id
     * 
     */
    function get() 
    {
        $asset_type_id = $this->input->post('asset_type_id');

        //Todos los atributos disponibles
        $assetOtherDataAttributeTable = Doctrine_Core::getTable('AssetOtherDataAttribute');
        $assetOtherDataAttribute = $assetOtherDataAttributeTable->retrieveAll($asset_type_id);
        $json_data = $this->json->encode(array('total' => $assetOtherDataAttribute->count(), 'results' => $assetOtherDataAttribute->toArray()));
        echo $json_data;
    }

    /**
     * add
     * 
     * Agrega un nuevo atributo para info
     * 
     * @post string asset_other_data_attribute_name
     */
    function add() 
    {
        $infoAtt = new AssetOtherDataAttribute();
        $infoAtt->asset_other_data_attribute_name = $this->input->post('asset_other_data_attribute_name');

        try 
        {
            $infoAtt->save();
            //Imprime el Tag en pantalla
            $msg = $this->translateTag('General', 'operation_successful');
            $asset_other_data_attribute_id = $infoAtt->asset_other_data_attribute_id;
            $success = true;
        } 
        catch (Exception $e) 
        {
            $asset_other_data_attribute_id = NULL;
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'asset_other_data_attribute_id' => $asset_other_data_attribute_id));
        echo $json_data;
    }

    /**
     * update 
     * 
     * Modifica atributo de info
     * 
     * @post int asset_other_data_attribute_id
     * @post string asset_other_data_attribute_name
     */
    function update() 
    {
        $infoAtt = Doctrine_Core::getTable('AssetOtherDataAttribute')->find($this->input->post('asset_other_data_attribute_id'));
        $infoAtt->asset_other_data_attribute_name = $this->input->post('asset_other_data_attribute_name');

        try 
        {
            $infoAtt->save();
            $success = true;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag('General', 'operation_successful');
        } 
        catch (Exception $e) 
        {
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * delete
     * 
     * Elimina un Dato Dinamico si no existe un Tipo de Activo asociado
     * @param  integer $asset_other_data_attribute_id
     */
    function delete() 
    {
        $asset_other_data_attribute_id = $this->input->post('asset_other_data_attribute_id');
        $checkDataInAttributeAssetType = Doctrine::getTable('AssetOtherDataAttribute')->checkDataInAttributeAssetType($asset_other_data_attribute_id);
        if ($checkDataInAttributeAssetType === false) 
        {
            Doctrine::getTable('AssetOtherDataAttribute')->deleteAttribute($asset_other_data_attribute_id);
            $success = true;

            //Imprime el Tag en pantalla
            $msg = $this->translateTag('General', 'operation_successful');
        } else {
            $success = false;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag('Asset', 'dynamic_data_not_eliminated_by_being_associated');
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

}
