<?php

/**
 */
class ModuleTable extends Doctrine_Table {

    /**
     * Retorna todos los modulos cargados en el sistmea
     * 
     */
    function retrieveAll($front = false, $text_autocomplete = NULL) {

        $q = Doctrine_Query::create()
                ->from('Module mo')
                ->orderBy('mo.module_name ASC');

        if ($front === true) {
            $q->where('module_position IS NOT NULL');
        }

        if (!is_null($text_autocomplete)) {
            $q->where('mo.module_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }

    function findModule($module) {

        $q = Doctrine_Query::create()
                ->from('Module mo')
                ->where('module_position IS NOT NULL')
                ->andWhere('mo.module_namespace = ?', $module );
     
        return $q->fetchOne();
    }

}
