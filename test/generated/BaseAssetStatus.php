<?php

/**
 * BaseAssetStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $asset_status_id
 * @property string $asset_status_name
 * @property string $asset_status_description
 * @property Doctrine_Collection $Asset
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseAssetStatus extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('asset_status');
        $this->hasColumn('asset_status_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('asset_status_name', 'string', 50, array(
             'type' => 'string',
             'length' => 50,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('asset_status_description', 'string', 255, array(
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
        $this->hasMany('Asset', array(
             'local' => 'asset_status_id',
             'foreign' => 'asset_status_id'));
    }
}