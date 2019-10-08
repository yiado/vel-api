<?php

/**
 * BaseServiceStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $service_status_id
 * @property string $service_status_name
 * @property string $service_status_commentary
 * @property Doctrine_Collection $Service
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseServiceStatus extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('service_status');
        $this->hasColumn('service_status_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('service_status_name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('service_status_commentary', 'string', 255, array(
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
        $this->hasMany('Service', array(
             'local' => 'service_status_id',
             'foreign' => 'service_status_id'));
    }
}
