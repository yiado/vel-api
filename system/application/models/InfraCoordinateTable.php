<?php

/**
 */
class InfraCoordinateTable extends Doctrine_Table {

    function findByCoordinates($nodeChild, $lat, $lng) {


        $q = Doctrine_Query :: create()
                ->from('InfraCoordinate ic')
                ->where('ic.node_latitude = ?', $lat)
//                ->where('ic.node_longitude = ?', $lng)
                ->andWhere('ic.node_id IN (' . $nodeChild . ')');
        return $resp = $q->fetchOne();
    }

    function retrieveByNode($node_id, $search_branch = null) {

        $final = array();

        if ($search_branch == 1) {

            $i = 0;
            $node = Doctrine_Core::getTable('Node')->find($node_id);

            $nodes = $node->getChildren();
//            echo 'asas';
//            print_r($nodes); exit();

            if ($nodes) {
                foreach ($nodes as $node) {
//                    echo 'node_id: '.$node->node_id;
                    //BUSCA LAS CORDENADAS
                    $q = Doctrine_Query :: create()
                            ->from('InfraCoordinate ic')
                            ->where('ic.node_id = ?', $node->node_id);
                    $resp = $q->fetchOne();
                    //BUSCA EL NOMBRE DEL NODO
                    $q = Doctrine_Query :: create()
                            ->from('Node n')
                            ->where('n.node_id = ?', $node->node_id);
                    $resp2 = $q->fetchOne();

                    $q = Doctrine_Query::create()
                            ->from('InfraInfo ff')
                            ->where('ff.node_id = ?', $node->node_id);

                    $resp3 = $q->fetchOne();
                    
//                    echo 'node_id : '.$node->node_id.'/'.'Superficie terreno: '.$resp3->infra_info_terrain_area_total;
                    $area_total = (isset($resp3->infra_info_terrain_area_total) && !is_null($resp3->infra_info_terrain_area_total)) ? $resp3->infra_info_terrain_area_total : 0;
                    $area_construida_total = (isset($resp3->infra_info_area_total) && !is_null($resp3->infra_info_area_total)) ? $resp3->infra_info_area_total : 0;

                    if ($resp) {
                        $final[$i]['node_id'] = $resp->node_id;
                        $final[$i]['node_longitude'] = $resp->node_longitude;
                        $final[$i]['node_latitude'] = $resp->node_latitude;
                        $final[$i]['node_name'] = ($resp2->node_name ? $resp2->node_name . ' - SUPERFICIE TERRENO TOTAL: ' . $area_total . ' - SUPERFICIE CONSTRIDA TOTAL: ' . $area_construida_total: '');
                        $i++;
                    }
                }
            }
        } else {

            $q = Doctrine_Query :: create()
                    ->from('InfraCoordinate ic')
                    ->where('ic.node_id = ?', $node_id);
            $resp = $q->fetchOne();

            //BUSCA EL NOMBRE DEL NODO
            $q = Doctrine_Query :: create()
                    ->from('Node n')
                    ->where('n.node_id = ?', $node_id);
            $resp2 = $q->fetchOne();

            //BUSCA INFO DE LA SUPERFICIE
            $q = Doctrine_Query::create()
                    ->from('InfraInfo ff')
                    ->where('ff.node_id = ?', $node_id);

            $resp3 = $q->fetchOne();

            $area_total = (isset($resp3->infra_info_terrain_area_total) && !is_null($resp3->infra_info_terrain_area_total)) ? $resp3->infra_info_terrain_area_total : 0;
            $area_construida_total = (isset($resp3->infra_info_area_total) && !is_null($resp3->infra_info_area_total)) ? $resp3->infra_info_area_total : 0;

            if ($resp) {
                $final[0]['node_id'] = $resp->node_id;
                $final[0]['node_longitude'] = $resp->node_longitude;
                $final[0]['node_latitude'] = $resp->node_latitude;
                $final[0]['node_name'] = ($resp2->node_name ? $resp2->node_name . ' - SUPERFICIE TERRENO TOTAL: ' . $area_total . ' - SUPERFICIE CONSTRIDA TOTAL: ' . $area_construida_total : '');
            } else {
                return null;
            }
        }

        return $final;
    }

