<?php
// system/application/plugins/doctrine_pi.php

// load Doctrine library
require_once APPPATH.'/plugins/doctrine/lib/Doctrine.php';

// load database configuration from CodeIgniter
require_once APPPATH.'/config/database.php';

// this will allow Doctrine to load Model classes automatically
spl_autoload_register(array('Doctrine', 'autoload'));

// we load our database connections into Doctrine_Manager
// this loop allows us to use multiple connections later on
	

// first we must convert to dsn format
$dsn = $db['default']['dbdriver'] .
	'://' . $db['default']['username'] .
	':' . $db['default']['password'].
	'@' . $db['default']['hostname'] .
	'/' . $db['default']['database'];

$conn = Doctrine_Manager::connection($dsn, 'default');
$conn->setCharset($db['default']['char_set']);
$conn->setCollate($db['default']['dbcollat']);
$conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);

// CodeIgniter's Model class needs to be loaded
require_once BASEPATH.'/libraries/Model.php';


// telling Doctrine where our models are located
Doctrine::loadModels(APPPATH.'models/listener');
Doctrine::loadModels(APPPATH.'models/generated');
Doctrine::loadModels(APPPATH.'models');


$conn->addRecordListener(new HydrationDateFormatListener());


// (OPTIONAL) CONFIGURATION BELOW

Doctrine_Manager::getInstance()->setAttribute(
	Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

// this will allow us to use "mutators"
Doctrine_Manager::getInstance()->setAttribute(
	Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

// this sets all table columns to notnull and unsigned (for ints) by default
Doctrine_Manager::getInstance()->setAttribute(
	Doctrine::ATTR_DEFAULT_COLUMN_OPTIONS,
	array('notnull' => true, 'unsigned' => true));

// set the default primary key to be named 'id', integer, 4 bytes
Doctrine_Manager::getInstance()->setAttribute(
	Doctrine::ATTR_DEFAULT_IDENTIFIER_OPTIONS,
	array('name' => 'id', 'type' => 'integer', 'length' => 4));
	