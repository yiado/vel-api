<?php

/**
 * @package    Controller
 * @subpackage NodeTypeCategoryController
 */
class NodeTypeCategoryController extends APP_Controller
{
    function NodeTypeCategoryController ()
    {
        parent::APP_Controller ();
    }

    /**
     * getList
     * 
     * Retorna categoria de los tipos de nodos
     */
    function getList ()
    {
        $nodeTypeCategoryTable = Doctrine_Core::getTable ( 'NodeTypeCategory' );
        $text_autocomplete = $this->input->post ( 'query' );
        $node_type_category = $nodeTypeCategoryTable->retrieveAll ( $text_autocomplete );

        if ( $node_type_category->count () )
        {
            echo '({"total":"' . $node_type_category->count () . '", "results":' . $this->json->encode ( $node_type_category->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     * 
     * Agrega nombre categoria de los tipos de nodos
     * 
     * @post string node_type_category_name
     */
    function add ()
    {
        $nodeTypeCategory = new NodeTypeCategory();
        $nodeTypeCategory[ 'node_type_category_name' ] = $this->input->post ( 'node_type_category_name' );

        try
        {
            $nodeTypeCategory->save ();
            $success = true;
            //Imprime el Tag en pantalla
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
        }
        catch ( Exception $e )
        {
            $success = false;
            $msg = $e->getMessage ();
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * update
     * 
     * Modifica nombre categoria de los tipos de nodos
     * 
     * @post int node_type_category_id
     * @post string node_type_category_name
     */
    function update ()
    {
        $nodeTypeCategory = Doctrine_Core::getTable ( 'NodeTypeCategory' )->find ( $this->input->post ( 'node_type_category_id' ) );
        $nodeTypeCategory[ 'node_type_category_name' ] = $this->input->post ( 'node_type_category_name' );
        $nodeTypeCategory->save ();
        echo '{"success": true}';
    }

    /**
     * delete
     * 
     * Elimina la categoria en caso de no existir un Nodo asociado
     * @post int $node_type_category_id
     */
    function delete ()
    {
        $node_type_category_id = $this->input->post ( 'node_type_category_id' );
        $checkNodeInCategory = Doctrine::getTable ( 'NodeTypeCategory' )->checkNodeInCategory ( $node_type_category_id );
        if ( $checkNodeInCategory === false )
        {
            $nodeTypeCategory = Doctrine::getTable ( 'NodeTypeCategory' )->find ( $node_type_category_id );
            if ( $nodeTypeCategory->delete () )
            {
                $exito = true;
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
            }
            else
            {
                $exito = false;
                $msg = 'Error: ';
            }
        }
        else
        {
            $exito = false;
            $msg = $this->translateTag ( 'General' , 'category_node_type_eliminated_associated_node' );
        }
        $json_data = $this->json->encode ( array ( 'success' => $exito , 'msg' => $msg ) );
        echo $json_data;
    }

}