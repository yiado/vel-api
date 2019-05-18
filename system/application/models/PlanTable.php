<?php

/**
 */
class PlanTable extends Doctrine_Table {

    function find($plan_id) {

        $q = Doctrine_Query :: create()
                ->from('Plan p')
                ->where('p.plan_id = ?', (is_array($plan_id) ? $plan_id['plan_id'] : $plan_id));


        return $q->fetchOne();
    }

    function retrieveCurrents($node_id, $plan_category_id = null) {

        $q = Doctrine_Query::create()
                ->from('Plan p')
                ->innerJoin('p.User')
                ->innerJoin('p.PlanCategory pc')
                ->where('node_id = ?', $node_id)
                ->andWhere('plan_current_version = 1');

        if (!is_null($plan_category_id)) {
            $q->andWhere('p.plan_category_id = ?', $plan_category_id);
        }

        $q->orderBy('pc.plan_category_name ASC');

        return $q->execute();
    }

    function retrieveCurrentP($node_id) {

        $q = Doctrine_Query::create()
                ->from('Plan p')
                ->innerJoin('p.User')
                ->innerJoin('p.PlanCategory pc')
                ->where('node_id = ?', $node_id)
                ->andWhere('plan_current_version = 1');

        $q->orderBy('pc.plan_category_name ASC');

        return $q->fetchOne();
    }

    function retrieveCurrentBIM($node_id,$plan_category_id) {

        $q = Doctrine_Query::create()
                ->from('Plan p')
                ->innerJoin('p.User')
                ->innerJoin('p.PlanCategory pc')
                ->where('node_id = ?', $node_id)
                ->andWhere('plan_category_id = ?', $plan_category_id)
                ->andWhere('plan_current_version = 1');

        $q->orderBy('pc.plan_category_name ASC');

        return $q->fetchOne();
    }

    /**
     * Retorna todos los planos del nodo aplicando los filtros de categoria en caso de ser necesario.
     * @param array $filters
     *
     */
    function retrieveByNode($filters = array()) {

        $q = Doctrine_Query::create()
                ->from('Plan p')
                ->innerJoin('p.User')
                ->innerJoin('p.PlanCategory pc')
                ->orderBy('pc.plan_category_name, plan_datetime');

        $flag = false;
        foreach ($filters as $field => $value) {

            if (!is_null($value)) {

                if ($flag === false) {

                    $q->where($field, $value);
                    $flag = true;
                } else {

                    $q->andWhere($field, $value);
                }
            }
        }
        return $q->execute();
    }

    function retrieveByNodeResumen($filters = array()) {

        $q = Doctrine_Query::create()
                ->from('Plan p')
                ->innerJoin('p.PlanCategory pc')
                ->innerJoin('pc.NodeType nt');

//                ->orderBy('pc.plan_category_name, plan_datetime')



        $flag = false;
        foreach ($filters as $field => $value) {

            if (!is_null($value)) {

                if ($flag === false) {

                    $q->where($field, $value);
                    $flag = true;
                } else {

                    $q->andWhere($field, $value);
                }
            }
        }

        $q->orderBy('p.plan_current_version DESC');
        return $q->fetchOne();
    }

    function retrieveByNodeExport($node_id) {

        $q = Doctrine_Query::create()
                ->from('Plan p')
                ->innerJoin('p.User')
                ->innerJoin('p.PlanCategory pc')
                ->where('p.node_id=?', $node_id)
                ->orderBy('pc.plan_category_name, plan_datetime');

        return $q->execute();
    }

    function retrieveVersions($node_id, $plan_category_id) {

        $q = Doctrine_Query::create()
                ->from('Plan p')
                ->innerJoin('p.PlanCategory pc')
                ->innerJoin('p.User')
                ->where('node_id = ?', $node_id)
                ->andWhere('p.plan_category_id = ?', $plan_category_id)
                ->orderBy('p.plan_datetime DESC');

        return $q->execute();
    }

    /*
     * Devuele la tupla del plan segun el id (PK) del parametro.
     * @param integer $plan_id
     * @return 1 Row
     * 
     */

    function retrieveById($plan_id) {

        $q = Doctrine_Query::create()
                ->from('Plan')
                ->andWhere('plan_id = ?', $plan_id);

        return $q->fetchOne();
    }

    function findByPlanName($plan_name) {

        $q = Doctrine_Query::create()
                ->from('Plan')
                ->Where('plan_filename = ?', $plan_name);

        return $q->fetchOne();
    }

    /*
     * Setea la categoria actual del plan.
     * Establece a 0 el valor categoria actual "anterior"
     * @param integer $node_id
     * @param integer $plan_id
     */

    function changeCurrentCategory($node_id, $plan_id, $plan_category_id) {

        $q = Doctrine_Query::create()
                ->update('Plan')
                ->set('plan_current_version', 0)
                ->where('plan_current_version = ?', 1)
                ->andWhere('node_id = ?', $node_id)
                ->andWhere('plan_category_id = ?', $plan_category_id)
                ->andWhere('plan_current_version = ?', 1)
                ->andWhere('plan_id <> ?', $plan_id);

        $q->execute();
    }

    /*
     * Eliminar el actual plano y dejar el anterior por fecha como actual.
     * @param integer $node_id
     * @param integer $plan_id
     */

    function deletePlan($node_id, $plan_id, $plan_category_id) {


        if (!empty($plan_id)) {
            $q = Doctrine_Query::create()
                    ->delete('Plan p')
                    ->where('p.plan_id = ?', $plan_id);

            $q->execute();

            //Quitar el archivo
            $path = './plans/';
            $file_name = './plans/';
            $file_full_path = $path . $file_name;
            if (unlink($file_full_path)) {
                //Buscamos el último plan de la categoria
                $q = Doctrine_Query::create()
                        ->from('Plan p')
                        ->innerJoin('p.PlanCategory pc')
                        ->innerJoin('p.User')
                        ->where('node_id = ?', $node_id)
                        ->andWhere('p.plan_category_id = ?', $plan_category_id)
                        ->orderBy('p.plan_datetime DESC')
                        ->limit(1);

                $a = $q->fetchOne();

                //Marcamos como versión actual el último plano obtenido
                $a->plan_current_version = 1;
                $a->save();
            }
        }
    }

    function findByNodeAndCategory($node_id, $plan_category_id) {
        $q = Doctrine_Query::create()
                ->from('Plan p')
                ->Where('p.node_id = ?', $node_id)
                ->andWhere('p.plan_category_id = ?', $plan_category_id);

        return $q->execute();
    }

    function retrivePenultimate($node_id, $plan_category_id) {
        $q = Doctrine_Query::create()
                ->from('Plan p')
                ->Where('p.node_id = ?', $node_id)
                ->andWhere('p.plan_category_id = ?', $plan_category_id)
                ->orderBy('p.plan_datetime DESC')
                ->offset(1)
        ;

        return $q->fetchOne();
    }

}
