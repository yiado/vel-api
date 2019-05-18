<?php

/** @package    Controller
 *  @subpackage PossibleStatusController
 */
class PossibleStatusController extends APP_Controller
{
    function PossibleStatusController ()
    {
        parent::APP_Controller ();
    }

    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        /*
          Se deshabilitó el flow de las OT, ahora devuelve todos los estados de la tabla system de los estados
          $statusPwoTable = Doctrine_Core::getTable('MtnPossibleStatusWorkOrder');
          $status = $statusPwoTable->retrieveAll($text_autocomplete);
         */
        $maintainer_type=1; //asset
        $mtnSystemWorkOrderStatus = Doctrine_Core::getTable ( 'MtnSystemWorkOrderStatus' );
        $status = $mtnSystemWorkOrderStatus->retrieveAll ( $text_autocomplete, $maintainer_type );

        $json_data = $this->json->encode ( array ( 'total' => $status->count () , 'results' => $status->toArray () ) );
        echo $json_data;
    }
    function getByNode ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        /*
          Se deshabilitó el flow de las OT, ahora devuelve todos los estados de la tabla system de los estados
          $statusPwoTable = Doctrine_Core::getTable('MtnPossibleStatusWorkOrder');
          $status = $statusPwoTable->retrieveAll($text_autocomplete);
         */
        $maintainer_type=2;//node
        $mtnSystemWorkOrderStatus = Doctrine_Core::getTable ( 'MtnSystemWorkOrderStatus' );
        $status = $mtnSystemWorkOrderStatus->retrieveAll ( $text_autocomplete ,$maintainer_type );

        $json_data = $this->json->encode ( array ( 'total' => $status->count () , 'results' => $status->toArray () ) );
        echo $json_data;
    }

	/**
     * add
     * 
     * Agrega un nuevo estado a la Tipo de O.T.
     * 
     * @post string $mtn_system_work_order_status_name
     */
    function add ()
    {
        //Recibimos los parametros
        $mtn_system_work_order_status_name = $this->input->post ( 'mtn_system_work_order_status_name' );
        try
        {
            $mtnSystemStatus = new MtnSystemWorkOrderStatus();
            $mtnSystemStatus->mtn_system_work_order_status_name = $mtn_system_work_order_status_name;
            $mtnSystemStatus->mtn_maintainer_type_id = 1;
            $mtnSystemStatus->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
    function addByNode ()
    {
        //Recibimos los parametros
        $mtn_system_work_order_status_name = $this->input->post ( 'mtn_system_work_order_status_name' );
        try
        {
            $mtnSystemStatus = new MtnSystemWorkOrderStatus();
            $mtnSystemStatus->mtn_system_work_order_status_name = $mtn_system_work_order_status_name;
            $mtnSystemStatus->mtn_maintainer_type_id = 2;
            $mtnSystemStatus->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

 /**
     * update
     * 
     * Modifica el Estado de la O.T.
     * 
     * @post int mtn_system_work_order_status_id  
     * @post string $mtn_system_work_order_status_name
     */
    function update ()
    {
        try
        {
            $mtnSystemStatus = Doctrine_Core::getTable ( 'MtnSystemWorkOrderStatus' )->find ( $this->input->post ( 'mtn_system_work_order_status_id' ) );
            $mtnSystemStatus[ 'mtn_system_work_order_status_name' ] = $this->input->post ( 'mtn_system_work_order_status_name' );
            $mtnSystemStatus->save ();
            $success = true;
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * delete
     * 
     * Elimina el Estado  si no esta asociado a una O.T.  
     * 
     * @post int mtn_system_work_order_status_id
     */
    function delete ()
    {
        try
        {
            $mtn_system_work_order_status_id = $this->input->post ( 'mtn_system_work_order_status_id' );
            $stateInWorkingOrder = Doctrine::getTable ( 'MtnSystemWorkOrderStatus' )->stateInWorkingOrder ( $mtn_system_work_order_status_id );
            if ( $stateInWorkingOrder === false )
            {
                $assetType = Doctrine::getTable ( 'MtnSystemWorkOrderStatus' )->find ( $mtn_system_work_order_status_id );
                if ( $assetType->delete () )
                {
                    $success = true;
                    $msg = $this->translateTag ( 'General' , 'operation_successful' );
                }
                else
                {
                    $success = false;
                    $msg = $this->translateTag ( 'General' , 'error' );
                }
            }
            else
            {
                $success = false;
                $msg = $this->translateTag ( 'Asset' , 'type_assets_not_eliminated_associated_assets' );
            }
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
    

    function stateAssigned ()
    {

        $mtnSystemWorkOrderStatus = Doctrine_Core::getTable ( 'MtnPossibleStatusWorkOrder' );
        $status = $mtnSystemWorkOrderStatus->stateAssignedType ();
        $json_data = $this->json->encode ( array ( 'total' => $status->count () , 'results' => $status->toArray () ) );
        echo $json_data;
    }

}