    function retrieveByNodeParent($node_id, $search_branch = null) {

        $final = array();

        $i = 0;
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $nodes = $node->getChildren();
//            echo 'asas';
//            print_r($nodes); exit();

        if ($nodes) {
            foreach ($nodes as $node) {
                //BUSCA LAS CORDENADAS
                $q = Doctrine_Query :: create()
                        ->from('InfraCoordinate ic')
                        ->where('ic.node_id = ?', $node->node_id);
                $resp = $q->fetchOne();
                //BUSCA EL NOMBRE DEL NODO
                $q = Doctrine_Query :: create()
                        ->from('Node n')
                        ->where('n.node_id = ?', $node->node_id);
                $resp2 = $q->fetchOne();

                $q = Doctrine_Query::create()
                        ->from('InfraInfo ff')
                        ->where('ff.node_id = ?', $node->node_id);

                $resp3 = $q->fetchOne();


                if ($resp) {
                    $final[$i]['node_id'] = $resp->node_id;
                    $final[$i]['node_longitude'] = $resp->node_longitude;
                    $final[$i]['node_latitude'] = $resp->node_latitude;
                    $final[$i]['node_name'] = ($resp2->node_name ? $resp2->node_name . ' - SUPERFICIE TERRENO TOTAL: ' . $resp3->infra_info_terrain_area_total . ' - SUPERFICIE CONSTRIDA TOTAL: ' . $resp3->infra_info_area_total : '');
                    $i++;
                }
            }
        }


        $q = Doctrine_Query :: create()
                ->from('InfraCoordinate ic')
                ->where('ic.node_id = ?', $node_id);
        $resp = $q->fetchOne();

        //BUSCA EL NOMBRE DEL NODO
        $q = Doctrine_Query :: create()
                ->from('Node n')
                ->where('n.node_id = ?', $node_id);
        $resp2 = $q->fetchOne();

        //BUSCA INFO DE LA SUPERFICIE
        $q = Doctrine_Query::create()
                ->from('InfraInfo ff')
                ->where('ff.node_id = ?', $node_id);

        $resp3 = $q->fetchOne();


        if ($resp) {
            $final[$i]['node_id'] = $resp->node_id;
            $final[$i]['node_longitude'] = $resp->node_longitude;
            $final[$i]['node_latitude'] = $resp->node_latitude;
            $final[$i]['node_name'] = ($resp2->node_name ? $resp2->node_name . ' - SUPERFICIE TERRENO TOTAL: ' . $resp3->infra_info_terrain_area_total . ' - SUPERFICIE CONSTRIDA TOTAL: ' . $resp3->infra_info_area_total : '');
        }

        if (count($final) > 0) {
            return $final;
        }
        return null;
    }

    function getById($node_id) {

        $q = Doctrine_Query::create()
                ->from('InfraInfo ff')
                ->where('ff.node_id = ?', $node_id);

        return $q->fetchOne();
    }

    function nodeChildData($node_id) {

        $final = array();



        $i = 0;
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $nodes = $node->getChildren();
//            echo 'asas';
//            print_r($nodes); exit();

        if ($nodes) {
            foreach ($nodes as $node) {


                //BUSCA EL NOMBRE DEL NODO
                $q = Doctrine_Query :: create()
                        ->from('Node n')
                        ->where('n.node_id = ?', $node->node_id);
                $resp2 = $q->fetchOne();

                //BUSCA LA INFORMACIÓN DEL NODO
                $q = Doctrine_Query::create()
                        ->from('InfraInfo ff')
                        ->where('ff.node_id = ?', $node->node_id);

                $resp3 = $q->fetchOne();


                if ($resp2 && $resp3) {
                    $final[$i]['value'] = $resp3->infra_info_area_total; // - SUPERFICIE CONSTRIDA TOTAL
                    $final[$i]['label'] = $resp2->node_name;
                    $i++;
                }
            }
        } else {

            $final = array();
        }

        return $final;
    }

}
