<?php

$anio = date('Y');
$mes = date('m');

$api_key = "f5be4d9b0a11b3941c2083ba4e6321fc789eba3e";
$api_formato = "json";
$api_url = "http://api.sbif.cl/api-sbifv3/recursos_api/uf/posteriores/".$anio."/".$mes."?apikey=".$api_key."&formato=".$api_formato;

$json = file_get_contents($api_url);
$data = json_decode($json,true);

foreach ($data['UFs'] as $uf){
    
    
}