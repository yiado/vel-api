<?php

/**
 */
class ReportTable extends Doctrine_Table {

    /**
     * Retorna todos los reportes del Sistema
     * 
     */
    function retrieveAll ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Report rp' )
                ->innerJoin ( 'rp.Module mo' )
                ->where('rp.report_id != ?', 7)
                ->orderBy ( 'rp.report_name ASC' );
        
        return $q->execute ();

    }
        function retrieveAllGroup($array_group=array()) {

        $q = Doctrine_Query::create()
                ->from('Report rp')
                ->innerJoin('rp.Module mo')
                ->innerJoin('rp.ReportUserGroup rug')
                ->where('rp.report_id != ?', 7)
                ->orderBy('rp.report_name ASC');
        
        foreach ($array_group as $field => $value) {

            if (!is_null($value)) {

                $q->orWhere('rug.user_group_id = ?', $value);
            }
        }
        return $q->execute();
    }

}