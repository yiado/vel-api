<?php

/**
 * BaseModuleAction
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $module_action_id
 * @property integer $module_id
 * @property string $module_action_name
 * @property string $module_action_uri
 * @property integer $module_action_is_public
 * @property integer $language_tag_id
 * @property Module $Module
 * @property Doctrine_Collection $UserGroupAction
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseModuleAction extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('module_action');
        $this->hasColumn('module_action_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('module_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('module_action_name', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('module_action_uri', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('module_action_is_public', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('language_tag_id', 'integer', 4, array(
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
        $this->hasOne('Module', array(
             'local' => 'module_id',
             'foreign' => 'module_id'));

        $this->hasMany('UserGroupAction', array(
             'local' => 'module_action_id',
             'foreign' => 'module_action_id'));
    }
}
