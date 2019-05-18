<?php

/**
 * @package Controller
 * @subpackage AssetMeasurementController
 */
class AssetMeasurementController extends APP_Controller
{

    function AssetMeasurementController()
    {
        parent::APP_Controller();
    }

    /**
     * get
     *
     * Lista las medidas de un equipo
     *
     * @post int asset_id
     */
    function get()
    {
        $assetMeasurementTable = Doctrine_Core::getTable('AssetMeasurement');
        $assetMeasurement = $assetMeasurementTable->retrieveById($this->input->post('asset_id'));

        if ($assetMeasurement->count())
        {
            echo '({"total":"' . $assetMeasurement->count() . '", "results":' . $this->json->encode($assetMeasurement->toArray()) . '})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add() //TIPO PREDICTIVO
    {
        try
        {
//RESCATA LOS POST
            $asset_id = $this->input->post('asset_id');
            $measure_unit_id = $this->input->post('measure_unit_id');
            $asset_measurement_date = $this->input->post('asset_measurement_date');
            $asset_measurement_cantity = $this->input->post('asset_measurement_cantity');
            $asset_measurement_comments = $this->input->post('asset_measurement_comments');

            $assetMeasurement = new AssetMeasurement();
            $assetMeasurement->asset_id = $asset_id;
            $assetMeasurement->measure_unit_id = $measure_unit_id;
            $assetMeasurement->asset_measurement_date = $asset_measurement_date;
            $assetMeasurement->asset_measurement_cantity = $asset_measurement_cantity;
            $assetMeasurement->asset_measurement_comments = $asset_measurement_comments;

            $measureUnit = Doctrine::getTable('MeasureUnit')->find($assetMeasurement->measure_unit_id);
            $asset = Doctrine::getTable('Asset')->find($assetMeasurement->asset_id);
            $node_id = $asset->node_id;
            $node = Doctrine::getTable('Node')->find($node_id);

            $this->syslog->register('add_asset_measurement', array(
                $measureUnit->measure_unit_name,
                $asset->asset_name,
                $node->getPath()
            )); // registering log

            $assetMeasurement->save();

            //BUSCA POR EL ASSET Y LA UNIDAD DE MEDIDA (son 2 registros como maximo)
            $AssetTriggerMeasurementConfigTable = Doctrine_Core::getTable('AssetTriggerMeasurementConfig')->retriveTypeMeasurement($asset_id, $measure_unit_id);

            foreach ($AssetTriggerMeasurementConfigTable as $AssetTriggerMeasurementConfig)
            {
                $start = $AssetTriggerMeasurementConfig['asset_trigger_measurement_config_start'];
                $end = $AssetTriggerMeasurementConfig['asset_trigger_measurement_config_end'];

                if (($asset_measurement_cantity < $start) || ($asset_measurement_cantity > $end))
                {

                    $AssetTable = Doctrine_Core::getTable('Asset')->find($asset_id);
                    $provider_id = $AssetTable['provider_id'];
                    if (empty($provider_id))
                    {
                        $msg = $this->translateTag('Asset', 'need_provider_asset');
                        $success = false;
                        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                        echo $json_data;
                        return;
                    }


                    //BUSCAR UN ESTADO VALIDO
                    $MtnConfigState = Doctrine_Core :: getTable('MtnConfigState')->retrieveByMenorUser(3); //3-> PREVENTIVO 
                    $mtn_config_state_id = $MtnConfigState['mtn_config_state_id'];

                    if (!empty($mtn_config_state_id))
                    {
                        //CREA OT
                        $MtnWorkOrderDB = new MtnWorkOrder();
                        $CI = & get_instance();
                        $nuevo_folio = Doctrine_Core::getTable('MtnWorkOrder')->lastFolioWo() + 1;
                        $MtnWorkOrderDB->mtn_work_order_folio = $CI->app->generateFolio($nuevo_folio);
                        $MtnWorkOrderDB->asset_id = $asset_id;
                        $MtnWorkOrderDB->mtn_work_order_creator_id = $this->session->userdata('user_id');
                        $MtnWorkOrderDB->provider_id = $provider_id;
                        $MtnWorkOrderDB->mtn_config_state_id = $mtn_config_state_id;
                        $MtnWorkOrderDB->mtn_work_order_date = date("Y-m-d");
                        $MtnWorkOrderDB->mtn_work_order_comment = $asset_measurement_comments;
                        $MtnWorkOrderDB->save();

                        //AGREGA LOG A LA OT
                        $MtnStatusLog = new MtnStatusLog();
                        $MtnStatusLog->user_id = $this->session->userdata('user_id');
                        $MtnStatusLog->mtn_work_order_id = $MtnWorkOrderDB->mtn_work_order_id;
                        $MtnStatusLog->mtn_config_state_id = $mtn_config_state_id;
                        $MtnStatusLog->mtn_status_log_comments = $this->translateTag('General', 'creation');

                        $node = Doctrine_Core::getTable('Node')->find($asset->node_id);

                        $this->syslog->register('add_predictive_ot', array(
                            $MtnWorkOrderDB->mtn_work_order_folio,
                            $measureUnit->measure_unit_name,
                            $asset->asset_name,
                            $node->getPath()
                        )); // registering log

                        $MtnStatusLog->save();
                    } else
                    {
                        $msg = $this->translateTag('Asset', 'creation');
                        $success = false;
                        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                        echo $json_data;
                        return;
                    }
                }
            }

            $msg = $this->translateTag('Asset', 'registered_with_success_read');
            $success = true;
        } catch (Exception $e)
        {
            $msg = $e->getMessage();
            $success = false;
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     *
     * Modifica una medida de un equipo
     *
     * @post int asset_id
     * @post int measure_unit_id
     * @post string asset_measurement_date
     * @post int asset_measurement_cantity
     * @post int asset_measurement_comments
     */
    function update()
    {
        $assetMeasurement = Doctrine_Core::getTable('AssetMeasurement')->find($this->input->post('asset_measurement_id'));

        $measurementAntes = $assetMeasurement->measure_unit_id;
        $measureUnitAntes = Doctrine::getTable('MeasureUnit')->find($measurementAntes);
        $measureUnitAntesName = $measureUnitAntes->measure_unit_name;

        $assetMeasurement->measure_unit_id = $this->input->post('measure_unit_id');

        $measurementAhora = $assetMeasurement->measure_unit_id;
        $measureUnitAhora = Doctrine::getTable('MeasureUnit')->find($measurementAhora);
        $measureUnitAhoraName = $measureUnitAhora->measure_unit_name;

        $fechaAntes = $assetMeasurement->asset_measurement_date;
        $assetMeasurement->asset_measurement_date = $this->input->post('asset_measurement_date');
        $fechaAhora = $assetMeasurement->asset_measurement_date;

        $cantidadAntes = $assetMeasurement->asset_measurement_cantity;
        $assetMeasurement->asset_measurement_cantity = $this->input->post('asset_measurement_cantity');
        $cantidadAhora = $assetMeasurement->asset_measurement_cantity;
        
        $comentarioAntes = $assetMeasurement->asset_measurement_comments;
        $assetMeasurement->asset_measurement_comments = $this->input->post('asset_measurement_comments');
        $comentarioAhora = $assetMeasurement->asset_measurement_comments;
        
        $assetMeasurement->save();

        $measureUnit = Doctrine::getTable('MeasureUnit')->find($assetMeasurement->measure_unit_id);
        $asset = Doctrine::getTable('Asset')->find($assetMeasurement->asset_id);
        $node_id = $asset->node_id;
        $node = Doctrine::getTable('Node')->find($node_id);

        $log_id = $this->syslog->register('update_asset_measurement', array(
            $measureUnit->measure_unit_name,
            $asset->asset_name,
            $node->getPath()
                )); // registering log

        if ($cantidadAntes != $cantidadAhora)
        {
            if ($log_id)
            {
                $logDetail = new LogDetail();
                $logDetail->log_id = $log_id;
                $logDetail->log_detail_param = $this->translateTag('General', 'value');
                $logDetail->log_detail_value_old = $cantidadAntes;
                $logDetail->log_detail_value_new = $cantidadAhora;
                $logDetail->save();
            }
        }

        if ($measureUnitAntesName != $measureUnitAhoraName)
        {
            if ($log_id)
            {
                $logDetail = new LogDetail();
                $logDetail->log_id = $log_id;
                $logDetail->log_detail_param = $this->translateTag('General', 'unit');
                $logDetail->log_detail_value_old = $measureUnitAntesName;
                $logDetail->log_detail_value_new = $measureUnitAhoraName;
                $logDetail->save();
            }
        }

        $fecha = $fechaAhora;
        list($fecha) = explode("T", $fecha);

        $fecha1 = $fecha;
        $fecha2 = date("d/m/Y", strtotime($fecha1));

        $fecha3 = $fechaAntes;
        $fecha4 = date("d/m/Y", strtotime($fecha3));

        if ($fecha2 != $fecha4)
        {
            if ($log_id)
            {
                $logDetail = new LogDetail();
                $logDetail->log_id = $log_id;
                $logDetail->log_detail_param = $this->translateTag('General', 'date');
                $logDetail->log_detail_value_old = $fecha4;
                $logDetail->log_detail_value_new = $fecha2;
                $logDetail->save();
            }
        }
        
        if ($comentarioAntes != $comentarioAhora)
        {
            if ($log_id)
            {
                $logDetail = new LogDetail();
                $logDetail->log_id = $log_id;
                $logDetail->log_detail_param = $this->translateTag('General', 'comment');
                $logDetail->log_detail_value_old = $comentarioAntes;
                $logDetail->log_detail_value_new = $comentarioAhora;
                $logDetail->save();
            }
        }

        echo '{"success": true}';
    }

    /**
     * delete
     *
     * Elimina una medida de un equipo
     *
     * @post int asset_measurement_id
     */
    function delete()
    {
        $assetMeasurement = Doctrine::getTable('AssetMeasurement')->find($this->input->post('asset_measurement_id'));

        $measureUnit = Doctrine::getTable('MeasureUnit')->find($assetMeasurement->measure_unit_id);
        $asset = Doctrine::getTable('Asset')->find($assetMeasurement->asset_id);
        $node_id = $asset->node_id;
        $node = Doctrine::getTable('Node')->find($node_id);

        $this->syslog->register('delete_asset_measurement', array(
            $measureUnit->measure_unit_name,
            $asset->asset_name,
            $node->getPath()
        )); // registering log

        if ($assetMeasurement->delete())
        {
            echo '{"success": true}';
        } else
        {
            echo '{"success": false}';
        }
    }

}
