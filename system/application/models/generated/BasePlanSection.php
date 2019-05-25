<?php

/**
 * BasePlanSection
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $plan_section_id
 * @property integer $plan_id
 * @property string $plan_section_name
 * @property string $plan_section_color
 * @property integer $plan_section_status
 * @property Plan $Plan
 * @property Doctrine_Collection $PlanNode
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BasePlanSection extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('plan_section');
        $this->hasColumn('plan_section_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('plan_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_section_name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_section_color', 'string', 10, array(
             'type' => 'string',
             'length' => 10,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_section_status', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '1',
             'notnull' => false,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasOne('Plan', array(
             'local' => 'plan_id',
             'foreign' => 'plan_id'));

        $this->hasMany('PlanNode', array(
             'local' => 'plan_section_id',
             'foreign' => 'plan_section_id'));
    }
}