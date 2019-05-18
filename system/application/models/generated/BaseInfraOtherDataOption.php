<?php

/**
 * BaseInfraOtherDataOption
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $infra_other_data_option_id
 * @property integer $infra_other_data_attribute_id
 * @property string $infra_other_data_option_name
 * @property InfraOtherDataAttribute $InfraOtherDataAttribute
 * @property Doctrine_Collection $InfraOtherDataValue
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseInfraOtherDataOption extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('infra_other_data_option');
        $this->hasColumn('infra_other_data_option_id', 'integer', 4, array(
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
        $this->hasColumn('infra_other_data_option_name', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
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

        $this->hasMany('InfraOtherDataValue', array(
             'local' => 'infra_other_data_option_id',
             'foreign' => 'infra_other_data_option_id'));
    }
}
