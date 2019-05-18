<?php

/**
 */
class MtnTaskTable extends Doctrine_Table
{
    /*
     * Devuelve todas las tareas registradas en la base de datos
     */

    function retrieveAll($mtn_plan_id = NULL, $text_autocomplete = NULL,$maintainer_type)
    {

        $q = Doctrine_Query::create()
                ->from('MtnTask t')
                ->orderBy('t.mtn_task_name ASC');

        if (!is_null($mtn_plan_id))
        {
            $q->where('t.mtn_task_id NOT IN (SELECT mpt.mtn_task_id FROM MtnPlanTask mpt WHERE mpt.mtn_plan_id = ?)', $mtn_plan_id);
        }

        if (!is_null($text_autocomplete))
        {
            $q->where('t.mtn_task_name LIKE ?', $text_autocomplete . '%');
        }
         $q->andWhere('t.mtn_maintainer_type_id = ?', $maintainer_type);

        return $q->execute();
    }
    
    /**
     *
     * checkDataInPlanTask
     * Retorna true en el caso que exista un Dato asociado a un Plan y False en el caso contrario
     * @param integer $mtn_task_id
     */
    function checkDataInPlanTask($mtn_task_id)
    {


        $q = Doctrine_Query::create()
                ->from('MtnPlanTask mpt')
                ->where('mpt.mtn_task_id = ?', $mtn_task_id)
                ->limit(1);

        $results = $q->execute();

        return ($results->count() == 0 ? false : true);
    }

}
