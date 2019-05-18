<?php

/**
 * BaseMtnOtherCosts
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $mtn_other_costs_id
 * @property string $mtn_other_costs_name
 * @property Doctrine_Collection $MtnWorkOrderOtherCosts
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseMtnOtherCosts extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('mtn_other_costs');
        $this->hasColumn('mtn_other_costs_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('mtn_other_costs_name', 'string', 30, array(
             'type' => 'string',
             'length' => 30,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasMany('MtnWorkOrderOtherCosts', array(
             'local' => 'mtn_other_costs_id',
             'foreign' => 'mtn_other_costs_id'));
    }
}
