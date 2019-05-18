<?php

/**
 * @package    Controller
 * @subpackage ConfigStateController
 */
class ConfigStateController extends APP_Controller
{

    function ConfigStateController()
    {
	parent::APP_Controller();
    }

    /**
     *
     * Lista los estados disponibles para el tipo de O.T.
     * @param integer $mtn_system_work_order_status_id
     *
     */
    function get()
    {
        $text_autocomplete = $this->input->post ( 'query' );
	$mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');
        $maintainer_type=1; //asset
        
	//Todos los estados disponibles
	$mtnSystemWorkOrderStatusTable = Doctrine_Core::getTable('MtnConfigState');
	$mtnSystemWorkOrderStatus = $mtnSystemWorkOrderStatusTable->retrieveAll($mtn_work_order_type_id, $text_autocomplete,$maintainer_type );
	$json_data = $this->json->encode(array('total' => $mtnSystemWorkOrderStatus->count(), 'results' => $mtnSystemWorkOrderStatus->toArray()));
	echo $json_data;
    }
    function getByNode()
    {
        $text_autocomplete = $this->input->post ( 'query' );
	$mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');
        $maintainer_type = 2;//node
	//Todos los estados disponibles
	$mtnSystemWorkOrderStatusTable = Doctrine_Core::getTable('MtnConfigState');
	$mtnSystemWorkOrderStatus = $mtnSystemWorkOrderStatusTable->retrieveAll($mtn_work_order_type_id, $text_autocomplete,$maintainer_type );
	$json_data = $this->json->encode(array('total' => $mtnSystemWorkOrderStatus->count(), 'results' => $mtnSystemWorkOrderStatus->toArray()));
	echo $json_data;
    }

    /**
     * getList
     *
     * Lista los estados segun tipo de O.T.
     *
     * @post mtn_config_state_id
     */
    function getAssociated()
    {
        $text_autocomplete = $this->input->post ( 'query' );
	$mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');
	$user_id = $this->session->userdata('user_id');
	$User = Doctrine_Core :: getTable('User')->findOneBy('user_id', $user_id);
	$user_type = $User['user_type'];

	if ($user_type == 'N' || $user_type == 'A' || $user_type == 'S')
	{
	    //ACA ENTRA SI ES USUARIO NORMAL O ADMINISTRADOR O SYSTEM (ACCESOS)
	    $mtnConfigStateTable = Doctrine_Core::getTable('MtnConfigState');
	    $mtnConfigState = $mtnConfigStateTable->retrieveByStateUser($mtn_work_order_type_id, $text_autocomplete);
	}
	else
	{
	    //ACA ENTRA SOLO SI ES PROVEDOR (ACCESOS)
	    $mtnConfigStateTable = Doctrine_Core::getTable('MtnConfigState');
	    $mtnConfigState = $mtnConfigStateTable->retrieveByStateProvider($mtn_work_order_type_id, $text_autocomplete);
	}
	if ($mtnConfigState->count())
	{
	    echo '({"total":"' . $mtnConfigState->count() . '", "results":' . $this->json->encode($mtnConfigState->toArray()) . '})';
	}
	else
	{
	    echo '({"total":"0", "results":[]})';
	}
    }
    
    function getAssociatedPrimero()
    {
	$mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');
	
	    $mtnConfigStateTable = Doctrine_Core::getTable('MtnConfigState')->retrieveByStateUserUno($mtn_work_order_type_id);
	    
            if ($mtnConfigStateTable)
            {
                echo '({"total":"' . 1 . '", "results":' . $this->json->encode($mtnConfigStateTable->toArray()) . '})';
            }
            else
            {
                echo '({"total":"0", "results":[]})';
            }
	
	
	    
	
    }

    function getAssociatedAll()
    {
	$mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');
	$mtnConfigStateTable = Doctrine_Core::getTable('MtnConfigState');
	$mtnConfigState = $mtnConfigStateTable->retrieveByState($mtn_work_order_type_id);

	if ($mtnConfigState->count())
	{
	    echo '({"total":"' . $mtnConfigState->count() . '", "results":' . $this->json->encode($mtnConfigState->toArray()) . '})';
	}
	else
	{
	    echo '({"total":"0", "results":[]})';
	}
    }

