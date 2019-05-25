<?php

/**
 * BaseMtnMaintainerType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $mtn_maintainer_type_id
 * @property string $mtn_maintainer_type_name
 * @property Doctrine_Collection $Contract
 * @property Doctrine_Collection $MtnComponent
 * @property Doctrine_Collection $MtnComponentType
 * @property Doctrine_Collection $MtnOtherCosts
 * @property Doctrine_Collection $MtnPlan
 * @property Doctrine_Collection $MtnPriceList
 * @property Doctrine_Collection $MtnSystemWorkOrderStatus
 * @property Doctrine_Collection $MtnTask
 * @property Doctrine_Collection $MtnWorkOrder
 * @property Doctrine_Collection $MtnWorkOrderType
 * @property Doctrine_Collection $Provider
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseMtnMaintainerType extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('mtn_maintainer_type');
        $this->hasColumn('mtn_maintainer_type_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('mtn_maintainer_type_name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasMany('Contract', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnComponent', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnComponentType', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnOtherCosts', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnPlan', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnPriceList', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnSystemWorkOrderStatus', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnTask', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnWorkOrder', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnWorkOrderType', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('Provider', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));
    }
}