<?php


/**
 */
class AssetTable extends Doctrine_Table {

    /**
     * retrieveByNodeId
     * 
     * Recupera los equipos por nodo especifico
     *  
     * @param int node_id
     * @param mixed $filters
     */
    function retrieveByNodeId($filters = array(), $node_id, $search_branch = false, $written_off = false, $start = null, $limit = null, $count = false) {
        $q = Doctrine_Query::create()
                ->select('a.*, at.*, ba.*, ac.*, al.*')
                ->from('Asset a')
                ->innerJoin('a.Node n')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->leftJoin('a.AssetCondition ac')
                ->leftJoin('a.AssetLoad al')
                ->where('a.node_id = ?', $node_id);

        if ($search_branch) {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $q->where('n.node_parent_id = ?', $node->node_parent_id)
                    ->andWhere('n.lft >= ?', $node->lft)
                    ->andWhere('n.rgt <= ?', $node->rgt);
        } else {
            $q->where('n.node_id = ?', $node_id);
        }

        if ($written_off) {
            $q->andWhere('a.asset_estate = ?', 1);
            
        } else {
            $q->andWhere('a.asset_estate = ?', 0);
        }

        $flag = false;
        foreach ($filters as $field => $value) {

            if (!is_null($value)) {

                $q->andWhere($field, $value);
            }
        }


        $q->orderBy('asset_name');
        
        if (!is_null($start)) {
            $q->offset($start);
        }
        if (!is_null($limit)) {
            $q->limit($limit);
        }
        
        if ($count) {
            $q->select('count(*)');
            return $q->fetchOne()->count;
        } else {
            return $q->execute();
        }
//        return $q->execute();
    }
    
    function retrieveByNodeIdCom($filters = array(), $node_id, $search_branch = false) {
        $q = Doctrine_Query::create()
                ->select('a.*, at.*, ba.*, ac.*, al.*')
                ->from('Asset a')
                ->innerJoin('a.Node n')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->leftJoin('a.AssetCondition ac')
                ->leftJoin('a.AssetLoad al')
                ->where('a.node_id = ?', $node_id);

        if ($search_branch) {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $q->where('n.node_parent_id = ?', $node->node_parent_id)
                    ->andWhere('n.lft >= ?', $node->lft)
                    ->andWhere('n.rgt <= ?', $node->rgt);
        } else {
            $q->where('n.node_id = ?', $node_id);
        }

        $flag = false;
        foreach ($filters as $field => $value) {

            if (!is_null($value)) {

                $q->andWhere($field, $value);
            }
        }


        $q->orderBy('asset_name');
        return $q->execute();
    }
    
    function retrieveByNodeIdTodo($node_id, $search_branch = false) {
        $q = Doctrine_Query::create()
                ->select('a.*, at.*, ba.*, ac.*, al.*')
                ->from('Asset a')
                ->innerJoin('a.Node n')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->leftJoin('a.AssetCondition ac')
                ->leftJoin('a.AssetLoad al')
                ->where('a.node_id = ?', $node_id);

        if ($search_branch) {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $q->where('n.node_parent_id = ?', $node->node_parent_id)
                    ->andWhere('n.lft >= ?', $node->lft)
                    ->andWhere('n.rgt <= ?', $node->rgt);
        } else {
            $q->where('n.node_id = ?', $node_id);
        }

//        $flag = false;
//        foreach ($filters as $field => $value) {
//
//            if (!is_null($value)) {
//
//                $q->andWhere($field, $value);
//            }
//        }
//
//
//        $q->orderBy('asset_name');
        return $q->execute();
    }
    
    function retrieveByNodeIdPapelera($filters = array()) {
        $q = Doctrine_Query::create()
                ->select('a.*, at.*, ba.*, ac.*, as.*')
                ->from('Asset a')
                ->innerJoin('a.Node n')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->leftJoin('a.AssetCondition ac')
                ->where('a.asset_estate = ?', 1);

        
        $flag = false;
        foreach ($filters as $field => $value) {

            if (!is_null($value)) {

                $q->andWhere($field, $value);
            }
        }


        $q->orderBy('asset_name');
        return $q->execute();
    }
    
