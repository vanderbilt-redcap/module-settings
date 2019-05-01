<?php
require_once("../../redcap_connect.php");
use \ExternalModules\ExternalModules;

$prefix = $_GET['prefix'];
if(empty($prefix)){
	throw new Exception("Please supply a 'prefix' parameter for this page.");
}
$version = ExternalModules::getSystemSetting($prefix, ExternalModules::KEY_VERSION);
if(empty($version)){
	throw new Exception("The module with prefix '$prefix' is currently disabled systemwide.");
}

$module = ExternalModules::getModuleInstance($prefix, $version);

// $pids = $module->framework->getProjectsWithModuleEnabled($pid);
$pid = 25;

function z($array, $path="") {
	global $arrs;
	
	foreach($array as $key => $val) {
		$path = $key
	}
}

function export() {
	global $module;
	global $pid;
	$settings = $module->framework->getProjectSettings($pid);
	$arrs = [];
	$fields = array_keys($settings);
	array_unshift($fields, "keys");
	$arrs[] = $fields;
	
	z($settings
	
	// $filename = "testCSV.json";
	// header("Content-Type:application/json"); 
	// header("Content-Disposition:attachment;filename=$filename"); 
	$output = fopen("php://output",'w');
	foreach($arrs as $arr) {
		fputcsv($output, $arr);
	}
	fclose($output);
}

// echo("<pre>");
// echo("</pre>");

export();