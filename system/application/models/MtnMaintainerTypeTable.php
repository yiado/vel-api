<?php
/**
 */
class MtnMaintainerTypeTable extends Doctrine_Table {
    
    function findAllTotal()
    {
        $q = Doctrine_Query::create()
                ->from('MtnMaintainerType mmt')
                ->orderBy('mmt.mtn_maintainer_type_name ASC');
        
        return $q->execute();
    }


}
