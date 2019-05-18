<?php

/**
 */
class MtnWorkOrderTable extends Doctrine_Table
{
    /*
     * Devuelve todas las OT que coinciden con los filtros
     * @param array $filters
     *
     */

    function findAllNode($filters = array(), $node_id, $start = null, $limit = null, $count = false)
    {
        $q = Doctrine_Query :: create ()
                ->select('wo.*,p.*,mcs.*,mswos.*,wot.*')
                ->from('MtnWorkOrder wo')
                ->innerJoin('wo.Provider p')
                ->innerJoin('wo.MtnConfigState mcs')
                ->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
                ->innerJoin('mcs.MtnWorkOrderType wot');
        
        if (!is_null($start)) {
            $q->offset($start);
        }
        if (!is_null($limit)) {
            $q->limit($limit);
        }
               
        $flag = false;
	foreach ($filters as $field => $value)
	{
	    if (!is_null($value))
	    {
		if ($flag === false)
		{
		    $q->where($field, $value);
		    $flag = true;
		}
		else
		{
		    $q->andWhere($field, $value);
		}
	    }
	}
        
        
        
        if ($node_id != 'root') {
            $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );
            $q->andWhere( 'wo.Node.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'wo.Node.lft > ?' , $node->lft )
                ->andWhere ( 'wo.Node.rgt < ?' , $node->rgt );
        }
        
        
        $q->andWhere('wo.mtn_maintainer_type_id = ?', 2);
        $q->andWhere('wo.mtn_work_order_status = ?', 0);
        $q->orderBy ( 'mtn_work_order_folio DESC' );
        
        
        
        if ($count) {
            $q->select('count(*)');
            return $q->fetchOne()->count;
        } else {
            return $q->execute();
        }
    }
    
    function retrieveAll($filters = array(), $node_id, $search_branch=false)
    {

	$q = Doctrine_Query::create()
		->select('wo.*,a.*, at.*, ba.*, ac.*, as.*,mswos.*,wot.*,p.*,mcs.*')
		->from('MtnWorkOrder wo')
		->innerJoin('wo.Asset a')
                ->innerJoin('a.Node n')
		->innerJoin('a.AssetType at')
		->innerJoin('a.Brand ba')
		->leftJoin('a.AssetCondition ac')
		->innerJoin('wo.MtnConfigState mcs')
		->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
		->innerJoin('mcs.MtnWorkOrderType wot')
		->innerJoin('wo.Provider p');

	if ($search_branch)
	{

	    $node = Doctrine_Core::getTable('Node')->find($node_id);

	    $q->where('n.node_parent_id = ?', $node->node_parent_id)
		    ->andWhere('n.lft >= ?', $node->lft)
		    ->andWhere('n.rgt <= ?', $node->rgt);
	}
	else
	{

	    $q->andWhere('a.node_id = ?', $node_id);
	}

	$flag = false;
	foreach ($filters as $field => $value)
	{
	    if (!is_null($value))
	    {
		if ($flag === false)
		{
		    $q->where($field, $value);
		    $flag = true;
		}
		else
		{
		    $q->andWhere($field, $value);
		}
	    }
	}
	$q->andWhere('a.asset_estate = ?', 0);
	$q->andWhere('wo.mtn_work_order_status = ?', 0);
	$q->orderBy('wo.mtn_work_order_folio ASC');

        return $q->execute();
    }
    
    function retrieveAllId($filters = array(), $node_id)
    {

	$q = Doctrine_Query::create()
		->select('wo.*,a.*, at.*, ba.*, ac.*, as.*,mswos.*,wot.*,p.*,mcs.*')
		->from('MtnWorkOrder wo')
		->innerJoin('wo.Asset a')
                ->innerJoin('a.Node n')
		->innerJoin('a.AssetType at')
		->innerJoin('a.Brand ba')
		->leftJoin('a.AssetCondition ac')
		->innerJoin('wo.MtnConfigState mcs')
		->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
		->innerJoin('mcs.MtnWorkOrderType wot')
		->innerJoin('wo.Provider p')
                ->andWhere('wo.node_id = ?', $node_id);;
	
	$flag = false;
	foreach ($filters as $field => $value)
	{
	    if (!is_null($value))
	    {
		if ($flag === false)
		{
		    $q->where($field, $value);
		    $flag = true;
		}
		else
		{
		    $q->andWhere($field, $value);
		}
	    }
	}
	$q->andWhere('a.asset_estate = ?', 0);
	$q->andWhere('wo.mtn_work_order_status = ?', 0);
        $q->andWhere('a.node_id = ?', $node_id);
	$q->orderBy('wo.mtn_work_order_folio ASC');
        
	return $q->execute();
    }

    function retrieveByProvider($filters = array(), $provider_id=null)
    {

	$q = Doctrine_Query::create()
//		->select('wo.*,a.*, at.*, ba.*, ac.*, as.*,mswos.*,wot.*,p.*,mcs.*')
		->from('MtnWorkOrder wo')
//		->innerJoin('wo.Asset a')
//		->innerJoin('a.Node n')
//		->innerJoin('a.AssetType at')
//		->innerJoin('a.Brand ba')
//		->leftJoin('a.AssetCondition ac')
		->innerJoin('wo.MtnConfigState mcs')
		->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
		->innerJoin('mcs.MtnWorkOrderType wot')
		->innerJoin('wo.Provider p')
		->where('wo.provider_id = ? ', $provider_id);

	foreach ($filters as $field => $value)
	{
	    if (!is_null($value))
	    {
		$q->andWhere($field, $value);
	    }
	}
//	$q->andWhere('a.asset_estate = ?', 0);
	$q->orderBy('wo.mtn_work_order_folio ASC');

	return $q->execute();
    }

