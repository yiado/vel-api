<?php

/**
 * @package Controller
 * @subpackage AssetConditionController 
 */
class AssetConditionController extends APP_Controller 
{
    function AssetConditionController() 
    {
        parent::APP_Controller();
    }

    /**
     * get
     * 
     * Lista todas las condiciones para los equipos 
     */
    function get() 
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $assetConditionTable = Doctrine_Core::getTable('AssetCondition');
        $assetCondition = $assetConditionTable->retrieveAll( $text_autocomplete );

        if ($assetCondition->count()) 
        {
            echo '({"total":"' . $assetCondition->count() . '", "results":' . $this->json->encode($assetCondition->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega una nueva condici�n para equipo
     * 
     * @post string asset_condition_name
     * @post string asset_condition_description
     */
    function add()
    {
        //Recibimos los parametros
        $asset_condition_name = $this->input->post('asset_condition_name');
        $asset_condition_description = $this->input->post('asset_condition_description');

        try
        {
            $assetCondition = new AssetCondition();
            $assetCondition->asset_condition_name = $asset_condition_name;
            $assetCondition->asset_condition_description = $asset_condition_description;
            $assetCondition->save();
            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e)
        {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     * 
     * Modifica una condici�n especifica de los equipos
     * 
     * @post int asset_condition_id
     * @post string asset_condition_name
     * @post string asset_condition_description
     */
    function update()
    {
        try
        {
            $assetCondition = Doctrine_Core::getTable('AssetCondition')->find($this->input->post('asset_condition_id'));
            $assetCondition['asset_condition_name'] = $this->input->post('asset_condition_name');
            $assetCondition['asset_condition_description'] = $this->input->post('asset_condition_description');
            $assetCondition->save();
            $msg = $this->translateTag('General', 'operation_successful');
            $success = true;
        } catch (Exception $e)
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
     * Elimina la Condicion si no esta asociada a un Activo en caso contrario no elimina.
     * 
     * @post int asset_condition_id
     */
    function delete() 
    {
        try 
        {
            $asset_condition_id = $this->input->post('asset_condition_id');
            $assetConditionInAsset = Doctrine::getTable('AssetCondition')->assetConditionInAsset($asset_condition_id);
            if ($assetConditionInAsset === false) 
            {
                $assetCondition = Doctrine::getTable('AssetCondition')->find($asset_condition_id);
                if ($assetCondition->delete()) 
                {
                    $success = true;
                    $msg = $this->translateTag('General', 'operation_successful');
                } else {
                    $success = false;
                    $msg = 'Error: ';
                }
            } else {
                $success = false;
                $msg = $this->translateTag('Asset', 'condition_not_eliminated_associated');
            }
        } catch (Exception $e) 
        {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

}