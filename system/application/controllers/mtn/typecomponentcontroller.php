<?php

/** @package    Controller
 *  @subpackage TaskController
 */
class TypecomponentController extends APP_Controller
{
    function TypecomponentController ()
    {
        parent::APP_Controller ();
    }

    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $mtnComponentTypeTable = Doctrine_Core::getTable ( 'MtnComponentType' );
               $maintainer_type=1; //asset
        $componentTypes = $mtnComponentTypeTable->retrieveAll ($text_autocomplete,$maintainer_type );

        if ( $componentTypes->count () )
        {
            echo '({"total":"' . $componentTypes->count () . '", "results":' . $this->json->encode ( $componentTypes->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    function getByNode ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $mtnComponentTypeTable = Doctrine_Core::getTable ( 'MtnComponentType' );
            $maintainer_type=2;//node
        $componentTypes = $mtnComponentTypeTable->retrieveAll ($text_autocomplete , $maintainer_type );

        if ( $componentTypes->count () )
        {
            echo '({"total":"' . $componentTypes->count () . '", "results":' . $this->json->encode ( $componentTypes->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
}