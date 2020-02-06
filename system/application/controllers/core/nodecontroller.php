<?php

/**
 * @package    Controller
 * @subpackage NodeController
 */
class NodeController extends APP_Controller {

    function NodeController() {
        parent::APP_Controller();
    }

    /**
     * getSibling
     * 
     * Trae los hijos de un nodo en formato grilla
     * 
     * @post int node
     */
    function getById() {
        $nodePadre = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));

        $node = $nodePadre->getNode();
        $NodeType = Doctrine_Core::getTable('NodeType')->find($nodePadre->node_type_id);
        $ruta = $nodePadre->getPath();
        $node_type_name = $NodeType->node_type_name;

        $nodesCantity = $node->getAncestors();

        $resultado = 0;

        $ContractSolo = Doctrine_Core::getTable('ContractNode')->findOneBy('node_id', $this->input->post('node_id'));


        if ($ContractSolo) {

            if ($ContractSolo) {
                $resultado = $ContractSolo->contract_id;
            }
        } else {

            if ($nodesCantity) {
                foreach ($nodesCantity as $nodes) {
                    $ContractNode = Doctrine_Core::getTable('ContractNode')->findOneBy('node_id', $nodes->node_id);
                    if ($ContractNode) {
                        $resultado = $ContractNode->contract_id;
                    }
                }
            }
        }


        if ($resultado != 0) {

            $contract = Doctrine_Core::getTable('Contract')->findOneBy('contract_id', $resultado);
            $provider = Doctrine_Core::getTable('Provider')->findOneBy('provider_id', $contract->provider_id);

            $final = $nodePadre->toArray();

            $Node = Doctrine_Core::getTable('Node')->find($final['node_id']);

            $final['provider_name'] = $provider->provider_name;
            $final['node_type_name'] = $node_type_name;
            $final['node_ruta'] = $ruta;

            $msg = $this->translateTag('General', 'operation_successful');
            $success = 'true';
        } else {
            $msg = $this->translateTag('Maintenance', 'no_supplier');
            $success = false;
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
            return;
        }
        echo '({"success":"' . 'true' . '", "results":' . $this->json->encode($final) . '})';
    }

    function getByNodeProviderTotal() {
        $nodePadre = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));

        $node = $nodePadre->getNode();
        $NodeType = Doctrine_Core::getTable('NodeType')->find($nodePadre->node_type_id);
        $ruta = $nodePadre->getPath();
        $node_type_name = $NodeType->node_type_name;

        $nodesCantity = $node->getAncestors();

        if ($nodesCantity) {
            // Con Ancestros 
            $ancestor = $nodesCantity->toArray();
            $nodos = $nodePadre->toArray();
            $proveedores = array();
            $proveedores = $ancestor;


            array_push($proveedores, $nodos);

            $proveedores_total = array();

            foreach ($proveedores as $provee) {

                $NodeTodos = Doctrine_Core::getTable('Node')->findAllContractNode($provee['node_id']);

                foreach ($NodeTodos as $NodeContact) {
                    $NodeTodosContract = Doctrine_Core::getTable('Contract')->find($NodeContact['contract_id']);

                    $NodeTodosProvider = Doctrine_Core::getTable('Provider')->find($NodeTodosContract['provider_id']);

                    array_push($proveedores_total, $NodeTodosProvider->toArray());
                }
            }
            $result = $this->limpiarArray($proveedores_total);
        } else {
            // Sin Ancestros

            $NodeTodos = Doctrine_Core::getTable('Node')->findAllContractNode($this->input->post('node_id'));

            $proveedores_total = array();

            foreach ($NodeTodos as $NodeContact) {
                $NodeTodosContract = Doctrine_Core::getTable('Contract')->find($NodeContact['contract_id']);

                $NodeTodosProvider = Doctrine_Core::getTable('Provider')->find($NodeTodosContract['provider_id']);

                array_push($proveedores_total, $NodeTodosProvider->toArray());
            }
            $result = $this->limpiarArray($proveedores_total);
        }

        if ($result) {
            echo '({"total":"' . count($result) . '", "results":' . $this->json->encode($result) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function limpiarArray($array) {
        $retorno = null;
        if ($array != null) {
            $retorno[0] = $array[0];
        }
        for ($i = 1; $i < count($array); $i++) {
            $repetido = false;
            $elemento = $array[$i];
            for ($j = 0; $j < count($retorno) && !$repetido; $j++) {
                if ($elemento == $retorno[$j]) {
                    $repetido = true;
                }
            }
            if (!$repetido) {
                $retorno[] = $elemento;
            }
        }
        return $retorno;
    }

    function getByIdNode() {
        $nodePadre = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));

        $node = $nodePadre->getNode();
        $NodeType = Doctrine_Core::getTable('NodeType')->find($nodePadre->node_type_id);
        $ruta = $nodePadre->getPath();
        $node_type_name = $NodeType->node_type_name;

        $nodesCantity = $node->getAncestors();

        $resultado = 0;

        $final = $nodePadre->toArray();

        $Node = Doctrine_Core::getTable('Node')->find($final['node_id']);

        $final['node_type_name'] = $node_type_name;
        $final['node_ruta'] = $ruta;

        $msg = $this->translateTag('General', 'operation_successful');
        $success = 'true';

        echo '({"success":"' . 'true' . '", "results":' . $this->json->encode($final) . '})';
    }

    function get() {
        $node = Doctrine_Core::getTable('Node')->findAll($this->input->post('query'));

        if ($node->count()) {
            echo '({"total":"' . $node->count() . '", "results":' . $this->json->encode($node->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getSibling() {
        $this->load->library('RowNodes');
        $node_id = ($this->input->post('node') && is_numeric($this->input->post('node')) ? $this->input->post('node') : NULL );

        if ($this->auth->get_user_data('user_type') == 'N' && $this->auth->get_user_data('user_tree_full') != 1) {
            $ancestors = null;

            if ($node_id) {
                $node = Doctrine_Core::getTable('Node')->find($node_id)->getNode();
                if ($node->getAncestors())
                    $ancestors = DoctrineObjectToArray($node->getAncestors()->toArray());
            }

            $nodes = $this->app->searchNodeBranch($node_id, $ancestors);
            $nodesCantity = count($nodes);
        }
        else {
            if ($node_id) { // si el un nodo con id
                $node = Doctrine_Core::getTable('Node')->find($this->input->post('node'))->getNode();
                $nodesCantity = $node->getNumberChildren();
                $nodes = $node->getChildren();
            } else { // nodos raices...no tiene id
                $treeObject = Doctrine_Core::getTable('Node')->getTree();
                $nodes = $treeObject->fetchRoots();
                $nodesCantity = count($nodes);
            }
        }
        $rowNodes = new RowNodes();

        if ($nodesCantity) {
            foreach ($nodes as $node) {
                $rowNodes->add($node->node_id, $node->node_name, $node->node_type_id, $node->NodeType->node_type_name, $node->NodeType->NodeTypeCategory->node_type_category_name);
            }
        }

        if ($rowNodes->count()) {
            echo '({"total":"' . $rowNodes->count() . '", "results":' . $rowNodes->toJson() . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * expand
     * 
     * Retorna los nodos hijos de un nodo
     * 
     * @post mixed node
     */
    function expand() {
//	return $this->expand2();
        $this->load->library('TreeNodes');

        if ($this->input->post('node') && is_numeric($this->input->post('node'))) { // si el un nodo con id
            $node = Doctrine_Core::getTable('Node')->find($this->input->post('node'))->getNode();
            $nodesCantity = $node->getNumberChildren();
            $nodes = $node->getChildren();
        } else { // nodos raices...no tiene id
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $nodes = $treeObject->fetchRoots();
            $nodesCantity = count($nodes);
        }

        $treeNodes = new TreeNodes();

        if ($nodesCantity) {
            foreach ($nodes as $node) {
                $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, false, "", $node->NodeType->node_type_name, $node->NodeType->NodeTypeCategory->node_type_category_name);
            }
        }

        echo $treeNodes->toJson();
    }

    function expanddeep() {
        $node = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));
        $ancestors = $node->getNode()->getAncestors();
        $this->load->library('TreeNodes');
        $treeNodes = array();

        for ($a = sizeof($ancestors) - 1; $a >= 0; $a--) {
            $treeNodes[$a] = array();
            $ancestor = $ancestors[$a];

            foreach (Doctrine_Core::getTable('Node')->find($ancestor->node_id)->getNode()->getChildren() as $node) {
                if ($ancestors[$a + 1]->node_id == $node->node_id) {
                    $treeNodes[$a][] = new TreeNode($node->node_id, $node->node_name, $node->node_type_id, false, true, "", $node->NodeType->node_type_name, $node->NodeType->NodeTypeCategory->node_type_category_name, false, $treeNodes[$a + 1]);
                } else {
                    $treeNodes[$a][] = new TreeNode($node->node_id, $node->node_name, $node->node_type_id, false, false, "", $node->NodeType->node_type_name, $node->NodeType->NodeTypeCategory->node_type_category_name);
                }
            }
        }
        $treeNodes[$a][] = new TreeNode($ancestor->node_id, $ancestor->node_name, $ancestor->node_type_id, false, true, "", $ancestor->NodeType->node_type_name, $ancestor->NodeType->NodeTypeCategory->node_type_category_name, false, $treeNodes[$a + 1]);
        echo $this->json->encode($treeNodes[$a]);
    }
  
    /**
     * addParent
     * 
     * Agregar un nodo raiz
     * 
     * @post string node_name
     * @post int node_type_id
     */
    function addParent() {
        $node = new Node();
        $node->node_name = $this->input->post('node_name');
        $node->node_type_id = $this->input->post('node_type_id');



        $node->save();
        $this->load->library('TreeNodes');
        $treeNodes = new TreeNodes();
        $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, true, "", $node->NodeType->node_type_name);
        echo '{"success": true, "node": ' . $treeNodes->toJson() . '}';

        $this->syslog->register('add_sibling_node', array(
            $node->node_name
        )); // registering log
    }

    /**
     * addSibling
     * 
     * Agregar uno o mas nodos hijos de un padre
     * 
     * @post int node_cantity
     * @post string node_prefix
     * @post int node_type_id
     * @post int node_parent_id
     */
    function addSibling() {
        $this->load->library('TreeNodes');
        $treeNodes = new TreeNodes();
        $curl_response = array();

        if ($this->input->post('node_parent_id') === 'root') {
            if ($this->input->post('node_cantity')) { // multiplos nodos
                for ($i = 1; $i <= $this->input->post('node_cantity'); $i++) {

                    $treeObject = Doctrine_Core::getTable('Node')->getTree();
                    $nodes = $treeObject->fetchRoots();
                    $nodesCantity = count($nodes);

                    if ($nodesCantity == '0') {
                        $resu = 'false';
                    } else {
                        $resu = 'false';
                        foreach ($nodes as $node) {
                            if ($node->node_name == $this->input->post('node_prefix') . $i) {
                                $resu = 'true';
                                continue;
                            }
                        }
                    }

                    if ($resu === 'false') {
                        $node = new Node();
                        $node['node_name'] = ($this->input->post('node_prefix') . $i);
                        $node['node_type_id'] = ($this->input->post('node_type_id'));
                        $node->save();


                        $treeObject = Doctrine_Core::getTable('Node')->getTree();
                        $treeObject->createRoot($node);


                        $this->syslog->register('add_sibling_node', array(
                            ($this->input->post('node_prefix') . $i),
                            $this->translateTag('General', 'root_node')
                        )); // registering log
                        $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, true, "", Doctrine::getTable('NodeType')->find($this->input->post('node_type_id'))->node_type_name);
                    } else {
                        $success = false;
                        $msg = $this->translateTag('General', 'there_is_a_name');
                    }
                }
            } else { // solo un nodo
                $treeObject = Doctrine_Core::getTable('Node')->getTree();
                $nodes = $treeObject->fetchRoots();
                $nodesCantity = count($nodes);

                if ($nodesCantity == '0') {
                    $resu = 'false';
                } else {
                    $resu = 'false';
                    foreach ($nodes as $node) {
                        if ($node->node_name == $this->input->post('node_name')) {
                            $resu = 'true';
                            continue;
                        }
                    }
                }
                if ($resu === 'false') {

                    $node = new Node();
                    $node['node_name'] = $this->input->post('node_name');
                    $node['node_type_id'] = ($this->input->post('node_type_id'));
                    $node->save();


                    $treeObject = Doctrine_Core::getTable('Node')->getTree();
                    $treeObject->createRoot($node);

                    $this->syslog->register('add_parent_node', array(
                        $node->node_name
                    )); // registering log
                    $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, true, "", Doctrine::getTable('NodeType')->find($this->input->post('node_type_id'))->node_type_name);
                } else {

                    $success = false;
                    $msg = $this->translateTag('General', 'there_is_a_name');
                }
            }
        } else {
            if ($this->input->post('node_cantity')) { // multiplos nodos
                for ($i = 1; $i <= $this->input->post('node_cantity'); $i++) {

                    $node = Doctrine_Core::getTable('Node')->find($this->input->post('node_parent_id'))->getNode();
                    $nodesCantity = $node->getNumberChildren();
                    $nodes = $node->getChildren();

                    if ($nodesCantity == '0') {
                        $resu = 'false';
                    } else {
                        $resu = 'false';
                        foreach ($nodes as $node) {
                            if ($node->node_name == $this->input->post('node_prefix') . $i) {
                                $resu = 'true';
                                continue;
                            }
                        }
                    }

                    if ($resu === 'false') {

                        $node = new Node();
                        $node['node_name'] = ($this->input->post('node_prefix') . $i);
                        $node['node_type_id'] = ($this->input->post('node_type_id'));
                        $node->save();

                        if ($this->input->post('node_type_category_id') == 3) {
                            $sensores_ = explode(",", $this->input->post('multiselect'));

                            $sensores = array();

                            foreach ($sensores_ as $key => $value) {
                                $sensores[]["id"] = intval($value);
                            }


                            $service_url = 'http://18.213.235.57:3001/api/v2/nodes';
                            $curl = curl_init($service_url);
                            $curl_post_data = json_encode(array(
                                "modelName" => $this->input->post('node_name'),
                                "manufacterName" => $this->input->post('node_name_developer'),
                                "description" => $this->input->post('node_description'),
                                "group_id" => intval($node->node_id),
                                "node_type" => $this->input->post('type_comunication'),
                                "sensors" => $sensores
                            ));

                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl, CURLOPT_POST, true);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Content-Length: ' . strlen($curl_post_data))
                            );
                            $curl_response = curl_exec($curl);
                            if ($curl_response === false) {
                                $info = curl_getinfo($curl);
//                        curl_close($curl);
                                die('error occured during curl exec. Additioanl info: ' . var_export($info));
                            }
                            curl_close($curl);
                            $decoded = json_decode($curl_response);
                            if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
                                die('error occured: ' . $decoded->response->errormessage);
                            }
                        }

                        if (is_numeric($this->input->post('node_parent_id'))) {
                            $node->getNode()->insertAsLastChildOf(Doctrine_Core::getTable('Node')->find($this->input->post('node_parent_id')));

                            $parentNode = Doctrine_Core::getTable('Node')->find($this->input->post('node_parent_id'));
                            $this->syslog->register('add_sibling_node', array(
                                ($this->input->post('node_prefix') . $i),
                                $parentNode->getPath()
                            )); // registering log
                        } else {
                            $treeObject = Doctrine_Core::getTable('Node')->getTree();
                            $treeObject->createRoot($node);

                            $parentNode = Doctrine_Core::getTable('Node')->find($this->input->post('node_parent_id'));
                            $this->syslog->register('add_sibling_node', array(
                                ($this->input->post('node_prefix') . $i),
                                $parentNode->getPath()
                            )); // registering log
                        }
                        $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, true, "", Doctrine::getTable('NodeType')->find($this->input->post('node_type_id'))->node_type_name);
                    } else {

                        $success = false;
                        $msg = $this->translateTag('General', 'there_is_a_name');
                    }
                }
            } else { // solo un nodo
                $node = Doctrine_Core::getTable('Node')->find($this->input->post('node_parent_id'))->getNode();
                $nodesCantity = $node->getNumberChildren();
                $nodes = $node->getChildren();

                if ($nodesCantity == '0') {
                    $resu = 'false';
                } else {
                    $resu = 'false';
                    foreach ($nodes as $node) {
                        if ($node->node_name == $this->input->post('node_name')) {
                            $resu = 'true';
                            continue;
                        }
                    }
                }
                if ($resu === 'false') {

                    $node = new Node();
                    $node['node_name'] = $this->input->post('node_name');
                    $node['node_type_id'] = ($this->input->post('node_type_id'));
                    $node->save();

                    if ($this->input->post('node_type_category_id') == 3) {
                        $sensores_ = explode(",", $this->input->post('multiselect'));

                        $sensores = array();

                        foreach ($sensores_ as $key => $value) {
                            $sensores[]["id"] = intval($value);
                        }


                        $service_url = 'http://18.213.235.57:3001/api/v2/nodes';
                        $curl = curl_init($service_url);
                        $curl_post_data = json_encode(array(
                            "modelName" => $this->input->post('node_name'),
                            "manufacterName" => $this->input->post('node_name_developer'),
                            "description" => $this->input->post('node_description'),
                            "group_id" => intval($node->node_id),
                            "node_type" => $this->input->post('type_comunication'),
                            "sensors" => $sensores
                        ));

                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($curl_post_data))
                        );
                        $curl_response = curl_exec($curl);
                        if ($curl_response === false) {
                            $info = curl_getinfo($curl);
//                        curl_close($curl);
                            die('error occured during curl exec. Additioanl info: ' . var_export($info));
                        }
                        curl_close($curl);
                        $decoded = json_decode($curl_response);
                        if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
                            die('error occured: ' . $decoded->response->errormessage);
                        }
                    }
                    if (is_numeric($this->input->post('node_parent_id'))) {
                        $parentNode = Doctrine_Core::getTable('Node')->find($this->input->post('node_parent_id'));
                        $node->getNode()->insertAsLastChildOf($parentNode);

                        $this->syslog->register('add_sibling_node', array(
                            $this->input->post('node_name'),
                            $parentNode->getPath()
                        )); // registering log
                    } else {
                        $treeObject = Doctrine_Core::getTable('Node')->getTree();
                        $treeObject->createRoot($node);

                        $this->syslog->register('add_parent_node', array(
                            $node->node_name
                        )); // registering log
                    }
                    $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, true, "", Doctrine::getTable('NodeType')->find($this->input->post('node_type_id'))->node_type_name);
                } else {

                    $success = false;
                    $msg = $this->translateTag('General', 'there_is_a_name');
                }
            }
        }

        if ($resu === 'false') {
            echo '{"success": true, "node": ' . $treeNodes->toJson() . '}';
        } else {
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'node' => $treeNodes->toJson()));
            echo $json_data;
        }
    }

    /**
     * delete
     * 
     * Eliminar un nodo
     * 
     * @post int node_id
     */
    function deleteGroupAsset($node) {

        //Se verifica si tiene nodos asociados
        $group_node = Doctrine::getTable('Node')->getChildNodes($node);

        if (count($group_node) > 0) {
            foreach ($group_node as $value) {
                $group = Doctrine::getTable('GroupAssetNode')->findByNode($value['node_id']);

                if ($group) {
                    if (!$group->delete()) {
                      return false;
                    }
                }
            }
        }
        return true;
    }

    function delete() {


        $node = Doctrine::getTable('Node')->find($this->input->post('node_id'));

        $result = $this->deleteGroupAsset($node);
        if (!$result) {
            echo '{"success": false}';
            exit();
        }
        
        $this->syslog->register('delete_node', array(
            $node->node_name,
            $node->getPath()
        )); // registering log
        //Se busca si posee sensores asociados al nodo
        $q = Doctrine_Query::create()
                ->select('n.node_id')
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->where('n.node_parent_id = ?', $node->node_parent_id)
                ->andWhere('nt.node_type_category_id = 3')
                ->andWhere('n.lft >= ?', $node->lft)
                ->andWhere('n.rgt <= ?', $node->rgt);

        $resuls = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        $array_result = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($resuls)), 0);

       
        if ($node->getNode()->delete()) {

            //Se borran los nodos de tipo sensor
            if (count($array_result) > 0) {
                foreach ($array_result as $value) {
                    $sensorDelete = $this->deleteSensor($value);
                    if(!$sensorDelete){
                       echo '{"success": false}';  
                    }
                }
            }
            echo '{"success": true}';  
           
        } else {
            echo '{"success": false}';
        }
    }

    /**
     * move
     * 
     * Cambia el nodo padre de un nodo
     * 
     * @post int node_id
     * @post int node_parent_id
     */
    function deleteSensor($node_id) {

        $service_url = 'http://18.213.235.57:3001/api/v2/nodes/' . $node_id;
        $ch = curl_init($service_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $curl_post_data = array();
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_post_data);
        $curl_response = curl_exec($ch);
        if ($curl_response === false) {
            $info = curl_getinfo($ch);
//                curl_close($curl);
            die('error occured during curl exec. Additioanl info: ' . var_export($info));
        }
        curl_close($ch);
        $decoded = json_decode($curl_response);
        if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
            return true;
        }
