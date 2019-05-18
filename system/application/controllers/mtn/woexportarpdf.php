<?php

/**
 * @package    Controller
 * @subpackage MtnController
 */
class WOExportarPdf extends APP_Controller
{
    function WOExportarPdf ()
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
        $workOrder = Doctrine_Core :: getTable ( 'MtnWorkOrder' )->retrieveById ( $mtn_work_order_id );
        $workOrderTask = Doctrine_Core::getTable ( 'MtnWorkOrderTask' )->retrieveAll ( $mtn_work_order_id );
        $workOrderOtherCost = Doctrine_Core::getTable ( 'MtnWorkOrderOtherCosts' )->retrieveAll ( $mtn_work_order_id );
        $createdBy = Doctrine_Core::getTable ( 'User' )->find ( $workOrder->mtn_work_order_creator_id );
        $currencyFormat = Doctrine_Core :: getTable ( 'Currency' )->retrieveById ( $this->config->item ( 'default_currency' ) );

        $data[ 'wo' ] = $workOrder->toArray ();
        $data[ 'task_list' ] = $workOrderTask;
        $data[ 'other_list' ] = $workOrderOtherCost->toArray ();
        $data[ 'createdBy' ] = $createdBy->toArray ();
        $data[ 'currency_format' ] = $currencyFormat;

        $number_folio = $workOrder->mtn_work_order_folio;
        $html = $this->load->view ( 'wordorderpdf' , $data , true );

        $this->load->library ( 'pdf' );
        $this->pdf->SetFont ( 'helvetica' , '' , 8 );

        // add a page
        $this->pdf->AddPage ();
        $this->pdf->writeHTML ( $html , true , false , true , false , '' );
        $this->pdf->Output ( 'ot_' . $number_folio . '.pdf' , 'D' );
        
    }
}