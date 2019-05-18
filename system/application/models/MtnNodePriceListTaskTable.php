<?php
/**
 */
class MtnNodePriceListTaskTable extends Doctrine_Table {

    //     function findAll ( $node_price_list_id )
//    {
//
//       $q = Doctrine_Query :: create()
//			->select('npl.*, nt.*, np.*, m.*')
//			->from('MtnNodePriceListTask npl')
//			->innerJoin('npl.MtnNodePriceList np')
//                        ->innerJoin('npl.MeasureUnit m')
//                        ->innerJoin('npl.MtnNodeTask nt')                            
//                        ->where ( 'npl.node_price_list_id = ?' , $node_price_list_id );
//
//        return $q->execute ();
//    }
    
    function findAll( $mtn_node_price_list_id ) {

         $q = Doctrine_Query :: create()
			->select('npl.*, nt.*, np.*, m.*')
			->from('MtnNodePriceListTask npl')
			->innerJoin('npl.MtnNodePriceList np')
                        ->innerJoin('npl.MeasureUnit m')
                        ->innerJoin('npl.MtnNodeTask nt');                                                         
              
            $q->where('npl.mtn_node_price_list_id = ?', $mtn_node_price_list_id);

        return $q->execute();
    }
    
    function findOneByNodeTaskId($query)
    {
         $q = Doctrine_Query :: create()
			->select('npl.*, nt.*, np.*, m.*')
			->from('MtnNodePriceListTask npl')
			->innerJoin('npl.MtnNodePriceList np');                                                                         
              
            $q->where('npl.mtn_node_task_id = ?', $query);

        return $q->execute();
        
        
    }

}
