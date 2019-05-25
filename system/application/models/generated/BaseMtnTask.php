<?php

/**
 * BaseMtnTask
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $mtn_task_id
 * @property integer $mtn_task_time
 * @property string $mtn_task_name
 * @property integer $mtn_maintainer_type_id
 * @property MtnMaintainerType $MtnMaintainerType
 * @property Doctrine_Collection $MtnPlanTask
 * @property Doctrine_Collection $MtnWorkOrderTask
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseMtnTask extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('mtn_task');
        $this->hasColumn('mtn_task_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('mtn_task_time', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_task_name', 'string', 50, array(
             'type' => 'string',
             'length' => 50,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_maintainer_type_id', 'integer', 4, array(
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
        $this->hasOne('MtnMaintainerType', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnPlanTask', array(
             'local' => 'mtn_task_id',
             'foreign' => 'mtn_task_id'));

        $this->hasMany('MtnWorkOrderTask', array(
             'local' => 'mtn_task_id',
             'foreign' => 'mtn_task_id'));
    }
}