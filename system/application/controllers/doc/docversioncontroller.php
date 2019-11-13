<?php

/**
 * @package Controller
 * @subpackage DocVersionController
 */
class DocVersionController extends APP_Controller {

    function DocVersionController() {
        parent::APP_Controller();
    }

    /**
     * get
     *
     * Retorna la version actual del Nodo
     */
    function get() {
        $doc_document_id = $this->input->post('doc_document_id');
        $versions = Doctrine_Core::getTable('DocVersion')->retrieveByDocument($doc_document_id);

        if ($versions->count()) {
            echo '({"total":"' . $versions->count() . '", "results":' . $this->json->encode($versions->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     *
     * Agrega un nuevo documento asociado a un nodo
     *
     * @post int node_id
     * @post int doc_document_id
     * @post int doc_version_code
     * @post int doc_version_code_client
     * @post string doc_version_comments
     * @post string doc_version_expiration
     */
    function add() {
        $file_uploaded = $this->input->file('documento');
        $file_extension = $this->app->getFileExtension($file_uploaded['name']);
        $docExtension = new DocExtension();
        $file_input = $file_uploaded['name'];
        $compare = Doctrine_Core::getTable('DocVersion')->compFileName($file_input);

        //Preguntar si el nombre documento no existe en la base de datos retornar mensaje "Debe ingresarlo como Documento"
        if ($compare === true) {
            $success = false;
            $msg = $this->translateTag('Document', 'you_must_enter_the_same_document');
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
            return;
        }

        if (($extension_id = $docExtension->isAllowed($file_extension)) !== false) {

            //Recibimos los parametros
            $node_id = $this->input->post('node_id');
            $doc_document_id = $this->input->post('doc_document_id');
            $doc_version_code_client = $this->input->post('doc_version_code_client');
            $doc_version_comments = $this->input->post('doc_version_comments');
            $doc_version_expiration = $this->input->post('doc_version_expiration');
            $doc_version_keyword = $this->input->post('doc_version_keyword');
            $doc_version_alert = $this->input->post('doc_version_alert');
            $doc_version_alert_email = $this->input->post('doc_version_alert_email');
            $doc_version_notification_email = $this->input->post('doc_version_notification_email');

            $doc_version = Doctrine_Core::getTable('DocVersion')->lastVersionDocument($doc_document_id);
            $doc_version_code = $doc_version + 1;

            $doc = Doctrine_Core::getTable('DocDocument')->retrieveById($doc_document_id);


            $doc_name = md5(time()) . '.' . $file_extension;


            //Obtenemos la conexi�n actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();
            //Iniciamos la transacci�n
            $conn->beginTransaction();

            try {
                //Insertamos la nueva Version en la tabla
                $version = new DocVersion();
                $version->doc_version_code = $doc_version_code;
                $version->doc_version_code_client = $doc_version_code_client;
                $version->doc_version_filename = $doc_name;
                $user_id = $this->auth->get_user_data('user_id');
                $version->user_id = $user_id;
                $version->doc_version_comments = $doc_version_comments;
                $version->doc_version_expiration = $doc_version_expiration;
                $version->doc_version_keyword = $doc_version_keyword;
                $version->doc_version_alert = $doc_version_alert;
                $version->doc_version_alert_email = $doc_version_alert_email;
                $version->doc_version_notification_email = $doc_version_notification_email;
                $version->doc_document_id = $doc_document_id;
                $version->save();

                //Rescatamos el id
                $doc_version_last_id = $version->doc_version_id;
                //Actualizamos el documento
                $doc->doc_current_version_id = $doc_version_last_id;
                $doc->save();

                //Creamos el nombre para el nuevo documento
                $config['upload_path'] = $this->config->item('doc_dir');
                $config['allowed_types'] = $file_extension;
                $config['file_name'] = $doc_name;

                //Restringuir tama�o y peso?
                //Carga de la libreria para el upload
                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('documento')) {
                    $success = false;
                    $msg = $this->upload->display_errors('-', '\n');
                    throw new Exception($msg);
                } else {
                    $doc_version_id = $version->doc_version_id;
                    $this->sendNotification($doc_version_id);

                    $node = Doctrine::getTable('Node')->find($node_id);
                    $docCategory = Doctrine::getTable('DocCategory')->find($doc->doc_category_id);

                    $this->addThumb($doc_name, $file_extension);

                    $this->syslog->register('add_version_document', array(
                        $doc_version_code,
                        $doc->doc_document_filename,
                        $docCategory->doc_category_name,
                        $node->getPath()
                    )); // registering log


                    $success = true;
                    $msg = $this->translateTag('General', 'operation_successful');

// Si todo OK, commit a la base de datos
                    $conn->commit();
                }
            } catch (Exception $e) {
                //Si hay error, rollback de los cambios en la base de datos
                $conn->rollback();
                $success = false;
                $msg = $e->getMessage();
            }
        } else {
            $success = false;
            $msg = $this->translateTag('Document', 'type_extension_not_allowed');
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     *
     * Actualiza un documento
     * @param integer $doc_version_id
     * @param string $doc_version_code_client
     * @param string $doc_version_comments
     * @method POST
     */
    function update() {
//        $node_id = $this->input->post('node_id');
        $version = Doctrine_Core::getTable('DocVersion')->find($this->input->post('doc_version_id'));

        $doc_version_keyword = $version->doc_version_keyword;
        $doc_version_internal = $version->doc_version_internal;
        $doc_version_expiration = $version->doc_version_expiration;
        $doc_version_alert = $version->doc_version_alert;
        $doc_version_comments = $version->doc_version_comments;
        $doc_version_alert_email = $version->doc_version_alert_email;

        $version->doc_version_code_client = $this->input->post('doc_version_code_client');
        $version->doc_version_comments = $this->input->post('doc_version_comments');
        $version->doc_version_internal = $this->input->post('doc_version_internal');
        $version->doc_version_expiration = $this->input->post('doc_version_expiration');
        $version->doc_version_keyword = $this->input->post('doc_version_keyword');
        $version->doc_version_alert = $this->input->post('doc_version_alert');
        $version->doc_version_alert_email = $this->input->post('doc_version_alert_email');

        try {

            $document = Doctrine::getTable('DocDocument')->find($this->input->post('doc_document_id'));
            $node_id = $document->node_id;
            $node = Doctrine::getTable('Node')->find($node_id);
            

            $log_id = $this->syslog->register('update_version_document', array(
                $version->doc_version_code,
                $document->doc_document_filename,
                $node->getPath()
            )); // registering log

            if ($doc_version_keyword != $this->input->post('doc_version_keyword')) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Document', 'keywords');
                    $logDetail->log_detail_value_old = $doc_version_keyword;
                    $logDetail->log_detail_value_new = $this->input->post('doc_version_keyword');
                    $logDetail->save();
                }
            }

            $fecha = $this->input->post('doc_version_expiration');
            list($fecha) = explode("T", $fecha);

            $fecha1 = $fecha;
            $fecha2 = date("d/m/Y", strtotime($fecha1));

            $fecha3 = $doc_version_expiration;
            $fecha4 = date("d/m/Y", strtotime($fecha3));

            if ($fecha4 != $fecha2) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'expiration_date');
                    $logDetail->log_detail_value_old = $fecha4;
                    $logDetail->log_detail_value_new = $fecha2;
                    $logDetail->save();
                }
            }
            
            $fechaInternal = $this->input->post('doc_version_internal');
            list($fechaInternal) = explode("T", $fechaInternal);

            $fechaInternal1 = $fechaInternal;
            $fechaInternal2 = date("d/m/Y", strtotime($fechaInternal1));

            $fechaInternal3 = $doc_version_internal;
            $fechaInternal4 = date("d/m/Y", strtotime($fechaInternal3));

            if ($fechaInternal4 != $fechaInternal2) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = 'Fecha Interna';
                    $logDetail->log_detail_value_old = $fechaInternal4;
                    $logDetail->log_detail_value_new = $fechaInternal2;
                    $logDetail->save();
                }
            }

            if ($doc_version_alert != $this->input->post('doc_version_alert')) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Document', 'alert_days');
                    $logDetail->log_detail_value_old = $doc_version_alert;
                    $logDetail->log_detail_value_new = $this->input->post('doc_version_alert');
                    $logDetail->save();
                }
            }

            if ($doc_version_comments != $this->input->post('doc_version_comments')) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'comment');
                    $logDetail->log_detail_value_old = $doc_version_comments;
                    $logDetail->log_detail_value_new = $this->input->post('doc_version_comments');
                    $logDetail->save();
                }
            }

            if ($doc_version_alert_email != $this->input->post('doc_version_alert_email')) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'expiration_mail_alert');
                    $logDetail->log_detail_value_old = $doc_version_alert_email;
                    $logDetail->log_detail_value_new = $this->input->post('doc_version_alert_email');
                    $logDetail->save();
                }
            }

            $version->save();
            $msg = $this->translateTag('General', 'operation_successful');
            $success = true;
        } catch (Exception $e) {

            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * Eliminar la version del documento y dejar el anterior por version como actual en caso de ser la primera version.
     * @method POST
     * @param integer $node_id
     * @param integer $doc_version_id
     * @param integer $doc_document_id
     *
     */
    function delete() {
        $node_id = $this->input->post('node_id');
        $doc_version_id = $this->input->post('doc_version_id');
        $doc_document_id = $this->input->post('doc_document_id');

        if (!$node_id) {
            $documentNode = Doctrine::getTable('DocDocument')->find($doc_document_id);
            $node_id = $documentNode->node_id;
        }

        $node = Doctrine::getTable('Node')->find($node_id);

        $document = Doctrine::getTable('DocDocument')->find($doc_document_id);
        $version = Doctrine::getTable('DocVersion')->find($doc_version_id);

        $this->syslog->register('delete_version_document', array(
            $version->doc_version_code,
            $document->doc_document_filename,
            $node->getPath()
        )); // registering log

        try {
            Doctrine_Core::getTable('DocVersion')->deleteVesionDocument($node_id, $doc_document_id, $doc_version_id);
            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function sendNotification($doc_version_id) {

        $q = Doctrine_Query::create()
                ->from('DocVersion dv')
                ->innerJoin('dv.DocDocument dd')
                ->innerJoin('dd.DocCategory dc')
                ->where('doc_version_expiration IS NOT NULL')
                ->andWhere('doc_version_alert IS NOT NULL')
                ->andWhere('doc_version_alert_email IS NOT NULL')
                ->andWhere('dv.doc_version_id = ?', $doc_version_id);

        $results = $q->fetchOne();

        $doc_version = Doctrine::getTable('DocVersion')->find($doc_version_id);
        $node_id = $doc_version->DocDocument->node_id;


        $node = Doctrine::getTable('Node')->find($node_id);

        $CI = & get_instance();
        $CI->load->library('NotificationUser');

        $to = trim($results['doc_version_notification_email']); //CORREO DESTINATARIO

        if ($to != null) {

            $subject = $this->translateTag('Document', 'mail_alert_document'); //ASUNTO

            $body = $this->translateTag('Document', 'mail_document_name') . $results['DocDocument']['doc_document_filename'] . "\n"; //CUERPO DEL MENSAJE
            $body .= $this->translateTag('Document', 'mail_category') . $results['DocDocument']['DocCategory']['doc_category_name'] . "\n";
            $body .= $this->translateTag('Document', 'mail_version') . $results['doc_version_code_client'] . "\n";
            $body .= $this->translateTag('Document', 'mail_description') . $results['doc_version_comments'] . "\n";
            $body .= $this->translateTag('Document', 'mail_expiration_date') . $results['doc_version_expiration'] . "\r\n";
            $body .= $this->translateTag('Document', 'mail_location') . $node->getPath() . "\r\n";

            copy($this->config->item('doc_dir') . $results['doc_version_filename'], $this->config->item('temp_dir') . $results['DocDocument']['doc_document_filename']);

            $CI->notificationuser->mail($to, $subject, $body, array($this->config->item('temp_dir') . $results['DocDocument']['doc_document_filename']));
            $body = '';

            unlink($this->config->item('temp_dir') . $results['DocDocument']['doc_document_filename']);
        }
    }

    function addThumb($doc_name, $file_extension) {


        $extensiones_permitidas = $this->config->item('doc_image_web');
        if (in_array(strtoupper($file_extension), $extensiones_permitidas)) {

            $this->load->library('image_lib');

            $img_cfg['image_library'] = 'gd2';
            $img_cfg['source_image'] = $this->config->item('doc_dir') . $doc_name;
            $img_cfg['maintain_ratio'] = FALSE;
            $config['create_thumb'] = TRUE;
            $img_cfg['new_image'] = $this->config->item('doc_dir') . 'thumb/' . $doc_name;
            $img_cfg['width'] = 150;
            $img_cfg['quality'] = 100;
            $img_cfg['height'] = 150;

            $this->image_lib->initialize($img_cfg);
            $this->image_lib->resize();
        }
    }

}