//        print_r($this->json->encode($decoded->data));
//        exit();
        if ($decoded->succes->status == 200) {
            return true;
        } else {
            return false;
        }
    }

    function move() {
        $node = Doctrine::getTable('Node')->find($this->input->post('node_id'));

        if ($this->input->post('node_parent_id') == 'root') {
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $treeObject->createRoot($node);
        } else {
            $node->getNode()->moveAsLastChildOf(Doctrine::getTable('Node')->find($this->input->post('node_parent_id')));
        }
    }

    /**
     * copy
     * 
     * Copia un(os) nodo(s)
     * 
     * @post int node_id
     * @post string copy_type
     */
    function copy() {
        $node_id = $this->input->post('node_id');
        $this->session->set_userdata('copy_node_id', $node_id);
        $this->session->set_userdata('copy_type', 'copy');
    }

    /**
     * cut
     * 
     * Corta un(os) nodo(s)
     * 
     * @post int node_id
     */
    function cut() {
        $node_id = $this->input->post('node_id');
        $this->session->set_userdata('copy_node_id', $node_id);
        $this->session->set_userdata('copy_type', 'cut');
    }

    /**
     * paste
     * 
     * Pega un(os) nodo(s)  
     * 
     * @post int node_parent_id
     */
    function paste() {

        $parentNodeAntiguo = Doctrine::getTable('Node')->find($this->input->post('node_parent_id'));

        $this->load->library('TreeNodes');
        $treeNodes = new TreeNodes();
        $parent_id = ($this->input->post('node_parent_id') == 'root' ? NULL : $this->input->post('node_parent_id'));
        $node_ids = explode(',', $this->session->userdata('copy_node_id'));
        $type = $this->session->userdata('copy_type');

        if (($type != 'cut' && $type != 'copy') || !count($node_ids)) {
            echo '{"success": false}';
            return;
        }
        $parentNode = Doctrine::getTable('Node')->find($parent_id);

        if ($type == 'cut') {
            foreach ($node_ids as $id) {
                $node = Doctrine::getTable('Node')->find($id);

                $this->syslog->register('move_node', array(
                    $node->node_name,
                    $node->getPath(),
                    $parentNode->getPath()
                )); // registering log

                $node->getNode()->moveAsLastChildOf($parentNode);
                $node->save();
                $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, true, "", $node->NodeType->node_type_name);
            }
            echo '{"copy_type": "cut", "node": ' . $treeNodes->toJson() . '}';
        } else if ($type == 'copy') {
            foreach ($node_ids as $id) {
                $node = Doctrine_Core::getTable('Node')->find($id);

                $this->syslog->register('copy_node', array(
                    $node->node_name,
                    $node->getPath(),
                    $parentNode->getPath()
                )); // registering log

                $new_node = new Node();
                $new_node['node_name'] = ($node->node_name);
                $new_node['node_type_id'] = ($node->node_type_id);
                $new_node['node_parent_id'] = $parent_id;
                $new_node->save();
                $new_node->getNode()->insertAsLastChildOf($parentNode);
                $treeNodes->add($new_node->node_id, $new_node->node_name, $new_node->node_type_id, false, true, "", $new_node->NodeType->node_type_name);
            }
            echo '{"copy_type": "copy", "node": ' . $treeNodes->toJson() . '}';
        }
    }

    function edit() {

        $action = $this->input->post('action');

        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            echo '{"success": false}';
        }
    }

    /**
     * update
     * 
     * Permite modificar el nombre del nodo
     * 
     * @post int node_id
     * @post string node_name
     */
    function update() {

        $node_id = $this->input->post('node_id');
//RESCATA NOMBRE ANTIGUO
        $node_name = Doctrine::getTable('Node')->find($node_id);
        $antiguo = $node_name->node_name;



//GUARDA NOMBRE NUEVO
        $node = Doctrine::getTable('Node')->find($node_id);
        $node->node_name = $this->input->post('node_name');
        $node->save();

        $log_id = $this->syslog->register('update_node', array(
            $antiguo,
            $node->node_name,
            $node->getPath()
        ));

        if ($log_id) {
            $logDetail = new LogDetail();
            $logDetail->log_id = $log_id;
            $logDetail->log_detail_param = $this->translateTag('General', 'name');
            $logDetail->log_detail_value_old = $antiguo;
            $logDetail->log_detail_value_new = $this->input->post('node_name');
            $logDetail->save();
        }

        $this->load->library('TreeNodes');
        $treeNodes = new TreeNodes();
        $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, true, "", $node->NodeType->node_type_name);
        echo '{"success": true, "node": ' . $treeNodes->toJson() . '}';
    }

    /**
     * Busca nodos en base a los filtros enviados
     * @method POST
     * 
     */
    function search() {
        $post_data = $this->input->postAll();

        //Filtros
        //Estaticos
        $filters = array();
        $node_ref = $post_data['node_id'];
        $node_name = $post_data['node_name'];
        $node_types = (!empty($post_data['node_type_id']) ? $post_data['node_type_id'] : NULL );
        $depth = ( empty($post_data['depth']) ? 0 : $post_data['depth'] );

        $conditions = array();
        $conditions['node'] = array();
        $conditions['infra'] = array();
        $conditions['otherdata'] = array();

        //Tipos de nodos
        //Se usarÃƒÆ’Ã‚Â¡ whereIn en la query
        if (!empty($node_types)) {
            $conditions['node'][] = array(
                'node_type_id',
                null,
                explode(',', $node_types)
            );
        }

        if (!empty($node_name)) {
            $conditions['node'][] = array(
                'node_name',
                'LIKE',
                '%' . $node_name . '%'
            );
        }

        //Quitamos los filtros estaticos, para evitar errores en el each de los filtros estaticos y dinamicos
        unset($post_data['depth']);
        unset($post_data['node_type_id']);
        unset($post_data['node_name']);


        foreach ($post_data as $key => $value) {
            if (!empty($value)) {

                if (is_numeric($key) && $post_data[$key . '_cb']) {
                    array_push($conditions['otherdata'], array(
                        str_replace('_txt', '', $key),
                        $post_data[$key . '_cb'],
                        $value
                    ));
                }

                if (strpos($key, '_txt') && (!empty($value) && !is_null($value))) {
                    array_push($conditions['infra'], array(
                        str_replace('_txt', '', $key),
                        $post_data[str_replace('_txt', '', $key) . '_cb'],
                        $value
                    ));
                }
            }
        }


        $q = Doctrine_Query::create()
                ->select('n.node_id, n.node_name, n.node_type_id, nt.node_type_id, nt.node_type_name, ntc.node_type_category_name')
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->groupBy('n.node_id')
                ->orderBy('n.node_name DESC');

        // restringiendo busqueda
        $user_id = $this->auth->get_user_data('user_id');
        $user_type = $this->auth->get_user_data('user_type');

        //No es usuario administrador
        if ($user_type != 'A' && $this->auth->get_user_data('user_tree_full') == 0) {
            $q->innerJoin('n.UserGroupNode ugn')
                    ->innerJoin('ugn.UserGroup ug')
                    ->innerJoin('ug.UserGroupUser ugu')
                    ->where('ugu.user_id = ?', $user_id);

            $flag = true;
        }


        //Datos estructurales
        if (count($conditions['infra'])) {
            $q->innerJoin('n.InfraInfo inf');

            foreach ($conditions['infra'] as $data) {
                $q->andWhere($data[0] . ' ' . $data[1] . ' ?', $data[2]);
            }
        }

        //Datos dinamicos
        if (count($conditions['otherdata'])) {
            $q->innerJoin('n.InfraOtherDataValue iodv');

            foreach ($conditions['otherdata'] as $data) {
                $q->andWhere('iodv.infra_other_data_attribute_id = ? AND (iodv.infra_other_data_value_value ' . $data[1] . ' ? OR iodv.infra_other_data_option_id = ? )', array($data[0], $data[2], $data[2]));
            }
        }

        //Datos del nodo
        if (count($conditions['node'])) {
            foreach ($conditions['node'] as $data) {
                if (is_array($data[2])) {
                    $q->andWhereIn($data[0], $data[2]);
                } else {
                    $q->andWhere($data[0] . ' ' . $data[1] . ' ?', $data[2]);
                }
            }
        }
//        echo'Aqui';
//        print_r($q->getSql());
//        exit();
//        print_r($q);
        //Crear los conditions para los filtros dinamicos
        $treeObject = Doctrine_Core::getTable('Node')->getTree();
        $nodes = array();

        //NULL=Rama completa, 1 = nodos directos
        $depth = ( $depth == 1 ? array('depth' => 1) : NULL);
        $depth = NULL;

        if (!is_numeric($node_ref)) {
            $aux_nodes = array();

            foreach ($treeObject->fetchRoots() as $root) {
                $treeObject->setBaseQuery($q);
                $aux_nodes = $treeObject->fetchBranch($root->node_id, $depth);
                $treeObject->resetBaseQuery();

                $nodes = array_merge($nodes, $aux_nodes->toArray());
            }
        } else {
            $treeObject->setBaseQuery($q);
            $aux = $treeObject->fetchBranch($node_ref, $depth);
            $nodes = $aux->toArray();
        }
        $treeObject->resetBaseQuery();

        $this->load->library('RowNodes');
        $rowNodes = new RowNodes();

        foreach ($nodes as $node) {
            $nodeObjectResult = Doctrine_Core::getTable('Node')->findOneByNodeId($node['node_id']);
            $rowNodes->add($node['node_id'], $node['node_name'], $node['node_type_id'], $node['NodeType']['node_type_name'], null, $nodeObjectResult->getPath());
        }
        $json_data = '{"total": ' . $rowNodes->count() . ', "results": ' . $rowNodes->toJson() . '}';
        echo $json_data;
    }

    function groupAsset() {

        // se captura el nodo seleccionado y lainformación del usuario
        $node = $this->input->post('node');
        $module = trim($this->input->post('module'));
        $user = $this->auth->get_user_data();

        // Si es tipo administrador se le asignan todos los permisos por defecto
        if ($user['user_type'] == 'A') {

            $json_data = $this->json->encode(array('success' => true, 'permits' => true));
            echo $json_data;
        } else {
            if (!empty($module)) {
                //Se busca el id del modulo
                $module = Doctrine_Core::getTable('Module')->findModule($module);

                if ($module) {

                    //En caso de que no sea administrador se buscan los grupos asignados al usuario
                    $groups = Doctrine_Core::getTable('User')->retrieveByID($user['user_id']);

                    // por defecto se asigna falso
                    $permits = false;

                    //Si el usuario posee un grupo asociado 
                    if ($groups->count() > 0) {
                        //Por cada grupo se busca si tiene permisos asociados al nodo 
                        foreach ($groups as $group) {

                            $nodeAsset = Doctrine_Core::getTable('GroupAssetNode')->findOneByUserGroupIdAndNodeId($group->user_group_id, $node, $module->module_id);

                            //Si posee permisos se coloca true en caso contrario se coloca false 
                            if ($nodeAsset) {
                                $permits = true;
                            } else {
                                $permits = ($permits) ? $permits : false;
                            }
                        }

                        $json_data = $this->json->encode(array('success' => true, 'permits' => $permits));
                        echo $json_data;
                    } else {

                        $json_data = $this->json->encode(array('success' => true, 'permits' => false));
                        echo $json_data;
                    }
                } else {
                    $json_data = $this->json->encode(array('success' => true, 'permits' => false));
                    echo $json_data;
                }
            } else {
                $json_data = $this->json->encode(array('success' => true, 'permits' => false));
                echo $json_data;
            }
        }
    }
    
    /**
     * 
     * @param type $node_id
     */
    function importList() {
        if (is_numeric($this->input->post('node_parent_id'))) {
            $this->load->library('TreeNodes');
            $treeNodes = new TreeNodes();
            $this->load->library('PHPExcel');
            $nodo = array();            
            $documentoExcel = $this->input->file('documentoExcel');
            $node_parent_id = $this->input->post('node_parent_id');
            
            if ($documentoExcel['tmp_name']) {
                $objPHPExcel = PHPExcel_IOFactory::load($documentoExcel['tmp_name']);
                $worksheets = $objPHPExcel->getWorksheetIterator();
                foreach ($worksheets as $worksheet) {
                    $highestRow         = $worksheet->getHighestRow();
                    $highestColumn      = $worksheet->getHighestColumn();
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                    for ($row = 1; $row <= $highestRow; ++ $row) {
                        if ($row > 1) {
                            
                            $node_level = (int) $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                            $nodeType = Doctrine::getTable('NodeType')->findOneByNodeTypeName($worksheet->getCellByColumnAndRow(2, $row)->getValue());
                            
                            $node = new Node();                            
                            $node['node_name'] = $worksheet->getCellByColumnAndRow(1, $row)->getValue();                        
                            $node['node_type_id'] = $nodeType->node_type_id;
                            $node->save();
                            
                            $nodo[$node_level] = $node->node_id;
                            
                            if ($node_level == 1) {
                                $nodo = array();
                                $nodo[$node_level] = $node->node_id;
                                $node_parent_id = $this->input->post('node_parent_id');
                            } else {
                                $node_parent_id = $nodo[$node_level - 1];
                            }
                        
                            $parentNode = Doctrine_Core::getTable('Node')->find($node_parent_id);
                            $node->getNode()->insertAsLastChildOf($parentNode);
                            $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, true, "", $nodeType->node_type_name);
                            $this->insertInfraInfo($highestColumnIndex, $worksheet, $row, $node);
                        }
                    }
                }
            }
            echo '({"success":"' . 'true' . '", "results":0})';
        } else {
            echo '({"success":"' . 'false' . '", "results":0})';            
        }
    }
    

    function insertInfraInfo($highestColumnIndex, $worksheet, $row, $node) {
        $info = new InfraInfo();
        $info->node_id = $node->node_id;
        $info->allowListener = true;
        for ($col = 3; $col < $highestColumnIndex; ++$col) {
            $isInfraInfoAttr = false;
            $columnName = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
            $languageTag = Doctrine_Core::getTable('LanguageTag')->findOneByLanguageTagValue($columnName);

            $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            if($languageTag){
                error_log($languageTag->language_tag_tag);
                $fieldInfraInfo = $this->config->item('fields_infra_info');                
                if( isset($fieldInfraInfo[$languageTag->language_tag_tag]) ) {
                    $isInfraInfoAttr = true;
                    $info->{$languageTag->language_tag_tag} = $value;
                }
            }
            
            if(!$isInfraInfoAttr) {
                $infraOtherDataAttribute = Doctrine_Core::getTable('InfraOtherDataAttribute')->findOneByInfraOtherDataAttributeName($columnName);
                if ($infraOtherDataAttribute) {
                    $infraOtherDataValue = new InfraOtherDataValue();
                    $infraOtherDataValue->infra_other_data_attribute_id = $infraOtherDataAttribute->infra_other_data_attribute_id;
                    $infraOtherDataValue->node_id = $node->node_id;
                    if ($infraOtherDataAttribute->infra_other_data_attribute_type <= 4) {                        
                        $infraOtherDataValue->infra_other_data_value_value = $value;
                    } else if ($infraOtherDataAttribute->infra_other_data_attribute_type == 5){
                        $infraOtherDataOption = Doctrine_Core::getTable('InfraOtherDataOption')->findOneByInfraOtherDataAttributeIdAndInfraOtherDataOptionName($infraOtherDataAttribute->infra_other_data_attribute_id,$value);
                        $infraOtherDataValue->infra_other_data_option_id = $infraOtherDataOption->infra_other_data_option_id;
                    }
                    $infraOtherDataValue->save();                    
                }
            }
        }
        $info->save();
    }
}


