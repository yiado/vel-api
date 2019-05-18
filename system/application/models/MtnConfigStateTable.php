<?php

/**
 */
class MtnConfigStateTable extends Doctrine_Table
{

    /**
     * 
     * Retorna todos los estados disponibles ignorando los que ya estan asociados al tipo de O.T.
     * Si el $mtn_system_work_order_status_id es NULL, lista todos los estados disponibles
     * @param integer $mtn_system_work_order_status_id
     * 
     */
    function retrieveAll($mtn_work_order_type_id = NULL, $text_autocomplete = NULL,$maintainer_type)
    {
        $q = Doctrine_Query::create()
                ->from('MtnSystemWorkOrderStatus mswos')
                ->orderBy('mswos.mtn_system_work_order_status_name ASC');

        if (!is_null($mtn_work_order_type_id))
        {
            $q->where('mswos.mtn_system_work_order_status_id NOT IN (SELECT mcs.mtn_system_work_order_status_id FROM MtnConfigState mcs WHERE mcs.mtn_work_order_type_id = ?)', $mtn_work_order_type_id);
        }
        
        if (!is_null($text_autocomplete))
        {
            $q->where('mswos.mtn_system_work_order_status_name LIKE ?', $text_autocomplete . '%');
        }
          $q->andWhere('mswos.mtn_maintainer_type_id = ?', $maintainer_type);
        return $q->execute();
    }

    /**
     * Devuelve los estados asociados al tipo O.T.
     * @param integer $mtn_config_state_id
     */
    function retrieveByState($mtn_work_order_type_id = NULL)
    {
        $q = Doctrine_Query::create()
                ->from('MtnConfigState mcs')
                ->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
                ->where('mcs.mtn_work_order_type_id = ?', $mtn_work_order_type_id)
                ->orderBy('mcs.mtn_config_state_order ASC');
        return $q->execute();
    }

    function retrieveByStateUser($mtn_work_order_type_id = NULL, $text_autocomplete = NULL)
    {
        $q = Doctrine_Query::create()
                ->from('MtnConfigState mcs')
                ->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
                ->where('mcs.mtn_work_order_type_id = ?', $mtn_work_order_type_id)
                ->andWhere('mcs.mtn_config_state_access_user = ?', 1)
                ->orderBy('mswos.mtn_system_work_order_status_name ASC');

        if (!is_null($text_autocomplete))
        {
            $q->where('mswos.mtn_system_work_order_status_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }
    
    function retrieveByStateUserUno($mtn_work_order_type_id)
    {
        $q = Doctrine_Query::create()
                ->from('MtnConfigState mcs')
                ->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
                ->where('mcs.mtn_work_order_type_id = ?', $mtn_work_order_type_id);

        return $q->fetchOne();
    }

    function retrieveByStateProvider($mtn_work_order_type_id = NULL, $text_autocomplete = NULL)
    {
        $q = Doctrine_Query::create()
                ->from('MtnConfigState mcs')
                ->innerJoin('mcs.MtnSystemWorkOrderStatus mswos')
                ->where('mcs.mtn_work_order_type_id = ?', $mtn_work_order_type_id)
                ->andWhere('mcs.mtn_config_state_access_provider = ?', 1)
                ->orderBy('mcs.mtn_config_state_order ASC');

        if (!is_null($text_autocomplete))
        {
            $q->where('mswos.mtn_system_work_order_status_name LIKE ?', $text_autocomplete . '%');
        }
        return $q->execute();
    }

    /**
     * Elimina los estados asociados al O.T.
     * @param integer $mtn_work_order_type_id
     */
    function eliminateCurrent($mtn_work_order_type_id)
    {
        $q = Doctrine_Query::create()
                ->delete('MtnConfigState mcs')
                ->where('mcs.mtn_work_order_type_id = ?', $mtn_work_order_type_id)
                ->andWhere('mtn_config_state_order >= 0');
        return $q->execute();
    }

    function retrieveByMayor($mtn_work_order_type_id)
    {
        $q = Doctrine_Query::create()
                // ->select('MAX(mcs.mtn_config_state_order)')
                ->from('MtnConfigState mcs')
                ->where('mcs.mtn_work_order_type_id = ?', $mtn_work_order_type_id)
                ->orderBy('mcs.mtn_config_state_order DESC')

//->where ( 'mcs.mtn_system_work_order_status_id = ?' , $mtn_system_work_order_status_id )
        ;
        //  return $q->execute ();
        return $q->fetchOne();
    }

    function findByBefore($mtn_work_order_type_id, $menos1)
    {
        $q = Doctrine_Query::create()
                ->from('MtnConfigState mcs')
                ->where('mcs.mtn_work_order_type_id = ?', $mtn_work_order_type_id)
                ->andWhere('mcs.mtn_config_state_order = ?', $menos1)
        ;
        return $q->fetchOne();
    }

    function retrieveByMenorProvider($mtn_work_order_type_id)
    {
        $q = Doctrine_Query::create()
                ->from('MtnConfigState mcs')
                ->where('mcs.mtn_work_order_type_id = ?', $mtn_work_order_type_id)
                ->andWhere('mtn_config_state_access_provider = ?', 1)
                ->orderBy('mcs.mtn_config_state_order ASC')
        ;
        return $q->fetchOne();
    }

    function retrieveByMenorUser($mtn_work_order_type_id)
    {
        $q = Doctrine_Query::create()
                ->from('MtnConfigState mcs')
                ->where('mcs.mtn_work_order_type_id = ?', $mtn_work_order_type_id)
                ->andWhere('mtn_config_state_access_user = ?', 1)
                ->orderBy('mcs.mtn_config_state_order ASC')
        ;
        return $q->fetchOne();
    }

}
