<?php

/**
 */
class MtnPlanTaskTable extends Doctrine_Table
{

    /**
     * retrieveById
     * 
     * Retorna valor de info por id
     * 
     * @param integer $mtn_plan_id
     */
    function retrieveAll ( $mtn_plan_id )
    {
        $q = Doctrine_Query :: create ()
                ->from ( 'MtnPlanTask pt' )
                ->leftJoin ( 'pt.MtnTask t' )
                ->where ( 'pt.mtn_plan_id = ?' , $mtn_plan_id );
        return $q->execute ();
    }

    function retrieveAllByIdTask ( $mtn_task_id , $mtn_plan_id )
    {

        $q = Doctrine_Query :: create ()
                ->from ( 'MtnPlanTask pt' )
                ->where ( 'pt.mtn_plan_id = ?' , $mtn_plan_id );

        return $q->execute ();
    }

}
