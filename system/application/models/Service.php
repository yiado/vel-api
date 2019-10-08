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
class Service extends BaseService {
    public function preHydrate(Doctrine_Event $event) {
        $data = $event->data;
        $data['Node'] = Doctrine_Core::getTable('Node')->findOneByNodeId($data['node_id'])->toArray();
        $event->data = $data;
    }
}