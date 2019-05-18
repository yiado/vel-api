<?php

/**
 * @package    Controller
 * @subpackage AssetController
 */
class AssetController extends APP_Controller {

    function AssetController() {
        parent::APP_Controller();
    }

    /**
     * get
     *
     * Lista los equipos asociados a un nodo
     *
     * @post int node_id
     */
    function getOne() {
        $asset_id = $this->input->post('asset_id');
        $assetTable = Doctrine_Core::getTable('Asset');
        $asset = $assetTable->retrieveOne($asset_id);
        if ($this->input->post('asset_id') && $asset->count()) {
            echo '({"success": true, "data":' . $this->json->encode($asset->toArray()) . '})';
        } else {
            echo '({"success": false, "data":{}})';
        }
    }

    function get() {
        $node_id = $this->input->post('node_id');
        $text_query = $this->input->post('query');
        $text_autocomplete = (empty($text_query) ? $this->input->post('asset_name') : $this->input->post('query'));
        $asset_num_serie_intern = $this->input->post('asset_num_serie_intern');
        $asset_load_folio = $this->input->post('asset_load_folio');
        $asset_num_factura = $this->input->post('asset_num_factura');
        $start_date = $this->input->post('start_date_lifetime');
        $end_date = $this->input->post('end_date_lifetime');
        $operador_compra = $this->input->post('operador_compra');
        $valor_compra = $this->input->post('valor_compra');
        $assetTable = Doctrine_Core::getTable('Asset');

        if (!empty($asset_load_folio)) {
            $CI = & get_instance();
            $asset_load_folio = $CI->app->generateFolio($asset_load_folio);
        }

        $filters = array(
            'asset_name LIKE ?' => (!empty($text_autocomplete) ? $text_autocomplete . '%' : NULL),
            'asset_num_serie_intern LIKE ?' => (!empty($asset_num_serie_intern) ? $asset_num_serie_intern . '%' : NULL),
            'asset_num_factura LIKE ?' => (!empty($asset_num_factura) ? $asset_num_factura . '%' : NULL),
            'al.asset_load_folio = ?' => (!empty($asset_load_folio) ? $asset_load_folio : NULL),
            'asset_load_date >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'asset_load_date <= ?' => (!empty($end_date) ? $end_date : NULL ),
            'asset_cost LIKE ?' => (!empty($operador_compra) ? $operador_compra . '%' : NULL),
            'asset_type_id = ?' => $this->input->post('asset_type_id'),
            'brand_id = ?' => $this->input->post('brand_id'),
            'asset_status_id = ?' => $this->input->post('asset_status_id'),
            'asset_condition_id = ?' => $this->input->post('asset_condition_id')
        );
        $asset = $assetTable->retrieveByNodeId($filters, $node_id, $this->input->post('search_branch'), $this->input->post('written_off'), $this->input->post('start'), $this->input->post('limit'));

        if ($asset->count()) {
//            if ($assetCont->count() < 100) {
//                echo '({"total":"' . $assetCont->count() . '", "results":' . $this->json->encode($assetCont->toArray()) . '})';
//            } else {
            echo '({"total":"' . $assetTable->retrieveByNodeId($filters, $node_id, $this->input->post('search_branch'), $this->input->post('written_off'), null, null, true) . '", "results":' . $this->json->encode($asset->toArray()) . '})';
//            }
        } else {
            echo '({"total":"0", "results":[]})';
        }

//        $json_data = $this->json->encode(array('total' => $asset->count(), 'results' => $asset->toArray()));
//        echo $json_data;
    }

    function getBin() {

        $text_query = $this->input->post('query');
        $text_autocomplete = (empty($text_query) ? $this->input->post('asset_name') : $this->input->post('query'));
        $asset_num_serie_intern = $this->input->post('asset_num_serie_intern');
        $start_date = $this->input->post('start_date_lifetime');
        $end_date = $this->input->post('end_date_lifetime');
        $operador_compra = $this->input->post('operador_compra');
        $valor_compra = $this->input->post('valor_compra');
        $assetTable = Doctrine_Core::getTable('Asset');
        $filters = array(
            'asset_name LIKE ?' => (!empty($text_autocomplete) ? $text_autocomplete . '%' : NULL),
            'asset_num_serie_intern LIKE ?' => (!empty($asset_num_serie_intern) ? $asset_num_serie_intern . '%' : NULL),
            'asset_upload_date >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'asset_upload_date <= ?' => (!empty($end_date) ? $end_date : NULL ),
            'asset_cost LIKE ?' => (!empty($operador_compra) ? $operador_compra . '%' : NULL),
            'asset_type_id = ?' => $this->input->post('asset_type_id'),
            'brand_id = ?' => $this->input->post('brand_id'),
            'asset_status_id = ?' => $this->input->post('asset_status_id'),
            'asset_condition_id = ?' => $this->input->post('asset_condition_id')
        );
        $asset = $assetTable->retrieveByNodeIdPapelera($filters);
        $json_data = $this->json->encode(array('total' => $asset->count(), 'results' => $asset->toArray()));
        echo $json_data;
    }

    /**
     * enviarPapelera
     *
     * Envia a la Papelera todos los Activos seleccionados
     */
    function enviarPapelera() {
//Recibimos los parametros
        $asset_ids = $this->input->post('asset_id');

        try {
            $asset_ids = explode(",", $asset_ids);

            foreach ($asset_ids as $asset_id) {
                $findGrId = Doctrine_Core::getTable('Asset')->find($asset_id);
                $findGrId->asset_estate = 1;
                $findGrId->save();
                $success = true;
                $msg = $this->translateTag('General', 'sent_to_the_trash_registration_successfully');
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));
        echo $json_data;
    }

    /**
     * sacarPapelera
     *
     * Restaura todos los documentos seleccionados de la Papelera
     */
    function sacarPapelera() {
//Recibimos los parametros
        $asset_ids = $this->input->post('asset_id');

        try {
            $asset_ids = explode(",", $asset_ids);

            foreach ($asset_ids as $asset_id) {
                $findGrId = Doctrine_Core::getTable('Asset')->find($asset_id);
                $findGrId->asset_estate = 0;
                $findGrId->save();
                $success = true;
                $msg = $this->translateTag('General', 'successfully_restored_record');
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));
        echo $json_data;
    }

