<?php

/**
 * BaseMtnComponent
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $mtn_component_id
 * @property integer $mtn_component_type_id
 * @property string $mtn_component_name
 * @property string $mtn_component_weight
 * @property integer $brand_id
 * @property string $mtn_component_model
 * @property string $mtn_component_manufacturer
 * @property string $mtn_component_comment
 * @property integer $mtn_maintainer_type_id
 * @property integer $measure_unit_id
 * @property MeasureUnit $MeasureUnit
 * @property MtnComponentType $MtnComponentType
 * @property Brand $Brand
 * @property MtnMaintainerType $MtnMaintainerType
 * @property Doctrine_Collection $MtnPriceListComponent
 * @property Doctrine_Collection $MtnWorkOrderTaskComponent
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseMtnComponent extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('mtn_component');
        $this->hasColumn('mtn_component_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('mtn_component_type_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_component_name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_component_weight', 'string', 20, array(
             'type' => 'string',
             'length' => 20,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('brand_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_component_model', 'string', 20, array(
             'type' => 'string',
             'length' => 20,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_component_manufacturer', 'string', 20, array(
             'type' => 'string',
             'length' => 20,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mtn_component_comment', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
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
        $this->hasColumn('measure_unit_id', 'integer', 4, array(
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
        $this->hasOne('MeasureUnit', array(
             'local' => 'measure_unit_id',
             'foreign' => 'measure_unit_id'));

        $this->hasOne('MtnComponentType', array(
             'local' => 'mtn_component_type_id',
             'foreign' => 'mtn_component_type_id'));

        $this->hasOne('Brand', array(
             'local' => 'brand_id',
             'foreign' => 'brand_id'));

        $this->hasOne('MtnMaintainerType', array(
             'local' => 'mtn_maintainer_type_id',
             'foreign' => 'mtn_maintainer_type_id'));

        $this->hasMany('MtnPriceListComponent', array(
             'local' => 'mtn_component_id',
             'foreign' => 'mtn_component_id'));

        $this->hasMany('MtnWorkOrderTaskComponent', array(
             'local' => 'mtn_component_id',
             'foreign' => 'mtn_component_id'));
    }
}
