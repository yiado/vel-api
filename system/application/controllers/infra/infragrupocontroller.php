<?php


/** @package    Controller
 *  @subpackage InfraGrupoController
 */
class InfraGrupoController extends APP_Controller {
	function InfraGrupoController() {
		parent :: APP_Controller();
	}

	function get() {
		$infraGrupo = Doctrine_Core :: getTable('InfraGrupo')->retrieveAll($this->input->post('query'));

		if ($infraGrupo->count()) {
			echo '({"total":"' . $infraGrupo->count() . '", "results":' . $this->json->encode($infraGrupo->toArray()) . '})';
		} else {
			echo '({"total":"0", "results":[]})';
		}
	}

	function getAll() {
		$node = Doctrine_Core :: getTable('Node')->findById($this->input->post('node_id'));

		$infraGrupo = Doctrine_Core :: getTable('InfraGrupo')->retrieveAllGrupos($node->node_type_id);

		if ($infraGrupo->count()) {
			echo '({"total":"' . $infraGrupo->count() . '", "results":' . $this->json->encode($infraGrupo->toArray()) . '})';
		} else {
			echo '({"total":"0", "results":[]})';
		}
	}

	function getByGrupos() {
		$infraGrupo = Doctrine_Core :: getTable('InfraGrupo')->findGrupoAtribute($this->input->post('infra_grupo_id'));

		if ($infraGrupo->count()) {
			echo '({"total":"' . $infraGrupo->count() . '", "results":' . $this->json->encode($infraGrupo->toArray()) . '})';
		} else {
			echo '({"total":"0", "results":[]})';
		}
	}

	/**
	 * add
	 * 
	 * Agrega un nuevo grupo de Otros Datos
	 * 
	 * @post string $infra_grupo_nombre
	 */
	function add() {
		$infra_grupo_nombre = $this->input->post('infra_grupo_nombre');

		try {
			$infraGrupo = new InfraGrupo();
			$infraGrupo->infra_grupo_nombre = $infra_grupo_nombre;

			$infraConfigGrupo = Doctrine_Core :: getTable('InfraGrupo')->retrieveByMayor();

			$order = $infraConfigGrupo['infra_grupo_order'];
			$order++;
			$infraGrupo->infra_grupo_order = $order;

			$infraGrupo->save();

			$success = true;
			$msg = $this->translateTag('General', 'operation_successful');
		} catch (Exception $e) {
			$success = false;
			$msg = $e->getMessage();
		}

		$json_data = $this->json->encode(array (
			'success' => $success,
			'msg' => $msg
		));
		echo $json_data;
	}

	/**
	 * delete
	 * 
	 * Elimina un Grupo de Otros Datos siempre que no tenga datos asociados  
	 * 
	 * @post int infra_grupo_id
	 */
	function delete() {
            try {
                    $infra_grupo_id = $this->input->post('infra_grupo_id');
                    $deleteInfraGrupo = Doctrine :: getTable('InfraGrupo')->deleteInfraGrupo($infra_grupo_id);
                    if ($deleteInfraGrupo === false) {
                            $infraGrupo = Doctrine :: getTable('InfraGrupo')->find($infra_grupo_id);
                            if ($infraGrupo->delete()) {
                                    $success = true;
                                    $msg = $this->translateTag('General', 'operation_successful');
                            } else {
                                    $success = false;
                                    $msg = $this->translateTag('General', 'error');
                            }
                    } else {
                            $success = false;
                            $msg = $this->translateTag('Infrastructure', 'attribute_has_an_associate');
                    }
            } catch (Exception $e) {
                    $success = false;
                    $msg = $e->getMessage();
            }

            $json_data = $this->json->encode(array (
                    'success' => $success,
                    'msg' => $msg
            ));
            echo $json_data;
	}

	function moveDown() {
            try {
                $infra_grupo_id = $this->input->post('infra_grupo_id');

                $infraConfigGrupo = Doctrine_Core :: getTable('InfraGrupo')->find($infra_grupo_id);

                $infra_grupo_id = $infraConfigGrupo->infra_grupo_id;
                $infra_grupo_order = $infraConfigGrupo->infra_grupo_order;
                $menos1 = $infra_grupo_order -1;

                if ($menos1 != 0) {
                    $infraConfigGrupo2 = Doctrine_Core :: getTable('InfraGrupo')->findByBefore($menos1);
                    $infra_grupo_id2 = $infraConfigGrupo2->infra_grupo_id;

                    $infraConfigGrupo3 = Doctrine_Core :: getTable('InfraGrupo')->find($infra_grupo_id2);
                    $infraConfigGrupo3->infra_grupo_order = $infra_grupo_order; //PONE EL VALOR ACTUAL
                    $infraConfigGrupo3->save();

                    $infraConfigGrupo4 = Doctrine_Core :: getTable('InfraGrupo')->find($infra_grupo_id);
                    $infraConfigGrupo4->infra_grupo_order = $menos1;
                    $infraConfigGrupo4->save();
                }

                $msg = $this->translateTag('General', 'operation_successful');
                $success = true;
            } catch (Exception $e) {
                    $success = false;
                    $msg = $e->getMessage();
            }

            $json_data = $this->json->encode(array (
                    'success' => $success,
                    'msg' => $msg
            ));
            echo $json_data;
	}

	function moveUp() {
            try {
                $infra_grupo_id = $this->input->post('infra_grupo_id');

                $infraConfigGrupo = Doctrine_Core :: getTable('InfraGrupo')->find($infra_grupo_id);

                $infra_grupo_id = $infraConfigGrupo->infra_grupo_id;
                $infra_grupo_order = $infraConfigGrupo->infra_grupo_order;
                $mas1 = $infra_grupo_order +1;

                $infraConfigGrupo5 = Doctrine_Core :: getTable('InfraGrupo')->retrieveByMayor();
                $stateMayorExistente = $infraConfigGrupo5->infra_grupo_order;

                if ($infra_grupo_order < $stateMayorExistente) {
                    $infraConfigGrupo2 = Doctrine_Core :: getTable('InfraGrupo')->findByBefore($mas1);
                    $infra_grupo_id2 = $infraConfigGrupo2->infra_grupo_id;

                    $infraConfigGrupo3 = Doctrine_Core :: getTable('InfraGrupo')->find($infra_grupo_id2);
                    $infraConfigGrupo3->infra_grupo_order = $infra_grupo_order;
                    $infraConfigGrupo3->save();

                    $infraConfigGrupo4 = Doctrine_Core :: getTable('InfraGrupo')->find($infra_grupo_id);
                    $infraConfigGrupo4->infra_grupo_order = $mas1;
                    $infraConfigGrupo4->save();
                }

                $msg = $this->translateTag('General', 'operation_successful');
                $success = true;
            } catch (Exception $e) {
                $success = false;
                $msg = $e->getMessage();
            }

            $json_data = $this->json->encode(array (
                    'success' => $success,
                    'msg' => $msg
            ));
            echo $json_data;
	}

}