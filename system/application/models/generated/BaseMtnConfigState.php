<?php

/**
 * BaseMtnConfigState
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $mtn_config_state_id
 * @property integer $mtn_work_order_type_id
 * @property integer $mtn_system_work_order_status_id
 * @property integer $mtn_config_state_access_user
 * @property integer $mtn_config_state_access_provider
 * @property integer $mtn_config_state_order
 * @property integer $mtn_config_state_default
 * @property integer $mtn_config_state_duration
 * @property MtnSystemWorkOrderStatus $MtnSystemWorkOrderStatus
 * @property MtnWorkOrderType $MtnWorkOrderType
 * @property Doctrine_Collection $MtnStatusLog
 * @property Doctrine_Collection $MtnWorkOrder
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseMtnConfigState extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('mtn_config_state');
        $this->hasColumn('mtn_config_state_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('mtn_work_order_type_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_system_work_order_status_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_config_state_access_user', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_config_state_access_provider', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_config_state_order', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_config_state_default', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_config_state_duration', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasOne('MtnSystemWorkOrderStatus', array(
             'local' => 'mtn_system_work_order_status_id',
             'foreign' => 'mtn_system_work_order_status_id'));

        $this->hasOne('MtnWorkOrderType', array(
             'local' => 'mtn_work_order_type_id',
             'foreign' => 'mtn_work_order_type_id'));

        $this->hasMany('MtnStatusLog', array(
             'local' => 'mtn_config_state_id',
             'foreign' => 'mtn_config_state_id'));

        $this->hasMany('MtnWorkOrder', array(
             'local' => 'mtn_config_state_id',
             'foreign' => 'mtn_config_state_id'));
    }
}