<?php

/**
 * BaseAssetInventory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $asset_inventory_id
 * @property integer $node_id
 * @property integer $asset_id
 * @property integer $user_id
 * @property timestamp $asset_inventory_datetime
 * @property User $User
 * @property Asset $Asset
 * @property Node $Node
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseAssetInventory extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('asset_inventory');
        $this->hasColumn('asset_inventory_id', 'integer', 4, array(
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
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('asset_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('asset_inventory_datetime', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }
    public function setUp() {
        parent::setUp();
        $this->hasOne('User', array(
             'local' => 'user_id',
             'foreign' => 'user_id'));

        $this->hasOne('Asset', array(
             'local' => 'asset_id',
             'foreign' => 'asset_id'));

        $this->hasOne('Node', array(
             'local' => 'node_id',
             'foreign' => 'node_id'));
    }
}
