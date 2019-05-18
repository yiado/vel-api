<?php
/**
 */
class MtnNodeWorkOrderDocumentTable extends Doctrine_Table {


    function findByWoId($wo_id) {

        $q = Doctrine_Query::create()
                ->select('ot.*, otd.*, c.*')
                ->from('MtnNodeWorkOrderDocument otd')
                ->innerJoin('otd.MtnNodeWorkOrder ot')
                ->innerJoin('otd.DocCategory c')               
                ->where('otd.mtn_node_work_order_id = ?', $wo_id);

         return $q->execute();
    }
    
}
