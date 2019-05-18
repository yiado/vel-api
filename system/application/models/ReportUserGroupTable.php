<?php

/**
 */
class ReportUserGroupTable extends Doctrine_Table {

    function retrieveByModule($report_id ) {

        $q = Doctrine_Query::create()
                ->from('UserGroup ug')
                ->leftJoin('ug.ReportUserGroup rug')
        ;

        $q->Where ( 'ug.user_group_id NOT IN (SELECT rug1.user_group_id FROM ReportUserGroup rug1 WHERE rug1.report_id = ?)' , $report_id );

        return $q->execute();
    }
    
        function retrieveByModulePermitted($report_id ) {

        $q = Doctrine_Query::create()
                ->from('UserGroup ug')
                ->leftJoin('ug.ReportUserGroup rug')
                ->where ('rug.report_id = ?' , $report_id)
        //  ->orderBy ( 'ma.module_action_uri ASC' )
        ;

        return $q->execute();
    }
    
    
     function deleteCurrentPermissionsGroup ( $report_id  )
    {

        $q = Doctrine_Query::create ()
                ->delete ( 'ReportUserGroup rug' )   
                ->Where ( 'rug.report_id = ?' , $report_id );
        return $q->execute ();
    }
    
    
    

}