    /**
     * add
     *
     * Agrega un nuevo equipo asociado a un nodo
     *
     * @post int node_id
     * @post int brand_id
     * @post int asset_type_id
     * @post int asset_status_id
     * @post int asset_condition_id
     * @post string asset_name
     * @post string asset_num_serie
     * @post string asset_num_serie_intern
     * @post float asset_cost
     * @post float asset_current_cost
     * @post string asset_purchase_date
     * @post string asset_expiration_date
     * @post string asset_description
     */
    function add() {
        $asset = new Asset();
        $asset->node_id = $this->input->post('node_id');
        $asset->asset_type_id = $this->input->post('asset_type_id');
        $asset->brand_id = $this->input->post('brand_id');
        $asset->asset_status_id = $this->input->post('asset_status_id');
        $asset->asset_condition_id = $this->input->post('asset_condition_id');
        $asset->asset_name = $this->input->post('asset_name');
        $asset->asset_num_serie = $this->input->post('asset_num_serie');
        $asset->asset_num_serie_intern = $this->input->post('asset_num_serie_intern');
        $asset->asset_num_factura = $this->input->post('asset_num_factura');
        $asset->asset_cost = $this->input->post('asset_cost');
        $asset->asset_current_cost = $this->input->post('asset_current_cost');
        $asset_purchase_date = $this->input->post('asset_purchase_date');
        $asset->asset_purchase_date = $asset_purchase_date;
        $asset_lifetime = $this->input->post('asset_lifetime');
        $asset->asset_lifetime = $asset_lifetime;
        date_default_timezone_set('UTC');
        $asset->asset_load_date = date("Y-m-d");
        $asset->asset_estate = 0;
        $asset->asset_description = $this->input->post('asset_description');
        try {
            $findNumInt = Doctrine_Core::getTable('Asset')->findNumInt($this->input->post('asset_num_serie_intern'));
            if ($findNumInt) {
                $success = false;
                $msg = $this->translateTag('Asset', 'there_is_internal_number');
            } else {
                $assetload = new AssetLoad();
                $assetload->user_id = $this->auth->get_user_data('user_id');
                $assetload->asset_load_date = date("Y-m-d");
                $CI = & get_instance();
                $nuevo_folio = Doctrine_Core :: getTable('AssetLoad')->lastFolioWo() + 1;
                $assetload->asset_load_folio = $CI->app->generateFolio($nuevo_folio);
                $assetload->save();

                //Calculamos la fecha de expiraciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n de la vida util en un campo separado para evitar futuras adaptaciones segÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âºn el motor de la BD
                if (!is_null($asset_purchase_date)) {
                    list($anio_compra, $mes_compra, $dia_compra) = explode('-', $asset_purchase_date);
                    $asset_expiration_date_lifetime = date('Y-m-d', mktime(0, 0, 0, $mes_compra, $dia_compra, $anio_compra + $asset_lifetime));
                    $asset->asset_expiration_date_lifetime = $asset_expiration_date_lifetime;
                } else {
                    $asset->asset_expiration_date_lifetime = null;
                }
                $asset->asset_load_id = $assetload->asset_load_id;
                $asset->save();
                $node = $asset->node_id;
                $user_id = $this->session->userdata('user_id');

                //Trae el valor del activo que se esta creando
                $selected_asset_ids = $asset->asset_id;

                //BUSCAR LA RUTA DE ORIGEN DEL ASSET
                $node = Doctrine_Core::getTable('Node')->find($node);
                $asset_log_detail = $node->getPath();
                $asset_log_type = 'asset_log_creation';

                $log_id = $this->syslog->register('add_asset', array(
                    $asset->asset_name,
                    $node->getPath()
                )); // registering log

                Doctrine_Core::getTable('AssetLog')->logMoveAsset($selected_asset_ids, $user_id, $asset_log_type, $asset_log_detail);
                $success = true;
                $msg = $this->translateTag('General', 'operation_successful');
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     *
     * Modifica un determinado equipo de un nodo
     *
     * @post int asset_id
     * @post int node_id
     * @post int brand_id
     * @post int asset_type_id
     * @post int asset_status_id
     * @post int asset_condition_id
     * @post string asset_name
     * @post int asset_num_serie
     * @post int asset_num_serie_intern
     * @post float asset_cost
     * @post float asset_current_cost
     * @post string asset_purchase_date
     * @post string asset_expiration_date
     * @post string asset_description
     */
    function update() {
        $asset = Doctrine_Core::getTable('Asset')->find($this->input->post('asset_id'));

        $asset_name = $asset->asset_name;
        $asset_num_serie = $asset->asset_num_serie;
        $asset_num_serie_intern = $asset->asset_num_serie_intern;
        $asset_num_factura = $asset->asset_num_factura;
        $asset_cost = $asset->asset_cost;
        $asset_current_cost = $asset->asset_current_cost;
        $asset_purchase_date_log = $asset->asset_purchase_date;
        $asset_lifetime_log = $asset->asset_lifetime;
        $asset_estate = $asset->asset_estate;
        $asset_description = $asset->asset_description;


        $brand_antes = Doctrine_Core::getTable('Brand')->find($asset->brand_id);
        $brand_antes->brand_name;
        $brand_ahora = Doctrine_Core::getTable('Brand')->find($this->input->post('brand_id'));
        $brand_ahora->brand_name;

        $asset_type_antes = Doctrine_Core::getTable('AssetType')->find($asset->asset_type_id);
        $asset_type_antes->asset_type_name;
        $asset_type_ahora = Doctrine_Core::getTable('AssetType')->find($this->input->post('asset_type_id'));
        $asset_type_ahora->asset_type_name;

        $asset_status_antes = Doctrine_Core::getTable('AssetStatus')->find($asset->asset_status_id);
        $asset_status_antes->asset_status_name;
        $asset_status_ahora = Doctrine_Core::getTable('AssetStatus')->find($this->input->post('asset_status_id'));
        $asset_status_ahora->asset_status_name;

        $asset_condition_antes = Doctrine_Core::getTable('AssetCondition')->find($asset->asset_condition_id);
        $asset_condition_antes->asset_condition_name;
        $asset_condition_ahora = Doctrine_Core::getTable('AssetCondition')->find($this->input->post('asset_condition_id'));
        $asset_condition_ahora->asset_condition_name;

        $asset->asset_condition_id = $this->input->post('asset_condition_id');
        $asset->asset_name = $this->input->post('asset_name');
        $asset->asset_num_serie = $this->input->post('asset_num_serie');
        $asset->asset_num_serie_intern = $this->input->post('asset_num_serie_intern');
        $asset->asset_num_factura = $this->input->post('asset_num_factura');
        $asset->asset_cost = $this->input->post('asset_cost');
        $asset->asset_current_cost = $this->input->post('asset_current_cost');
        $asset_purchase_date = $this->input->post('asset_purchase_date');
        $asset->asset_purchase_date = $asset_purchase_date;
        $asset_lifetime = $this->input->post('asset_lifetime');
        $asset->asset_lifetime = $asset_lifetime;
        $asset_estate = $this->input->post('asset_estate');

        $node = Doctrine_Core::getTable('Node')->find($asset->node_id);
        $asset_log_detail = $node->getPath();

        $fecha = $this->input->post('asset_purchase_date');
        list($fecha) = explode("T", $fecha);

        $fecha1 = $fecha;
        $fecha2 = date("d/m/Y", strtotime($fecha1));

        $fecha3 = $asset_purchase_date_log;
        $fecha4 = date("d/m/Y", strtotime($fecha3));

        try {
            $findNumInt = Doctrine_Core::getTable('Asset')->findNumInt($this->input->post('asset_num_serie_intern'));
            if ($findNumInt and ( $this->input->post('asset_num_serie_intern') != $findNumInt->asset_num_serie_intern)) {
                if ($this->input->post('asset_num_serie_intern') == $findNumInt->asset_num_serie_intern) {

                    $log_id = $this->syslog->register('update_asset', array(
                        $asset->asset_name,
                        $asset_log_detail
                    )); // registering log

                    if ($asset_name != $this->input->post('asset_name')) {
                        if ($log_id) {
                            $asset->asset_name = $this->input->post('asset_name');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('General', 'name');
                            $logDetail->log_detail_value_old = $asset_name;
                            $logDetail->log_detail_value_new = $this->input->post('asset_name');
                            $logDetail->save();
                        }
                    }

                    if ($asset_num_serie != $this->input->post('asset_num_serie')) {
                        if ($log_id) {
                            $asset->asset_num_serie = $this->input->post('asset_num_serie');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('Asset', 'serial_number');
                            $logDetail->log_detail_value_old = $asset_num_serie;
                            $logDetail->log_detail_value_new = $this->input->post('asset_num_serie');
                            $logDetail->save();
                        }
                    }

                    if ($asset_num_serie_intern != $this->input->post('asset_num_serie_intern')) {
                        if ($log_id) {
                            $asset->asset_num_serie_intern = $this->input->post('asset_num_serie_intern');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('Asset', 'internal_number');
                            $logDetail->log_detail_value_old = $asset_num_serie_intern;
                            $logDetail->log_detail_value_new = $this->input->post('asset_num_serie_intern');
                            $logDetail->save();
                        }
                    }

                    if ($asset_num_factura != $this->input->post('asset_num_factura')) {
                        if ($log_id) {
                            $asset->asset_num_factura = $this->input->post('asset_num_factura');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('Asset', 'invoice_number');
                            $logDetail->log_detail_value_old = $asset_num_factura;
                            $logDetail->log_detail_value_new = $this->input->post('asset_num_factura');
                            $logDetail->save();
                        }
                    }

                    if ($brand_antes->brand_name != $brand_ahora->brand_name) {
                        if ($log_id) {
                            $asset->brand_id = $this->input->post('brand_id');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('General', 'brand');
                            $logDetail->log_detail_value_old = $brand_antes->brand_name;
                            $logDetail->log_detail_value_new = $brand_ahora->brand_name;
                            $logDetail->save();
                        }
                    }

                    if ($asset_type_antes->asset_type_name != $asset_type_ahora->asset_type_name) {
                        if ($log_id) {
                            $asset->asset_type_id = $this->input->post('asset_type_id');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('Asset', 'asset_type');
                            $logDetail->log_detail_value_old = $asset_type_antes->asset_type_name;
                            $logDetail->log_detail_value_new = $asset_type_ahora->asset_type_name;
                            $logDetail->save();
                        }
                    }

                    if ($asset_status_antes->asset_status_name != $asset_status_ahora->asset_status_name) {
                        if ($log_id) {
                            $asset->asset_status_id = $this->input->post('asset_status_id');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('General', 'state');
                            $logDetail->log_detail_value_old = $asset_status_antes->asset_status_name;
                            $logDetail->log_detail_value_new = $asset_status_ahora->asset_status_name;
                            $logDetail->save();
                        }
                    }

                    if ($asset_condition_antes->asset_condition_name != $asset_condition_ahora->asset_condition_name) {
                        if ($log_id) {
                            $asset->asset_condition_id = $this->input->post('asset_condition_id');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('General', 'condition');
                            $logDetail->log_detail_value_old = $asset_condition_antes->asset_condition_name;
                            $logDetail->log_detail_value_new = $asset_condition_ahora->asset_condition_name;
                            $logDetail->save();
                        }
                    }

                    if ($asset_cost != $this->input->post('asset_cost')) {
                        if ($log_id) {
                            $asset->asset_cost = $this->input->post('asset_cost');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('Asset', 'purchase_value');
                            $logDetail->log_detail_value_old = $asset_cost;
                            $logDetail->log_detail_value_new = $this->input->post('asset_cost');
                            $logDetail->save();
                        }
                    }

                    if ($asset_current_cost != $this->input->post('asset_current_cost')) {
                        if ($log_id) {
                            $asset->asset_current_cost = $this->input->post('asset_current_cost');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('Asset', 'current_cost');
                            $logDetail->log_detail_value_old = $asset_current_cost;
                            $logDetail->log_detail_value_new = $this->input->post('asset_current_cost');
                            $logDetail->save();
                        }
                    }

                    if ($fecha4 != $fecha2) {
                        if ($log_id) {
                            $asset->asset_purchase_date = $this->input->post('asset_purchase_date');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('Asset', 'purchase_date');
                            $logDetail->log_detail_value_old = $fecha4;
                            $logDetail->log_detail_value_new = $fecha2;
                            $logDetail->save();
                        }
                    }

                    if ($asset_lifetime_log != $this->input->post('asset_lifetime')) {
                        if ($log_id) {
                            $asset->asset_lifetime = $this->input->post('asset_lifetime');
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('Asset', 'lifetime');
                            $logDetail->log_detail_value_old = $asset_lifetime_log;
                            $logDetail->log_detail_value_new = $this->input->post('asset_lifetime');
                            $logDetail->save();
                        }
                    }

                    if ($asset_description != $this->input->post('asset_description')) {
                        if ($log_id) {
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('General', 'description');
                            $logDetail->log_detail_value_old = $asset_description;
                            $logDetail->log_detail_value_new = $this->input->post('asset_description');
                            $logDetail->save();
                        }
                    }


                    //Transformar 
                    $asset->asset_estate = ( $asset_estate == 'true' ? '1' : '0' );

                    //Enviamos los registro a la tabla log en caso de que el estado del Activo pase a Baja
                    if ($asset->asset_estate == 1) {

                        $user_id = $this->session->userdata('user_id');
                        $selected_asset_ids = $asset->asset_id;


                        if ($log_id) {
                            $logDetail = new LogDetail();
                            $logDetail->log_id = $log_id;
                            $logDetail->log_detail_param = $this->translateTag('General', 'unsubscribe');
                            $logDetail->log_detail_value_old = "Falso";
                            $logDetail->log_detail_value_new = "True";
                            $logDetail->save();
                        }

                        //BUSCAR LA RUTA DE ORIGEN DEL ASSET


                        $asset_log_type = 'asset_log_low';
                        Doctrine_Core::getTable('AssetLog')->logMoveAsset($selected_asset_ids, $user_id, $asset_log_type, $asset_log_detail);
                    }
                    $asset->asset_description = $this->input->post('asset_description');
                    $asset->asset_last_inventory = $this->input->post('asset_last_inventory');

                    //Calculamos la fecha de expiraciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n de la vida util en un campo separado para evitar futuras adaptaciones segÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âºn el motor de la BD
                    if (!is_null($asset_purchase_date)) {
                        list($anio_compra, $mes_compra, $dia_compra) = explode('-', $asset_purchase_date);
                        $asset_expiration_date_lifetime = date('Y-m-d', mktime(0, 0, 0, $mes_compra, $dia_compra, $anio_compra + $asset_lifetime));
                        $asset->asset_expiration_date_lifetime = $asset_expiration_date_lifetime;
                    } else {
                        $asset->asset_expiration_date_lifetime = null;
                    }


                    foreach ($this->input->postall() as $attr_id => $val) {

                        if (is_numeric($attr_id)) {

                            $value = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAttributeAsset($this->input->post('asset_id'), $attr_id);

                            if ($value === false) {
                                $value = new AssetOtherDataValue();
                            }

                            $asset_value = $value->asset_other_data_value_value;

                            $value->asset_other_data_attribute_id = $attr_id;
                            $value->asset_id = $this->input->post('asset_id');
                            $value->asset_other_data_value_value = $val;
                            $value->save();

                            $other_data_attribute = Doctrine_Core::getTable('AssetOtherDataAttribute')->find($value->asset_other_data_attribute_id);
                            $asset_other_data_attribute_name = $other_data_attribute->asset_other_data_attribute_name;

                            if ($asset_value != $val) {
                                if ($log_id) {
                                    $logDetail = new LogDetail();
                                    $logDetail->log_id = $log_id;
                                    $logDetail->log_detail_param = $this->translateTag('General', 'other_data');
                                    $logDetail->log_detail_value_old = $asset_other_data_attribute_name . " " . $asset_value;
                                    $logDetail->log_detail_value_new = $asset_other_data_attribute_name . " " . $value->asset_other_data_value_value;
                                    $logDetail->save();
                                }
                            }
                        }
                    }

                    $asset->asset_type_id = $this->input->post('asset_type_id');
                    $asset->asset_status_id = $this->input->post('asset_status_id');
                    $asset->brand_id = $brand_ahora->brand_id;

                    $asset->save();
                    $msg = $this->translateTag('General', 'operation_successful');
                    $success = true;
                } else {
                    $success = false;
                    $msg = $this->translateTag('Asset', 'there_is_internal_number');
                }
            } else {

                $log_id = $this->syslog->register('update_asset', array(
                    $asset->asset_name,
                    $asset_log_detail
                )); // registering log

                if ($asset_name != $this->input->post('asset_name')) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('General', 'name');
                        $logDetail->log_detail_value_old = $asset_name;
                        $logDetail->log_detail_value_new = $this->input->post('asset_name');
                        $logDetail->save();
                    }
                }

                if ($asset_num_serie != $this->input->post('asset_num_serie')) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Asset', 'serial_number');
                        $logDetail->log_detail_value_old = $asset_num_serie;
                        $logDetail->log_detail_value_new = $this->input->post('asset_num_serie');
                        $logDetail->save();
                    }
                }

                if ($asset_num_serie_intern != $this->input->post('asset_num_serie_intern')) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Asset', 'internal_number');
                        $logDetail->log_detail_value_old = $asset_num_serie_intern;
                        $logDetail->log_detail_value_new = $this->input->post('asset_num_serie_intern');
                        $logDetail->save();
                    }
                }

