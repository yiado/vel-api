<?php

/**
 * BasePlan
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $plan_id
 * @property integer $node_id
 * @property integer $plan_category_id
 * @property integer $user_id
 * @property integer $plan_current_version
 * @property string $plan_filename
 * @property string $plan_version
 * @property string $plan_comments
 * @property string $plan_description
 * @property timestamp $plan_datetime
 * @property Node $Node
 * @property User $User
 * @property PlanCategory $PlanCategory
 * @property Doctrine_Collection $PlanNode
 * @property Doctrine_Collection $PlanSection
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BasePlan extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('plan');
        $this->hasColumn('plan_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('node_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_category_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_current_version', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_filename', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_version', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_comments', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_description', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('plan_datetime', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasOne('Node', array(
             'local' => 'node_id',
             'foreign' => 'node_id'));

        $this->hasOne('User', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasOne('PlanCategory', array(
             'local' => 'plan_category_id',
             'foreign' => 'plan_category_id'));

        $this->hasMany('PlanNode', array(
             'local' => 'plan_id',
             'foreign' => 'plan_id'));

        $this->hasMany('PlanSection', array(
             'local' => 'plan_id',
             'foreign' => 'plan_id'));
    }
}