<?php

/**
 * BaseInfraOtherDataAttributeNodeType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $infra_other_data_attribute_node_type_id
 * @property integer $infra_other_data_attribute_id
 * @property integer $node_type_id
 * @property integer $infra_other_data_attribute_node_type_order
 * @property integer $infra_other_data_attribute_node_type_the_sumary
 * @property InfraOtherDataAttribute $InfraOtherDataAttribute
 * @property NodeType $NodeType
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseInfraOtherDataAttributeNodeType extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('infra_other_data_attribute_node_type');
        $this->hasColumn('infra_other_data_attribute_node_type_id', 'integer', 4, array(
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
        $this->hasColumn('node_type_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_other_data_attribute_node_type_order', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('infra_other_data_attribute_node_type_the_sumary', 'integer', 4, array(
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
        $this->hasOne('InfraOtherDataAttribute', array(
             'local' => 'infra_other_data_attribute_id',
             'foreign' => 'infra_other_data_attribute_id'));

        $this->hasOne('NodeType', array(
             'local' => 'node_type_id',
             'foreign' => 'node_type_id'));
    }
}
