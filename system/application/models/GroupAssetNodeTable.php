<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GroupAssetNodeTable extends Doctrine_Table {

    function deletePermissionsGroupAsset($user_group_id, $module_id) {

        $q = Doctrine_Query::create()
                ->delete('GroupAssetNode ga')
                ->Where('ga.user_group_id = ?', $user_group_id)
                ->andWhere('ga.module_id = ?', $module_id);

        return $q->execute();
    }

    function findOneByUserGroupIdAndNodeId($user_group_id, $node_id, $module_id) {

        $q = Doctrine_Query::Create()
                ->from('GroupAssetNode')
                ->where('user_group_id = ?', $user_group_id)
                ->andWhere('node_id = ?', $node_id)
                ->andWhere('module_id = ?', $module_id);

        return $q->fetchOne();
    }

    function permissionsGroupAsset($user_group_id) {

        $q = Doctrine_Query::create()
                ->from('GroupAssetNode ga')
                ->Where('ga.user_group_id = ?', $user_group_id);

        return $q->execute();
    }

    function groupAssetBrand($nodes_branch) {

        $q = Doctrine_Query::create()
                ->from('GroupAssetNode ga')
                ->Where('ga.node_id IN (:nodes)')->setParams($nodes_branch);


        return $q->execute();
}
    
       function findByNode($node_id) {

        $q = Doctrine_Query::create()
                ->from('GroupAssetNode')
                ->Where('node_id = ? ',$node_id);


        return $q->execute();
    }
}
