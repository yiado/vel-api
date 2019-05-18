<?php

class ContractAssetcontroller extends APP_Controller
{

    function ContractAssetcontroller()
    {
        parent::APP_Controller();
    }

    function get()
    {
        $contract_id = $this->input->post('contract_id');
        $contractAssetTable = Doctrine_Core::getTable('ContractAsset');
        $contractAsset = $contractAssetTable->retrieveAll($contract_id);

        if ($contractAsset->count())
        {
            echo '({"total":"' . $contractAsset->count() . '", "results":' . $this->json->encode($contractAsset->toArray()) . '})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getAll()
    {
        $node_id = $this->input->post('node_id');
        $asset_type_id = $this->input->post('asset_type_id');
        $brand_id = $this->input->post('brand_id');
        $filters = array(
            'a.asset_type_id = ?' => (!empty($asset_type_id) ? $asset_type_id : NULL),
            'a.brand_id = ?' => (!empty($brand_id) ? $brand_id : NULL)
        );
        $contractAssetTable = Doctrine_Core::getTable('ContractAsset');
        $contractAsset = $contractAssetTable->retrieveByFilter($filters, $node_id);

        if ($contractAsset->count())
        {
            echo '({"total":"' . $contractAsset->count() . '", "results":' . $this->json->encode($contractAsset->toArray()) . '})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add()
    {
        $asset_id = $this->input->post('asset_id');
        $contract_id = $this->input->post('contract_id');

        $Contract = Doctrine::getTable('Contract')->find($contract_id);

        try
        {
            $ContractAsset = new ContractAsset();
            $ContractAsset->asset_id = $asset_id;
            $ContractAsset->contract_id = $contract_id;
            $ContractAsset->save();

            //ACTUALIZA EL PROVEDOR DEL ASSET
            $Asset = Doctrine_Core::getTable('Asset')->find($asset_id);
            $Asset->provider_id = $Contract->provider_id;
            $Asset->save();

            $contract = Doctrine_Core::getTable('Contract')->find($contract_id);
            $provider = Doctrine_Core::getTable('Provider')->find($contract->provider_id);


            $this->syslog->register('associate_contract_asset', array(
                $Asset->asset_name,
                $provider->provider_name,
                $contract->contract_date_start,
                $contract->contract_date_finish
            )); // registering log

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

    function delete()
    {

        $ContractAsset = Doctrine::getTable('ContractAsset')->find($this->input->post('contract_asset_id'));

        $asset_id = $ContractAsset->asset_id;

        try
        {
            //ACTUALIZA EL PROVEDOR DEL ASSET
            $Asset = Doctrine_Core::getTable('Asset')->find($asset_id);
            $Asset->provider_id = NULL;
            $Asset->save();

            $contract = Doctrine_Core::getTable('Contract')->find($ContractAsset->contract_id);
            $provider = Doctrine_Core::getTable('Provider')->find($contract->provider_id);


            $this->syslog->register('delete_associate_contract_asset', array(
                $Asset->asset_name,
                $provider->provider_name,
                $contract->contract_date_start,
                $contract->contract_date_finish
            )); // registering log

            $ContractAsset->delete();
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
