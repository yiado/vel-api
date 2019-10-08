<?php

/**
 * BaseSolicitud
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $solicitud_id
 * @property integer $node_id
 * @property integer $user_id
 * @property integer $solicitud_type_id
 * @property integer $solicitud_estado_id
 * @property timestamp $solicitud_fecha
 * @property string $solicitud_folio
 * @property string $solicitud_factura_archivo
 * @property string $solicitud_factura_nombre
 * @property string $solicitud_factura_numero
 * @property string $solicitud_oc_archivo
 * @property string $solicitud_oc_nombre
 * @property string $solicitud_oc_numero
 * @property string $solicitud_comen_user
 * @property string $solicitud_comen_admin
 * @property Node $Node
 * @property User $User
 * @property SolicitudType $SolicitudType
 * @property SolicitudEstado $SolicitudEstado
 * @property Doctrine_Collection $SolicitudLog
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseSolicitud extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('solicitud');
        $this->hasColumn('solicitud_id', 'integer', 4, array(
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
        $this->hasColumn('solicitud_type_id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_estado_id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_fecha', 'timestamp', null, array(
            'type' => 'timestamp',
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_folio', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_factura_archivo', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_factura_nombre', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_factura_numero', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_oc_archivo', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_oc_nombre', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_oc_numero', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_comen_user', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('solicitud_comen_admin', 'string', 255, array(
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
        $this->hasOne('Node', array(
            'local' => 'node_id',
            'foreign' => 'node_id'));
        $this->hasOne('User', array(
            'local' => 'user_id',
            'foreign' => 'user_id'));

        $this->hasOne('SolicitudType', array(
            'local' => 'solicitud_type_id',
            'foreign' => 'solicitud_type_id'));

        $this->hasOne('SolicitudEstado', array(
            'local' => 'solicitud_estado_id',
            'foreign' => 'solicitud_estado_id'));

        $this->hasMany('SolicitudLog', array(
            'local' => 'solicitud_id',
            'foreign' => 'solicitud_id'));
    }

}
