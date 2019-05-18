<?php

/**
 * @package Controller
 * @subpackage Modulecontroller
 */
class PermissionsController extends APP_Controller {

    function PermissionsController() {
        parent :: APP_Controller();
    }

    /**
     *
     * Lista los permisos del grupo a un modulo
     */
    function get() {
        $user_group_id = $this->input->post('user_group_id');
        $module_id = $this->input->post('module_id');
        $language_id = $this->session->userdata('language_id');
        $permissionsGroup = Doctrine_Core :: getTable('UserGroupAction')->permissionsGroupInModule($user_group_id, $module_id, $language_id);
        $json_data = $this->json->encode(array('total' => $permissionsGroup->count(), 'results' => $permissionsGroup->toArray()));
        echo $json_data;
    }

    /*
     * Setea los permisos de un grupo en un modulo
     * @param integer $user_group_id
     *
     */

    function add() {
        $user_group_id = $this->input->post('user_group_id');
        $permissionsToGroup = explode(',', $this->input->post('permissionsToGroup'));
        $nodes = $this->input->post('nodes');
        $branches = $this->input->post('branches');
        $module_id = $this->input->post('module_id');
        $language_id = $this->session->userdata('language_id');

        $nodes = (!empty($nodes)) ? array_combine(array_values($nodes), array_values($nodes)) : array();

        if (empty($branches)) {
            $branches = array();
        }

        try {
            //Obtenemos la conexión actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

            //Iniciar transacción
            $conn->beginTransaction();

            //se busca la información del grupo
            $group = Doctrine::getTable('UserGroup')->retrieveById($user_group_id);

            //se busca la informacion del modulo:
            $module = Doctrine::getTable('Module')->find($module_id);
            // print_r($module->module_name); exit;
            // Se buscan los permisos originales del grupo asociado al modulo
            $permissionsGroup = Doctrine_Core :: getTable('UserGroupAction')->permissionsGroupInModule($user_group_id, $module_id, $language_id)->toArray();

            $permission = array();
            $permissionDelete = array();
            $permissionAdd = array();

            foreach ($permissionsGroup as $key => $value) {

                array_push($permission, $value['module_action_id']);
            }

            // permisos agregados al grupo
            $permissionA = array_diff($permissionsToGroup, $permission);

            //Se busca la información del modulo.
            foreach ($permissionA as $value) {
                $language_id = $this->session->userdata('language_id');
                $nameModule = Doctrine_Core::getTable('ModuleAction')->retrieveModule($module_id, $user_group_id, $value, $language_id);
                if ((!empty($nameModule)) && (!is_null($nameModule))) {
                    if ($nameModule->LanguageTag->language_tag_value) {
                        array_push($permissionAdd, $nameModule->LanguageTag->language_tag_value);
                    }
                }
            }

            $permissionAdd = implode(',', $permissionAdd);

            //permisos eliminados al grupo
            $permissionD = array_diff($permission, $permissionsToGroup);
            foreach ($permissionD as $value) {
                $language_id = $this->session->userdata('language_id');
                $nameModule = Doctrine_Core::getTable('ModuleAction')->retrieveModule($module_id, $user_group_id, $value, $language_id);
                if ((!empty($nameModule)) && (!is_null($nameModule))) {
                    if ($nameModule->LanguageTag->language_tag_value) {
                        array_push($permissionDelete, $nameModule->LanguageTag->language_tag_value);
                    }
                }
            }

            $permissionDelete = implode(',', $permissionDelete);
           

            //Eliminamos la config actual
            Doctrine_Core::getTable('UserGroupAction')->deleteCurrentPermissionsGroup($user_group_id, $module_id);
            
             
            //Insert de los nodos asociados al grupo
            if (!empty($permissionsToGroup[0])) {
                foreach ($permissionsToGroup as $module_action_id) {
                    $userGroupAction = new UserGroupAction();
                    $userGroupAction->user_group_id = $user_group_id;
                    $userGroupAction->module_action_id = $module_action_id;
                    $userGroupAction->save();
                }
            }

            //Se verifica si el modulo es de activos y se almacena a que nodos tienen permiso las acciones
          
                //Eliminamos la config actual asociada al grupo
                Doctrine_Core::getTable('GroupAssetNode')->deletePermissionsGroupAsset($user_group_id,$module_id);
             
                //Insert de los fields en la configuración para el tipo de nodo
                if (count($nodes) > 0) {
                    foreach ($nodes as $node) {
                        $groupAssetNode = new GroupAssetNode();
                        $groupAssetNode->user_group_id = $user_group_id;
                        $groupAssetNode->node_id = $node;
                        $groupAssetNode->module_id = $module_id;
                        $groupAssetNode->save();
                    }
                }

                if (count($branches) > 0) {
                    //Sacamos los nodos de cada rama seleccionada
                    $treeObject = Doctrine_Core::getTable('Node')->getTree();
                    foreach ($branches as $branch) {
                        //Nodos de la rama
                        $nodes_branch = $treeObject->fetchBranch($branch);

                        //Insert de los nodos id de la rama que no son insertados en el each anterior.
                        foreach ($nodes_branch as $node) {
                            //Si el node id no fue insertado en el each de los nodos
                            if (empty($nodes[$node->node_id]) && !Doctrine_Core::getTable('GroupAssetNode')->findOneByUserGroupIdAndNodeId($user_group_id, $node->node_id, $module_id)) {
                                $groupAssetNodeB = new GroupAssetNode();
                                $groupAssetNodeB->user_group_id = $user_group_id;
                                $groupAssetNodeB->node_id = $node->node_id;
                                $groupAssetNodeB->module_id = $module_id;
                                @$groupAssetNodeB->save();
                            }
                        }
                    }
                }
            

            //Commit de la transacción
            $conn->commit();
            $success = true;
            $msg = $this->translateTag('General', 'successfully_assigned_permission');

            if (!empty($permissionAdd)) {
                $this->syslog->register('add_permissions_group', array($permissionAdd, $module->module_name, $group->user_group_name));
            }
            if (!empty($permissionDelete)) {
                $this->syslog->register('delete_permissions_group', array($permissionDelete, $module->module_name, $group->user_group_name));
            }
        } catch (Exception $e) {
            //Rollback de la transacción
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * Expand el nivel del node con los permisos que posee el grupo seleccionado
     * @param integer $group_id
     * @param mixed $node
     * @method POST
     */
    function expand() {
        $this->load->library('TreeNodes');
        $user_group_id = $this->input->post('user_group_id');

        if (is_null($user_group_id))
            return;

        $q = Doctrine_Query::create()
                ->select('node_name, node_id, node_type_id')
                ->from('Node n')
                ->leftJoin('n.UserGroupNode ugn ON ugn.user_group_id = ' . $user_group_id);

        if ($this->input->post('node') && is_numeric($this->input->post('node'))) { // si el un nodo con id
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $treeObject->setBaseQuery($q);
            $nodes = $treeObject->fetchBranch($this->input->post('node'), array('depth' => 1));
            $nodesCantity = count($nodes);
        } else { // nodos raices...no tienen id
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $treeObject->setBaseQuery($q);
            $nodes = $treeObject->fetchRoots();
            $nodesCantity = count($nodes);
        }
        $treeNodes = new TreeNodes();

        if ($nodesCantity) {
            foreach ($nodes as $node) {
                //Mandamos en true el parametro del chekbok checked si existe el user_group_id y es igual al user_group_id que se manda por POST.
                $group = Doctrine_Core::getTable('UserGroupNode')->findOneByUserGroupIdAndNodeId($user_group_id, $node->node_id);

                if ($group) {
                    $checked_node = (!empty($group->user_group_id) && ($group->user_group_id == $user_group_id) ? true : false);
                } else {
                    $checked_node = false;
                }
                if ($node->node_id == $this->input->post('node'))
                    continue;

                $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, false, "", $node->NodeType->node_type_name, $node->NodeType->NodeTypeCategory->node_type_category_name, $checked_node);
            }
        }

        echo $treeNodes->toJson();
    }

  

    function expandAsset() {
        $this->load->library('TreeNodes');
        $user_group_id = $this->input->post('user_group_id');
        $module_id = $this->input->post('module_id');

        if (is_null($user_group_id))
            return;

        $q = Doctrine_Query::create()
                ->select('node_name, node_id, node_type_id')
                ->from('Node n')
                ->leftJoin('n.GroupAssetNode ga ON ga.user_group_id = ' . $user_group_id);

        if ($this->input->post('node') && is_numeric($this->input->post('node'))) { // si el un nodo con id
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $treeObject->setBaseQuery($q);
            $nodes = $treeObject->fetchBranch($this->input->post('node'), array('depth' => 1));
            $nodesCantity = count($nodes);
        } else { // nodos raices...no tienen id
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $treeObject->setBaseQuery($q);
            $nodes = $treeObject->fetchRoots();
            $nodesCantity = count($nodes);
        }
        $treeNodes = new TreeNodes();

        if ($nodesCantity) {
            foreach ($nodes as $node) {
                //Mandamos en true el parametro del chekbok checked si existe el user_group_id y es igual al user_group_id que se manda por POST.

                $group = Doctrine_Core::getTable('GroupAssetNode')->findOneByUserGroupIdAndNodeId($user_group_id, $node->node_id, $module_id);

                if ($group) {
                    $checked_node = (!empty($group->user_group_id) && ($group->user_group_id == $user_group_id) ? true : false);
                } else {
                    $checked_node = false;
                }

                if ($node->node_id == $this->input->post('node'))
                    continue;

                $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, false, "", $node->NodeType->node_type_name, $node->NodeType->NodeTypeCategory->node_type_category_name, $checked_node);
            }
        }

        echo $treeNodes->toJson();
    }

