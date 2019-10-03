<?php

/** @package    Controller
 *  @subpackage PlanVersionController
 */
class PlanVersionController extends APP_Controller {

    function PlanVersionController() {
        parent::APP_Controller();
    }

    /**
     * get
     *
     * Lista una version del documento siempre que haya mas de una
     *
     * @post int node_id
     */
    function get() {
        $node_id = $this->input->post('node_id');
        $plan_category_id = $this->input->post('plan_category_id');
        $versions = Doctrine_Core::getTable('Plan')->retrieveVersions($node_id, $plan_category_id);

        if ($versions->count()) {
            echo '({"total":"' . $versions->count() . '", "results":' . $this->json->encode($versions->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getLastVersion() {
        $node_id = $this->input->post('node_id');
        $plan_category_id = $this->input->post('plan_category_id');
        $plans = Doctrine_Core::getTable('Plan')->retrieveCurrents($node_id, $plan_category_id);

        if ($plans->count()) {
            echo '({"total":"' . $plans->count() . '", "results":' . $this->json->encode($plans->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * Elimina un plano de la base de datos
     * @method POST
     * @param integer $plan_id
     * @param integer $node_id
     * @param integer $plan_category_id
     *
     */
    function delete() {
        try {
            $plan_id_s = $this->input->post('plan_id');
            $arregloPlanIds = explode(",", $plan_id_s);
            
        

            foreach ($arregloPlanIds as $plan_id) {
                //RESCATA LOS VALORES ACTUALES
                $PlanTable = Doctrine_Core::getTable('Plan')->find($plan_id);
                $node_id = $PlanTable->node_id;
                $plan_category_id = $PlanTable->plan_category_id;
                $plan_current_version = $PlanTable->plan_current_version;
                $version = $PlanTable->plan_version;
                $fileName =  $PlanTable->plan_filename;
                
                $url = "{$this->config->config['bimapi']['base_url']}" . $node_id . '/' . $version . '/' . $fileName;
                $token = $this->getTokenBimApi();
             
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    "Authorization: {$token}"
                ));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $curl_response = curl_exec($curl);
                if ($curl_response === false) {
                    $info = curl_getinfo($curl);

                    $success = false;
                    $msg = 'Error al consultar API';
                    $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                    echo $json_data;
                }
                curl_close($curl);
                $decoded = json_decode($curl_response);
                
                
                if ($decoded->status != 200) {
                    $success = false;
                    $msg = $decoded->status . '-' . $decoded->msg;
                    $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                    echo $json_data;
                    exit();
                } elseif ($decoded->status == 200 ) {

                    if ($plan_current_version == 0) {// 0= VERSION ANTIGUA
                        //BORRA DE TABLA PLAN 
                        if ($PlanTable->delete()) {
                            $success = true;
                            $msg = $this->translateTag('General', 'operation_successful');

                            $plan_category = Doctrine::getTable('PlanCategory')->find($PlanTable->plan_category_id);
                            $node = Doctrine::getTable('Node')->find($node_id);
                            $this->syslog->register('delete_version_plan', array(
                                $PlanTable->plan_filename,
                                $PlanTable->plan_description,
                                $plan_category->plan_category_name,
                                $node->getPath()
                            )); // registering log
                        }
                    } elseif ($plan_current_version == 1) {// 1= VERSION ACTUAL
                        $PlanTable2 = Doctrine_Core::getTable('Plan')->findByNodeAndCategory($node_id, $plan_category_id);


                        if ($PlanTable2->count() > 1) {
                            //ENTRA SI HAY MAS DE UN REGISTRO EN LA TABLA PLAN Y TIENE Q PASAR LAS PROPIEDADES A VERSION ANTERIOR 
                            $PlanTable3 = Doctrine_Core::getTable('Plan')->retrivePenultimate($node_id, $plan_category_id);
                            $plan_id_anterior = $PlanTable3->plan_id;

                            //ACTUALIZA LA VERSION ANTERIOR Y LA MARCA COMO ULTIMA 
                            $PlanTableUpdate = Doctrine_Core::getTable('Plan')->find($plan_id_anterior);
                            $PlanTableUpdate->plan_current_version = 1;



                            $PlanTableUpdate->save();

                            //BORRA LA ULTIMA VERSION 
                            $PlanTable->delete();



                            //ACTUALIZA LAS PROPIEDADES EN BASE A LA VERSION ANTERIOR
                            $PlanNodeUpdatesObj = Doctrine_Core::getTable('PlanNode')->findBy('plan_id', $plan_id);
                            $PlanNodeUpdates = $PlanNodeUpdatesObj->toArray();

                            foreach ($PlanNodeUpdates as $PlanNodeUpdate) {
                                $PlanNodeChange = Doctrine_Core::getTable('PlanNode')->find($PlanNodeUpdate['plan_node_id']);
                                $PlanNodeChange->plan_id = $plan_id_anterior;
                                $PlanNodeChange->save();
                            }
                            $success = true;
                            $msg = $this->translateTag('General', 'operation_successful');
                        } else {
                            //BORRA EL UNICO REGISTRO DE LA TABLA PLAN                
                            $PlanTable->delete();
                            //BORRA TODOS LOS REGISTROS DE LA TABLA PLAN_NODE 
                            $NodePlans = Doctrine_Core::getTable('PlanNode')->findBy('plan_id', $PlanTable->plan_id);
                            $NodePlans->delete();

                            $success = true;
                            $msg = $this->translateTag('General', 'operation_successful');
                        }

                        $plan_category = Doctrine::getTable('PlanCategory')->find($PlanTable->plan_category_id);
                        $node = Doctrine::getTable('Node')->find($node_id);
                        $this->syslog->register('delete_version_plan', array(
                            $PlanTable->plan_filename,
                            $PlanTable->plan_description,
                            $plan_category->plan_category_name,
                            $node->getPath()
                        )); // registering log
                    }
                } else {
                    $success = false;
                    $msg = 'Error al consultar API';
                    $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                    echo $json_data;
                    exit();
                }
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
     * Actualiza los campos del Plano
     * @param integer $plan_id
     * @param string $plan_description
     * @param string $plan_version
     * @param string $plan_comments
     * @method POST
     */
    function update() {
        $plan = Doctrine_Core::getTable('Plan')->find($this->input->post('plan_id'));
        $plan->plan_description = $this->input->post('plan_description');
        $plan->plan_version = $this->input->post('plan_version');
        $plan->plan_comments = $this->input->post('plan_comments');

        try {
            $plan->save();
            $msg = $this->translateTag('Plan', 'successfully_updated_plan');
            $success = true;
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function getTokenBimApi() {
        $serviceUrl = "{$this->config->config['bimapi']['base_url']}authenticate";
        $ch = curl_init($serviceUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->config->config['bimapi']['credenciales']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if (curl_error($ch)) {
            $result = curl_error($ch);
        }
        curl_close($ch);
        $decoded = json_decode($result, 1);
        return $decoded['token'];
    }

}