    function retrieveOne($asset_id) {
        $q = Doctrine_Query::create()
                ->select('a.*, at.*, ba.*, ac.*, as.*')
                ->from('Asset a')
                ->innerJoin('a.Node n')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->leftJoin('a.AssetStatus as')
                ->leftJoin('a.AssetCondition ac')
                ->where('a.asset_id = ?', $asset_id);

        return $q->fetchOne();
    }
    
    function findNumInt($asset_num_serie_intern) {
        $q = Doctrine_Query::create()
                ->from('Asset a')
                ->where('a.asset_num_serie_intern = ?', $asset_num_serie_intern);

        return $q->fetchOne();
    }
    
    function findNodeParcial($node_id) {
        $q = Doctrine_Query::create()
                ->from('Asset a')
                ->where('a.node_id = ?', $node_id);

        return $q->execute();
    }
    
    function findIdAssetInventary($asset_id, $node_id) {
        $q = Doctrine_Query::create()
                ->from('AssetInventory ai')
                ->where('ai.asset_id = ?', $asset_id)
                ->andWhere('ai.asset_id = ?', $node_id);

        return $q->fetchOne();
    }

    function retrieveByNodeIdExport($node_id) {
        $q = Doctrine_Query::create()
                ->from('Asset a')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->leftJoin('a.AssetCondition ac')
                ->leftJoin('a.AssetStatus as')
                ->where('a.node_id = ?', $node_id)
                ->orderBy('asset_name');
        return $q->execute();
    }

    function cutAndPasteAsset($selected_asset_ids, $node_destino) {
        $connection = Doctrine_Manager::connection();
        $sqlUpdate = 'UPDATE asset SET node_id=' . $node_destino . ' where asset_id =';
        $asset_array = explode(",", $selected_asset_ids);
        $sqlFinal = '';
        foreach ($asset_array as $asset) {
            $sqlFinal .= $sqlUpdate . $asset . ';';
        }
        $connection->execute($sqlFinal);
    }

    function findNodeOrigen($endAssetId) {
        $q = Doctrine_Query::create()
                ->select('a.node_id')
                ->from('Asset a')
                ->where('a.asset_id = ?', $endAssetId)
                ->limit(1);
        return $q->fetchOne();
    }

    function getAssetDiff($node_id, $asset_excluded) {
        $q = Doctrine_Query::create()
                ->from('Asset a')
                ->where('node_id = ?', $node_id)
                ->andWhereNotIn('asset_id', $asset_excluded);
        return $q->execute();
    }

    /**
     *
     * assetInOt
     * Retorna true en el caso que exista un Activo asociado a la OT  y False en el caso contrario
     */
    function assetInOt($asset_id) {

        $q = Doctrine_Query::create()
                ->from('Asset a')
                ->innerJoin('a.MtnWorkOrder mwo')
                ->where('mwo.asset_id = ?', $asset_id)
                ->limit(1);

        $results = $q->execute();
        return ($results->count() == 0 ? false : true);
    }
    
    function retrieveOneByNumIntern($asset_num_serie_intern) {
        $q = Doctrine_Query::create()
        		->select('a.*')
                ->from('Asset a')
                ->where('a.asset_num_serie_intern = ?', $asset_num_serie_intern);
                
        return $q->fetchOne();
    }
    
    function getTotals ( $node_id, $asset_type_id ) {
    	
        $q = Doctrine_Query::create()
        		->select('*')
                ->addSelect('COUNT(*) as cantidad')
                ->addSelect('SUM(asset_cost) as costo_total')
                ->from('Asset a')
                ->where('a.node_id = ?', $node_id)
                ->andWhere('a.asset_type_id = ?', $asset_type_id)
                ->groupBy('node_id');
                
        return $q->fetchOne();
    	
    }
    
    function findTotalCompleto ( ) {
    	
         $q = Doctrine_Query::create()
                ->from('Asset a')
                ->orderBy('asset_id');
        return $q->execute();
    	
    }
    
    

}