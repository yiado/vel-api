<?php

/**
 * @package    Controller
 * @subpackage AssetDocumentController
 */
class AssetDocumentController extends APP_Controller {

    function AssetDocumentController() {
        parent::APP_Controller();
    }

    /**
     * get
     *
     * Retorna los documentos asociados al Activo
     */
    function get() {
        $asset_id = $this->input->post('asset_id');
        $asset_document = Doctrine_Core::getTable('AssetDocument')->retrieveByAsset($asset_id);

        if ($asset_document->count()) {
            echo '({"total":"' . $asset_document->count() . '", "results":' . $this->json->encode($asset_document->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     *
     * Agrega un nuevo documento asociado a un Activo
     *
     * @post int asset_id
     * @post int asset_document_id
     * @post string asset_document_filename
     * @post string asset_document_description
     */
    function add() {
        $file_uploaded = $this->input->file('documento');
        $file_extension = $this->app->getFileExtension($file_uploaded['name']);
        $file_name_actual = $this->app->getFileName($file_uploaded['name']);
        $docExtension = new DocExtension();

        if (($extension_id = $docExtension->isAllowed($file_extension)) !== false) {
            //Recibimos los parametros
            $asset_id = $this->input->post('asset_id');
            $asset_document_id = $this->input->post('asset_document_id');
            $asset_document_filename = $this->input->post('asset_document_filename');
            $asset_document_comments = $this->input->post('asset_document_comments');
            $asset_document_description = $this->input->post('asset_document_description');

            $assetDoc = Doctrine_Core::getTable('AssetDocument')->retrieveByAsset($asset_document_id);

            $doc_name = md5(time()) . '.' . $file_extension;

            //Obtenemos la conexiï¿½n actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

            //Iniciamos la transacciï¿½n
            $conn->beginTransaction();

            try {
                //Insertamos el nuevo documento en la tabla
                $asset_document = new AssetDocument();
                $asset_document->asset_id = $asset_id;
                $user_id = $this->auth->get_user_data('user_id');
                $asset_document->user_id = $user_id;
                $asset_document->asset_document_filename = $file_name_actual . '.' . $file_extension;
                $asset_document->asset_document_description = $asset_document_description;
                $asset_document->asset_document_comments = $asset_document_comments;
                $asset_document->doc_extension_id = $extension_id;
                $asset_document->asset_document_name = $doc_name;

                $asset = Doctrine::getTable('Asset')->find($asset_document->asset_id);
                $node_id = $asset->node_id;
                //ESTO AUMENTA LA CUENTA DE LOS ARCHIVOS DEL ACTIVO
                $asset->asset_document_count = $asset->asset_document_count + 1;
                $asset->save();
                $node = Doctrine::getTable('Node')->find($node_id);

                $this->syslog->register('add_asset_document', array(
                    $file_name_actual . '.' . $file_extension,
                    $asset_document_filename,
                    $asset->asset_name,
                    $node->getPath()
                )); // registering log

                $asset_document->save();

                //Creamos el nombre para el nuevo documento
                $config['upload_path'] = $this->config->item('asset_doc_dir');
                $config['allowed_types'] = $file_extension;
                $config['file_name'] = $doc_name;

                //Restringuir tamaï¿½o y peso?
                //Carga de la libreria para el upload
                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('documento')) {
                    $success = false;
                    $msg = $this->upload->display_errors('-', '\n');
                    throw new Exception($msg);
                } else {
                    $success = true;
                    $msg = $this->translateTag('General', 'operation_successful');
                    // 
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
            $msg = $this->translateTag('Documen', 'type_extension_not_allowed');
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * delete
     *
     * Elimina el documento relacionado al Activo
     *
     * @param int $dasset_document_id
     */
    function delete() {
        $asset_document = Doctrine::getTable('AssetDocument')->find($this->input->post('asset_document_id'));



        try {
            $asset = Doctrine::getTable('Asset')->find($asset_document->asset_id);
            $node_id = $asset->node_id;
            //ESTO AUMENTA LA CUENTA DE LOS ARCHIVOS DEL ACTIVO
            $asset->asset_document_count = $asset->asset_document_count - 1;
            $asset->save();
            $node = Doctrine::getTable('Node')->find($node_id);

            $this->syslog->register('delete_asset_document', array(
                $asset_document->asset_document_filename,
                $asset->asset_name,
                $node->getPath()
            )); // registering log

            $success = 'true';

            $asset_document->delete();
            //Quitar el archivo
            $path = './asset_doc/';
            $file_full_path = $path . $asset_document->asset_document_name;
            unlink($file_full_path);
            $msg = $this->translateTag('Documen', 'successfully_deleted_document');
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function download($asset_document_id) {
        $this->load->helper('download');
        $asset_document = Doctrine_Core::getTable('AssetDocument')->find($asset_document_id);
        $file_name = $asset_document->asset_document_filename;
        $data = file_get_contents($this->config->item('asset_doc_dir') . $asset_document->asset_document_name); // Read the file's contents
        force_download($file_name, $data);
    }

    function update() {

        $AssetDocument = Doctrine_Core::getTable('AssetDocument')->find($this->input->post('asset_document_id'));

        $asset_document_description_antes = $AssetDocument->asset_document_description;
        $AssetDocument->asset_document_description = $this->input->post('asset_document_description');
        $asset_document_description_despues = $AssetDocument->asset_document_description;

        $asset_document_comments_antes = $AssetDocument->asset_document_comments;
        $AssetDocument->asset_document_comments = $this->input->post('asset_document_comments');
        $asset_document_comments_despues = $AssetDocument->asset_document_comments;

        $Asset = Doctrine_Core::getTable('Asset')->find($AssetDocument->asset_id);
        $node = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));

        $asset_name = $Asset->asset_name;
        try {
            $AssetDocument->save();
            $msg = $this->translateTag('General', 'operation_successful');
            $success = true;

            $log_id = $this->syslog->register('update_asset_document', array(
                $Asset->asset_name,
                $node->getPath()
            )); // registering log


            if ($asset_document_description_antes != $asset_document_description_despues) {
                if ($log_id) {

                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'description');
                    $logDetail->log_detail_value_old = $asset_document_description_antes;
                    $logDetail->log_detail_value_new = $asset_document_description_despues;
                    $logDetail->save();
                }
            }

            if ($asset_document_comments_antes != $asset_document_comments_despues) {
                if ($log_id) {

                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = "comentarios";
                    $logDetail->log_detail_value_old = $asset_document_comments_antes;
                    $logDetail->log_detail_value_new = $asset_document_comments_despues;
                    $logDetail->save();
                }
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

}
