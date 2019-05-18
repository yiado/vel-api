<?php

/**
 * @package    Model
 * @subpackage Node
 */
class Node extends BaseNode
{
    /**
     * hasSibling
     * 
     * Indica si el nodo tiene hijo
     * 
     * @return bool
     */
    public function hasSibling ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Node' )
                ->where ( 'node_parent_id = ' . $this->node_id );

        return ($q->count () > 0 ? true : false);
    }

    /**
     * getSibling
     * 
     * Retorna los hijos inmediatos de un nodo
     * 
     * @return array
     */
    public function getChildren ()
    {

        return $this->getNode ()->getChildren ();
    }

    public function getPath ( $separator='/' , $include_node=true )
    {

        $buffer = array ( );

        if ( ! $this->getNode ()->isRoot () )
        {

            $ancestors = $this->getNode ()->getAncestors ();
            if ( $ancestors )
            {
                foreach ( $ancestors as $ancestor )
                {

                    array_push ( $buffer , $ancestor->node_name );
                }
            }
        }

        if ( $include_node === true )
        {

            array_push ( $buffer , $this->node_name );
        }

        return implode ( $separator , $buffer );
    }

}

