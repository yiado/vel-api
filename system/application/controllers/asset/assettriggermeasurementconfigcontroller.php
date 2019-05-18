<?php

/**
 * @package    Controller
 * @subpackage AssetTriggerMeasurementConfigController
 */
class AssetTriggerMeasurementConfigController extends APP_Controller
{

    function AssetTriggerMeasurementConfigController()
    {
        parent::APP_Controller();
    }

    /**
     * get
     *
     * Lista todas las configuraciones de las Lecturas
     *
     */
    function get()
    {
        $asset_trigger_config = Doctrine_Core::getTable('AssetTriggerMeasurementConfig')->retrieveAll();

        if ($asset_trigger_config->count())
        {
            echo '({"total":"' . $asset_trigger_config->count() . '", "results":' . $this->json->encode($asset_trigger_config->toArray()) . '})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     *
     * Agrega una nueva configuracion de Lectura Asociada al Tipo de Activo
     *
     * @post int asset_type_id
     * @post int asset_trigger_measurement_config_id
     * @post int measure_unit_id
     * @post int mtn_plan_id
     * @post int asset_trigger_measurement_config_start
     * @post int asset_trigger_measurement_config_end
     * @param int asset_trigger_measurement_tolerance
     * @post int type_config_mail
     * @post int type_config_sms
     * @post string asset_trigger_measurement_config_notificacion_mails
     */
    function add()
    {

        try
        {

            $triggers_asset_measurement = Doctrine::getTable('AssetTriggerMeasurementConfig')->checkTriggerAssetMeasurence($this->input->post('asset_type_id'), $this->input->post('measure_unit_id'));


            if ($triggers_asset_measurement->count())
            {
                $msg = $this->translateTag('Asset', 'reading_configuration_and_entered');
                $success = false;
            } else
            {
                $asset_measurement = new AssetTriggerMeasurementConfig();
                $asset_measurement->asset_type_id = $this->input->post('asset_type_id');
                $asset_measurement->asset_trigger_measurement_config_id = $this->input->post('asset_trigger_measurement_config_id');
                $asset_measurement->mtn_plan_id = $this->input->post('mtn_plan_id');
                $asset_measurement->measure_unit_id = $this->input->post('measure_unit_id');
                $asset_measurement->asset_trigger_measurement_config_notificacion_mails = $this->input->post('asset_trigger_measurement_config_notificacion_mails');

                //Recibimos los parametros de las variables de los Checkbox
                $type_config_mail = (int) $this->input->post('type_config_mail');
                $type_config_sms = (int) $this->input->post('type_config_sms');
                $asset_new_method = $type_config_mail + $type_config_sms;
                $asset_measurement->asset_trigger_measurement_config_notificacion_method = $asset_new_method;

                $rango1 = (int) $this->input->post('rango1');
                $rango2 = (int) $this->input->post('rango2');

                $asset_measurement->asset_trigger_measurement_config_start = $rango1;
                $asset_measurement->asset_trigger_measurement_config_end = $rango2;

                $assetType = Doctrine_Core::getTable('AssetType')->find($this->input->post('asset_type_id'));
                $measureUnit = Doctrine_Core::getTable('MeasureUnit')->find($this->input->post('measure_unit_id'));
//                $asset = Doctrine_Core::getTable('Asset')->find($this->input->post('asset_type_id'));
//                $node = Doctrine::getTable('Node')->find($asset->node_id);

                $this->syslog->register('add_config_measurement', array(
                    $assetType->asset_type_name,
                    $measureUnit->measure_unit_description
//                        ,
//                    $asset->asset_name,
//                    $node->getPath()
                )); // registering log

                $asset_measurement->save();
                $msg = $this->translateTag('General', 'operation_successful');
                $success = true;
            }
        } catch (Exception $e)
        {
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * Update de la configuracion de la lectura asociada al tipo de Activo
     *
     * @param int asset_type_id
     * @param int asset_trigger_measurement_config_id
     * @param int measure_unit_id
     * @param int mtn_plan_id
     * @param int asset_trigger_measurement_config_start
     * @param int asset_trigger_measurement_config_end
     * @param int asset_trigger_measurement_tolerance
     * @param int type_config_mail
     * @param int type_config_sms
     * @param string asset_trigger_measurement_config_notificacion_mails
     */
    function update()
    {
        $asset_measurement = Doctrine_Core::getTable('AssetTriggerMeasurementConfig')->find($this->input->post('asset_trigger_measurement_config_id'));
        $asset_measurement->asset_type_id = $this->input->post('asset_type_id');
        $asset_measurement->asset_trigger_measurement_config_id = $this->input->post('asset_trigger_measurement_config_id');
        $asset_measurement->mtn_plan_id = $this->input->post('mtn_plan_id');
        $asset_measurement->measure_unit_id = $this->input->post('measure_unit_id');
        $notificacion_mails_antes = $asset_measurement->asset_trigger_measurement_config_notificacion_mails;
        $asset_measurement->asset_trigger_measurement_config_notificacion_mails = $this->input->post('asset_trigger_measurement_config_notificacion_mails');
        $notificacion_mails_despues = $asset_measurement->asset_trigger_measurement_config_notificacion_mails;

        //Recibimos los parametros de las variables de los Checkbox
        $type_config_mail = (int) $this->input->post('type_config_mail');
        $type_config_sms = (int) $this->input->post('type_config_sms');
        $asset_new_method = $type_config_mail + $type_config_sms;

        $notificacion_method_antes = $asset_measurement->asset_trigger_measurement_config_notificacion_method;
        $asset_measurement->asset_trigger_measurement_config_notificacion_method = $asset_new_method;
        $notificacion_method_despues = $asset_measurement->asset_trigger_measurement_config_notificacion_method;

        $rango1 = (int) $this->input->post('rango1');
        $rango2 = (int) $this->input->post('rango2');
        
        $rango1_antes = $asset_measurement->asset_trigger_measurement_config_start;
        $asset_measurement->asset_trigger_measurement_config_start = $rango1;
        $rango1_despues = $asset_measurement->asset_trigger_measurement_config_start;
        $rango2_antes = $asset_measurement->asset_trigger_measurement_config_end;
        $asset_measurement->asset_trigger_measurement_config_end = $rango2;
        $rango2_despues = $asset_measurement->asset_trigger_measurement_config_end;

        try
        {
            $assetType = Doctrine_Core::getTable('AssetType')->find($this->input->post('asset_type_id'));
            $measureUnit = Doctrine_Core::getTable('MeasureUnit')->find($this->input->post('measure_unit_id'));
            $asset = Doctrine_Core::getTable('Asset')->find($this->input->post('asset_type_id'));
            $node = Doctrine::getTable('Node')->find($asset->node_id);

            $log_id = $this->syslog->register('update_config_measurement', array(
                $assetType->asset_type_name,
                $measureUnit->measure_unit_description,
                $asset->asset_name,
                $node->getPath()
            )); // registering log
            
            if ($rango1_antes != $rango1_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Asset', 'operating_out_of_range');
                    $logDetail->log_detail_value_old = $rango1_antes;
                    $logDetail->log_detail_value_new = $rango1_despues;
                    $logDetail->save();
                }
            }
            
            if ($rango2_antes != $rango2_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Asset', 'operating_out_of_range');
                    $logDetail->log_detail_value_old = $rango2_antes;
                    $logDetail->log_detail_value_new = $rango2_despues;
                    $logDetail->save();
                }
            }
            
            if ($notificacion_mails_antes != $notificacion_mails_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Asset', 'mail_addresses_separated_by_commas');
                    $logDetail->log_detail_value_old = $notificacion_mails_antes;
                    $logDetail->log_detail_value_new = $notificacion_mails_despues;
                    $logDetail->save();
                }
            }
            
            if ($notificacion_method_antes != $notificacion_method_despues)
            {
                if ($notificacion_method_antes == '3' && $notificacion_method_despues == '2')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = "Mail y Sms";
                        $logDetail->log_detail_value_new = "Sms";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '3' && $notificacion_method_despues == '0')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = "Mail y Sms";
                        $logDetail->log_detail_value_new = " ";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '3' && $notificacion_method_despues == '1')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = "Mail y Sms";
                        $logDetail->log_detail_value_new = "Mail";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '2' && $notificacion_method_despues == '0')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = "Sms";
                        $logDetail->log_detail_value_new = " ";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '2' && $notificacion_method_despues == '1')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = "Sms";
                        $logDetail->log_detail_value_new = "Mail";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '2' && $notificacion_method_despues == '3')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = "Sms";
                        $logDetail->log_detail_value_new = "Mail y Sms";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '1' && $notificacion_method_despues == '3')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = "Mail";
                        $logDetail->log_detail_value_new = "Mail y Sms";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '1' && $notificacion_method_despues == '2')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = "Mail";
                        $logDetail->log_detail_value_new = "Sms";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '1' && $notificacion_method_despues == '0')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = "Mail";
                        $logDetail->log_detail_value_new = "";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '0' && $notificacion_method_despues == '1')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = " ";
                        $logDetail->log_detail_value_new = "Mail";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '0' && $notificacion_method_despues == '2')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = " ";
                        $logDetail->log_detail_value_new = "Sms";
                        $logDetail->save();
                    }
                }
                if ($notificacion_method_antes == '0' && $notificacion_method_despues == '3')
                {
                    if ($log_id)
                    {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Core', 'notification');
                        $logDetail->log_detail_value_old = " ";
                        $logDetail->log_detail_value_new = "Mail y Sms";
                        $logDetail->save();
                    }
                }
            } 
         
            
            $asset_measurement->save();
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e)
        {
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * delete
     *
     * Elimina una configuracion de lectura
     *
     * @post integer asset_trigger_measurement_config_id
     */
    function delete()
    {
        $asset_measurement = Doctrine::getTable('AssetTriggerMeasurementConfig')->find($this->input->post('asset_trigger_measurement_config_id'));

        try
        {
            $assetType = Doctrine_Core::getTable('AssetType')->find($asset_measurement->asset_type_id);
            $measureUnit = Doctrine_Core::getTable('MeasureUnit')->find($asset_measurement->measure_unit_id);
//            $asset = Doctrine_Core::getTable('Asset')->find($asset_measurement->asset_type_id);
//            $node = Doctrine::getTable('Node')->find($asset->node_id);
            
            $this->syslog->register('delete_config_measurement', array(
                $assetType->asset_type_name,
                $measureUnit->measure_unit_description
//                    ,
//                $assetType->asset_type_name
//                    ,
//                $node->getPath()
            )); // registering log

            $asset_measurement->delete();
            $success = true;
            $msg = $this->translateTag('Asset', 'type_of_asset_configuration_is_deleted_successfully');
        } catch (Exception $e)
        {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * verifyConfiguration
     *
     * Verifica Configuracion del Tipo de Activo y Unidad de medida
     *
     * @post integer asset_type_id
     * @post integer measure_unit_id
     */
    function verifyConfiguration()
    {
        $asset_type_id = $this->input->post('asset_type_id');
        $measure_unit_id = $this->input->post('measure_unit_id');
        $triggers_asset_measurement = Doctrine::getTable('AssetTriggerMeasurementConfig')->checkTriggerAssetMeasurence($asset_type_id, $measure_unit_id);
        $assetId = Doctrine_Core::getTable('AssetTriggerMeasurementConfig')->checkAssetId($asset_type_id);

        if ($assetId === false)
        {
            $valor = 0;
        } else
        {
            $valor = $assetId->asset_type_id;
        }
        if ($asset_type_id === $valor)
        {
            $existe_intervalo = false;
            $existe_rango = false;

            //Revisar valor entregado por $existe_intervalo cuando ya existe un intervalo
            foreach ($triggers_asset_measurement as $trigger)
            {

                if (is_null($trigger->asset_trigger_measurement_config_end))
                {
                    $existe_intervalo = true;
                } else
                {
                    $existe_rango = true;
                }
            }
            $result = array('existe_intervalo' => $existe_intervalo, 'existe_rango' => $existe_rango);
        } else
        {
            $result = true;
        }
        $json_data = $this->json->encode(array('success' => true, 'result' => $result));
        echo $json_data;
    }

    /**
     * verifyUpdate
     *
     * Verifica Configuracion del Tipo de Activo y Unidad de medida
     *
     * @post integer asset_type_id
     * @post integer measure_unit_id
     */
    function verifyUpdate()
    {
        $asset_type_id = $this->input->post('asset_type_id');
        $measure_unit_id = $this->input->post('measure_unit_id');
        $asset_trigger_measurement_config_id = $this->input->post('asset_trigger_measurement_config_id');
        $triggers_asset_measurement = Doctrine::getTable('AssetTriggerMeasurementConfig')->checkTriggerAssetMeasurence($asset_type_id, $measure_unit_id);
        $assetId = Doctrine_Core::getTable('AssetTriggerMeasurementConfig')->checkAssetId($asset_type_id);

        if ($assetId === false)
        {
            $valor = 0;
        } else
        {
            $valor = $assetId->asset_type_id;
        }
        if ($asset_type_id === $valor)
        {
            $existe_intervalo = false;
            $existe_rango = false;

            $asset_measurement = Doctrine::getTable('AssetTriggerMeasurementConfig')->find($asset_trigger_measurement_config_id);
            $asset_measurement_id_table = $asset_measurement->measure_unit_id;
            $asset_trigger_measurement_config_id_table = $asset_measurement->asset_trigger_measurement_config_id;

            //Revisar valor entregado por $existe_intervalo cuando ya existe un intervalo
            foreach ($triggers_asset_measurement as $trigger)
            {
                if (is_null($trigger->asset_trigger_measurement_config_end))
                {
                    if ($asset_measurement_id_table === $measure_unit_id && $asset_trigger_measurement_config_id === $asset_trigger_measurement_config_id_table)
                    {
                        $existe_intervalo = false;
                    } else
                    {
                        $existe_intervalo = true;
                    }
                } else
                {
                    if ($asset_measurement_id_table === $measure_unit_id && $asset_trigger_measurement_config_id === $asset_trigger_measurement_config_id_table)
                    {
                        $existe_rango = false;
                    } else
                    {
                        $existe_rango = true;
                    }
                }
            }
            $result = array('existe_intervalo' => $existe_intervalo, 'existe_rango' => $existe_rango);
        } else
        {
            $result = true;
        }
        $json_data = $this->json->encode(array('success' => true, 'result' => $result));
        echo $json_data;
    }

}
