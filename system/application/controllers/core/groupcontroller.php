<?php

/**
 * @package Controller
 * @subpackage GroupController
 */
class GroupController extends APP_Controller {

    function GroupController() {
        parent::APP_Controller();
    }

    /**
     * Lista todos los grupos del sistema
     */
    function get() {
        $text_autocomplete = $this->input->post('query');
        $user_id = $this->input->post('user_id');
        $userGroup = Doctrine_Core::getTable('UserGroup')->retrieveAll((empty($user_id) ? NULL : $user_id), $text_autocomplete);
        $json_data = $this->json->encode(array('total' => $userGroup->count(), 'results' => $userGroup->toArray()));
        echo $json_data;
    }

    /**
     * 
     * Agrega un grupo al sistema
     * 
     * @param string $user_group_name
     */
    function add() {
      
        $userGroup = new UserGroup();
        $userGroup->user_group_name = $this->input->post('user_group_name');
        try {
            $userGroup->save();
            $success = true;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag('General', 'operation_successful');
            $this->syslog->register('add_group', array($userGroup->user_group_name));
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * 
     * Modifica los datos de un grupo del sistema
     * 
     * @param integer $group_id
     * @param string $user_group_name
     */
    function update() {
        $user_group_id = $this->input->post('user_group_id');
        $user_group_name = $this->input->post('user_group_name');
        $userGroup = Doctrine_Core::getTable('UserGroup')->retrieveById($user_group_id);
        $userGroup->user_group_name = $user_group_name;
        try {
            $userGroup->save();
            $success = true;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag('General', 'operation_successful');
            $this->syslog->register('update_group', array($lastGroup, $userGroup->user_group_name));
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * 
     * Eliminar un grupo de usuarios
     * 
     * @param integer $user_group_id
     */
    function delete() {
        $user_group_id = $this->input->post('user_group_id');
        $userGroup = Doctrine::getTable('UserGroup')->retrieveById($user_group_id);
         $group = $userGroup->user_group_name;
        try {
            $userGroupUser = Doctrine::getTable('UserGroup')->retrieveByIdUser($user_group_id);
            if ($userGroupUser) {
                $success = 'false';
                $msg = $this->translateTag('General', 'you_can_not_delete_the_group');
            } else {
                $userGroup->delete();
                $success = 'true';
                $msg = $this->translateTag('General', 'operation_successful');
                $this->syslog->register('delete_group', array($group));
            }
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }

        //Output 
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * Agrega los usuarios al grupo especificado
     * @param integer $user_group_id
     * @param string $usersToGroup
     * @method POST
     */
    function addUser() {
        $user_group_id = $this->input->post('user_group_id');
        $usersToGroup = explode(',', $this->input->post('usersToGroup'));

        try {
            //Obtenemos la conexión actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

            //Iniciar transacción
            $conn->beginTransaction();

            //se busca la información del grupo
            $group = Doctrine::getTable('UserGroup')->retrieveById($user_group_id);

            // se buscan los usuarios pertenecientes al grupo seleccionado por el usuario
            $userGroupUser = Doctrine_Core::getTable('UserGroupUser')->retrieveUsersByGroup($user_group_id)->toArray();

            $userGroup = array();
            $usersDelete = array();
            $usersAdd = array();

            foreach ($userGroupUser as $key => $value) {

                array_push($userGroup, $value['user_id']);
            }
            // usuarios eliminados del grupo
            $usersD = array_diff($userGroup, $usersToGroup);

            //nombre de usuarios eliminados del grupo
//            print_r($userGroupUser);exit();
            foreach ($userGroupUser as $key => $value) {
                if (in_array($value['user_id'], $usersD)) {
                    array_push($usersDelete, $value['User']['user_username']);
                }
            }
            $usersDelete = str_replace(",", ", ", implode(',', $usersDelete));


            // usuarios agregados al grupo
            $usersA = array_diff($usersToGroup, $userGroup);

            //nombre de usuarios agregados al grupo
//            print_r($usersA);

            foreach ($usersA as $value) {


                $user = Doctrine_Core::getTable('User')->find($value);
//                print_r($user); exit();
                if ((!empty($user) && !is_null($user))) {
//                    echo 'aqui';exit();
                    if ($user->user_username) {
                        array_push($usersAdd, $user->user_username);
                    }
                }
            }
            $usersAdd = str_replace(",", ", ", implode(',', $usersAdd));

//            print_r($usersAdd); exit();
            //Eliminamos la config actual
            Doctrine_Core::getTable('UserGroupUser')->deleteCurrentUsersInGroup($user_group_id);

            //Insert de los usuarios al grupo
            if (!empty($usersToGroup[0])) {
                foreach ($usersToGroup as $user_id) {
                    $userGroupUser = new UserGroupUser();
                    $userGroupUser->user_group_id = $user_group_id;
                    $userGroupUser->user_id = $user_id;
                    $userGroupUser->save();
                }
            }
            //Commit de la transacción
            $conn->commit();
            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
            //regisro de log de usuarios eliminados del grupo.
            if (!empty($usersDelete)) {
                $this->syslog->register('delete_user_group', array($usersDelete, $group->user_group_name));
            }
            //regisro de log de usuarios agregados al grupo.
            if (!empty($usersAdd)) {
                $this->syslog->register('add_user_group', array($usersAdd, $group->user_group_name));
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
     * Retorna todos los usuarios asociados al grupo
     * @param integer $user_group_id
     */
    function getUsers() {
        $user_group_id = $this->input->post('user_group_id');
        $userGroupUser = Doctrine_Core::getTable('UserGroupUser')->retrieveUsersByGroup($user_group_id);
        $json_data = $this->json->encode(array('total' => $userGroupUser->count(), 'results' => $userGroupUser->toArray()));
        echo $json_data;
    }

    /**
     * Retorna los usuarios que no están asociados al grupo especicado por parametro.
     * @param integer $user_group_id
     */
    function usersOutsideGroup() {
        $user_group_id = $this->input->post('user_group_id');
        $userGroupUser = Doctrine_Core::getTable('UserGroupUser')->retrieveUsersOutsideGroup($user_group_id);
        $json_data = $this->json->encode(array('total' => $userGroupUser->count(), 'results' => $userGroupUser->toArray()));
        echo $json_data;
    }

}
