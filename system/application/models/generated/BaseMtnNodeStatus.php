<?php

/**
 * BaseMtnNodeStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $mtn_node_status_id
 * @property string $mtn_node_status_name
 * @property Doctrine_Collection $MtnNodeWorkOrder
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseMtnNodeStatus extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('mtn_node_status');
        $this->hasColumn('mtn_node_status_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('mtn_node_status_name', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasMany('MtnNodeWorkOrder', array(
             'local' => 'mtn_node_status_id',
             'foreign' => 'mtn_node_status_id'));
    }
}