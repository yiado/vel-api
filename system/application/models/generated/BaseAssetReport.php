<?php

abstract class BaseAssetReport extends Doctrine_Record {

    public function setTableDefinition() {
        $this->setTableName('asset_report');
        $this->hasColumn('node_id', 'integer', 11, array(
            'type' => 'integer',
            'length' => 11,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('asset_name', 'string', 30, array(
            'type' => 'string',
            'length' => 30,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('asset_estate', 'integer', 11, array(
            'type' => 'integer',
            'length' => 11,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'autoincrement' => false,
        ));
//
        $this->hasColumn('asset_num_serie_intern', 'string', 50, array(
            'type' => 'string',
            'length' => 50,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
//
        $this->hasColumn('asset_load_date', 'date', null, array(
            'type' => 'date',
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
//
        $this->hasColumn('asset_num_factura', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('asset_type_name', 'string', 50, array(
            'type' => 'string',
            'length' => 50,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('asset_load_folio', 'string', 11, array(
            'type' => 'string',
            'length' => 11,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('infra_other_data_value_value', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'autoincrement' => false,
        ));
//
        $this->hasColumn('asset_other_data_value_value', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));

        $this->hasColumn('location', 'string');


    }


}
