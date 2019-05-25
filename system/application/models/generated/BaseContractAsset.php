<?php

/**
 * BaseContractAsset
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $contract_asset_id
 * @property integer $asset_id
 * @property integer $contract_id
 * @property Contract $Contract
 * @property Asset $Asset
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseContractAsset extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('contract_asset');
        $this->hasColumn('contract_asset_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('asset_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('contract_id', 'integer', 4, array(
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
        $this->hasOne('Contract', array(
             'local' => 'contract_id',
             'foreign' => 'contract_id'));

        $this->hasOne('Asset', array(
             'local' => 'asset_id',
             'foreign' => 'asset_id'));
        
        $this->hasOne('AssetType', array(
             'local' => 'asset_type_id',
             'foreign' => 'asset_type_id'));
        
         $this->hasOne('Brand', array(
             'local' => 'brand_id',
             'foreign' => 'brand_id'));
    }
}