    /**
     * add
     *
     * Agrega un nuevo atributo para info segun el tipo de activo
     *
     * @post int asset_other_data_attribute_id
     * @post int asset_type_id
     */
    //    function add ()
    //    {
    //        $mtn_work_order_type_id = $this->input->post ( 'mtn_work_order_type_id' );
    //        $itemselector=$this->input->post ( 'itemselector' );
    //        $itemselector=str_replace(',,',',',$itemselector);
    //        $infoSelectedFields = explode ( ',' , $itemselector);
    //        try
    //        {
    //            //Obtenemos la conexi�n actual
    //            $conn = Doctrine_Manager::getInstance ()->getCurrentConnection ();
    //
	//            //	Iniciar transacci�n
    //           $conn->beginTransaction ();
    //
	//            //Eliminamos la config actual
    //            Doctrine_Core::getTable ( 'MtnConfigState' )->eliminateCurrent ( $mtn_work_order_type_id );
    //
	//            //Insert de los fields en la configuraci�n para el tipo de activo
    //            if ( ! empty ( $infoSelectedFields[ 0 ] ) )
    //            {
    //                foreach ( $infoSelectedFields as $key => $field )
    //                {
    //
	//                    $mtnConfigState = new MtnConfigState();
    //
	//
	//                     $mtnConfigState->mtn_work_order_type_id = $mtn_work_order_type_id;
    //                     $mtnConfigState->mtn_system_work_order_status_id = $field;
    //
	//                   $mtnConfigState->mtn_config_state_order = $key;
    //
	//                    $mtnConfigState->save ();
    //                }
    //            }
    //
	//            //Commit de la transacci�n
    //            $conn->commit ();
    //            $success = true;
    //
	//            //Imprime el Tag en pantalla
    //            $msg = $this->translateTag ( 'General' , 'operation_successful' );
    //        }
    //        catch ( Exception $e )
    //        {
    //            //Rollback de la transacci�n
    //            $conn->rollback ();
    //            $success = false;
    //            $msg = $e->getMessage ();
    //        }
    //
	//        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
    //        echo $json_data;
    //    }


    function add()
    {
	$mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');
	$mtn_system_work_order_status_id = $this->input->post('mtn_system_work_order_status_id');
	$mtn_config_state_access_user = $this->input->post('mtn_config_state_access_user');
	$mtn_config_state_access_provider = $this->input->post('mtn_config_state_access_provider');
	$mtn_config_state_duration = $this->input->post('mtn_config_state_duration');

	try
	{
	    $mtnConfigState = new MtnConfigState();
	    $mtnConfigState->mtn_work_order_type_id = $mtn_work_order_type_id;
	    $mtnConfigState->mtn_system_work_order_status_id = $mtn_system_work_order_status_id;
	    $mtnConfigState->mtn_config_state_access_user = $mtn_config_state_access_user;
	    $mtnConfigState->mtn_config_state_access_provider = $mtn_config_state_access_provider;
	    $mtnConfigState->mtn_config_state_duration = $mtn_config_state_duration;

	    $MtnConfigState2 = Doctrine_Core::getTable('MtnConfigState');
	    $MtnConfigStateTable = $MtnConfigState2->retrieveByMayor($mtn_work_order_type_id);

	    $order = $MtnConfigStateTable['mtn_config_state_order'];
	    $order++;
	    $mtnConfigState->mtn_config_state_order = $order;
	    $mtnConfigState->save();

	    $success = true;
	    $msg = $this->translateTag('General', 'operation_successful');
	} catch (Exception $e)
	{
	    $success = false;
	    $msg = $e->getMessage();
	}

	$json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
	echo $json_data;
    }

    function update()
    {
	$mtn_config_state_id = $this->input->post('mtn_config_state_id');
	$mtn_work_order_type_id = $this->input->post('mtn_work_order_type_id');
	$mtn_system_work_order_status_id = $this->input->post('mtn_system_work_order_status_id');
	$mtn_config_state_access_user = $this->input->post('mtn_config_state_access_user');
	$mtn_config_state_access_provider = $this->input->post('mtn_config_state_access_provider');
	$mtn_config_state_duration = $this->input->post('mtn_config_state_duration');

	$MtnConfigState = Doctrine_Core::getTable('MtnConfigState')->find($mtn_config_state_id);

	$MtnConfigState->mtn_config_state_access_user = ($mtn_config_state_access_user == 'true' ? '1' : '0');
	$MtnConfigState->mtn_config_state_access_provider = ($mtn_config_state_access_provider == 'true' ? '1' : '0');
	$MtnConfigState->mtn_config_state_duration = $mtn_config_state_duration;

	try
	{

	    $MtnConfigState->save();
	    $success = true;

	    $msg = $this->translateTag('General', 'operation_successful');
	} catch (Exception $e)
	{
	    $success = false;
	    $msg = $e->getMessage();
	}

	//Output
	$json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
	echo $json_data;
    }

    function delete()
    {
	$MtnConfigStateTable = Doctrine::getTable('MtnConfigState')->find($this->input->post('mtn_config_state_id'));

	try
	{
	    $MtnConfigStateTable->delete();
	    $success = true;
	    $msg = $this->translateTag('General', 'operation_successful');
	} catch (Exception $e)
	{
	    $success = false;
	    $msg = $e->getMessage();
	}

	$json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
	echo $json_data;
    }

