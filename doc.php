<?php

ini_set("display_errors","1");

require_once('system/application/plugins/doctrine/lib/Doctrine.php');

spl_autoload_register(array('Doctrine','autoload'));

$dsn = 'mysql:dbname=igeo_u_2016;host=localhost';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

Doctrine_Manager::connection($dbh);

// import method takes one parameter : the import directory ( the directory where
// the generated record files will be put in
Doctrine::generateModelsFromDb ('test', array('doctrine'), array('generateTableClasses' => true));