<?php

/**
 * @package    Controller
 * @subpackage MtnController
 */
class WOExportarNodePdf extends APP_Controller
{
    function WOExportarNodePdf ()
    {
        parent::APP_Controller ();
    }

    /**
     * exportPdf
     *
     * Exporta la Orden de Trabajo en formato pdf
     *
     */
    function exportPdf ( $mtn_work_order_id = null )
    {
 
        $data = array ( );
        $workOrder = Doctrine_Core :: getTable ( 'MtnWorkOrder' )->retrieveByIdNode ( $mtn_work_order_id );
        
        $final= $workOrder->toArray();
        
        $Node = Doctrine_Core::getTable('Node')->find($final['node_id']);
        $ruta=$Node->getPath();
            
        $AuxNode= $Node->toArray();
        $NodeType = Doctrine_Core::getTable('NodeType')->find($Node['node_type_id']);
        $AuxNodeType= $NodeType->toArray();
        
        list($anio, $mes, $dia) = explode("-", $workOrder->mtn_work_order_date);
        $fecha = $dia. '/'. $mes. '/' . $anio;
            
        $final['node_name'] = $AuxNode['node_name'];
        $final['node_type_name'] = $AuxNodeType['node_type_name'];
        $final['node_ruta'] = $ruta;
        $final['fecha'] = $fecha;

        $workOrderTask = Doctrine_Core::getTable ( 'MtnWorkOrderTask' )->retrieveAll ( $mtn_work_order_id );
        $workOrderOtherCost = Doctrine_Core::getTable ( 'MtnWorkOrderOtherCosts' )->retrieveAll ( $mtn_work_order_id );
        $createdBy = Doctrine_Core::getTable ( 'User' )->find ( $workOrder->mtn_work_order_creator_id );
        $currencyFormat = Doctrine_Core :: getTable ( 'Currency' )->retrieveById ( $this->config->item ( 'default_currency' ) );

        $data[ 'wo' ] = $final;
        $data[ 'task_list' ] = $workOrderTask;
        $data[ 'other_list' ] = $workOrderOtherCost->toArray ();
        $data[ 'createdBy' ] = $createdBy->toArray ();
        $data[ 'currency_format' ] = $currencyFormat;

        $number_folio = $workOrder->mtn_work_order_folio;
        $html = $this->load->view ( 'wordorderpdfallnode' , $data , true );

        $this->load->library ( 'pdf' );
        $this->pdf->SetFont ( 'helvetica' , '' , 8 );

        // add a page
        $this->pdf->AddPage ();
        $this->pdf->writeHTML ( $html , true , false , true , false , '' );
        $this->pdf->Output ( 'ot_' . $number_folio . '.pdf' , 'D' );
        
    }
}