    /**
     * Devuelve la tupla de la WO
     * @param integer $mtn_work_order_id
     */
    function retrieveById($mtn_work_order_id)
    {

	$q = Doctrine_Query::create()
		->from('MtnWorkOrder wo')
		->innerJoin('wo.Provider p')
		->innerJoin('wo.Asset a')
		->innerJoin('wo.User u')
		->innerJoin('wo.MtnConfigState mcs')
		->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
		->innerJoin('mcs.MtnWorkOrderType mwot')
		->where('wo.mtn_work_order_id = ?', $mtn_work_order_id)
	;


	return $q->fetchOne();
    }
    
        
    /**
     * Devuelve la tupla de la WO
     * @param integer $mtn_work_order_id
     */
    function retrieveByIdNode($mtn_work_order_id)
    {

	$q = Doctrine_Query::create()
		->from('MtnWorkOrder wo')
		->innerJoin('wo.Provider p')
		->innerJoin('wo.User u')
		->innerJoin('wo.MtnConfigState mcs')
		->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
		->innerJoin('mcs.MtnWorkOrderType mwot')
		->where('wo.mtn_work_order_id = ?', $mtn_work_order_id)
	;


	return $q->fetchOne();
    }
    
    
    function countToDelete($mtn_work_order_type_id)
    {

	$q = Doctrine_Query::create()
                ->select('count(*)')
		->from('MtnWorkOrder wo')
		->innerJoin('wo.MtnConfigState mcs')		
		->innerJoin('mcs.MtnWorkOrderType mwot')
		->where('mwot.mtn_work_order_type_id = ?', $mtn_work_order_type_id)
	;


	return $q->fetchOne()->count;
    }

    /**
     * Devuelve el último folio de la OT generada
     * @return integer
     *
     */
    function lastFolioWo()
    {

	$q = Doctrine_Query::create()
		->from('MtnWorkOrder wo')
		->orderBy('mtn_work_order_folio DESC')
		->limit(1);

	$results = $q->fetchOne();

	$last_folio = (int) ( empty($results->mtn_work_order_folio) ? 0 : $results->mtn_work_order_folio );

	return $last_folio;
    }

    function retrieveByFilter($filters=array(), $node_id, $provider_id)
    {

	$q = Doctrine_Query::create()
		->select('a.*, at.*, ba.*, ac.*, as.*,asc.*,c.*')
		->from('Asset a')
		->innerJoin('a.ContractAsset asc')
		->innerJoin('asc.Contract c')
		->innerJoin('a.Node n')
		->innerJoin('a.AssetType at')
		->innerJoin('a.Brand ba')
		->where('a.node_id = ?', $node_id);

	$node = Doctrine_Core::getTable('Node')->find($node_id);

	$q->where('n.node_parent_id = ?', $node->node_parent_id)
		->andWhere('n.lft >= ?', $node->lft)
		->andWhere('n.rgt <= ?', $node->rgt);

	foreach ($filters as $field => $value)
	{
	    if (!is_null($value))
	    {
		$q->andWhere($field, $value);
	    }
	}
	$q->andWhere('c.provider_id = ?', $provider_id);
	$q->andWhere('a.asset_estate = ?', 0);
	//    $q->andWhere ( 'a.asset_id NOT IN ( SELECT mwo.asset_id FROM MtnWorkOrder mwo)' );
	$q->orderBy('asset_name');

	return $q->execute();
    }
    function retrieveByFilterByNode($filters=array(), $node_id)
    {

	$q = Doctrine_Query::create()
		//->select('a.*, at.*, ba.*, ac.*, as.*,asc.*,c.*')
		->from('Node n')    
		->innerJoin('n.NodeType nt')		
		->where('n.node_id = ?', $node_id);
		

	$node = Doctrine_Core::getTable('Node')->find($node_id);

	$q->where('n.node_parent_id = ?', $node->node_parent_id)
		->andWhere('n.lft >= ?', $node->lft)
		->andWhere('n.rgt <= ?', $node->rgt);

	foreach ($filters as $field => $value)
	{
	    if (!is_null($value))
	    {
		$q->andWhere($field, $value);
	    }
	}

	//$q->andWhere('a.asset_estate = ?', 0);
	//    $q->andWhere ( 'a.asset_id NOT IN ( SELECT mwo.asset_id FROM MtnWorkOrder mwo)' );
	//$q->orderBy('asset_name');

	return $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    }

    function retrieveProvider($asset_id)
    {

	$q = Doctrine_Query::create()
		->select('ca.*, c.*,p.*')
		->from('ContractAsset ca')
		->innerJoin('ca.Contract c')
		->innerJoin('c.Provider p')
		->where('ca.asset_id = ?', $asset_id)

	;

	return $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    }

    function retrievePlanTask($mtn_plan_id)
    {

	$q = Doctrine_Query :: create()
		->from('MtnPlanTask pt')
		->where('pt.mtn_plan_id = ?', $mtn_plan_id);

	;

	return $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    }

    function retrieveWO($fecha_insert, $asset_id)
    {

	$q = Doctrine_Query :: create()
		->from('MtnWorkOrder mwo')
		->where('mwo.mtn_work_order_date = ?', $fecha_insert)
		->andWhere('mwo.asset_id = ?', $asset_id)
		->limit(1);
	;
	//return $q->fetchOne();
	return $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    }
    function retrieveWOByNode($fecha_insert, $node_id)
    {

	$q = Doctrine_Query :: create()
		->from('MtnWorkOrder mwo')
		->where('mwo.mtn_work_order_date = ?', $fecha_insert)
		->andWhere('mwo.node_id = ?', $node_id)
		->limit(1);
	;
	//return $q->fetchOne();
	return $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    }
    

}
