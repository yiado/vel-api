<?php

/**
 * BaseInfraOtherDataValue
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $infra_other_data_value_id
 * @property integer $infra_other_data_attribute_id
 * @property integer $node_id
 * @property integer $infra_other_data_option_id
 * @property string $infra_other_data_value_value
 * @property InfraOtherDataAttribute $InfraOtherDataAttribute
 * @property InfraOtherDataOption $InfraOtherDataOption
 * @property Node $Node
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseInfraOtherDataValue extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('infra_other_data_value');
        $this->hasColumn('infra_other_data_value_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('infra_other_data_attribute_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('node_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_other_data_option_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_other_data_value_value', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasOne('InfraOtherDataAttribute', array(
             'local' => 'infra_other_data_attribute_id',
             'foreign' => 'infra_other_data_attribute_id'));

        $this->hasOne('InfraOtherDataOption', array(
             'local' => 'infra_other_data_option_id',
             'foreign' => 'infra_other_data_option_id'));

        $this->hasOne('Node', array(
             'local' => 'node_id',
             'foreign' => 'node_id'));
    }
}
