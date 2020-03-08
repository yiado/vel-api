<?php

/**
 * BaseService
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $service_id
 * @property integer $node_id
 * @property integer $user_id
 * @property integer $service_type_id
 * @property integer $service_status_id
 * @property timestamp $service_date
 * @property string $service_organism
 * @property string $service_phone
 * @property string $service_commentary
 * @property Node $Node
 * @property User $User
 * @property ServiceType $ServiceType
 * @property ServiceStatus $ServiceStatus
 * @property RequestEvaluation $RequestEvaluation
 * @property Doctrine_Collection $ServiceLog
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseService extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('service');
        $this->hasColumn('service_id', 'integer', 4, array(
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
        $this->hasColumn('user_id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('service_type_id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('service_status_id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('service_date', 'timestamp', null, array(
            'type' => 'timestamp',
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('service_organism', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('service_phone', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('service_commentary', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('service_reject', 'string', 2000, array(
            'type' => 'string',
            'length' => 2000,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('request_evaluation_id', 'integer', 4, array(
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
        $this->hasOne('Node', array(
            'local' => 'node_id',
            'foreign' => 'node_id'));
        
        $this->hasOne('User', array(
            'local' => 'user_id',
            'foreign' => 'user_id'));

        $this->hasOne('ServiceType', array(
            'local' => 'service_type_id',
            'foreign' => 'service_type_id'));

        $this->hasOne('ServiceStatus', array(
            'local' => 'service_status_id',
            'foreign' => 'service_status_id'));
        
        $this->hasOne('RequestEvaluation', array(
            'local' => 'request_evaluation_id',
            'foreign' => 'request_evaluation_id'));

        $this->hasMany('ServiceLog', array(
            'local' => 'service_id',
            'foreign' => 'service_id'));
    }

}
