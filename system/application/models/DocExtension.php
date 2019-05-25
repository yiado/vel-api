<?php

/**
 * DocExtension
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class DocExtension extends BaseDocExtension
{

    function isAllowed ( $extension )
    {

        $extensions = Doctrine_Core::getTable ( 'DocExtension' )->retrieveByExtension ( strtoupper($extension) );

        foreach ( $extensions->toArray () as $ext )
        {

            if ( in_array ( strtoupper($extension) , explode ( ',' , strtoupper($ext['doc_extension_extension']) ) ) )
            {

                return $ext[ 'doc_extension_id' ];
            }
        }

        return false;
    }

}