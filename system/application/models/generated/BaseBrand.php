<?php

/**
 * BaseBrand
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $brand_id
 * @property string $brand_name
 * @property Doctrine_Collection $Asset
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseBrand extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('brand');
        $this->hasColumn('brand_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('brand_name', 'string', 50, array(
             'type' => 'string',
             'length' => 50,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasMany('Asset', array(
             'local' => 'brand_id',
             'foreign' => 'brand_id'));
    }
}
