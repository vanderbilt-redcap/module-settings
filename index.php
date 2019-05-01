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

function sendCSV() {
	global $module;
	global $pid;
	$settings = $module->framework->getProjectSettings($pid);
	
	// echo("<pre>");
	// print_r($settings);
	// echo("</pre>");
	
	$arrs = [];
	$fields = array_unshift(array_keys($settings), "keys");
	$arrs[] = $fields;
	
	// recurse through settings to 
	
	// $output = fopen("php://output",'w');
	// $filename = "testCSV.json";
	// header("Content-Type:application/json"); 
	// header("Content-Disposition:attachment;filename=$filename"); 
	// fputcsv($output,
	// fclose($output);
}

// echo("<pre>");
// echo("</pre>");

sendCSV();