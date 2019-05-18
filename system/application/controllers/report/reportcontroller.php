<?php

/**
 * @package Controller
 * @subpackage ReportController
 */
class ReportController extends APP_Controller {

    function ReportController() {
        parent::APP_Controller();
    }

    /**
     * 
     * Lista todos los reportes del sistema
     */
    function get() {
//TRAE TODOS LOS GRUPOS DE ESE USUARIO
        $user_id = $this->session->userdata('user_id');

        $User = Doctrine_Core::getTable('User')->find($user_id);

        //ADMINISTRADOR
        if ($User->user_type == 'A') {
            $report = Doctrine_Core::getTable('Report')->retrieveAll();
            $json_data = $this->json->encode(array('total' => $report->count(), 'results' => $report->toArray()));
            echo $json_data;
        } else {
            ////OTRO TIPO DE USUARIO
            $UserGroupUsers = Doctrine_Core::getTable('UserGroupUser')->retrieveArrayGroup($user_id);

            //crea un arreglo de grupos permitidos para visualizar        
            foreach ($UserGroupUsers as $UserGroupUser) {
                $array_group[] = $UserGroupUser->user_group_id;
            }

            $report = Doctrine_Core::getTable('Report')->retrieveAllGroup($array_group);
            $json_data = $this->json->encode(array('total' => $report->count(), 'results' => $report->toArray()));
            echo $json_data;
        }
    }

    function getReportUserGroup() {
        ini_set('memory_limit', '256M');
        $report_id = $this->input->post('report_id');
        //$user_group_id = $this->input->post ( 'user_group_id' );

        $ReportUserGroup = Doctrine_Core::getTable('ReportUserGroup')->retrieveByModule($report_id);
        $json_data = $this->json->encode(array('total' => $ReportUserGroup->count(), 'results' => $ReportUserGroup->toArray()));
        echo $json_data;
    }

    function getReportUserGroupPermitted() {
        $report_id = $this->input->post('report_id');
        //$user_group_id = $this->input->post ( 'user_group_id' );

        $ReportUserGroup = Doctrine_Core::getTable('ReportUserGroup')->retrieveByModulePermitted($report_id);
        $json_data = $this->json->encode(array('total' => $ReportUserGroup->count(), 'results' => $ReportUserGroup->toArray()));
        echo $json_data;
    }

    function add() {
        $report_id = $this->input->post('report_id');
        $permissionsToGroup = explode(',', $this->input->post('permissionsToGroup'));


        try {
            //Obtenemos la conexión actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

            //Iniciar transacción
            $conn->beginTransaction();

            //Eliminamos la config actual
            Doctrine_Core::getTable('ReportUserGroup')->deleteCurrentPermissionsGroup($report_id);

            //Insert de los fields en la configuración para el tipo de nodo
            if (!empty($permissionsToGroup[0])) {
                foreach ($permissionsToGroup as $user_group_id) {
                    $ReportUserGroup = new ReportUserGroup();
                    $ReportUserGroup->report_id = $report_id;
                    $ReportUserGroup->user_group_id = $user_group_id;


                    $report = Doctrine_Core::getTable('Report')->find($report_id);
                    $userGroup = Doctrine_Core::getTable('UserGroup')->find($ReportUserGroup->user_group_id);

                    $this->syslog->register('add_report_group', array(
                        $report->report_name,
                        $userGroup->user_group_name
                            )); // registering log
                    
                    $ReportUserGroup->save();
                }
            }

            //Commit de la transacción
            $conn->commit();
            $success = true;
            $msg = $this->translateTag('General', 'successfully_assigned_permission');
        } catch (Exception $e) {
            //Rollback de la transacción
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

}