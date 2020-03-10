<?php

/**
 * Service
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Rdi extends BaseRdi {

    public function preHydrate(Doctrine_Event $event) {
        $data = $event->data;
        $data['Node'] = Doctrine_Core::getTable('Node')->findOneByNodeId($data['node_id'])->toArray();
        $event->data = $data;
    }

    function sendNotificationRecibido($node) {
        $CI = & get_instance();
        $nodos_ancestros = $this->getAncestros($node);
        $date = new DateTime($this->rdi_created_at);
        $fecha = $date->format('d/m/Y H:i');
        $body = $CI->load->view('mails/rdi',
                array(
                    'rdi' => $this,
                    'fecha' => $fecha,
                    'nodos_ancestros' => $nodos_ancestros,
                    'serviceStatus' => $this->RdiStatus
                ),
                true
        );
        $CI->load->library('NotificationUser');
        $CI->notificationuser->mail($this->User->user_email, 'Solicitud de información recibida', $body);
        $CI->notificationuser->mail($this->RdiAdmin->User->user_email, 'Nueva Solicitud de información', $body);
    }

    function sendNotificationUpdate($serviceStatus) {
        $CI = & get_instance();
        $node = Doctrine_Core::getTable('Node')->find($this->node_id);
        $nodos_ancestros = $this->getAncestros($node);
        $date = new DateTime($this->rdi_updated_at);
        $fecha = $date->format('d/m/Y H:i');
        $body = $CI->load->view('mails/rdi_change_status',
                array(
                    'rdi' => $this,
                    'fecha' => $fecha,
                    'nodos_ancestros' => $nodos_ancestros,
                    'serviceStatus' => $serviceStatus
                ),
                true
        );
        $CI->load->library('NotificationUser');
        $CI->notificationuser->mail($this->User->user_email, 'Cambio estado de solicitud', $body);
    }

    function sendEvaluation($serviceStatus) {
        $CI = & get_instance();
        $node = Doctrine_Core::getTable('Node')->find($this->node_id);
        $nodos_ancestros = $this->getAncestros($node);
        $date = new DateTime($this->rdi_updated_at);
        $fecha = $date->format('d/m/Y H:i');
        $body = $CI->load->view('mails/rdi_evaluation',
                array(
                    'rdi' => $this,
                    'fecha' => $fecha,
                    'nodos_ancestros' => $nodos_ancestros,
                    'serviceStatus' => $serviceStatus
                ),
                true
        );
        $CI->load->library('NotificationUser');
        $CI->notificationuser->mail($this->User->user_email, 'Para seguir mejorando evaluanos', $body);
    }

    function getAncestros($node) {
        $nodos_ancestros = array();
        if ($node->getNode()->getLevel()) {
            foreach ($node->getNode()->getAncestors()->toArray() as $nodo) {
                $nodos_ancestros[] = $nodo['node_name'];
            }
            $nodos_ancestros[] = $node->toArray()['node_name'];
        } else {
            $nodos_ancestros[] = $node->toArray()['node_name'];
        }
        return $nodos_ancestros;
    }

}
