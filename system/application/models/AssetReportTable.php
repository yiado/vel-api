<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class AssetReportTable extends Doctrine_Table
{
    function findAsset($child_nodes=null) {

        $q = Doctrine_Query :: create()
                ->select('*')
                ->from('AssetReport');

        if(!empty($child_nodes) && !is_null($child_nodes)){
            $q->where( 'node_id IN ('.$child_nodes.')');
        }
        
        $q->orderBy('node_id asc');
        
        return $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    }
    
}
