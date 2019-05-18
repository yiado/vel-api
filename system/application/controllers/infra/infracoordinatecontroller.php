<?php

/**
 * @package    Controller
 * @subpackage infrainfoController
 */
class infraCoordinateController extends APP_Controller {

    function infraCoordinateController() {
        parent::APP_Controller();
    }

    /** get
     * @param integer $node_id
     * 
     * @method POST 
     */
    function get() {
        $node_id = $this->input->post('node_id');
        $search_branch = $this->input->post('search_branch');
        $active_tab = $this->input->post('active_tab');

        $this->load->library('TreeNodes');
        $treeObject = Doctrine_Core::getTable('Node')->getTree();
        $nodes = $treeObject->fetchRoots();
        
      
        
        if(($nodes[0]->node_id == $node_id) && ($active_tab == 'mapResumen')){
              $infraCoordinate = Doctrine_Core::getTable('InfraCoordinate')->retrieveByNodeParent($node_id, $search_branch);
           
        }else{
             $infraCoordinate = Doctrine_Core::getTable('InfraCoordinate')->retrieveByNode($node_id, $search_branch);
        }
        
//        $infraCoordinate = Doctrine_Core::getTable('InfraCoordinate')->findByNodeId($node_id);
       

        if (count($infraCoordinate) >= 1) {
            echo '({"total":"' . count($infraCoordinate) . '", "results":' . $this->json->encode($infraCoordinate) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function version() {
        echo CI_VERSION;
    }

    function edit() {
        $action = $this->input->post('action');

        if (method_exists($this, $action)) {
            $this->$action();

            $node = Doctrine::getTable('Node')->find($this->input->post('node_id'));
            $this->syslog->register('edit_infra_coordinate', array(
                $node->getPath()
            )); // registering log
        } else {
            echo '{"success": false}';
        }
    }

    function add() {
        $infraCoordinate = Doctrine_Core::getTable('InfraCoordinate')->find($this->input->post('node_id'));
        if (!$infraCoordinate) {
            $infraCoordinate = new InfraCoordinate();
        }
        $infraCoordinate->fromArray($this->input->postall());
        $infraCoordinate->save();
        echo '{"success": true}';
    }

    function update() {
        $infraCoordinate = Doctrine_Core::getTable('InfraCoordinate')->find($this->input->post('node_id'));
        $infraCoordinate->fromArray($this->input->postall());
        $infraCoordinate->save();
        echo '{"success": true}';
    }

    function delete() {
        $infraCoordinate = Doctrine_Core::getTable('InfraCoordinate')->find($this->input->post('node_id'));
        $infraCoordinate->delete();
        echo '{"success": true}';
    }

}
