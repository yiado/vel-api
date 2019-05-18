<?php
/**
 */
class MtnStatusLogTable extends Doctrine_Table {

  function retrieveLogByWorkOrderId($mtn_work_order_id)
    {

	$q = Doctrine_Query :: create()
		->from('MtnStatusLog msl')
		->innerJoin('msl.User u')
		->innerJoin('msl.MtnConfigState mcs')
		->innerJoin('mcs.MtnWorkOrderType mwot')
		->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
		->where('msl.mtn_work_order_id = ?', $mtn_work_order_id)		
	;
	
	return $q->execute();
    }
}
