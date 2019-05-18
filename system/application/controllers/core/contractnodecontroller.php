<?php

class ContractNodecontroller extends APP_Controller {

    function ContractNodecontroller() {
        parent::APP_Controller();
    }

    function get() {
        $contract_id = $this->input->post('contract_id');
        $contractNodeTable = Doctrine_Core::getTable('ContractNode');
        $contractNode = $contractNodeTable->retrieveAll($contract_id);

        foreach ($contractNode->toArray() as $key=>$contract) {

            $final[] = $contract;

            $Node = Doctrine_Core::getTable('Node')->find($contract['node_id']);
            $AuxNode= $Node->toArray();
            $NodeType = Doctrine_Core::getTable('NodeType')->find($Node['node_type_id']);
            $AuxNodeType= $NodeType->toArray();
            
              $final[$key]['node_name'] = $AuxNode['node_name'];
              $final[$key]['node_type_name'] = $AuxNodeType['node_type_name'];
            
        }
        
        if ($contractNode->count()) {
            echo '({"total":"' . $contractNode->count() . '", "results":' . $this->json->encode($final) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getByProvider() {
        
        $provider_type_id = $this->input->post('provider_type_id');
        $contractNodeTable = Doctrine_Core::getTable('ContractNode')->retrieveByTypeProvider($provider_type_id);

        if ($contractNodeTable->count()) {
            echo '({"total":"' . $contractNodeTable->count() . '", "results":' . $this->json->encode($contractNodeTable->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add() {
        $node_id = $this->input->post('node_id');
        $contract_id = $this->input->post('contract_id');
        
        $Contract = Doctrine::getTable('Contract')->find($contract_id);

        try {
//            $node = Doctrine::getTable('ContractNode')->findOneBy('node_id', $node_id);
//            
//            if ($node){
//                $success = 'false';
//                $msg = "Ya existe el Contrato con este Recinto";
//            } else {
                $ContractNode = new ContractNode();
                $ContractNode->node_id = $node_id;
                $ContractNode->contract_id = $contract_id;
                $ContractNode->save();

                //ACTUALIZA EL PROVEDOR DEL NODE
                $Node = Doctrine_Core::getTable('Node')->find($node_id);
//                $Asset->provider_id = $Contract->provider_id;
//                $Asset->save();
                $contract = Doctrine_Core::getTable('Contract')->find($contract_id);
                $provider = Doctrine_Core::getTable('Provider')->find($contract->provider_id);
                $this->syslog->register('associate_contract_node', array(
                    $Node->node_name,
                    $provider->provider_name,
                    $contract->contract_date_start,
                    $contract->contract_date_finish
                )); // registering log

                $success = 'true';
                $msg = $this->translateTag('General', 'operation_successful');

//            }
            
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function delete() {

        $ContractNode = Doctrine::getTable('ContractNode')->find($this->input->post('contract_node_id'));

        $node_id = $ContractNode->node_id;

        try {
            //ACTUALIZA EL PROVEDOR DEL ASSET
            $node = Doctrine_Core::getTable('Node')->find($node_id);
//            $Asset->provider_id = NULL;
//            $Asset->save();

            $contract = Doctrine_Core::getTable('Contract')->find($ContractNode->contract_id);
            $provider = Doctrine_Core::getTable('Provider')->find($contract->provider_id);


            $this->syslog->register('delete_associate_contract_node', array(
                $node->node_name,
                $provider->provider_name,
                $contract->contract_date_start,
                $contract->contract_date_finish
            )); // registering log

            $ContractNode->delete();
            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

}

?>