function bulkLoadExcell() {
    ini_set('memory_limit', '512M');
    $this->load->library('TreeNodes');
    $treeNodes = new TreeNodes();

    $documentoExcel = $this->input->file('documentoExcel');
    $documentoExcel = $documentoExcel['tmp_name'];
    $this->load->library('PHPExcel');

    $sheetIndex = 0;

    $piso_uno = 0;
    $piso_dos = 0;
    $piso_tres = 0;
    $piso_cuatro = 0;
    $piso_cinco = 0;
    $piso_seis = 0;
    $piso_siete = 0;
    $piso_ocho = 0;

    $cont = 0;

    if ($documentoExcel) {
        $objPHPExcel2 = PHPExcel_IOFactory :: load($documentoExcel);
        $rowIterator2 = $objPHPExcel2->getActiveSheet()->getRowIterator();
        $objWorksheet2 = $objPHPExcel2->getActiveSheet();

        $rowsCount2 = $objWorksheet2->getHighestRow(); // cantidad de lineas en el excel    

        foreach ($rowIterator2 as $row2) {

            $rowIndex2 = $row2->getRowIndex();

            $nombre_recinto = $objWorksheet2->getCell('A' . $rowIndex2)->getCalculatedValue();
            $codigo_subrecinto = $objWorksheet2->getCell('B' . $rowIndex2)->getCalculatedValue();
            $nombre_subrecinto = $objWorksheet2->getCell('C' . $rowIndex2)->getCalculatedValue();
            $organismo = $objWorksheet2->getCell('D' . $rowIndex2)->getCalculatedValue();
            $departamento = $objWorksheet2->getCell('E' . $rowIndex2)->getCalculatedValue();
            $unidad = $objWorksheet2->getCell('F' . $rowIndex2)->getCalculatedValue();
            $actividad = $objWorksheet2->getCell('G' . $rowIndex2)->getCalculatedValue();
            $uso = $objWorksheet2->getCell('H' . $rowIndex2)->getCalculatedValue();
            $uso_particular = $objWorksheet2->getCell('I' . $rowIndex2)->getCalculatedValue();
            $propiedad_recinto = $objWorksheet2->getCell('J' . $rowIndex2)->getCalculatedValue();
            $estatus_recinto = $objWorksheet2->getCell('K' . $rowIndex2)->getCalculatedValue();
            $estado_recinto = $objWorksheet2->getCell('L' . $rowIndex2)->getCalculatedValue();
            $cantidad_usuarios = $objWorksheet2->getCell('M' . $rowIndex2)->getCalculatedValue();
            $ventanas = $objWorksheet2->getCell('N' . $rowIndex2)->getCalculatedValue();
            $aire_acondicionado = $objWorksheet2->getCell('O' . $rowIndex2)->getCalculatedValue();
            $calefaccion = $objWorksheet2->getCell('P' . $rowIndex2)->getCalculatedValue();
            $luminarias = $objWorksheet2->getCell('Q' . $rowIndex2)->getCalculatedValue();
            $enchufes = $objWorksheet2->getCell('R' . $rowIndex2)->getCalculatedValue();
            $punto_red = $objWorksheet2->getCell('S' . $rowIndex2)->getCalculatedValue();
            $proyector = $objWorksheet2->getCell('T' . $rowIndex2)->getCalculatedValue();
            $wifi = $objWorksheet2->getCell('U' . $rowIndex2)->getCalculatedValue();
            $observacion_recinto = $objWorksheet2->getCell('V' . $rowIndex2)->getCalculatedValue();
            $ultima_actualizacion = $objWorksheet2->getCell('W' . $rowIndex2)->getCalculatedValue();
            $superficie = $objWorksheet2->getCell('X' . $rowIndex2)->getCalculatedValue();
            $nombre_piso = $objWorksheet2->getCell('Y' . $rowIndex2)->getCalculatedValue();
            $tipo = $objWorksheet2->getCell('Z' . $rowIndex2)->getCalculatedValue();

            $NodeType = Doctrine_Core :: getTable('NodeType')->findOneBy('node_type_name', $tipo);
            $node_name = Doctrine_Core :: getTable('Node')->findOneBy('node_name', $nombre_piso);

            $piso = Doctrine_Core :: getTable('Node')->findOneBy('node_name', $nombre_piso);

            if ($piso) {
                $node = Doctrine_Core::getTable('Node')->find($node_name->node_id)->getNode();
                $nodesCantity = $node->getNumberChildren();
                $nodes = $node->getChildren();

                if ($nodesCantity == '0') {
                    $resu = 'false';
                } else {
                    $resu = 'false';
                    foreach ($nodes as $node) {
                        if ($node->node_name == $nombre_piso) {
                            $resu = 'true';
                            continue;
                        }
                    }
                }

                if ($resu === 'false') {


                    $node = new Node();
                    $node['node_name'] = $codigo_subrecinto;
                    $node['node_type_id'] = $NodeType->node_type_id;
                    $node->save();

                    $info = new InfraInfo();
                    $info->node_id = $node->node_id;
                    $info->infra_info_usable_area = $superficie;
                    $info->save();

                    if ($nombre_piso === 'Piso 001') {
                        $piso_uno = $superficie + $piso_uno;

                        if ($nombre_recinto) {
                            $existe_nombre_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'NOMBRE RECINTO');

                            $nombre_recinto_id = $existe_nombre_recinto->infra_other_data_attribute_id;
                            if ($existe_nombre_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_recinto_id, $nombre_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($codigo_subrecinto) {
                            $existe_codigo_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $codigo_subrecinto_id = $existe_codigo_subrecinto->infra_other_data_attribute_id;
                            if ($existe_codigo_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $codigo_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($codigo_recinto_id, $codigo_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $codigo_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($nombre_subrecinto) {
                            $existe_nombre_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'NOMBRE SUBRECINTO');
                            $nombre_subrecinto_id = $existe_nombre_subrecinto->infra_other_data_attribute_id;
                            if ($existe_nombre_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_subrecinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_subrecinto_id, $nombre_subrecinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($organismo) {

                            $existe_organismo = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ORGANISMO');
                            $organismo_id = $existe_organismo->infra_other_data_attribute_id;
                            if ($existe_organismo->infra_other_data_attribute_type == 5) {
                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $organismo_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($organismo_id, $organismo);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $organismo_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $organismo_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $organismo;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($departamento) {
                            $existe_departamento = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'DEPARTAMENTO');
                            $departamento_id = $existe_departamento->infra_other_data_attribute_id;
                            if ($existe_departamento->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $departamento_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($departamento_id, $departamento);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $departamento_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $departamento_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $departamento;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($unidad) {
                            $existe_unidad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'UNIDAD');
                            $unidad_id = $existe_unidad->infra_other_data_attribute_id;

                            if ($existe_unidad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $unidad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($unidad_id, $unidad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $unidad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $unidad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $unidad;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($actividad) {
                            $existe_actividad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ACTIVIDAD');
                            $actividad_id = $existe_actividad->infra_other_data_attribute_id;

                            if ($existe_actividad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $actividad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($actividad_id, $actividad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $actividad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $actividad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $actividad;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso) {
                            $existe_uso = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO');
                            $uso_id = $existe_uso->infra_other_data_attribute_id;

                            if ($existe_uso->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_id, $uso);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso_particular) {
                            $existe_uso_particular = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO PARTICULAR');
                            $uso_particular_id = $existe_uso_particular->infra_other_data_attribute_id;

                            if ($existe_uso_particular->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_particular_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_particular_id, $uso_particular);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso_particular;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($propiedad_recinto) {
                            $existe_propiedad_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROPIEDAD RECINTO');
                            $propiedad_recinto_id = $existe_propiedad_recinto->infra_other_data_attribute_id;

                            if ($existe_propiedad_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $propiedad_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($propiedad_recinto_id, $propiedad_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $propiedad_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estatus_recinto) {
                            $existe_estatus_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTATUS RECINTO');
                            $estatus_recinto_id = $existe_estatus_recinto->infra_other_data_attribute_id;

                            if ($existe_estatus_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estatus_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estatus_recinto_id, $estatus_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estatus_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estado_recinto) {
                            $existe_estado_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTADO RECINTO');
                            $estado_recinto_id = $existe_estado_recinto->infra_other_data_attribute_id;

                            if ($existe_estado_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estado_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estado_recinto_id, $estado_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estado_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($cantidad_usuarios) {
                            $existe_cantidad_usuarios = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CANTIDAD USUARIOS');
                            $cantidad_usuarios_id = $existe_cantidad_usuarios->infra_other_data_attribute_id;

                            if ($existe_cantidad_usuarios->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $cantidad_usuarios_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($cantidad_usuarios_id, $cantidad_usuarios);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $cantidad_usuarios;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 25;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($ventanas) {
                            $existe_ventanas = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'VENTANAS');
                            $ventanas_id = $existe_ventanas->infra_other_data_attribute_id;

                            if ($existe_ventanas->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $ventanas_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($ventanas_id, $ventanas);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $ventanas;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($aire_acondicionado) {
                            $existe_aire_acondicionado = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'AIRE ACONDICIONADO');
                            $aire_acondicionado_id = $existe_aire_acondicionado->infra_other_data_attribute_id;

                            if ($existe_aire_acondicionado->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $aire_acondicionado_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($aire_acondicionado_id, $aire_acondicionado);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $aire_acondicionado;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($calefaccion) {
                            $existe_calefaccion = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CALEFACCION');
                            $calefaccion_id = $existe_calefaccion->infra_other_data_attribute_id;

                            if ($existe_calefaccion->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $calefaccion_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($calefaccion_id, $calefaccion);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $calefaccion;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($luminarias) {
                            $existe_luminarias = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'LUMINARIAS');
                            $luminarias_id = $existe_luminarias->infra_other_data_attribute_id;

                            if ($existe_luminarias->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $luminarias_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($luminarias_id, $luminarias);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {

                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $luminarias;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 29;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($enchufes) {
                            $existe_enchufes = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ENCHUFES');
                            $enchufes_id = $existe_enchufes->infra_other_data_attribute_id;

                            if ($existe_enchufes->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $enchufes_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($enchufes_id, $enchufes);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $enchufes;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 30;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }


                        if ($punto_red) {
                            $existe_punto_red = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PUNTOS DE RED');
                            $punto_red_id = $existe_punto_red->infra_other_data_attribute_id;

                            if ($existe_punto_red->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $punto_red_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($punto_red_id, $punto_red);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                if ($punto_red === '0') {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = "0";
                                    $infra_other->save();
                                    $cont++;
                                } else {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = $punto_red;
                                    $infra_other->save();
                                    $cont++;
                                }
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 31;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($proyector) {
                            $existe_proyector = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROYECTOR');
                            $proyector_id = $existe_proyector->infra_other_data_attribute_id;

                            if ($existe_proyector->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $proyector_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($proyector_id, $proyector);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $proyector_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $proyector_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $proyector;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($wifi) {
                            $existe_wifi = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'WIFI');
                            $wifi_id = $existe_wifi->infra_other_data_attribute_id;


                            if ($existe_wifi->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $wifi_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($wifi_id, $wifi);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $wifi_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $wifi_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $wifi;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($observacion_recinto) {
                            $existe_observacion_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'OBSERVACION RECINTO');
                            $observacion_recinto_id = $existe_observacion_recinto->infra_other_data_attribute_id;

                            if ($existe_observacion_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $observacion_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($observacion_recinto_id, $observacion_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $observacion_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        $infra_other = new InfraOtherDataValue();
                        $infra_other->infra_other_data_attribute_id = 38;
                        $infra_other->node_id = $node->node_id;
                        $infra_other->infra_other_data_value_value = "2013-10-15";
                        $infra_other->save();
                    }

                    if ($nombre_piso === 'Piso 002') {
                        $piso_dos = $superficie + $piso_dos;



                        if ($nombre_recinto) {
                            $existe_nombre_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'NOMBRE RECINTO');

                            $nombre_recinto_id = $existe_nombre_recinto->infra_other_data_attribute_id;
                            if ($existe_codigo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_recinto_id, $nombre_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($codigo_subrecinto) {
                            $existe_codigo_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $codigo_subrecinto_id = $existe_codigo_subrecinto->infra_other_data_attribute_id;
                            if ($existe_codigo_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $codigo_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($codigo_recinto_id, $codigo_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $codigo_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($nombre_subrecinto) {
                            $existe_nombre_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $nombre_subrecinto_id = $existe_nombre_subrecinto->infra_other_data_attribute_id;
                            if ($existe_nombre_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_subrecinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_subrecinto_id, $nombre_subrecinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($organismo) {
                            $existe_organismo = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ORGANISMO');
                            $organismo_id = $existe_organismo->infra_other_data_attribute_id;
                            if ($existe_organismo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $organismo_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($organismo_id, $organismo);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $organismo_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $organismo_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $organismo;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($departamento) {
                            $existe_departamento = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'DEPARTAMENTO');
                            $departamento_id = $existe_departamento->infra_other_data_attribute_id;
                            if ($existe_departamento->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $departamento_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($departamento_id, $departamento);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $departamento_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $departamento_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $departamento;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($unidad) {
                            $existe_unidad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'UNIDAD');
                            $unidad_id = $existe_unidad->infra_other_data_attribute_id;

                            if ($existe_unidad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $unidad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($unidad_id, $unidad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $unidad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $unidad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $unidad;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($actividad) {
                            $existe_actividad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ACTIVIDAD');
                            $actividad_id = $existe_actividad->infra_other_data_attribute_id;

                            if ($existe_actividad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $actividad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($actividad_id, $actividad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $actividad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $actividad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $actividad;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso) {
                            $existe_uso = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO');
                            $uso_id = $existe_uso->infra_other_data_attribute_id;

                            if ($existe_uso->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_id, $uso);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso_particular) {
                            $existe_uso_particular = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO PARTICULAR');
                            $uso_particular_id = $existe_uso_particular->infra_other_data_attribute_id;

                            if ($existe_uso_particular->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_particular_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_particular_id, $uso_particular);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso_particular;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($propiedad_recinto) {
                            $existe_propiedad_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROPIEDAD RECINTO');
                            $propiedad_recinto_id = $existe_propiedad_recinto->infra_other_data_attribute_id;

                            if ($existe_propiedad_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $propiedad_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($propiedad_recinto_id, $propiedad_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $propiedad_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estatus_recinto) {
                            $existe_estatus_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTATUS RECINTO');
                            $estatus_recinto_id = $existe_estatus_recinto->infra_other_data_attribute_id;

                            if ($existe_estatus_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estatus_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estatus_recinto_id, $estatus_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estatus_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estado_recinto) {
                            $existe_estado_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTADO RECINTO');
                            $estado_recinto_id = $existe_estado_recinto->infra_other_data_attribute_id;

                            if ($existe_estado_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estado_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estado_recinto_id, $estado_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estado_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($cantidad_usuarios) {
                            $existe_cantidad_usuarios = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CANTIDAD USUARIOS');
                            $cantidad_usuarios_id = $existe_cantidad_usuarios->infra_other_data_attribute_id;

                            if ($existe_cantidad_usuarios->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $cantidad_usuarios_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($cantidad_usuarios_id, $cantidad_usuarios);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $cantidad_usuarios;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 25;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }


                        if ($ventanas) {
                            $existe_ventanas = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'VENTANAS');
                            $ventanas_id = $existe_ventanas->infra_other_data_attribute_id;

                            if ($existe_ventanas->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $ventanas_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($ventanas_id, $ventanas);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $ventanas;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($aire_acondicionado) {
                            $existe_aire_acondicionado = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'AIRE ACONDICIONADO');
                            $aire_acondicionado_id = $existe_aire_acondicionado->infra_other_data_attribute_id;

                            if ($existe_aire_acondicionado->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $aire_acondicionado_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($aire_acondicionado_id, $aire_acondicionado);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $aire_acondicionado;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($calefaccion) {
                            $existe_calefaccion = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CALEFACCION');
                            $calefaccion_id = $existe_calefaccion->infra_other_data_attribute_id;

                            if ($existe_calefaccion->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $calefaccion_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($calefaccion_id, $calefaccion);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $calefaccion;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($luminarias) {
                            $existe_luminarias = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'LUMINARIAS');
                            $luminarias_id = $existe_luminarias->infra_other_data_attribute_id;

                            if ($existe_luminarias->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $luminarias_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($luminarias_id, $luminarias);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {

                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $luminarias;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 29;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($enchufes) {
                            $existe_enchufes = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ENCHUFES');
                            $enchufes_id = $existe_enchufes->infra_other_data_attribute_id;

                            if ($existe_enchufes->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $enchufes_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($enchufes_id, $enchufes);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $enchufes;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 30;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($punto_red) {
                            $existe_punto_red = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PUNTOS DE RED');
                            $punto_red_id = $existe_punto_red->infra_other_data_attribute_id;

                            if ($existe_punto_red->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $punto_red_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($punto_red_id, $punto_red);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                if ($punto_red === '0') {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = "0";
                                    $infra_other->save();
                                    $cont++;
                                } else {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = $punto_red;
                                    $infra_other->save();
                                    $cont++;
                                }
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 31;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($proyector) {
                            $existe_proyector = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROYECTOR');
                            $proyector_id = $existe_proyector->infra_other_data_attribute_id;

                            if ($existe_proyector->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $proyector_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($proyector_id, $proyector);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $proyector_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $proyector_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $proyector;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($wifi) {
                            $existe_wifi = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'WIFI');
                            $wifi_id = $existe_wifi->infra_other_data_attribute_id;

                            if ($existe_wifi->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $wifi_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($wifi_id, $wifi);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $wifi_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $wifi_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $wifi;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($observacion_recinto) {
                            $existe_observacion_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'OBSERVACION RECINTO');
                            $observacion_recinto_id = $existe_observacion_recinto->infra_other_data_attribute_id;

                            if ($existe_observacion_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $observacion_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($observacion_recinto_id, $observacion_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $observacion_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        $infra_other = new InfraOtherDataValue();
                        $infra_other->infra_other_data_attribute_id = 38;
                        $infra_other->node_id = $node->node_id;
                        $infra_other->infra_other_data_value_value = "2013-10-15";
                        $infra_other->save();
                    }


                    if ($nombre_piso === 'Piso 003') {
                        $piso_tres = $superficie + $piso_tres;

                        if ($nombre_recinto) {
                            $existe_nombre_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'NOMBRE RECINTO');

                            $nombre_recinto_id = $existe_nombre_recinto->infra_other_data_attribute_id;
                            if ($existe_codigo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_recinto_id, $nombre_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($codigo_subrecinto) {
                            $existe_codigo_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $codigo_subrecinto_id = $existe_codigo_subrecinto->infra_other_data_attribute_id;
                            if ($existe_codigo_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $codigo_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($codigo_recinto_id, $codigo_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $codigo_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($nombre_subrecinto) {
                            $existe_nombre_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $nombre_subrecinto_id = $existe_nombre_subrecinto->infra_other_data_attribute_id;
                            if ($existe_nombre_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_subrecinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_subrecinto_id, $nombre_subrecinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($organismo) {
                            $existe_organismo = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ORGANISMO');
                            $organismo_id = $existe_organismo->infra_other_data_attribute_id;
                            if ($existe_organismo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $organismo_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($organismo_id, $organismo);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $organismo_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $organismo_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $organismo;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($departamento) {
                            $existe_departamento = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'DEPARTAMENTO');
                            $departamento_id = $existe_departamento->infra_other_data_attribute_id;
                            if ($existe_departamento->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $departamento_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($departamento_id, $departamento);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $departamento_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $departamento_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $departamento;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($unidad) {
                            $existe_unidad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'UNIDAD');
                            $unidad_id = $existe_unidad->infra_other_data_attribute_id;

                            if ($existe_unidad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $unidad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($unidad_id, $unidad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $unidad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $unidad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $unidad;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($actividad) {
                            $existe_actividad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ACTIVIDAD');
                            $actividad_id = $existe_actividad->infra_other_data_attribute_id;

                            if ($existe_actividad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $actividad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($actividad_id, $actividad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $actividad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $actividad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $actividad;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso) {
                            $existe_uso = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO');
                            $uso_id = $existe_uso->infra_other_data_attribute_id;

                            if ($existe_uso->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_id, $uso);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso_particular) {
                            $existe_uso_particular = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO PARTICULAR');
                            $uso_particular_id = $existe_uso_particular->infra_other_data_attribute_id;

                            if ($existe_uso_particular->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_particular_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_particular_id, $uso_particular);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso_particular;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($propiedad_recinto) {
                            $existe_propiedad_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROPIEDAD RECINTO');
                            $propiedad_recinto_id = $existe_propiedad_recinto->infra_other_data_attribute_id;

                            if ($existe_propiedad_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $propiedad_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($propiedad_recinto_id, $propiedad_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $propiedad_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estatus_recinto) {
                            $existe_estatus_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTATUS RECINTO');
                            $estatus_recinto_id = $existe_estatus_recinto->infra_other_data_attribute_id;

                            if ($existe_estatus_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estatus_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estatus_recinto_id, $estatus_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estatus_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estado_recinto) {
                            $existe_estado_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTADO RECINTO');
                            $estado_recinto_id = $existe_estado_recinto->infra_other_data_attribute_id;

                            if ($existe_estado_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estado_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estado_recinto_id, $estado_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estado_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($cantidad_usuarios) {
                            $existe_cantidad_usuarios = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CANTIDAD USUARIOS');
                            $cantidad_usuarios_id = $existe_cantidad_usuarios->infra_other_data_attribute_id;

                            if ($existe_cantidad_usuarios->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $cantidad_usuarios_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($cantidad_usuarios_id, $cantidad_usuarios);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $cantidad_usuarios;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 25;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($ventanas) {
                            $existe_ventanas = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'VENTANAS');
                            $ventanas_id = $existe_ventanas->infra_other_data_attribute_id;

                            if ($existe_ventanas->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $ventanas_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($ventanas_id, $ventanas);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $ventanas;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($aire_acondicionado) {
                            $existe_aire_acondicionado = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'AIRE ACONDICIONADO');
                            $aire_acondicionado_id = $existe_aire_acondicionado->infra_other_data_attribute_id;

                            if ($existe_aire_acondicionado->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $aire_acondicionado_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($aire_acondicionado_id, $aire_acondicionado);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $aire_acondicionado;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($calefaccion) {
                            $existe_calefaccion = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CALEFACCION');
                            $calefaccion_id = $existe_calefaccion->infra_other_data_attribute_id;

                            if ($existe_calefaccion->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $calefaccion_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($calefaccion_id, $calefaccion);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $calefaccion;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($luminarias) {
                            $existe_luminarias = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'LUMINARIAS');
                            $luminarias_id = $existe_luminarias->infra_other_data_attribute_id;

                            if ($existe_luminarias->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $luminarias_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($luminarias_id, $luminarias);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {

                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $luminarias;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 29;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($enchufes) {
                            $existe_enchufes = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ENCHUFES');
                            $enchufes_id = $existe_enchufes->infra_other_data_attribute_id;

                            if ($existe_enchufes->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $enchufes_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($enchufes_id, $enchufes);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $enchufes;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 30;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($punto_red) {
                            $existe_punto_red = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PUNTOS DE RED');
                            $punto_red_id = $existe_punto_red->infra_other_data_attribute_id;

                            if ($existe_punto_red->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $punto_red_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($punto_red_id, $punto_red);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                if ($punto_red === '0') {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = "0";
                                    $infra_other->save();
                                    $cont++;
                                } else {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = $punto_red;
                                    $infra_other->save();
                                    $cont++;
                                }
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 31;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($proyector) {
                            $existe_proyector = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROYECTOR');
                            $proyector_id = $existe_proyector->infra_other_data_attribute_id;

                            if ($existe_proyector->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $proyector_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($proyector_id, $proyector);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $proyector_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $proyector_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $proyector;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($wifi) {
                            $existe_wifi = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'WIFI');
                            $wifi_id = $existe_wifi->infra_other_data_attribute_id;

                            if ($existe_wifi->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $wifi_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($wifi_id, $wifi);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $wifi_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $wifi_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $wifi;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($observacion_recinto) {
                            $existe_observacion_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'OBSERVACION RECINTO');
                            $observacion_recinto_id = $existe_observacion_recinto->infra_other_data_attribute_id;

                            if ($existe_observacion_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $observacion_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($observacion_recinto_id, $observacion_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $observacion_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        $infra_other = new InfraOtherDataValue();
                        $infra_other->infra_other_data_attribute_id = 38;
                        $infra_other->node_id = $node->node_id;
                        $infra_other->infra_other_data_value_value = "2013-10-15";
                        $infra_other->save();
                    }

                    if ($nombre_piso === 'Piso 004') {
                        $piso_cuatro = $superficie + $piso_cuatro;

                        if ($nombre_recinto) {
                            $existe_nombre_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'NOMBRE RECINTO');

                            $nombre_recinto_id = $existe_nombre_recinto->infra_other_data_attribute_id;
                            if ($existe_codigo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_recinto_id, $nombre_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($codigo_subrecinto) {
                            $existe_codigo_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $codigo_subrecinto_id = $existe_codigo_subrecinto->infra_other_data_attribute_id;
                            if ($existe_codigo_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $codigo_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($codigo_recinto_id, $codigo_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $codigo_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($nombre_subrecinto) {
                            $existe_nombre_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $nombre_subrecinto_id = $existe_nombre_subrecinto->infra_other_data_attribute_id;
                            if ($existe_nombre_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_subrecinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_subrecinto_id, $nombre_subrecinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($organismo) {
                            $existe_organismo = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ORGANISMO');
                            $organismo_id = $existe_organismo->infra_other_data_attribute_id;
                            if ($existe_organismo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $organismo_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($organismo_id, $organismo);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $organismo_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $organismo_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $organismo;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($departamento) {
                            $existe_departamento = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'DEPARTAMENTO');
                            $departamento_id = $existe_departamento->infra_other_data_attribute_id;
                            if ($existe_departamento->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $departamento_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($departamento_id, $departamento);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $departamento_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $departamento_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $departamento;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($unidad) {
                            $existe_unidad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'UNIDAD');
                            $unidad_id = $existe_unidad->infra_other_data_attribute_id;

                            if ($existe_unidad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $unidad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($unidad_id, $unidad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $unidad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $unidad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $unidad;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($actividad) {
                            $existe_actividad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ACTIVIDAD');
                            $actividad_id = $existe_actividad->infra_other_data_attribute_id;

                            if ($existe_actividad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $actividad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($actividad_id, $actividad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $actividad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $actividad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $actividad;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso) {
                            $existe_uso = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO');
                            $uso_id = $existe_uso->infra_other_data_attribute_id;

                            if ($existe_uso->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_id, $uso);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso_particular) {
                            $existe_uso_particular = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO PARTICULAR');
                            $uso_particular_id = $existe_uso_particular->infra_other_data_attribute_id;

                            if ($existe_uso_particular->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_particular_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_particular_id, $uso_particular);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso_particular;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($propiedad_recinto) {
                            $existe_propiedad_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROPIEDAD RECINTO');
                            $propiedad_recinto_id = $existe_propiedad_recinto->infra_other_data_attribute_id;

                            if ($existe_propiedad_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $propiedad_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($propiedad_recinto_id, $propiedad_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $propiedad_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estatus_recinto) {
                            $existe_estatus_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTATUS RECINTO');
                            $estatus_recinto_id = $existe_estatus_recinto->infra_other_data_attribute_id;

                            if ($existe_estatus_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estatus_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estatus_recinto_id, $estatus_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estatus_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estado_recinto) {
                            $existe_estado_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTADO RECINTO');
                            $estado_recinto_id = $existe_estado_recinto->infra_other_data_attribute_id;

                            if ($existe_estado_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estado_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estado_recinto_id, $estado_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estado_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($cantidad_usuarios) {
                            $existe_cantidad_usuarios = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CANTIDAD USUARIOS');
                            $cantidad_usuarios_id = $existe_cantidad_usuarios->infra_other_data_attribute_id;

                            if ($existe_cantidad_usuarios->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $cantidad_usuarios_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($cantidad_usuarios_id, $cantidad_usuarios);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $cantidad_usuarios;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 25;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($ventanas) {
                            $existe_ventanas = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'VENTANAS');
                            $ventanas_id = $existe_ventanas->infra_other_data_attribute_id;

                            if ($existe_ventanas->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $ventanas_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($ventanas_id, $ventanas);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $ventanas;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($aire_acondicionado) {
                            $existe_aire_acondicionado = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'AIRE ACONDICIONADO');
                            $aire_acondicionado_id = $existe_aire_acondicionado->infra_other_data_attribute_id;

                            if ($existe_aire_acondicionado->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $aire_acondicionado_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($aire_acondicionado_id, $aire_acondicionado);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $aire_acondicionado;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($calefaccion) {
                            $existe_calefaccion = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CALEFACCION');
                            $calefaccion_id = $existe_calefaccion->infra_other_data_attribute_id;

                            if ($existe_calefaccion->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $calefaccion_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($calefaccion_id, $calefaccion);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $calefaccion;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($luminarias) {
                            $existe_luminarias = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'LUMINARIAS');
                            $luminarias_id = $existe_luminarias->infra_other_data_attribute_id;

                            if ($existe_luminarias->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $luminarias_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($luminarias_id, $luminarias);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {

                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $luminarias;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 29;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($enchufes) {
                            $existe_enchufes = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ENCHUFES');
                            $enchufes_id = $existe_enchufes->infra_other_data_attribute_id;

                            if ($existe_enchufes->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $enchufes_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($enchufes_id, $enchufes);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $enchufes;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 30;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }


                        if ($punto_red) {
                            $existe_punto_red = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PUNTOS DE RED');
                            $punto_red_id = $existe_punto_red->infra_other_data_attribute_id;

                            if ($existe_punto_red->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $punto_red_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($punto_red_id, $punto_red);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                if ($punto_red === '0') {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = "0";
                                    $infra_other->save();
                                    $cont++;
                                } else {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = $punto_red;
                                    $infra_other->save();
                                    $cont++;
                                }
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 31;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($proyector) {
                            $existe_proyector = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROYECTOR');
                            $proyector_id = $existe_proyector->infra_other_data_attribute_id;

                            if ($existe_proyector->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $proyector_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($proyector_id, $proyector);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $proyector_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $proyector_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $proyector;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($wifi) {
                            $existe_wifi = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'WIFI');
                            $wifi_id = $existe_wifi->infra_other_data_attribute_id;

                            if ($existe_wifi->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $wifi_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($wifi_id, $wifi);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $wifi_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $wifi_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $wifi;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($observacion_recinto) {
                            $existe_observacion_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'OBSERVACION RECINTO');
                            $observacion_recinto_id = $existe_observacion_recinto->infra_other_data_attribute_id;

                            if ($existe_observacion_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $observacion_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($observacion_recinto_id, $observacion_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $observacion_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

//                          

                        $infra_other = new InfraOtherDataValue();
                        $infra_other->infra_other_data_attribute_id = 38;
                        $infra_other->node_id = $node->node_id;
                        $infra_other->infra_other_data_value_value = "2013-10-15";
                        $infra_other->save();
                    }

                    if ($nombre_piso === 'Piso 005') {
                        $piso_cinco = $superficie + $piso_cinco;

                        if ($nombre_recinto) {
                            $existe_nombre_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'NOMBRE RECINTO');

                            $nombre_recinto_id = $existe_nombre_recinto->infra_other_data_attribute_id;
                            if ($existe_codigo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_recinto_id, $nombre_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($codigo_subrecinto) {
                            $existe_codigo_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $codigo_subrecinto_id = $existe_codigo_subrecinto->infra_other_data_attribute_id;
                            if ($existe_codigo_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $codigo_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($codigo_recinto_id, $codigo_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $codigo_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($nombre_subrecinto) {
                            $existe_nombre_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $nombre_subrecinto_id = $existe_nombre_subrecinto->infra_other_data_attribute_id;
                            if ($existe_nombre_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_subrecinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_subrecinto_id, $nombre_subrecinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($organismo) {
                            $existe_organismo = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ORGANISMO');
                            $organismo_id = $existe_organismo->infra_other_data_attribute_id;
                            if ($existe_organismo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $organismo_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($organismo_id, $organismo);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $organismo_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $organismo_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $organismo;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($departamento) {
                            $existe_departamento = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'DEPARTAMENTO');
                            $departamento_id = $existe_departamento->infra_other_data_attribute_id;
                            if ($existe_departamento->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $departamento_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($departamento_id, $departamento);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $departamento_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $departamento_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $departamento;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($unidad) {
                            $existe_unidad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'UNIDAD');
                            $unidad_id = $existe_unidad->infra_other_data_attribute_id;

                            if ($existe_unidad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $unidad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($unidad_id, $unidad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $unidad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $unidad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $unidad;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($actividad) {
                            $existe_actividad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ACTIVIDAD');
                            $actividad_id = $existe_actividad->infra_other_data_attribute_id;

                            if ($existe_actividad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $actividad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($actividad_id, $actividad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $actividad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $actividad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $actividad;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso) {
                            $existe_uso = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO');
                            $uso_id = $existe_uso->infra_other_data_attribute_id;

                            if ($existe_uso->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_id, $uso);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso_particular) {
                            $existe_uso_particular = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO PARTICULAR');
                            $uso_particular_id = $existe_uso_particular->infra_other_data_attribute_id;

                            if ($existe_uso_particular->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_particular_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_particular_id, $uso_particular);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso_particular;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($propiedad_recinto) {
                            $existe_propiedad_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROPIEDAD RECINTO');
                            $propiedad_recinto_id = $existe_propiedad_recinto->infra_other_data_attribute_id;

                            if ($existe_propiedad_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $propiedad_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($propiedad_recinto_id, $propiedad_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $propiedad_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estatus_recinto) {
                            $existe_estatus_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTATUS RECINTO');
                            $estatus_recinto_id = $existe_estatus_recinto->infra_other_data_attribute_id;

                            if ($existe_estatus_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estatus_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estatus_recinto_id, $estatus_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estatus_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estado_recinto) {
                            $existe_estado_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTADO RECINTO');
                            $estado_recinto_id = $existe_estado_recinto->infra_other_data_attribute_id;

                            if ($existe_estado_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estado_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estado_recinto_id, $estado_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estado_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($cantidad_usuarios) {
                            $existe_cantidad_usuarios = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CANTIDAD USUARIOS');
                            $cantidad_usuarios_id = $existe_cantidad_usuarios->infra_other_data_attribute_id;

                            if ($existe_cantidad_usuarios->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $cantidad_usuarios_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($cantidad_usuarios_id, $cantidad_usuarios);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $cantidad_usuarios;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 25;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($ventanas) {
                            $existe_ventanas = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'VENTANAS');
                            $ventanas_id = $existe_ventanas->infra_other_data_attribute_id;

                            if ($existe_ventanas->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $ventanas_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($ventanas_id, $ventanas);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $ventanas;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($aire_acondicionado) {
                            $existe_aire_acondicionado = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'AIRE ACONDICIONADO');
                            $aire_acondicionado_id = $existe_aire_acondicionado->infra_other_data_attribute_id;

                            if ($existe_aire_acondicionado->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $aire_acondicionado_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($aire_acondicionado_id, $aire_acondicionado);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $aire_acondicionado;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($calefaccion) {
                            $existe_calefaccion = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CALEFACCION');
                            $calefaccion_id = $existe_calefaccion->infra_other_data_attribute_id;

                            if ($existe_calefaccion->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $calefaccion_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($calefaccion_id, $calefaccion);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $calefaccion;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($luminarias) {
                            $existe_luminarias = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'LUMINARIAS');
                            $luminarias_id = $existe_luminarias->infra_other_data_attribute_id;

                            if ($existe_luminarias->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $luminarias_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($luminarias_id, $luminarias);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {

                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $luminarias;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 29;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($enchufes) {
                            $existe_enchufes = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ENCHUFES');
                            $enchufes_id = $existe_enchufes->infra_other_data_attribute_id;

                            if ($existe_enchufes->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $enchufes_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($enchufes_id, $enchufes);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $enchufes;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 30;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }


                        if ($punto_red) {
                            $existe_punto_red = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PUNTOS DE RED');
                            $punto_red_id = $existe_punto_red->infra_other_data_attribute_id;

                            if ($existe_punto_red->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $punto_red_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($punto_red_id, $punto_red);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                if ($punto_red === '0') {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = "0";
                                    $infra_other->save();
                                    $cont++;
                                } else {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = $punto_red;
                                    $infra_other->save();
                                    $cont++;
                                }
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 31;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($proyector) {
                            $existe_proyector = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROYECTOR');
                            $proyector_id = $existe_proyector->infra_other_data_attribute_id;

                            if ($existe_proyector->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $proyector_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($proyector_id, $proyector);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $proyector_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $proyector_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $proyector;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($wifi) {
                            $existe_wifi = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'WIFI');
                            $wifi_id = $existe_wifi->infra_other_data_attribute_id;

                            if ($existe_wifi->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $wifi_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($wifi_id, $wifi);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $wifi_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $wifi_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $wifi;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($observacion_recinto) {
                            $existe_observacion_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'OBSERVACION RECINTO');
                            $observacion_recinto_id = $existe_observacion_recinto->infra_other_data_attribute_id;

                            if ($existe_observacion_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $observacion_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($observacion_recinto_id, $observacion_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $observacion_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        $infra_other = new InfraOtherDataValue();
                        $infra_other->infra_other_data_attribute_id = 38;
                        $infra_other->node_id = $node->node_id;
                        $infra_other->infra_other_data_value_value = "2013-10-15";
                        $infra_other->save();
                    }

                    if ($nombre_piso === 'ZOCALO 001') {
                        $piso_seis = $superficie + $piso_seis;

                        if ($nombre_recinto) {
                            $existe_nombre_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'NOMBRE RECINTO');

                            $nombre_recinto_id = $existe_nombre_recinto->infra_other_data_attribute_id;
                            if ($existe_codigo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_recinto_id, $nombre_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($codigo_subrecinto) {
                            $existe_codigo_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $codigo_subrecinto_id = $existe_codigo_subrecinto->infra_other_data_attribute_id;
                            if ($existe_codigo_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $codigo_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($codigo_recinto_id, $codigo_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $codigo_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($nombre_subrecinto) {
                            $existe_nombre_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $nombre_subrecinto_id = $existe_nombre_subrecinto->infra_other_data_attribute_id;
                            if ($existe_nombre_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_subrecinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_subrecinto_id, $nombre_subrecinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($organismo) {
                            $existe_organismo = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ORGANISMO');
                            $organismo_id = $existe_organismo->infra_other_data_attribute_id;
                            if ($existe_organismo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $organismo_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($organismo_id, $organismo);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $organismo_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $organismo_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $organismo;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($departamento) {
                            $existe_departamento = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'DEPARTAMENTO');
                            $departamento_id = $existe_departamento->infra_other_data_attribute_id;
                            if ($existe_departamento->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $departamento_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($departamento_id, $departamento);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $departamento_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $departamento_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $departamento;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($unidad) {
                            $existe_unidad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'UNIDAD');
                            $unidad_id = $existe_unidad->infra_other_data_attribute_id;

                            if ($existe_unidad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $unidad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($unidad_id, $unidad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $unidad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $unidad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $unidad;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($actividad) {
                            $existe_actividad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ACTIVIDAD');
                            $actividad_id = $existe_actividad->infra_other_data_attribute_id;

                            if ($existe_actividad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $actividad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($actividad_id, $actividad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $actividad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $actividad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $actividad;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso) {
                            $existe_uso = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO');
                            $uso_id = $existe_uso->infra_other_data_attribute_id;

                            if ($existe_uso->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_id, $uso);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso_particular) {
                            $existe_uso_particular = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO PARTICULAR');
                            $uso_particular_id = $existe_uso_particular->infra_other_data_attribute_id;

                            if ($existe_uso_particular->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_particular_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_particular_id, $uso_particular);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso_particular;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($propiedad_recinto) {
                            $existe_propiedad_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROPIEDAD RECINTO');
                            $propiedad_recinto_id = $existe_propiedad_recinto->infra_other_data_attribute_id;

                            if ($existe_propiedad_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $propiedad_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($propiedad_recinto_id, $propiedad_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $propiedad_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estatus_recinto) {
                            $existe_estatus_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTATUS RECINTO');
                            $estatus_recinto_id = $existe_estatus_recinto->infra_other_data_attribute_id;

                            if ($existe_estatus_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estatus_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estatus_recinto_id, $estatus_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estatus_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estado_recinto) {
                            $existe_estado_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTADO RECINTO');
                            $estado_recinto_id = $existe_estado_recinto->infra_other_data_attribute_id;

                            if ($existe_estado_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estado_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estado_recinto_id, $estado_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estado_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($cantidad_usuarios) {
                            $existe_cantidad_usuarios = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CANTIDAD USUARIOS');
                            $cantidad_usuarios_id = $existe_cantidad_usuarios->infra_other_data_attribute_id;

                            if ($existe_cantidad_usuarios->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $cantidad_usuarios_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($cantidad_usuarios_id, $cantidad_usuarios);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $cantidad_usuarios;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 25;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($ventanas) {
                            $existe_ventanas = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'VENTANAS');
                            $ventanas_id = $existe_ventanas->infra_other_data_attribute_id;

                            if ($existe_ventanas->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $ventanas_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($ventanas_id, $ventanas);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $ventanas;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($aire_acondicionado) {
                            $existe_aire_acondicionado = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'AIRE ACONDICIONADO');
                            $aire_acondicionado_id = $existe_aire_acondicionado->infra_other_data_attribute_id;

                            if ($existe_aire_acondicionado->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $aire_acondicionado_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($aire_acondicionado_id, $aire_acondicionado);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $aire_acondicionado;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($calefaccion) {
                            $existe_calefaccion = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CALEFACCION');
                            $calefaccion_id = $existe_calefaccion->infra_other_data_attribute_id;

                            if ($existe_calefaccion->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $calefaccion_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($calefaccion_id, $calefaccion);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $calefaccion;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($luminarias) {
                            $existe_luminarias = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'LUMINARIAS');
                            $luminarias_id = $existe_luminarias->infra_other_data_attribute_id;

                            if ($existe_luminarias->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $luminarias_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($luminarias_id, $luminarias);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {

                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $luminarias;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 29;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($enchufes) {
                            $existe_enchufes = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ENCHUFES');
                            $enchufes_id = $existe_enchufes->infra_other_data_attribute_id;

                            if ($existe_enchufes->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $enchufes_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($enchufes_id, $enchufes);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $enchufes;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 30;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }


                        if ($punto_red) {
                            $existe_punto_red = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PUNTOS DE RED');
                            $punto_red_id = $existe_punto_red->infra_other_data_attribute_id;

                            if ($existe_punto_red->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $punto_red_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($punto_red_id, $punto_red);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                if ($punto_red === '0') {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = "0";
                                    $infra_other->save();
                                    $cont++;
                                } else {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = $punto_red;
                                    $infra_other->save();
                                    $cont++;
                                }
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 31;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($proyector) {
                            $existe_proyector = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROYECTOR');
                            $proyector_id = $existe_proyector->infra_other_data_attribute_id;

                            if ($existe_proyector->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $proyector_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($proyector_id, $proyector);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $proyector_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $proyector_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $proyector;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($wifi) {
                            $existe_wifi = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'WIFI');
                            $wifi_id = $existe_wifi->infra_other_data_attribute_id;

                            if ($existe_wifi->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $wifi_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($wifi_id, $wifi);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $wifi_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $wifi_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $wifi;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($observacion_recinto) {
                            $existe_observacion_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'OBSERVACION RECINTO');
                            $observacion_recinto_id = $existe_observacion_recinto->infra_other_data_attribute_id;

                            if ($existe_observacion_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $observacion_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($observacion_recinto_id, $observacion_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $observacion_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        $infra_other = new InfraOtherDataValue();
                        $infra_other->infra_other_data_attribute_id = 38;
                        $infra_other->node_id = $node->node_id;
                        $infra_other->infra_other_data_value_value = "2013-10-15";
                        $infra_other->save();
                    }

                    if ($nombre_piso === 'SUBTERRANEO 001') {
                        $piso_siete = $superficie + $piso_siete;

                        if ($nombre_recinto) {
                            $existe_nombre_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'NOMBRE RECINTO');

                            $nombre_recinto_id = $existe_nombre_recinto->infra_other_data_attribute_id;
                            if ($existe_codigo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_recinto_id, $nombre_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($codigo_subrecinto) {
                            $existe_codigo_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $codigo_subrecinto_id = $existe_codigo_subrecinto->infra_other_data_attribute_id;
                            if ($existe_codigo_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $codigo_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($codigo_recinto_id, $codigo_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $codigo_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($nombre_subrecinto) {
                            $existe_nombre_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $nombre_subrecinto_id = $existe_nombre_subrecinto->infra_other_data_attribute_id;
                            if ($existe_nombre_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_subrecinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_subrecinto_id, $nombre_subrecinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($organismo) {
                            $existe_organismo = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ORGANISMO');
                            $organismo_id = $existe_organismo->infra_other_data_attribute_id;
                            if ($existe_organismo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $organismo_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($organismo_id, $organismo);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $organismo_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $organismo_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $organismo;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($departamento) {
                            $existe_departamento = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'DEPARTAMENTO');
                            $departamento_id = $existe_departamento->infra_other_data_attribute_id;
                            if ($existe_departamento->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $departamento_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($departamento_id, $departamento);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $departamento_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $departamento_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $departamento;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($unidad) {
                            $existe_unidad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'UNIDAD');
                            $unidad_id = $existe_unidad->infra_other_data_attribute_id;

                            if ($existe_unidad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $unidad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($unidad_id, $unidad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $unidad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $unidad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $unidad;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($actividad) {
                            $existe_actividad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ACTIVIDAD');
                            $actividad_id = $existe_actividad->infra_other_data_attribute_id;

                            if ($existe_actividad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $actividad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($actividad_id, $actividad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $actividad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $actividad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $actividad;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso) {
                            $existe_uso = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO');
                            $uso_id = $existe_uso->infra_other_data_attribute_id;

                            if ($existe_uso->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_id, $uso);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso_particular) {
                            $existe_uso_particular = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO PARTICULAR');
                            $uso_particular_id = $existe_uso_particular->infra_other_data_attribute_id;

                            if ($existe_uso_particular->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_particular_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_particular_id, $uso_particular);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso_particular;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($propiedad_recinto) {
                            $existe_propiedad_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROPIEDAD RECINTO');
                            $propiedad_recinto_id = $existe_propiedad_recinto->infra_other_data_attribute_id;

                            if ($existe_propiedad_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $propiedad_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($propiedad_recinto_id, $propiedad_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $propiedad_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estatus_recinto) {
                            $existe_estatus_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTATUS RECINTO');
                            $estatus_recinto_id = $existe_estatus_recinto->infra_other_data_attribute_id;

                            if ($existe_estatus_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estatus_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estatus_recinto_id, $estatus_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estatus_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estado_recinto) {
                            $existe_estado_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTADO RECINTO');
                            $estado_recinto_id = $existe_estado_recinto->infra_other_data_attribute_id;

                            if ($existe_estado_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estado_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estado_recinto_id, $estado_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estado_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($cantidad_usuarios) {
                            $existe_cantidad_usuarios = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CANTIDAD USUARIOS');
                            $cantidad_usuarios_id = $existe_cantidad_usuarios->infra_other_data_attribute_id;

                            if ($existe_cantidad_usuarios->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $cantidad_usuarios_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($cantidad_usuarios_id, $cantidad_usuarios);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $cantidad_usuarios;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 25;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($ventanas) {
                            $existe_ventanas = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'VENTANAS');
                            $ventanas_id = $existe_ventanas->infra_other_data_attribute_id;

                            if ($existe_ventanas->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $ventanas_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($ventanas_id, $ventanas);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $ventanas;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($aire_acondicionado) {
                            $existe_aire_acondicionado = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'AIRE ACONDICIONADO');
                            $aire_acondicionado_id = $existe_aire_acondicionado->infra_other_data_attribute_id;

                            if ($existe_aire_acondicionado->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $aire_acondicionado_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($aire_acondicionado_id, $aire_acondicionado);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $aire_acondicionado;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($calefaccion) {
                            $existe_calefaccion = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CALEFACCION');
                            $calefaccion_id = $existe_calefaccion->infra_other_data_attribute_id;

                            if ($existe_calefaccion->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $calefaccion_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($calefaccion_id, $calefaccion);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $calefaccion;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($luminarias) {
                            $existe_luminarias = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'LUMINARIAS');
                            $luminarias_id = $existe_luminarias->infra_other_data_attribute_id;

                            if ($existe_luminarias->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $luminarias_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($luminarias_id, $luminarias);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {

                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $luminarias;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 29;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($enchufes) {
                            $existe_enchufes = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ENCHUFES');
                            $enchufes_id = $existe_enchufes->infra_other_data_attribute_id;

                            if ($existe_enchufes->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $enchufes_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($enchufes_id, $enchufes);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $enchufes;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 30;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }


                        if ($punto_red) {
                            $existe_punto_red = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PUNTOS DE RED');
                            $punto_red_id = $existe_punto_red->infra_other_data_attribute_id;

                            if ($existe_punto_red->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $punto_red_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($punto_red_id, $punto_red);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                if ($punto_red === '0') {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = "0";
                                    $infra_other->save();
                                    $cont++;
                                } else {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = $punto_red;
                                    $infra_other->save();
                                    $cont++;
                                }
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 31;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($proyector) {
                            $existe_proyector = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROYECTOR');
                            $proyector_id = $existe_proyector->infra_other_data_attribute_id;

                            if ($existe_proyector->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $proyector_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($proyector_id, $proyector);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $proyector_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $proyector_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $proyector;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($wifi) {
                            $existe_wifi = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'WIFI');
                            $wifi_id = $existe_wifi->infra_other_data_attribute_id;

                            if ($existe_wifi->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $wifi_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($wifi_id, $wifi);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $wifi_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $wifi_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $wifi;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($observacion_recinto) {
                            $existe_observacion_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'OBSERVACION RECINTO');
                            $observacion_recinto_id = $existe_observacion_recinto->infra_other_data_attribute_id;

                            if ($existe_observacion_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $observacion_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($observacion_recinto_id, $observacion_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $observacion_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        $infra_other = new InfraOtherDataValue();
                        $infra_other->infra_other_data_attribute_id = 38;
                        $infra_other->node_id = $node->node_id;
                        $infra_other->infra_other_data_value_value = "2013-10-15";
                        $infra_other->save();
                    }
                    if ($nombre_piso === 'SUBTERRANEO 002') {
                        $piso_ocho = $superficie + $piso_ocho;

                        if ($nombre_recinto) {
                            $existe_nombre_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'NOMBRE RECINTO');

                            $nombre_recinto_id = $existe_nombre_recinto->infra_other_data_attribute_id;
                            if ($existe_codigo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_recinto_id, $nombre_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($codigo_subrecinto) {
                            $existe_codigo_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $codigo_subrecinto_id = $existe_codigo_subrecinto->infra_other_data_attribute_id;
                            if ($existe_codigo_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $codigo_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($codigo_recinto_id, $codigo_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $codigo_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $codigo_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($nombre_subrecinto) {
                            $existe_nombre_subrecinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CODIGO SUBRECINTO');
                            $nombre_subrecinto_id = $existe_nombre_subrecinto->infra_other_data_attribute_id;
                            if ($existe_nombre_subrecinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $nombre_subrecinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($nombre_subrecinto_id, $nombre_subrecinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $nombre_subrecinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $nombre_subrecinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($organismo) {
                            $existe_organismo = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ORGANISMO');
                            $organismo_id = $existe_organismo->infra_other_data_attribute_id;
                            if ($existe_organismo->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $organismo_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($organismo_id, $organismo);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $organismo_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $organismo_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $organismo;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($departamento) {
                            $existe_departamento = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'DEPARTAMENTO');
                            $departamento_id = $existe_departamento->infra_other_data_attribute_id;
                            if ($existe_departamento->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $departamento_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($departamento_id, $departamento);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $departamento_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $departamento_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $departamento;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($unidad) {
                            $existe_unidad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'UNIDAD');
                            $unidad_id = $existe_unidad->infra_other_data_attribute_id;

                            if ($existe_unidad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $unidad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($unidad_id, $unidad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $unidad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $unidad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $unidad;
                                $infra_other->save();
                                $cont++;
                            }
                        }


                        if ($actividad) {
                            $existe_actividad = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ACTIVIDAD');
                            $actividad_id = $existe_actividad->infra_other_data_attribute_id;

                            if ($existe_actividad->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $actividad_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($actividad_id, $actividad);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $actividad_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $actividad_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $actividad;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso) {
                            $existe_uso = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO');
                            $uso_id = $existe_uso->infra_other_data_attribute_id;

                            if ($existe_uso->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_id, $uso);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($uso_particular) {
                            $existe_uso_particular = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'USO PARTICULAR');
                            $uso_particular_id = $existe_uso_particular->infra_other_data_attribute_id;

                            if ($existe_uso_particular->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $uso_particular_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($uso_particular_id, $uso_particular);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $uso_particular_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $uso_particular;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($propiedad_recinto) {
                            $existe_propiedad_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROPIEDAD RECINTO');
                            $propiedad_recinto_id = $existe_propiedad_recinto->infra_other_data_attribute_id;

                            if ($existe_propiedad_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $propiedad_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($propiedad_recinto_id, $propiedad_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $propiedad_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $propiedad_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estatus_recinto) {
                            $existe_estatus_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTATUS RECINTO');
                            $estatus_recinto_id = $existe_estatus_recinto->infra_other_data_attribute_id;

                            if ($existe_estatus_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estatus_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estatus_recinto_id, $estatus_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estatus_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estatus_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($estado_recinto) {
                            $existe_estado_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ESTADO RECINTO');
                            $estado_recinto_id = $existe_estado_recinto->infra_other_data_attribute_id;

                            if ($existe_estado_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $estado_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($estado_recinto_id, $estado_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $estado_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $estado_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($cantidad_usuarios) {
                            $existe_cantidad_usuarios = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CANTIDAD USUARIOS');
                            $cantidad_usuarios_id = $existe_cantidad_usuarios->infra_other_data_attribute_id;

                            if ($existe_cantidad_usuarios->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $cantidad_usuarios_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($cantidad_usuarios_id, $cantidad_usuarios);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $cantidad_usuarios_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $cantidad_usuarios;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 25;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($ventanas) {
                            $existe_ventanas = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'VENTANAS');
                            $ventanas_id = $existe_ventanas->infra_other_data_attribute_id;

                            if ($existe_ventanas->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $ventanas_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($ventanas_id, $ventanas);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $ventanas_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $ventanas;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($aire_acondicionado) {
                            $existe_aire_acondicionado = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'AIRE ACONDICIONADO');
                            $aire_acondicionado_id = $existe_aire_acondicionado->infra_other_data_attribute_id;

                            if ($existe_aire_acondicionado->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $aire_acondicionado_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($aire_acondicionado_id, $aire_acondicionado);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $aire_acondicionado_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $aire_acondicionado;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($calefaccion) {
                            $existe_calefaccion = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'CALEFACCION');
                            $calefaccion_id = $existe_calefaccion->infra_other_data_attribute_id;

                            if ($existe_calefaccion->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $calefaccion_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($calefaccion_id, $calefaccion);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $calefaccion_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $calefaccion;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($luminarias) {
                            $existe_luminarias = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'LUMINARIAS');
                            $luminarias_id = $existe_luminarias->infra_other_data_attribute_id;

                            if ($existe_luminarias->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $luminarias_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($luminarias_id, $luminarias);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {

                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $luminarias_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $luminarias;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 29;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($enchufes) {
                            $existe_enchufes = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'ENCHUFES');
                            $enchufes_id = $existe_enchufes->infra_other_data_attribute_id;

                            if ($existe_enchufes->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $enchufes_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($enchufes_id, $enchufes);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $enchufes_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $enchufes;
                                $infra_other->save();
                                $cont++;
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 30;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }


                        if ($punto_red) {
                            $existe_punto_red = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PUNTOS DE RED');
                            $punto_red_id = $existe_punto_red->infra_other_data_attribute_id;

                            if ($existe_punto_red->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $punto_red_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($punto_red_id, $punto_red);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                if ($punto_red === '0') {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = "0";
                                    $infra_other->save();
                                    $cont++;
                                } else {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $punto_red_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_value_value = $punto_red;
                                    $infra_other->save();
                                    $cont++;
                                }
                            }
                        } else {
                            $infra_other = new InfraOtherDataValue();
                            $infra_other->infra_other_data_attribute_id = 31;
                            $infra_other->node_id = $node->node_id;
                            $infra_other->infra_other_data_value_value = "0";
                            $infra_other->save();
                            $cont++;
                        }

                        if ($proyector) {
                            $existe_proyector = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'PROYECTOR');
                            $proyector_id = $existe_proyector->infra_other_data_attribute_id;

                            if ($existe_proyector->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $proyector_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($proyector_id, $proyector);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $proyector_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $proyector_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $proyector;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($wifi) {
                            $existe_wifi = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'WIFI');
                            $wifi_id = $existe_wifi->infra_other_data_attribute_id;

                            if ($existe_wifi->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $wifi_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($wifi_id, $wifi);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $wifi_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $wifi_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $wifi;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        if ($observacion_recinto) {
                            $existe_observacion_recinto = Doctrine_Core :: getTable('InfraOtherDataAttribute')->findOneBy('infra_other_data_attribute_name', 'OBSERVACION RECINTO');
                            $observacion_recinto_id = $existe_observacion_recinto->infra_other_data_attribute_id;

                            if ($existe_observacion_recinto->infra_other_data_attribute_type == 5) {

                                $InfraOtherDataOption = Doctrine_Core :: getTable('InfraOtherDataOption')->findOneBy('infra_other_data_attribute_id', $observacion_recinto_id);
                                $value = Doctrine_Core::getTable('Node')->retrieveByArea($observacion_recinto_id, $observacion_recinto);
                                if ($value) {
                                    $infra_other = new InfraOtherDataValue();
                                    $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                    $infra_other->node_id = $node->node_id;
                                    $infra_other->infra_other_data_option_id = $value->infra_other_data_option_id;
                                    $infra_other->save();
                                    $cont++;
                                }
                            } else {
                                $infra_other = new InfraOtherDataValue();
                                $infra_other->infra_other_data_attribute_id = $observacion_recinto_id;
                                $infra_other->node_id = $node->node_id;
                                $infra_other->infra_other_data_value_value = $observacion_recinto;
                                $infra_other->save();
                                $cont++;
                            }
                        }

                        $infra_other = new InfraOtherDataValue();
                        $infra_other->infra_other_data_attribute_id = 38;
                        $infra_other->node_id = $node->node_id;
                        $infra_other->infra_other_data_value_value = "2013-10-15";
                        $infra_other->save();
                    }

                    if (is_numeric($node_name->node_id)) {
                        $parentNode = Doctrine_Core::getTable('Node')->find($node_name->node_id);
                        $node->getNode()->insertAsLastChildOf($parentNode);

                        $this->syslog->register('add_sibling_node', array(
                            $this->input->post('node_name'),
                            $parentNode->getPath()
                        )); // registering log
                    } else {
                        $treeObject = Doctrine_Core::getTable('Node')->getTree();
                        $treeObject->createRoot($node);

                        $this->syslog->register('add_parent_node', array(
                            $node->node_name
                        )); // registering log
                    }
                    $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, true, "", $tipo);
                    $cont++;
                } else {
                    $success = false;
                    $msg = $this->translateTag('General', 'there_is_a_name');
                }
            }
        }//for
    }//Series

    $nodePadrePiso1 = Doctrine_Core :: getTable('Node')->findOneBy('node_name', 'Piso 001');

    if ($nodePadrePiso1) {

        $existe = Doctrine_Core :: getTable('Node')->nodeExiste($nodePadrePiso1->node_id);
        if ($existe) {
            $existe->infra_info_usable_area_total = $piso_uno;
            $existe->save();
        } else {
            $infoPadre = new InfraInfo();
            $infoPadre->node_id = $nodePadrePiso1->node_id;
            $infoPadre->infra_info_usable_area_total = $piso_uno;
            $infoPadre->save();
        }
    }

    $nodePadrePiso2 = Doctrine_Core :: getTable('Node')->findOneBy('node_name', 'Piso 002');

    if ($nodePadrePiso2) {

        $existe = Doctrine_Core :: getTable('Node')->nodeExiste($nodePadrePiso2->node_id);
        if ($existe) {
            $existe->infra_info_usable_area_total = $piso_dos;
            $existe->save();
        } else {
            $infoPadre = new InfraInfo();
            $infoPadre->node_id = $nodePadrePiso2->node_id;
            $infoPadre->infra_info_usable_area_total = $piso_dos;
            $infoPadre->save();
        }
    }

    $nodePadrePiso3 = Doctrine_Core :: getTable('Node')->findOneBy('node_name', 'Piso 003');

    if ($nodePadrePiso3) {

        $existe = Doctrine_Core :: getTable('Node')->nodeExiste($nodePadrePiso3->node_id);
        if ($existe) {
            $existe->infra_info_usable_area_total = $piso_tres;
            $existe->save();
        } else {
            $infoPadre = new InfraInfo();
            $infoPadre->node_id = $nodePadrePiso3->node_id;
            $infoPadre->infra_info_usable_area_total = $piso_tres;
            $infoPadre->save();
        }
    }

    $nodePadrePiso4 = Doctrine_Core :: getTable('Node')->findOneBy('node_name', 'Piso 004');

    if ($nodePadrePiso4) {

        $existe = Doctrine_Core :: getTable('Node')->nodeExiste($nodePadrePiso4->node_id);
        if ($existe) {
            $existe->infra_info_usable_area_total = $piso_cuatro;
            $existe->save();
        } else {
            $infoPadre = new InfraInfo();
            $infoPadre->node_id = $nodePadrePiso4->node_id;
            $infoPadre->infra_info_usable_area_total = $piso_cuatro;
            $infoPadre->save();
        }
    }

    $nodePadrePiso55 = Doctrine_Core :: getTable('Node')->findOneBy('node_name', 'Piso 005');

    if ($nodePadrePiso55) {

        $existe = Doctrine_Core :: getTable('Node')->nodeExiste($nodePadrePiso55->node_id);
        if ($existe) {
            $existe->infra_info_usable_area_total = $piso_cinco;
            $existe->save();
        } else {
            $infoPadre = new InfraInfo();
            $infoPadre->node_id = $nodePadrePiso55->node_id;
            $infoPadre->infra_info_usable_area_total = $piso_cinco;
            $infoPadre->save();
        }
    }

    $nodePadrePiso66 = Doctrine_Core :: getTable('Node')->findOneBy('node_name', 'ZOCALO 001');

    if ($nodePadrePiso66) {

        $existe = Doctrine_Core :: getTable('Node')->nodeExiste($nodePadrePiso66->node_id);
        if ($existe) {
            $existe->infra_info_usable_area_total = $piso_seis;
            $existe->save();
        } else {
            $infoPadre = new InfraInfo();
            $infoPadre->node_id = $nodePadrePiso66->node_id;
            $infoPadre->infra_info_usable_area_total = $piso_seis;
            $infoPadre->save();
        }
    }

    $nodePadrePiso99 = Doctrine_Core :: getTable('Node')->findOneBy('node_name', 'SUBTERRANEO 001');

    if ($nodePadrePiso99) {

        $existe = Doctrine_Core :: getTable('Node')->nodeExiste($nodePadrePiso99->node_id);
        if ($existe) {
            $existe->infra_info_usable_area_total = $piso_siete;
            $existe->save();
        } else {
            $infoPadre = new InfraInfo();
            $infoPadre->node_id = $nodePadrePiso99->node_id;
            $infoPadre->infra_info_usable_area_total = $piso_siete;
            $infoPadre->save();
        }
    }

    $nodePadrePiso110 = Doctrine_Core :: getTable('Node')->findOneBy('node_name', 'SUBTERRANEO 002');

    if ($nodePadrePiso110) {

        $existe = Doctrine_Core :: getTable('Node')->nodeExiste($nodePadrePiso110->node_id);
        if ($existe) {
            $existe->infra_info_usable_area_total = $piso_ocho;
            $existe->save();
        } else {
            $infoPadre = new InfraInfo();
            $infoPadre->node_id = $nodePadrePiso110->node_id;
            $infoPadre->infra_info_usable_area_total = $piso_ocho;
            $infoPadre->save();
        }
    }

    if ($cont > 0) {

        $msg = "Se agregaron la cantidad de: " . $cont . ".  " . "Nodos Exitosamente";
        $success = true;


        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    } else {

        $msg = "No hay Nodos Los cuales agregar";
        $success = false;

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }
}
