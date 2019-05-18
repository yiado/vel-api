<?php

/** @package    Controller
 *  @subpackage PlanCategoryController
 */
class PlanCategoryController extends APP_Controller
{
    function PlanCategoryController ()
    {
        parent::APP_Controller ();
    }

    function getList()
    {
        $node_type = Doctrine_Core::getTable('NodeType')->retrieveAllPlan();

        if ($node_type->count())
        {
            echo '({"total":"' . $node_type->count() . '", "results":' . $this->json->encode($node_type->toArray()) . '})';
        } else
        {
            echo '({"total":"0", "results":[]})';
        }
    }
    
    function asociarPlanCategoryAndTypeNode() {
        $node_type_ids = $this->input->post('node_type_ids');
        $plan_category_id = $this->input->post ( 'plan_category_id' );

        try {
            $node_type_ids = explode(",", $node_type_ids);
            
            foreach ($node_type_ids as $node_type_id) {
                $nodeType = Doctrine_Core::getTable('NodeType')->find($node_type_id);
                $nodeType->plan_category_id = $plan_category_id;
                $nodeType->save();
                $success = true;
                $msg = 'Nodos Asociados Exitosamente';
                
            }
            
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
                ));
        echo $json_data;
    }
    
    function desasociarPlanCategoryAndTypeNode() {
        $node_type_ids = $this->input->post('node_type_ids');

        try {
            $node_type_ids = explode(",", $node_type_ids);
            
            foreach ($node_type_ids as $node_type_id) {
                $nodeType = Doctrine_Core::getTable('NodeType')->find($node_type_id);
                $nodeType->plan_category_id = null;
                $nodeType->save();
                $success = true;
                $msg = 'Nodos Desasociados Exitosamente';
                
            }
            
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
                ));
        echo $json_data;
    }

    /**
     * get
     *
     * Lista las ultimas versiones de planos por categoria
     *
     * @post int node_id
     */
    function get ()
    {
        $text_autocomplete = $this->input->post ( 'query' );
        $plan_categories = Doctrine_Core::getTable ( 'PlanCategory' )->retrieveAll ( $text_autocomplete );

        if ( $plan_categories->count () )
        {
            echo '({"total":"' . $plan_categories->count () . '", "results":' . $this->json->encode ( $plan_categories->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * add
     *
     * Agregar categoria a los Planos
     *
     * @post string plan_category_name
     */
    function add ()
    {
        $plan_category = new PlanCategory();
        $plan_category_name = $this->input->post ( 'plan_category_name' );
        $plan_category_description = $this->input->post ( 'plan_category_description' );
        $plan_category_default = ( int ) $this->input->post ( 'plan_category_default' );
        $plan_category->plan_category_name = $plan_category_name;
        $plan_category->plan_category_description = $plan_category_description;
        $plan_category->plan_category_default = $plan_category_default;

        try
        {
            //Obtenemos la conexión actual
            $conn = Doctrine_Manager::getInstance ()->getCurrentConnection ();

            //Iniciamos la transacción
            $conn->beginTransaction ();

            //Setear la categoria por defecto para los planos que pueden ser vinculados a los nodos
            if ( $plan_category_default === 1 )
            {
                Doctrine_Core::getTable ( 'PlanCategory' )->unSetDefaultCategory ();
            }

            $plan_category->save ();
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
            $success = true;
            
            // Si todo OK, commit a la base de datos
            $conn->commit ();
        }
        catch ( Exception $e )
        {
            $conn->rollback ();
            $msg = $e->getMessage ();
            $success = false;
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * update
     *
     * Modifica nombre categoria de los tipos de Planos
     *
     * @post int node_id
     * @post string plan_category_name
     */
    function update ()
    {
        $plan_category_id = $this->input->post ( 'plan_category_id' );
        $plan_category = Doctrine_Core::getTable ( 'PlanCategory' )->find ( $plan_category_id );
        $plan_category_name = $this->input->post ( 'plan_category_name' );
        $plan_category_description = $this->input->post ( 'plan_category_description' );
        $plan_category_default = $this->input->post ( 'plan_category_default' );

        $plan_category->plan_category_name = $plan_category_name;
        $plan_category->plan_category_description = $plan_category_description;

        try
        {
            //Obtenemos la conexión actual
            $conn = Doctrine_Manager::getInstance ()->getCurrentConnection ();

            //Iniciamos la transacción
            $conn->beginTransaction ();

            //Setear la categoria por defecto para los planos que pueden ser vinculados a los nodos
            if ( $plan_category_default == 'true' )
            {
                Doctrine_Core::getTable ( 'PlanCategory' )->unSetDefaultCategory ( $plan_category_id );
                $plan_category_default = 1;
            }

            $plan_category->plan_category_default = $plan_category_default;
            $plan_category->save ();
            $msg = $this->translateTag ( 'General' , 'operation_successful' );
            $success = true;
            
            // Si todo OK, commit a la base de datos
            $conn->commit ();
        }
        catch ( Exception $e )
        {
            $conn->rollback ();
            $msg = $e->getMessage ();
            $success = false;
        }

        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

    /**
     * delete
     *
     * Elimina la categoria en caso de no existir un Plan asociado
     * @post int $plan_category_id
     */
    function delete ()
    {
        $plan_category_id = $this->input->post ( 'plan_category_id' );
        $checkPlanInCategory = Doctrine::getTable ( 'PlanCategory' )->checkPlanInCategory ( $plan_category_id );

        if ( $checkPlanInCategory === false )
        {
            $planCategory = Doctrine::getTable ( 'PlanCategory' )->find ( $plan_category_id );

            if ( $planCategory->delete () )
            {
                $msg = $this->translateTag ( 'General' , 'operation_successful' );
                $success = true;
            }
            else
            {
                $msg = $this->translateTag ( 'Plan' , 'category_plan_cant_be_eliminated' );
                $success = false;
            }
        }
        else
        {
            $success = false;
            $msg = $this->translateTag ( 'Plan' , 'category_plan_cant_be_eliminated' );
        }
        $json_data = $this->json->encode ( array ( 'success' => $success , 'msg' => $msg ) );
        echo $json_data;
    }

}

