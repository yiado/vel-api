<?php

/** @package    Controller
 *  @subpackage Typewocontroller (work order)
 */
class Contractcontroller extends APP_Controller
{

    function Contractcontroller()
    {

        parent::APP_Controller();
    }

    function get()
    {
        $contractTable = Doctrine_Core::getTable('Contract');
        $contract = $contractTable->retrieveAllAsset();

        if ($contract->count())
        {
            echo '({"total":"' . $contract->count() . '", "results":' . $this->json->encode($contract->toArray()) . '})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    
    function getNode()
    {
        $contractTable = Doctrine_Core::getTable('Contract');
        $contract = $contractTable->retrieveAllNode();

        if ($contract->count())
        {
            echo '({"total":"' . $contract->count() . '", "results":' . $this->json->encode($contract->toArray()) . '})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    
    function getByProvider() {
        
        $provider_type_id = $this->input->post('provider_type_id');
        $contractTable = Doctrine_Core::getTable('Contract')->retrieveAllByTypeProbiver($provider_type_id);

        if ($contractTable->count()) {
            echo '({"total":"' . $contractTable->count() . '", "results":' . $this->json->encode($contractTable->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add()
    {
        try
        {
            $provider_id = $this->input->post('provider_id');
            $contract_date_start = $this->input->post('contract_date_start');
            $contract_date_finish = $this->input->post('contract_date_finish');
            $contract_description = $this->input->post('contract_description');
            $contract = new Contract();
            $contract->provider_id = $provider_id;
            $contract->contract_date_start = $contract_date_start;
            $contract->contract_date_finish = $contract_date_finish;
            $contract->contract_description = $contract_description;

            $provider = Doctrine_Core::getTable('Provider')->find($provider_id);

            $this->syslog->register('add_contract', array(
                $provider->provider_name,
                $contract_date_start,
                $contract_date_finish
            )); // registering log

            $contract->save();
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
    
    function addNode()
    {
        try
        {
            $provider_id = $this->input->post('provider_id');
            $contract_date_start = $this->input->post('contract_date_start');
            $contract_date_finish = $this->input->post('contract_date_finish');
            $contract_description = $this->input->post('contract_description');
            
            $contract = new Contract();
            $contract->provider_id = $provider_id;
            $contract->contract_date_start = $contract_date_start;
            $contract->contract_date_finish = $contract_date_finish;
            $contract->contract_description = $contract_description;
            $contract->mtn_maintainer_type_id = 2;

            $provider = Doctrine_Core::getTable('Provider')->find($provider_id);

            $this->syslog->register('add_contract', array(
                $provider->provider_name,
                $contract_date_start,
                $contract_date_finish
            )); // registering log

            $contract->save();
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

    function update()
    {
        try
        {
            $provider_id = $this->input->post('provider_id');
            $contract_date_start = $this->input->post('contract_date_start');
            $contract_date_finish = $this->input->post('contract_date_finish');
            $contract_description = $this->input->post('contract_description');

            $contract = Doctrine_Core::getTable('Contract')->find($this->input->post('contract_id'));
            $provider_antes = $contract->provider_id;
            $contract->provider_id = $provider_id;
            
            $fecha_star_antes = $contract->contract_date_start;
            $contract->contract_date_start = $contract_date_start;
            $fecha_star_despues = $contract->contract_date_start;
            
            $fecha_finish_antes = $contract->contract_date_finish;
            $contract->contract_date_finish = $contract_date_finish;
            $fecha_finish_despues = $contract->contract_date_finish;
            
            $description_antes = $contract->contract_description;
            $contract->contract_description = $contract_description;
            $description_despues = $contract->contract_description;

            $contract->save();
            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
            
            $provider_antes = Doctrine_Core::getTable('Provider')->find($provider_antes);
            $provider_despues = Doctrine_Core::getTable('Provider')->find($provider_id);

            list($contract_date_start) = explode('T', $contract_date_start);
            list($contract_date_finish) = explode('T', $contract_date_finish);

            $log_id = $this->syslog->register('update_contract', array(
                $provider_despues->provider_name,
                $contract_date_start,
                $contract_date_finish
                    )); // registering log

            if ($description_antes != $description_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'description');
                    $logDetail->log_detail_value_old = $description_antes;
                    $logDetail->log_detail_value_new = $description_despues;
                    $logDetail->save();
                }
            }
            
            if ($provider_antes != $provider_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'provider');
                    $logDetail->log_detail_value_old = $provider_antes->provider_name;
                    $logDetail->log_detail_value_new = $provider_despues->provider_name;
                    $logDetail->save();
                }
            }
            
            $fecha = $fecha_star_antes;
            list($fecha) = explode("T", $fecha);

            $fecha1 = $fecha;
            $fecha2 = date("d/m/Y", strtotime($fecha1));

            $fecha3 = $fecha_star_despues;
            $fecha4 = date("d/m/Y", strtotime($fecha3));
            
            if ($fecha2 != $fecha4)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'start_date');
                    $logDetail->log_detail_value_old = $fecha2;
                    $logDetail->log_detail_value_new = $fecha4;
                    $logDetail->save();
                }
            }
            
            $fecha5 = $fecha_finish_antes;
            list($fecha5) = explode("T", $fecha5);

            $fecha6 = $fecha5;
            $fecha7 = date("d/m/Y", strtotime($fecha6));

            $fecha8 = $fecha_finish_despues;
            $fecha9 = date("d/m/Y", strtotime($fecha8));
            
            if ($fecha7 != $fecha9)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'end_date');
                    $logDetail->log_detail_value_old = $fecha7;
                    $logDetail->log_detail_value_new = $fecha9;
                    $logDetail->save();
                }
            }
            
        } catch (Exception $e)
        {
            $success = false;
            $msg = $e->getMessage();
        }
        //Output 
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function delete()
    {
        try
        {
            $contract_id = $this->input->post('contract_id');
            $contract = Doctrine::getTable('Contract')->find($contract_id);

            $provider = Doctrine_Core::getTable('Provider')->find($contract->provider_id);

            $this->syslog->register('delete_contract', array(
                $provider->provider_name,
                $contract->contract_date_start,
                $contract->contract_date_finish
            )); // registering log

            $contract->delete();
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

}

?>
