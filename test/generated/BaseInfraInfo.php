<?php

/**
 * BaseInfraInfo
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $infra_info_id
 * @property integer $node_id
 * @property integer $infra_info_option_id_1
 * @property integer $infra_info_option_id_2
 * @property integer $infra_info_option_id_3
 * @property integer $infra_info_option_id_4
 * @property float $infra_info_usable_area
 * @property float $infra_info_usable_area_total
 * @property float $infra_info_area
 * @property float $infra_info_area_total
 * @property float $infra_info_volume
 * @property float $infra_info_volume_total
 * @property float $infra_info_length
 * @property float $infra_info_width
 * @property float $infra_info_height
 * @property integer $infra_info_capacity
 * @property integer $infra_info_capacity_total
 * @property float $infra_info_terrain_area
 * @property float $infra_info_terrain_area_total
 * @property string $infra_info_additional_1
 * @property string $infra_info_additional_2
 * @property string $infra_info_additional_3
 * @property string $infra_info_additional_4
 * @property InfraInfoOption $InfraInfoOption
 * @property InfraInfoOption $InfraInfoOption_2
 * @property InfraInfoOption $InfraInfoOption_3
 * @property InfraInfoOption $InfraInfoOption_4
 * @property Node $Node
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseInfraInfo extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('infra_info');
        $this->hasColumn('infra_info_id', 'integer', 4, array(
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
        $this->hasColumn('infra_info_option_id_1', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_option_id_2', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_option_id_3', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_option_id_4', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_usable_area', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_usable_area_total', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_area', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_area_total', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_volume', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_volume_total', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_length', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_width', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_height', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_capacity', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_capacity_total', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_terrain_area', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_terrain_area_total', 'float', 11, array(
             'type' => 'float',
             'length' => 11,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0.000',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_additional_1', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_additional_2', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_additional_3', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_info_additional_4', 'string', 255, array(
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
        $this->hasOne('InfraInfoOption', array(
             'local' => 'infra_info_option_id_1',
             'foreign' => 'infra_info_option_id'));

        $this->hasOne('InfraInfoOption as InfraInfoOption_2', array(
             'local' => 'infra_info_option_id_2',
             'foreign' => 'infra_info_option_id'));

        $this->hasOne('InfraInfoOption as InfraInfoOption_3', array(
             'local' => 'infra_info_option_id_3',
             'foreign' => 'infra_info_option_id'));

        $this->hasOne('InfraInfoOption as InfraInfoOption_4', array(
             'local' => 'infra_info_option_id_4',
             'foreign' => 'infra_info_option_id'));

        $this->hasOne('Node', array(
             'local' => 'node_id',
             'foreign' => 'node_id'));
    }
}