    function moveDown()
    {
	try
	{
	    $mtn_config_state_id = $this->input->post('mtn_config_state_id');

	    //---BUSCA EL ACTUAL Y LE RESTA 1 AL CAMPO "ORDEN"---
	    $MtnConfigState = Doctrine_Core::getTable('MtnConfigState')->find($mtn_config_state_id);

	    $mtn_config_state_id = $MtnConfigState['mtn_config_state_id'];
	    $mtn_work_order_type_id = $MtnConfigState['mtn_work_order_type_id'];
	    $mtn_config_state_order = $MtnConfigState['mtn_config_state_order'];
	    $menos1 = $mtn_config_state_order - 1;




	    if ($menos1 != 0)//VALIDA QUE EL ORDEN NO SEA MENOR Q CERO
	    {
		//--- VACIA EL CAMPO "ORDEN" ACTUAL
		$MtnConfigState['mtn_config_state_order'] = '';
		$MtnConfigState->save();

		//----BUSCA EL ANTERIOR Y LE SUMA 1 AL CAMPO "ORDEN"
		$MtnConfigState2 = Doctrine_Core::getTable('MtnConfigState')->findByBefore($mtn_work_order_type_id, $menos1);
		$mtn_config_state_id2 = $MtnConfigState2['mtn_config_state_id'];

		$MtnConfigState3 = Doctrine_Core::getTable('MtnConfigState')->find($mtn_config_state_id2);
		$MtnConfigState3['mtn_config_state_order'] = $mtn_config_state_order; //PONE EL VALOR ACTUAL
		$MtnConfigState3->save();


		//---BUSCA EL CAMPO ACTUAL Y DE RESTA 1
		$MtnConfigState4 = Doctrine_Core::getTable('MtnConfigState')->find($mtn_config_state_id);
		$MtnConfigState4['mtn_config_state_order'] = $menos1;
		$MtnConfigState4->save();
	    }



	    $msg = $this->translateTag('General', 'operation_successful');
	    $success = true;
	} catch (Exception $e)
	{
	    $success = false;
	    $msg = $e->getMessage();
	}

	$json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
	echo $json_data;
    }

    function moveUp()
    {
	try
	{
	    $mtn_config_state_id = $this->input->post('mtn_config_state_id');

	    //---BUSCA EL ACTUAL Y LE SUMA 1 AL CAMPO "ORDEN"---
	    $MtnConfigState = Doctrine_Core::getTable('MtnConfigState')->find($mtn_config_state_id);

	    $mtn_config_state_id = $MtnConfigState['mtn_config_state_id'];
	    $mtn_work_order_type_id = $MtnConfigState['mtn_work_order_type_id'];
	    $mtn_config_state_order = $MtnConfigState['mtn_config_state_order'];
	    $mas1 = $mtn_config_state_order + 1;



	    $MtnConfigState5 = Doctrine_Core::getTable('MtnConfigState')->retrieveByMayor($mtn_work_order_type_id);
	    $stateMayorExistente = $MtnConfigState5['mtn_config_state_order'];


	    if ($mtn_config_state_order < $stateMayorExistente)//VALIDA QUE EL ORDEN NO SEA MAYOR QUE EL MAYOR EXISTENTE
	    {
		//--- VACIA EL CAMPO "ORDEN" ACTUAL
		$MtnConfigState['mtn_config_state_order'] = '';
		$MtnConfigState->save();

		//----BUSCA EL SUCESOR Y RESTA 1 AL CAMPO "ORDEN"
		$MtnConfigState2 = Doctrine_Core::getTable('MtnConfigState')->findByBefore($mtn_work_order_type_id, $mas1);
		$mtn_config_state_id2 = $MtnConfigState2['mtn_config_state_id'];

		$MtnConfigState3 = Doctrine_Core::getTable('MtnConfigState')->find($mtn_config_state_id2);
		$MtnConfigState3['mtn_config_state_order'] = $mtn_config_state_order; //PONE EL VALOR ACTUAL
		$MtnConfigState3->save();

		//---BUSCA EL CAMPO ACTUAL Y SUMA 1
		$MtnConfigState4 = Doctrine_Core::getTable('MtnConfigState')->find($mtn_config_state_id);
		$MtnConfigState4['mtn_config_state_order'] = $mas1;
		$MtnConfigState4->save();
	    }

	    $msg = $this->translateTag('General', 'operation_successful');
	    $success = true;
	} catch (Exception $e)
	{
	    $success = false;
	    $msg = $e->getMessage();
	}

	$json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
	echo $json_data;
    }

}