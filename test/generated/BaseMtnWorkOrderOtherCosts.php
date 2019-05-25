<?php

/**
 * BaseMtnWorkOrderOtherCosts
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $mtn_work_order_other_costs_id
 * @property integer $mtn_work_order_id
 * @property integer $mtn_other_costs_id
 * @property integer $mtn_work_order_other_costs_costs
 * @property string $mtn_work_order_other_costs_comment
 * @property MtnWorkOrder $MtnWorkOrder
 * @property MtnOtherCosts $MtnOtherCosts
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseMtnWorkOrderOtherCosts extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('mtn_work_order_other_costs');
        $this->hasColumn('mtn_work_order_other_costs_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('mtn_work_order_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_other_costs_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_work_order_other_costs_costs', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_work_order_other_costs_comment', 'string', 50, array(
             'type' => 'string',
             'length' => 50,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasOne('MtnWorkOrder', array(
             'local' => 'mtn_work_order_id',
             'foreign' => 'mtn_work_order_id'));

        $this->hasOne('MtnOtherCosts', array(
             'local' => 'mtn_other_costs_id',
             'foreign' => 'mtn_other_costs_id'));
    }
}