    function expandUserPath() {
        $user_id = $this->input->post('user_id');
//         echo $this->input->post('node');

        $User = Doctrine_Core::getTable('User')->find($user_id);

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

                $checked_node = (!empty($User->user_path) && ($User->user_path == $node->node_id) ? true : false);


                $treeNodes->add($node->node_id, $node->node_name, $node->node_type_id, false, false, "", $node->NodeType->node_type_name, $node->NodeType->NodeTypeCategory->node_type_category_name, $checked_node);
            }
        }

        echo $treeNodes->toJson();
    }

    /**
     * Setea los permisos del grupo en el tree
     * @param array $nodes
     * @param array $branches
     */
    function setTree() {

//		ini_set("memory_limit","2000M");
        $type_save = $this->input->post('type_save');
        $nodes_checked = $this->input->post('nodes');
        $branches = $this->input->post('branches');
        $user_group_id = $this->input->post('user_group_id');

        if (!empty($nodes_checked)) {
            $nodes = array_combine(array_values($nodes_checked), array_values($nodes_checked));
        } else {
            $nodes = array();
        }

        if (empty($branches)) {
            $branches = array();
        }
        try {
            $users = Doctrine_Core::getTable('UserGroupUser')->retrieveUsersByGroup($user_group_id);

            foreach ($users as $user) {
                $xml_name = $this->app->getTempFileDir('treexml/' . $user->user_id . '.xml');
                @unlink($xml_name);
            }

            //Obtenemos la conexión actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

            //Iniciar transacción
            $conn->beginTransaction();

            if ($type_save == 'false') {
                //Eliminamos los permisos a los nodos actuales
                Doctrine_Core::getTable('UserGroupNode')->deleteCurrentAccess($user_group_id);
            }


            //Insert de los nodos id
            foreach ($nodes as $node_id) {
                $userGroupNode = new UserGroupNode();
                $userGroupNode->user_group_id = $user_group_id;
                $userGroupNode->node_id = $node_id;
                $userGroupNode->save();
            }

            //Sacamos los nodos de cada rama seleccionada
            $treeObject = Doctrine_Core::getTable('Node')->getTree();

            foreach ($branches as $branch) {
                //Nodos de la rama
                $nodes_branch = $treeObject->fetchBranch($branch);

                //Insert de los nodos id de la rama que no son insertados en el each anterior.
                foreach ($nodes_branch as $node) {
                    //Si el node id no fue insertado en el each de los nodos
                    if (empty($nodes[$node->node_id]) && !Doctrine_Core::getTable('UserGroupNode')->findOneByUserGroupIdAndNodeId($user_group_id, $node->node_id)) {
                        $userGroupNode = new UserGroupNode();
                        $userGroupNode->user_group_id = $user_group_id;
                        $userGroupNode->node_id = $node->node_id;
                        @$userGroupNode->save();
                    }
                }
            }

            //Commit de la transacción
            $conn->commit();
            $success = true;
            $msg = $this->translateTag('General', 'access_successfully_appointed_group');
        } catch (Exception $e) {
            //Rollback de la transacción
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function setUserPath() {
//PONE LA RUTA DE INICIO DEL USUARIO
        $user_id = $this->input->post('user_id');
        $user_path = $this->input->post('user_path'); // ESTO ES UN NODE_ID


        try {
            $User = Doctrine_Core::getTable('User')->find($user_id);
            $User->user_path = $user_path;
            $User->save();
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
