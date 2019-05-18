<?php

/** @package    Controller
 *  @subpackage Woothercostscontroller
 */
class Woothercostscontroller extends APP_Controller
{
    function Woothercostscontroller ()
    {
        parent::APP_Controller ();
    }

    /*
     * Devuelve todos los otros costos asociados a una OT
     * @param integer $mtn_work_order_id
     */

    function get ()
    {
        $mtn_work_order_id = ( int ) $this->input->post ( 'mtn_work_order_id' );
        $mtnWorkOrderOtherCostsTable = Doctrine_Core::getTable ( 'MtnWorkOrderOtherCosts' );
        $woOtherCosts = $mtnWorkOrderOtherCostsTable->retrieveAll ( $mtn_work_order_id );
        $json_data = $this->json->encode ( array ( 'total' => $woOtherCosts->count () , 'results' => $woOtherCosts->toArray () ) );
        echo $json_data;
    }

    /*
     * Agrega otro costo a la OT
     * @param post data
     */

    function add ()
    {
        try
        {
            $mtn_work_order_id = ( int ) $this->input->post ( 'mtn_work_order_id' );
            $mtn_other_costs_id = ( int ) $this->input->post ( 'mtn_other_costs_id' );
            $mtn_work_order_other_costs_costs = ( int ) $this->input->post ( 'mtn_work_order_other_costs_costs' );
            $mtn_work_order_other_costs_comment = $this->input->post ( 'mtn_work_order_other_costs_comment' );
            $mtnWorkOrderOtherCosts = new MtnWorkOrderOtherCosts();
            $mtnWorkOrderOtherCosts->mtn_work_order_id = $mtn_work_order_id;
            $mtnWorkOrderOtherCosts->mtn_other_costs_id = $mtn_other_costs_id;
            $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs = $mtn_work_order_other_costs_costs;
            $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment = $mtn_work_order_other_costs_comment;
            $mtnWorkOrderOtherCosts->save ();
            
//            $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtn_work_order_id);
//            $mtnOtherCosts = Doctrine_Core::getTable('MtnOtherCosts')->find($mtn_other_costs_id);
//            $asset = Doctrine_Core::getTable('Asset')->find($MtnWorkOrderDB->asset_id);
//
//            $this->syslog->register('add_othercosts_ot', array(
//                $mtnOtherCosts->mtn_other_costs_name,
//                $MtnWorkOrderDB->mtn_work_order_folio,
//                $asset->asset_name
//            )); // registering log

            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }
    
    function addNode ()
    {
        try
        {
            $mtn_work_order_id = ( int ) $this->input->post ( 'mtn_work_order_id' );
            $mtn_other_costs_id = ( int ) $this->input->post ( 'mtn_other_costs_id' );
            $mtn_work_order_other_costs_costs = ( int ) $this->input->post ( 'mtn_work_order_other_costs_costs' );
            $mtn_work_order_other_costs_comment = $this->input->post ( 'mtn_work_order_other_costs_comment' );
            $mtnWorkOrderOtherCosts = new MtnWorkOrderOtherCosts();
            $mtnWorkOrderOtherCosts->mtn_work_order_id = $mtn_work_order_id;
            $mtnWorkOrderOtherCosts->mtn_other_costs_id = $mtn_other_costs_id;
            $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs = $mtn_work_order_other_costs_costs;
            $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment = $mtn_work_order_other_costs_comment;
            $mtnWorkOrderOtherCosts->save ();
            
            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /*
     * Actualiza una tupla de otros costos de la ot
     * @param array post data
     */

    function update ()
    {
        try
        {
            $mtn_work_order_other_costs_id = ( int ) $this->input->post ( 'mtn_work_order_other_costs_id' );
            $mtn_other_costs_id = ( int ) $this->input->post ( 'mtn_other_costs_id' );
            $mtn_work_order_other_costs_costs = ( int ) $this->input->post ( 'mtn_work_order_other_costs_costs' );
            $mtn_work_order_other_costs_comment = $this->input->post ( 'mtn_work_order_other_costs_comment' );

            //Buscamos la tupla
            $mtnWorkOrderOtherCosts = Doctrine_Core::getTable ( 'mtnWorkOrderOtherCosts' )->find ( $mtn_work_order_other_costs_id );
            
            //Seteamos los datos
            $other_costs_antes_id = $mtnWorkOrderOtherCosts->mtn_other_costs_id;
            $mtnWorkOrderOtherCosts->mtn_other_costs_id = $mtn_other_costs_id;
            $other_costs_antes = $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs;
            $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs = $mtn_work_order_other_costs_costs;
            $other_costs_despues = $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_costs;
            $costs_comment_antes = $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment;
            $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment = $mtn_work_order_other_costs_comment;
            $costs_comment_despues = $mtnWorkOrderOtherCosts->mtn_work_order_other_costs_comment;
            $mtnWorkOrderOtherCosts->save ();
            
            $MtnWorkOrderDB = Doctrine_Core :: getTable('MtnWorkOrder')->find($mtnWorkOrderOtherCosts->mtn_work_order_id);
            $mtnOtherCostsAntes = Doctrine_Core::getTable('MtnOtherCosts')->find($other_costs_antes_id);
            $mtnOtherCostsDespues = Doctrine_Core::getTable('MtnOtherCosts')->find($mtn_other_costs_id);
            $asset = Doctrine_Core::getTable('Asset')->find($MtnWorkOrderDB->asset_id);

            $log_id = $this->syslog->register('update_othercosts_ot', array(
                $mtnOtherCostsDespues->mtn_other_costs_name,
                $MtnWorkOrderDB->mtn_work_order_folio,
                $asset->asset_name
            )); // registering log
            
            
            if ($mtnOtherCostsAntes->mtn_other_costs_name != $mtnOtherCostsDespues->mtn_other_costs_name)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Maintenance', 'name_costs');
                    $logDetail->log_detail_value_old = $mtnOtherCostsAntes->mtn_other_costs_name;
                    $logDetail->log_detail_value_new = $mtnOtherCostsDespues->mtn_other_costs_name;
                    $logDetail->save();
                }
            }
            
            if ($other_costs_antes != $other_costs_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'value');
                    $logDetail->log_detail_value_old = $other_costs_antes;
                    $logDetail->log_detail_value_new = $other_costs_despues;
                    $logDetail->save();
                }
            }
            
             if ($costs_comment_antes != $costs_comment_despues)
            {
                if ($log_id)
                {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'comment');
                    $logDetail->log_detail_value_old = $costs_comment_antes;
                    $logDetail->log_detail_value_new = $costs_comment_despues;
                    $logDetail->save();
                }
            }

            $success = 'true';
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = 'false';
            $msg = $e->getMessage ();
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * Elimina una tuppla de otro costo asociado a la OT
     * 
     * @param integer $mtn_work_order_other_costs_id 
     */
    function delete ()
    {
        $mtn_work_order_other_costs_id = $this->input->post ( 'mtn_work_order_other_costs_id' );
        $mtnWorkOrderOtherCosts = Doctrine::getTable ( 'mtnWorkOrderOtherCosts' )->find ( $mtn_work_order_other_costs_id );

        if ( $mtnWorkOrderOtherCosts->delete () )
        {
            $success = 'true';
        }
        else
        {
            $success = 'false';
        }
        $json_data = $this->json->encode ( array ( 'success' => $success ) );
        echo $json_data;
    }
}