                if ($asset_num_factura != $this->input->post('asset_num_factura')) {
                    if ($log_id) {
                        $asset->asset_num_factura = $this->input->post('asset_num_factura');
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Asset', 'invoice_number');
                        $logDetail->log_detail_value_old = $asset_num_factura;
                        $logDetail->log_detail_value_new = $this->input->post('asset_num_factura');
                        $logDetail->save();
                    }
                }

                if ($brand_antes->brand_name != $brand_ahora->brand_name) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('General', 'brand');
                        $logDetail->log_detail_value_old = $brand_antes->brand_name;
                        $logDetail->log_detail_value_new = $brand_ahora->brand_name;
                        $logDetail->save();
                    }
                }

                if ($asset_type_antes->asset_type_name != $asset_type_ahora->asset_type_name) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Asset', 'asset_type');
                        $logDetail->log_detail_value_old = $asset_type_antes->asset_type_name;
                        $logDetail->log_detail_value_new = $asset_type_ahora->asset_type_name;
                        $logDetail->save();
                    }
                }

                if ($asset_status_antes->asset_status_name != $asset_status_ahora->asset_status_name) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('General', 'state');
                        $logDetail->log_detail_value_old = $asset_status_antes->asset_status_name;
                        $logDetail->log_detail_value_new = $asset_status_ahora->asset_status_name;
                        $logDetail->save();
                    }
                }

                if ($asset_condition_antes->asset_condition_name != $asset_condition_ahora->asset_condition_name) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('General', 'condition');
                        $logDetail->log_detail_value_old = $asset_condition_antes->asset_condition_name;
                        $logDetail->log_detail_value_new = $asset_condition_ahora->asset_condition_name;
                        $logDetail->save();
                    }
                }

                if ($asset_cost != $this->input->post('asset_cost')) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Asset', 'purchase_value');
                        $logDetail->log_detail_value_old = $asset_cost;
                        $logDetail->log_detail_value_new = $this->input->post('asset_cost');
                        $logDetail->save();
                    }
                }

                if ($asset_current_cost != $this->input->post('asset_current_cost')) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Asset', 'current_cost');
                        $logDetail->log_detail_value_old = $asset_current_cost;
                        $logDetail->log_detail_value_new = $this->input->post('asset_current_cost');
                        $logDetail->save();
                    }
                }

                if ($fecha4 != $fecha2) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Asset', 'purchase_date');
                        $logDetail->log_detail_value_old = $fecha4;
                        $logDetail->log_detail_value_new = $fecha2;
                        $logDetail->save();
                    }
                }

                if ($asset_lifetime_log != $this->input->post('asset_lifetime')) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('Asset', 'lifetime');
                        $logDetail->log_detail_value_old = $asset_lifetime_log;
                        $logDetail->log_detail_value_new = $this->input->post('asset_lifetime');
                        $logDetail->save();
                    }
                }

                if ($asset_description != $this->input->post('asset_description')) {
                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('General', 'description');
                        $logDetail->log_detail_value_old = $asset_description;
                        $logDetail->log_detail_value_new = $this->input->post('asset_description');
                        $logDetail->save();
                    }
                }


                //Transformar 
                $asset->asset_estate = ( $asset_estate == 'true' ? '1' : '0' );

                //Enviamos los registro a la tabla log en caso de que el estado del Activo pase a Baja
                if ($asset->asset_estate == 1) {

                    $user_id = $this->session->userdata('user_id');
                    $selected_asset_ids = $asset->asset_id;


                    if ($log_id) {
                        $logDetail = new LogDetail();
                        $logDetail->log_id = $log_id;
                        $logDetail->log_detail_param = $this->translateTag('General', 'unsubscribe');
                        $logDetail->log_detail_value_old = "Falso";
                        $logDetail->log_detail_value_new = "True";
                        $logDetail->save();
                    }

                    //BUSCAR LA RUTA DE ORIGEN DEL ASSET


                    $asset_log_type = 'asset_log_low';
                    Doctrine_Core::getTable('AssetLog')->logMoveAsset($selected_asset_ids, $user_id, $asset_log_type, $asset_log_detail);
                }
                $asset->asset_description = $this->input->post('asset_description');
                $asset->asset_last_inventory = $this->input->post('asset_last_inventory');

                //Calculamos la fecha de expiraciÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n de la vida util en un campo separado para evitar futuras adaptaciones segÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âºn el motor de la BD
                if (!is_null($asset_purchase_date)) {
                    list($anio_compra, $mes_compra, $dia_compra) = explode('-', $asset_purchase_date);
                    $asset_expiration_date_lifetime = date('Y-m-d', mktime(0, 0, 0, $mes_compra, $dia_compra, $anio_compra + $asset_lifetime));
                    $asset->asset_expiration_date_lifetime = $asset_expiration_date_lifetime;
                } else {
                    $asset->asset_expiration_date_lifetime = null;
                }

                $asset->asset_type_id = $this->input->post('asset_type_id');
                $asset->asset_status_id = $this->input->post('asset_status_id');
                $asset->brand_id = $brand_ahora->brand_id;

                $asset->save();



                foreach ($this->input->postall() as $attr_id => $val) {

                    if (is_numeric($attr_id)) {

                        $value = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAttributeAsset($this->input->post('asset_id'), $attr_id);

                        if ($value === false) {
                            $value = new AssetOtherDataValue();
                        }

                        $asset_value = $value->asset_other_data_value_value;

                        $value->asset_other_data_attribute_id = $attr_id;
                        $value->asset_id = $this->input->post('asset_id');
                        $value->asset_other_data_value_value = $val;
                        $value->save();

                        $other_data_attribute = Doctrine_Core::getTable('AssetOtherDataAttribute')->find($value->asset_other_data_attribute_id);
                        $asset_other_data_attribute_name = $other_data_attribute->asset_other_data_attribute_name;

                        if ($asset_value != $val) {
                            if ($log_id) {
                                $logDetail = new LogDetail();
                                $logDetail->log_id = $log_id;
                                $logDetail->log_detail_param = $this->translateTag('General', 'other_data');
                                $logDetail->log_detail_value_old = $asset_other_data_attribute_name . " " . $asset_value;
                                $logDetail->log_detail_value_new = $asset_other_data_attribute_name . " " . $value->asset_other_data_value_value;
                                $logDetail->save();
                            }
                        }
                    }
                }
                $msg = $this->translateTag('General', 'operation_successful');
                $success = true;
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * delete
     *
     * Elimina un equipo de un nodo
     *
     * @post integer asset_id
     */
    function delete() {
        $asset_id = $this->input->post('asset_id');
        $asset = Doctrine::getTable('Asset')->find($asset_id);
        $node = Doctrine::getTable('Node')->find($asset->node_id);


        try {

            $asset = Doctrine::getTable('Asset')->find($asset_id);
            if ($asset->delete()) {
                $this->syslog->register('delete_asset', array(
                    $asset->asset_name,
                    $node->getPath()
                )); // registering log

                $success = true;
                $msg = $this->translateTag('General', 'operation_successful');
            } else {
                $success = false;
                $msg = $this->translateTag('General', 'error');
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * exportList
     *
     * Rediccciona al metodo dependiendo del tipo de salida (pdf, excel)
     *
     */
    function exportList() {

        $node_id = $this->input->post('node_id');
        switch ($this->input->post('output_type')) {
            case 'e':
                $this->exportListExcel($node_id);
                break;

            case 'p':
                $this->exportListPDF($node_id);
                break;
        }
    }

    /**
     * exportListExcel
     *
     * Exporta el listado actual en formato excel
     *
     */
    function exportListExcel($node_id = null) {

        ini_set('memory_limit', '2048M');
        set_time_limit('60000000');
        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');


        $node_id = $this->input->post('node_id');
        $text_query = $this->input->post('query');
        $text_autocomplete = (empty($text_query) ? $this->input->post('asset_name') : $this->input->post('query'));
        $asset_num_serie_intern = $this->input->post('asset_num_serie_intern');
        $asset_num_factura = $this->input->post('asset_num_factura');
        $start_date = $this->input->post('start_date_lifetime');
        $end_date = $this->input->post('end_date_lifetime');
        $operador_compra = $this->input->post('operador_compra');
        $valor_compra = $this->input->post('valor_compra');
        $assetTable = Doctrine_Core::getTable('Asset');

        $filters = array(
            'asset_name LIKE ?' => (!empty($text_autocomplete) ? $text_autocomplete . '%' : NULL),
            'asset_num_serie_intern LIKE ?' => (!empty($asset_num_serie_intern) ? $asset_num_serie_intern . '%' : NULL),
            'asset_num_factura LIKE ?' => (!empty($asset_num_factura) ? $asset_num_factura . '%' : NULL),
            'asset_load_date >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'asset_load_date <= ?' => (!empty($end_date) ? $end_date : NULL ),
            'asset_cost LIKE ?' => (!empty($operador_compra) ? $operador_compra . '%' : NULL),
            'asset_type_id = ?' => $this->input->post('asset_type_id'),
            'brand_id = ?' => $this->input->post('brand_id'),
            'asset_status_id = ?' => $this->input->post('asset_status_id'),
            'asset_condition_id = ?' => $this->input->post('asset_condition_id')
        );

        $assets = $assetTable->retrieveByNodeId($filters, $node_id, $this->input->post('search_branch'), $this->input->post('written_off'));
//        print_r($assets); exit();
        $sheet->setCellValue('A1', $this->translateTag('Asset', 'folio_number'))
                ->setCellValue('B1', $this->translateTag('General', 'name'))
                ->setCellValue('C1', $this->translateTag('Asset', 'num_series'))
                ->setCellValue('D1', $this->translateTag('Asset', 'num_series_internal'))
                ->setCellValue('E1', $this->translateTag('Asset', 'invoice_number'))
                ->setCellValue('F1', $this->translateTag('General', 'brand'))
                ->setCellValue('G1', $this->translateTag('General', 'type'))
                ->setCellValue('H1', $this->translateTag('General', 'state'))
                ->setCellValue('I1', $this->translateTag('General', 'condition'))
                ->setCellValue('J1', $this->translateTag('Asset', 'purchase_value'))
                ->setCellValue('K1', $this->translateTag('Asset', 'present_value'))
                ->setCellValue('L1', $this->translateTag('Asset', 'purchase_date'))
                ->setCellValue('M1', $this->translateTag('General', 'asset_load_date'))
                ->setCellValue('N1', $this->translateTag('Asset', 'lifetime'))
                ->setCellValue('O1', $this->translateTag('General', 'description'))
                ->setCellValue('P1', $this->translateTag('Core', 'location'))
                ->setCellValue('Q1', "CODIGO AUGE");

        $rcount = 1;
        foreach ($assets as $asset) {

            if ($asset->asset_load_id) {
                $assetFolio = Doctrine_Core::getTable('AssetLoad')->find($asset->asset_load_id);
                $asset_load_folio = $assetFolio->asset_load_folio;
            } else {
                $asset_load_folio = '';
            }



            $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);

            if ($assetOtherDatas) {

                $value = $assetOtherDatas->asset_other_data_value_value;
            } else {
                $value = "";
            }

            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $asset_load_folio)
                    ->setCellValueExplicit('B' . $rcount, $asset->asset_name)
                    ->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $asset->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E' . $rcount, $asset->asset_num_factura, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('F' . $rcount, $asset->Brand->brand_name)
                    ->setCellValue('G' . $rcount, $asset->AssetType->asset_type_name)
                    ->setCellValue('H' . $rcount, $asset->AssetStatus->asset_status_name)
                    ->setCellValue('I' . $rcount, $asset->AssetCondition->asset_condition_name)
                    ->setCellValue('J' . $rcount, $asset->asset_cost)
                    ->setCellValue('K' . $rcount, $asset->asset_current_cost)
                    ->setCellValue('L' . $rcount, $asset->asset_purchase_date)
                    ->setCellValue('M' . $rcount, $asset->asset_load_date)
                    ->setCellValue('N' . $rcount, $asset->asset_lifetime)
                    ->setCellValue('O' . $rcount, $asset->asset_description)
                    ->setCellValue('P' . $rcount, $asset->asset_path)
                    ->setCellValueExplicit('Q' . $rcount, $value, PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);

        $sheet->getStyle('A1:Q1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:Q1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:Q' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));

        echo '{"success": true, "file": "' . $this->input->post('file_name') . '.xls"}';

        $node_all = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));
        $this->syslog->register('export_list_asset', array(
            $this->input->post('file_name') . '.xls',
            $node_all->getPath()
        )); // registering log
    }

    /**
     * exportListPDF
     *
     * Exporta el listado actual en formato pdf
     *
     */
    function exportListPDF($node_id = null) {
        ini_set('memory_limit', '2048M');
        set_time_limit('60000000');
        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');

        $sheet->setCellValue('A1', $this->translateTag('General', 'name'))
                ->setCellValue('B1', $this->translateTag('Asset', 'num_series'))
                ->setCellValue('C1', $this->translateTag('Asset', 'num_series_internal'))
                ->setCellValue('D1', $this->translateTag('Asset', 'invoice_number'))
                ->setCellValue('E1', $this->translateTag('General', 'type'))
                ->setCellValue('F1', $this->translateTag('General', 'state'))
                ->setCellValue('G1', $this->translateTag('General', 'condition'));


        // Filtros
        $node_id = $this->input->post('node_id');
        $text_query = $this->input->post('query');
        $text_autocomplete = (empty($text_query) ? $this->input->post('asset_name') : $this->input->post('query'));
        $asset_num_serie_intern = $this->input->post('asset_num_serie_intern');
        $asset_num_factura = $this->input->post('asset_num_factura');
        $start_date = $this->input->post('start_date_lifetime');
        $end_date = $this->input->post('end_date_lifetime');
        $operador_compra = $this->input->post('operador_compra');
        $valor_compra = $this->input->post('valor_compra');
        $assetTable = Doctrine_Core::getTable('Asset');

        $rcount = 1;
        $filters = array(
            'asset_name LIKE ?' => (!empty($text_autocomplete) ? $text_autocomplete . '%' : NULL),
            'asset_num_serie_intern LIKE ?' => (!empty($asset_num_serie_intern) ? $asset_num_serie_intern . '%' : NULL),
            'asset_num_factura LIKE ?' => (!empty($asset_num_factura) ? $asset_num_factura . '%' : NULL),
            'asset_load_date >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'asset_load_date <= ?' => (!empty($end_date) ? $end_date : NULL ),
            'asset_cost LIKE ?' => (!empty($operador_compra) ? $operador_compra . '%' : NULL),
            'asset_type_id = ?' => $this->input->post('asset_type_id'),
            'brand_id = ?' => $this->input->post('brand_id'),
            'asset_status_id = ?' => $this->input->post('asset_status_id'),
            'asset_condition_id = ?' => $this->input->post('asset_condition_id')
        );

        $assets = $assetTable->retrieveByNodeId($filters, $node_id, $this->input->post('search_branch'), $this->input->post('written_off'));

        foreach ($assets as $asset) {
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $asset->asset_num_serie, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $asset->asset_num_factura, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('E' . $rcount, $asset->AssetType->asset_type_name)
                    ->setCellValue('F' . $rcount, $asset->AssetStatus->asset_status_name)
                    ->setCellValue('G' . $rcount, $asset->AssetCondition->asset_condition_name);
        }

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);

        $sheet->getStyle('A1:G1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:G1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:G' . $rcount)->getFont()->applyFromArray(array(
            'name' => 'Arial',
            'size' => 8
        ));

        $sheet->getStyle('A1:G' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $this->phpexcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'PDF');
        $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.pdf'));

        echo '{"success": true, "file": "' . $this->input->post('file_name') . '.pdf"}';

        $node_all = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));
        $this->syslog->register('export_list_asset', array(
            $this->input->post('file_name') . '.pdf',
            $node_all->getPath()
        )); // registering log
    }

    function paste() {
        $node_destino = $this->input->post('node_id');
        $user_id = $this->session->userdata('user_id');
        $selected_asset_ids = $this->session->userdata('selected_asset_ids');

        //RESCATA EL ULTIMO VALOR DEL ARREGLO   
        $arregloAsset = explode(",", $selected_asset_ids);
        $endAssetId = end($arregloAsset);

        //BUSCAR UN NODO A PARTIR DEL ULTIMO ASSET DEL ARREGLO
        $node_origen = Doctrine_Core::getTable('Asset')->findNodeOrigen($endAssetId);
        $node_id = $node_origen->node_id;

        //BUSCAR LA RUTA DE ORIGEN DEL ASSET
        $node = Doctrine_Core::getTable('Node')->find($node_id);
        $asset_log_detail = $node->getPath();

        $asset_log_type = 'asset_log_move';

        if ($node_destino == $node_id) {
            $success = false;
            $msg = $this->translateTag('Asset', 'can_not_be_relocated_in_the_same_place');
        } else {

            try {
                Doctrine_Core::getTable('Asset')->cutAndPasteAsset($selected_asset_ids, $node_destino);
                Doctrine_Core::getTable('AssetLog')->logMoveAsset($selected_asset_ids, $user_id, $asset_log_type, $asset_log_detail);
                $msg = $this->translateTag('General', 'operation_successful');

                $node_destino_log = Doctrine::getTable('Node')->find($node_destino);
                $asset = Doctrine::getTable('Asset')->find($endAssetId);

                $this->syslog->register('move_asset', array(
                    $asset->asset_name,
                    $asset_log_detail,
                    $node_destino_log->getPath()
                )); // registering log
                $success = true;
            } catch (Exception $e) {
                $success = false;
                $msg = $e->getMessage();
            }
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function move() {
        $this->session->set_userdata('selected_asset_ids', $this->input->post('asset_id'));

        echo '{"success": false}';
    }

    function edit() {
        $action = $this->input->post('action');

        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            echo '{"success": false}';
        }
    }

    function resume() {

        $node_id = $this->input->post('node_id');

        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');
        $node_parent = Doctrine_Core::getTable('Node')->find($node_id);

        $q = Doctrine_Query::create()
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->where('.node_parent_id = ?', $node_parent->node_parent_id)
                ->andWhere('n.lft > ?', $node_parent->lft)
                ->andWhere('n.rgt < ?', $node_parent->rgt)
                ->orderBy('node_name');

        $resuls = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $rcont = 1;


        $sheet->setCellValue('A1', $this->translateTag('General', 'campus'));
        $sheet->setCellValue('B1', $this->translateTag('General', 'route'));

        $sheet->setCellValue('C1', $this->translateTag('Asset', 'chair'));
        $sheet->mergeCells('C1:D1');
        $sheet->setCellValue('C2', $this->translateTag('General', 'quantity'));
        $sheet->setCellValue('D2', $this->translateTag('General', 'value'));

        $sheet->setCellValue('E1', $this->translateTag('Asset', 'table'));
        $sheet->mergeCells('E1:F1');
        $sheet->setCellValue('E2', $this->translateTag('General', 'quantity'));
        $sheet->setCellValue('F2', $this->translateTag('General', 'value'));

        $sheet->setCellValue('G1', $this->translateTag('Asset', 'projector'));
        $sheet->mergeCells('G1:H1');
        $sheet->setCellValue('G2', $this->translateTag('General', 'quantity'));
        $sheet->setCellValue('H2', $this->translateTag('General', 'value'));

        $sheet->setCellValue('I1', $this->translateTag('Asset', 'split'));
        $sheet->mergeCells('I1:J1');
        $sheet->setCellValue('I2', $this->translateTag('General', 'quantity'));
        $sheet->setCellValue('J2', $this->translateTag('General', 'value'));

        $rcont = 2;
        foreach ($resuls as $node) {

            $rcont++;

            $node_a = Doctrine_Core::getTable('Node')->find($node_id);

            $silla = Doctrine_Core::getTable('Asset')->getTotals($node['node_id'], 8);
            $mesa = Doctrine_Core::getTable('Asset')->getTotals($node['node_id'], 9);
            $proyector = Doctrine_Core::getTable('Asset')->getTotals($node['node_id'], 12);
            $split = Doctrine_Core::getTable('Asset')->getTotals($node['node_id'], 4);

            $sheet->setCellValue('A' . $rcont, $node['node_name']);
            $sheet->setCellValue('B' . $rcont, $node_a->getPath());
            $sheet->setCellValue('C' . $rcont, @$silla->cantidad);
            $sheet->setCellValue('D' . $rcont, @$silla->costo_total);
            $sheet->setCellValue('E' . $rcont, @$mesa->cantidad);
            $sheet->setCellValue('F' . $rcont, @$mesa->costo_total);
            $sheet->setCellValue('G' . $rcont, @$proyector->cantidad);
            $sheet->setCellValue('H' . $rcont, @$proyector->costo_total);
            $sheet->setCellValue('I' . $rcont, @$split->cantidad);
            $sheet->setCellValue('J' . $rcont, @$split->costo_total);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);


        $sheet->getStyle('A1:J2')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:J2')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:J' . $rcont)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border :: BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($node_parent->node_name . '.xls'));
        echo '{"success": true, "file": "' . $node_parent->node_name . '.xls"}';
    }

    function exportCompleto() {

        ini_set('memory_limit', '2048M');
        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');

//        $node_id = $this->input->post('node_id');
        $node_id = 2; // TORRE 15
        $text_query = $this->input->post('query');
        $text_autocomplete = (empty($text_query) ? $this->input->post('asset_name') : $this->input->post('query'));
        $asset_num_serie_intern = $this->input->post('asset_num_serie_intern');
        $asset_num_factura = $this->input->post('asset_num_factura');
        $start_date = $this->input->post('start_date_lifetime');
        $end_date = $this->input->post('end_date_lifetime');
        $operador_compra = $this->input->post('operador_compra');
        $valor_compra = $this->input->post('valor_compra');
        $assetTable = Doctrine_Core::getTable('Asset');

        $filters = array(
            'asset_name LIKE ?' => (!empty($text_autocomplete) ? $text_autocomplete . '%' : NULL),
            'asset_num_serie_intern LIKE ?' => (!empty($asset_num_serie_intern) ? $asset_num_serie_intern . '%' : NULL),
            'asset_num_factura LIKE ?' => (!empty($asset_num_factura) ? $asset_num_factura . '%' : NULL),
            'asset_load_date >= ?' => (!empty($start_date) ? $start_date : NULL ),
            'asset_load_date <= ?' => (!empty($end_date) ? $end_date : NULL ),
            'asset_cost LIKE ?' => (!empty($operador_compra) ? $operador_compra . '%' : NULL),
            'asset_type_id = ?' => $this->input->post('asset_type_id'),
            'brand_id = ?' => $this->input->post('brand_id'),
//            'asset_status_id = ?' => $this->input->post('asset_status_id'),
            'asset_condition_id = ?' => $this->input->post('asset_condition_id')
        );

        $assets = $assetTable->retrieveByNodeIdCom($filters, $node_id, 1);



//        $assets = Doctrine_Core::getTable('Asset')->findTotalCompleto();

        $sheet->setCellValue('A1', "CODIGO DE RECINTO")
                ->setCellValue('B1', $this->translateTag('General', 'name'))
                ->setCellValue('C1', $this->translateTag('Asset', 'num_series'))
                ->setCellValue('D1', $this->translateTag('Asset', 'num_series_internal'))
                ->setCellValue('E1', $this->translateTag('General', 'brand'))
                ->setCellValue('F1', $this->translateTag('General', 'type'))
                ->setCellValue('G1', $this->translateTag('General', 'state'))
                ->setCellValue('H1', $this->translateTag('General', 'condition'))
                ->setCellValue('I1', $this->translateTag('Asset', 'purchase_value'))
                ->setCellValue('J1', $this->translateTag('Asset', 'present_value'))
                ->setCellValue('K1', $this->translateTag('Asset', 'purchase_date'))
                ->setCellValue('L1', $this->translateTag('General', 'asset_load_date'))
                ->setCellValue('M1', $this->translateTag('Asset', 'lifetime'))
                ->setCellValue('N1', $this->translateTag('General', 'description'))
                ->setCellValue('O1', $this->translateTag('Core', 'location'))
                ->setCellValue('P1', "CODIGO AUGE")
                ->setCellValue('Q1', "ESTADO ACTIVO");

        $rcount = 1;
        foreach ($assets as $asset) {
            $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAsset($asset->asset_id);

            if ($asset->asset_estate == 1) {
                $dado_baja = "DADO DE BAJA";
            } else {
                $dado_baja = "";
            }

            if ($assetOtherDatas) {

                $value = $assetOtherDatas->asset_other_data_value_value;
            } else {
                $value = "";
            }
            $nodeCodigo = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 46);
            if ($nodeCodigo) {

                $valueCodigo = $nodeCodigo->infra_other_data_value_value;
            } else {
                $valueCodigo = "";
            }

            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $valueCodigo, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $asset->asset_name)
                    ->setCellValueExplicit('C' . $rcount, $asset->asset_num_serie, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $asset->asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('E' . $rcount, $asset->Brand->brand_name)
                    ->setCellValue('F' . $rcount, $asset->AssetType->asset_type_name)
                    ->setCellValue('G' . $rcount, $asset->AssetStatus->asset_status_name)
                    ->setCellValue('H' . $rcount, $asset->AssetCondition->asset_condition_name)
                    ->setCellValue('I' . $rcount, $asset->asset_cost)
                    ->setCellValue('J' . $rcount, $asset->asset_current_cost)
                    ->setCellValue('K' . $rcount, $asset->asset_purchase_date)
                    ->setCellValue('L' . $rcount, $asset->asset_load_date)
                    ->setCellValue('M' . $rcount, $asset->asset_lifetime)
                    ->setCellValue('N' . $rcount, $asset->asset_description)
                    ->setCellValue('O' . $rcount, $asset->asset_path)
                    ->setCellValueExplicit('P' . $rcount, $value, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('Q' . $rcount, $dado_baja);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);



        $sheet->getStyle('A1:Q1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:Q1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:Q' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir("VACIADO DE ACTIVOS TORRE 15" . '.xls'));

        echo '{"success": true, "file": "' . "VACIADO DE ACTIVOS TORRE 15" . '.xls"}';
    }

    function cortarPegarTorre15() {

        ini_set('memory_limit', '2048M');

        define('DB_BD', 'igeo_u_2016_v2');
        define('DB_USUARIO', 'root');
        define('DB_PASS', '');
        define('DB_HOST', 'localhost');

        define("SECURE", TRUE);

        $conn = mysqli_connect(DB_HOST, DB_USUARIO, DB_PASS, DB_BD) or die("Error " . mysqli_error($conn));
        $conn->set_charset("utf8");

        if ($consulta = $conn->prepare("CALL consulta_activos()")) {
            $consulta->execute();
            $consulta->store_result();
            if ($consulta->num_rows > 0) {
                $consulta->bind_result($node_id, $asset_name, $asset_estate, $asset_num_serie_intern, $asset_load_date, $asset_num_factura, $asset_type_name, $asset_load_folio, $infra_other_data_value_value, $asset_other_data_value_value);

                $rows = array();
                while ($consulta->fetch()) {
                    $rows[] = array(
                        'node_id' => $node_id,
                        'asset_name' => $asset_name,
                        'asset_estate' => $asset_estate,
                        'asset_num_serie_intern' => $asset_num_serie_intern,
                        'asset_load_date' => $asset_load_date,
                        'asset_num_factura' => $asset_num_factura,
                        'asset_type_name' => $asset_type_name,
                        'asset_load_folio' => $asset_load_folio,
                        'infra_other_data_value_value' => $infra_other_data_value_value,
                        'asset_other_data_value_value' => $asset_other_data_value_value
                    );
//                  $rows['asset_path'] = Doctrine_Core::getTable ( 'Node' )->find ( $node_id )->getPath ();
                }
//                $final = $rows;
//                foreach ($rows as $value) {
//                    
//                }
                echo '<pre>';
                print_r($rows);
                echo '</pre>';
                exit;
            } else {
                return false;
            }
        } else {
            return false;
        }

        $consulta->close();

//        conectar();
//        $qry="CALL consulta_activos()";
//        $ejec=mysql_query($qry);
//        
//        echo '<pre>';
//        print_r($ejec);
//        echo '</pre>';
        exit;


        $node_id = 2; // TORRE 15
        $assets = Doctrine_Core::getTable('Asset')->retrieveByNodeIdTodo($node_id, 1);


        $node_nueva_posicion = 0; //Ingresar nuevo nodo de Posicion
        foreach ($assets as $asset) {
            $asset_posicion = Doctrine_Core::getTable('Asset')->find($asset->asset_id);
            $asset_posicion->node_id = $node_nueva_posicion;
            $asset_posicion->save();
        }

        $json_data = $this->json->encode(array('success' => true, 'msg' => 'Posicion cambiada exitosamente'));
        echo $json_data;
    }

    function exportCompleto2() {

        ini_set('memory_limit', '2048M');
        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');

        define('DB_BD', 'igeo_u_2016_v2');
        define('DB_USUARIO', 'root');
        define('DB_PASS', '');
        define('DB_HOST', 'localhost');

        define("SECURE", TRUE);

        $conn = mysqli_connect(DB_HOST, DB_USUARIO, DB_PASS, DB_BD) or die("Error " . mysqli_error($conn));
        $conn->set_charset("utf8");

        if ($consulta = $conn->prepare("CALL consulta_activos()")) {
            $consulta->execute();
            $consulta->store_result();

            if ($consulta->num_rows > 0) {
                $consulta->bind_result($node_id, $asset_name, $asset_estate, $asset_num_serie_intern, $asset_load_date, $asset_num_factura, $asset_type_name, $asset_load_folio, $infra_other_data_value_value, $asset_other_data_value_value);


                $sheet->setCellValue('A1', "CODIGO DE RECINTO")
                        ->setCellValue('B1', $this->translateTag('General', 'name'))
                        ->setCellValue('C1', $this->translateTag('Asset', 'num_series_internal'))
                        ->setCellValue('D1', $this->translateTag('General', 'type'))
                        ->setCellValue('E1', $this->translateTag('Core', 'location'))
                        ->setCellValue('F1', "CODIGO AUGE")
                        ->setCellValue('G1', "ESTADO ACTIVO")
                        ->setCellValue('H1', "FECHA DE CARGA")
                        ->setCellValue('I1', "FOLIO")
                        ->setCellValue('J1', "NUMERO DE FACTURA");

                $rcount = 1;
                $rows = array();
                while ($consulta->fetch()) {
                    $rows[] = array(
                        'node_id' => $node_id,
                        'asset_name' => $asset_name,
                        'asset_estate' => $asset_estate,
                        'asset_num_serie_intern' => $asset_num_serie_intern,
                        'asset_load_date' => $asset_load_date,
                        'asset_num_factura' => $asset_num_factura,
                        'asset_type_name' => $asset_type_name,
                        'asset_load_folio' => $asset_load_folio,
                        'infra_other_data_value_value' => $infra_other_data_value_value,
                        'asset_other_data_value_value' => $asset_other_data_value_value
                    );

                    if ($asset_estate == 1) {
                        $dado_baja = "DADO DE BAJA";
                    } else {
                        $dado_baja = "";
                    }
                    $rcount++;
                    $sheet->setCellValueExplicit('A' . $rcount, $infra_other_data_value_value, PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('B' . $rcount, $asset_name)
                            ->setCellValueExplicit('C' . $rcount, $asset_num_serie_intern, PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValue('D' . $rcount, $asset_type_name)
                            ->setCellValue('E' . $rcount, Doctrine_Core::getTable('Node')->find($node_id)->getPath())
                            ->setCellValueExplicit('F' . $rcount, $asset_other_data_value_value, PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValue('G' . $rcount, $dado_baja)
                            ->setCellValue('H' . $rcount, $asset_load_date)
                            ->setCellValue('I' . $rcount, $asset_load_folio)
                            ->setCellValue('J' . $rcount, $asset_num_factura);
                }

                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->getColumnDimension('I')->setAutoSize(true);
                $sheet->getColumnDimension('J')->setAutoSize(true);

                $sheet->getStyle('A1:J1')->getFont()->applyFromArray(array(
                    'bold' => true
                ));

                $sheet->getStyle('A1:J1')->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'd9e5f4'
                    )
                ));

                $sheet->getStyle('A1:J' . $rcount)->getBorders()->applyFromArray(array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => '808080'
                        )
                    )
                ));

                $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
                $objWriter->save($this->app->getTempFileDir("VACIADO COMPLETO ACTIVOS" . '.xls'));

                echo '{"success": true, "file": "' . "VACIADO COMPLETO ACTIVOS" . '.xls"}';
            } else {
                return false;
            }
        } else {
            return false;
        }
        $consulta->close();
    }

    function exportCompleto3($node_id = null) {

        ini_set('memory_limit', '2048M');
        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');

        $node_id = $this->input->post('node_id');

        if ($node_id != 'root') {
            //Se determina si es el nodo padre
            $this->load->library('TreeNodes');
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $nodes = $treeObject->fetchRoots();


            if ($nodes[0]->node_id == $node_id) {
                $child_nodes = '';
            } else {

                $dataNode = Doctrine_Core::getTable('Node')->find($node_id);
                $queryChild = $this->db->query("SELECT node_id from node WHERE lft>=" . $dataNode->lft . " and rgt <=" . $dataNode->rgt . " and node_parent_id=17807");

                //Se obtine el resultado en forma de arreglo
                $data_child = $queryChild->result_array();
                $queryChild->free_result();
                $this->db->close();

                $child_nodes = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($data_child)), 0);
                //Se separa por coma
                $child_nodes = implode(",", $child_nodes);
            }

            $data_all = Doctrine_Core::getTable('AssetReport')->findAsset($child_nodes);


            if (count($data_all) > 0) {
                $sheet->setCellValue('A1', "CODIGO DE RECINTO")
                        ->setCellValue('B1', $this->translateTag('General', 'name'))
                        ->setCellValue('C1', $this->translateTag('Asset', 'num_series_internal'))
                        ->setCellValue('D1', $this->translateTag('General', 'type'))
                        ->setCellValue('E1', $this->translateTag('Core', 'location'))
                        ->setCellValue('F1', "CODIGO AUGE")
                        ->setCellValue('G1', "ESTADO ACTIVO")
                        ->setCellValue('H1', "FECHA DE CARGA")
                        ->setCellValue('I1', "FOLIO")
                        ->setCellValue('J1', "NUMERO DE FACTURA");


                $rcount = 2;

                foreach ($data_all as $value) {

                    if ($value['asset_estate'] == 1) {
                        $dado_baja = "DADO DE BAJA";
                    } else {
                        $dado_baja = "";
                    }

                    $sheet->setCellValueExplicit('A' . $rcount, $value['infra_other_data_value_value'], PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('B' . $rcount, $value['asset_name'])
                            ->setCellValueExplicit('C' . $rcount, $value['asset_num_serie_intern'], PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValue('D' . $rcount, $value['asset_type_name'])
                            ->setCellValue('E' . $rcount, $value['location'])
                            ->setCellValueExplicit('F' . $rcount, $value['asset_other_data_value_value'], PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValue('G' . $rcount, $dado_baja)
                            ->setCellValue('H' . $rcount, $value['asset_load_date'])
                            ->setCellValue('I' . $rcount, $value['asset_load_folio'])
                            ->setCellValue('J' . $rcount, $value['asset_num_factura']);


                    $rcount++;
                }

                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->getColumnDimension('I')->setAutoSize(true);
                $sheet->getColumnDimension('J')->setAutoSize(true);

                $sheet->getStyle('A1:J1')->getFont()->applyFromArray(array(
                    'bold' => true
                ));

                $sheet->getStyle('A1:J1')->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'd9e5f4'
                    )
                ));

                $rcount--;
                $sheet->getStyle('A1:J' . $rcount)->getBorders()->applyFromArray(array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => '808080'
                        )
                    )
                ));

                $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
                $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));

                echo '{"success": true, "file": "' . $this->input->post('file_name') . '.xls"}';
            } else {
                echo '{"success": false, "msg": "Sin información", "file": ""}';
            }
        }else{
                 echo '{"success": false, "msg": "Sin información", "file": ""}';
        }
   
    }

}
