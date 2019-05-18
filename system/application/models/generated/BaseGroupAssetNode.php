<?php

abstract class BaseGroupAssetNode extends Doctrine_Record {

    public function setTableDefinition() {

        $this->setTableName('group_asset_node');
        $this->hasColumn('id_group_asset_node', 'integer', 11, array(
            'type' => 'integer',
            'length' => 11,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('user_group_id', 'integer', 11, array(
            'type' => 'integer',
            'length' => 11,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('node_id', 'integer', 11, array(
            'type' => 'integer',
            'length' => 11,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('module_id', 'integer', 11, array(
            'type' => 'integer',
            'length' => 11,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
    }

    public function setUp() {
        parent::setUp();
        $this->hasOne('Node', array(
            'local' => 'node_id',
            'foreign' => 'node_id'));

        $this->hasOne('UserGroup', array(
            'local' => 'user_group_id',
            'foreign' => 'user_group_id'));
        
        $this->hasOne('Module', array(
            'local' => 'module_id',
            'foreign' => 'module_id'));
    }

}
