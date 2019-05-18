<?php

/**
 * @package Controller
 * @subpackage AssetInsuranceController
 */
class AssetInsuranceController extends APP_Controller
{

    function AssetInsuranceController()
    {
        parent::APP_Controller();
    }

    /**
     * get
     *
     * Lista las garantías asociadas a un equipo
     *
     * @post int asset_id
     */
    function get()
    {
        $assetInsuranceTable = Doctrine_Core::getTable('AssetInsurance');
        $assetInsurance = $assetInsuranceTable->findByAsset($this->input->post('asset_id'));

        if ($assetInsurance->count())
        {
            echo '({"total":"' . $assetInsurance->count() . '", "results":' . $this->json->encode($assetInsurance->toArray()) . '})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     *
     * Agrega una nueva garantía a un equipo
     *
     * @post int asset_id
     * @post int asset_insurance_status_id
     * @post int provider_id
     * @post string asset_insurance_begin_date
     * @post string asset_insurance_expiration_date
     * @post string asset_insurance_description
     */
    function add()
    {
        $assetInsurance = new AssetInsurance();
        $assetInsurance->asset_id = $this->input->post('asset_id');
        $assetInsurance->provider_id = $this->input->post('provider_id');
        $assetInsurance->asset_insurance_begin_date = $this->input->post('asset_insurance_begin_date');
        $assetInsurance->asset_insurance_expiration_date = $this->input->post('asset_insurance_expiration_date');
        $assetInsurance->asset_insurance_description = $this->input->post('asset_insurance_description');

        try
        {
            $provider = Doctrine::getTable('Provider')->find($assetInsurance->provider_id);
            $asset = Doctrine::getTable('Asset')->find($assetInsurance->asset_id);
            $node_id = $asset->node_id;
            $node = Doctrine::getTable('Node')->find($node_id);

            $this->syslog->register('add_asset_insurance', array(
                $provider->provider_name,
                $asset->asset_name,
                $node->getPath()
            )); // registering log


            $assetInsurance->save();
            $msg = $this->translateTag('Asset', 'registered_successfully_assurance');
            $success = true;
        } catch (Execption $e)
        {
            $msg = $e->getMessage();
            $success = false;
        }

        $json_data = $this->json->encode(array('sucess' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     *
     * Modifica garantía asociada a un equipo
     *
     * @post int asset_insurance_id
     * @post int asset_id
     * @post int asset_insurance_status_id
     * @post int provider_id
     * @post string asset_insurance_begin_date
     * @post string asset_insurance_expiration_date
     * @post string asset_insurance_description
     */
    function update()
    {
       
        $assetInsurance = Doctrine_Core::getTable('AssetInsurance')->find($this->input->post('asset_insurance_id'));
        $assetInsurance->asset_id = $this->input->post('asset_id');
        
        $assetProvider = $assetInsurance->provider_id;
        $ProviderAntes = Doctrine_Core::getTable('Provider')->find($assetProvider);
        $ProviderNameAntes = $ProviderAntes->provider_name;
        $assetInsurance->provider_id = $this->input->post('provider_id');
        $ProviderAhora = Doctrine_Core::getTable('Provider')->find($this->input->post('provider_id'));
        $ProviderNameAhora = $ProviderAhora->provider_name;
        
        $fechaBase = $assetInsurance->asset_insurance_begin_date;
        $assetInsurance->asset_insurance_begin_date = $this->input->post('asset_insurance_begin_date');
        
        $fechaBaseFin = $assetInsurance->asset_insurance_expiration_date;
        $assetInsurance->asset_insurance_expiration_date = $this->input->post('asset_insurance_expiration_date');
        
        $ComentarioAntes = $assetInsurance->asset_insurance_description;
        $assetInsurance->asset_insurance_description = $this->input->post('asset_insurance_description');

        try
        {
            $provider = Doctrine::getTable('Provider')->find($assetInsurance->provider_id);
            $asset = Doctrine::getTable('Asset')->find($assetInsurance->asset_id);
            $node_id = $asset->node_id;
            $node = Doctrine::getTable('Node')->find($node_id);

            $log_id = $this->syslog->register('update_asset_insurance', array(
                $provider->provider_name,
                $asset->asset_name,
                $node->getPath()
                    )); // registering log

            $assetInsurance->save();
            $msg = $this->translateTag('Asset', 'updated_successfully_assurance');
            $success = true;

            if ($ProviderNameAntes != $ProviderNameAhora)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'provider');
                    $logDetail->log_detail_value_old = $ProviderNameAntes;
                    $logDetail->log_detail_value_new = $ProviderNameAhora;
                    $logDetail->save();
                }
            }
            
            $fecha = $this->input->post('asset_insurance_begin_date');
            list($fecha) = explode("T", $fecha);

            $fecha1 = $fecha;
            $fecha2 = date("d/m/Y", strtotime($fecha1));

            $fecha3 = $fechaBase;
            $fecha4 = date("d/m/Y", strtotime($fecha3));
            
            if ($fecha2 != $fecha4)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'start_date');
                    $logDetail->log_detail_value_old = $fecha4;
                    $logDetail->log_detail_value_new = $fecha2;
                    $logDetail->save();
                }
            }
            
            $fecha = $this->input->post('asset_insurance_expiration_date');
            list($fecha) = explode("T", $fecha);

            $fecha5 = $fecha;
            $fecha6 = date("d/m/Y", strtotime($fecha5));

            $fecha7 = $fechaBaseFin;
            $fecha8 = date("d/m/Y", strtotime($fecha7));
            
            if ($fecha6 != $fecha8)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'end_date');
                    $logDetail->log_detail_value_old = $fecha8;
                    $logDetail->log_detail_value_new = $fecha6;
                    $logDetail->save();
                }
            }
            
            if ($ComentarioAntes != $this->input->post('asset_insurance_description'))
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'description');
                    $logDetail->log_detail_value_old = $ComentarioAntes;
                    $logDetail->log_detail_value_new = $this->input->post('asset_insurance_description');
                    $logDetail->save();
                }
            }
            
        } catch (Execption $e)
        {
            $msg = $e->getMessage();
            $success = false;
        }

        $json_data = $this->json->encode(array('sucess' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * delete
     *
     * Elimina garantía de un equipo
     *
     * @post int asset_insurance_id
     */
    function delete()
    {
        $assetInsurance = Doctrine::getTable('AssetInsurance')->find($this->input->post('asset_insurance_id'));
        $success = null;
        $msg = null;

        try
        {
            $provider = Doctrine::getTable('Provider')->find($assetInsurance->provider_id);
            $asset = Doctrine::getTable('Asset')->find($assetInsurance->asset_id);
            $node_id = $asset->node_id;
            $node = Doctrine::getTable('Node')->find($node_id);

            $this->syslog->register('delete_asset_insurance', array(
                $provider->provider_name,
                $asset->asset_name,
                $node->getPath()
            )); // registering log

            $assetInsurance->delete();
            $msg = $this->translateTag('Asset', 'successfully_eliminated_assurance');
            $success = true;
        } catch (Execption $e)
        {
            $msg = $e->getMessage();
            $success = false;
        }

        $json_data = $this->json->encode(array('sucess' => $success, 'msg' => $msg));
        echo $json_data;
    }

}