<?php

/**
 * BaseMeasureUnit
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $measure_unit_id
 * @property string $measure_unit_name
 * @property string $measure_unit_description
 * @property Doctrine_Collection $AssetMeasurement
 * @property Doctrine_Collection $AssetTriggerMeasurementConfig
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseMeasureUnit extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('measure_unit');
        $this->hasColumn('measure_unit_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('measure_unit_name', 'string', 30, array(
             'type' => 'string',
             'length' => 30,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('measure_unit_description', 'string', 255, array(
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
        $this->hasMany('AssetMeasurement', array(
             'local' => 'measure_unit_id',
             'foreign' => 'measure_unit_id'));

        $this->hasMany('AssetTriggerMeasurementConfig', array(
             'local' => 'measure_unit_id',
             'foreign' => 'measure_unit_id'));
    }
}
