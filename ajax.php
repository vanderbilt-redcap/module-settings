<?php
define("NOAUTH", true);
require_once("../../redcap_connect.php");
use \ExternalModules\ExternalModules;

function optionizeElements($element) {
	$p = new \Project($element);
	return "<option value='$element'>{$p->project['app_title']}</option>";
	// unset($p);
}

function z($array, $path="") {
	global $arrs;
	
	foreach($array as $key => $val) {
		$path = $key;
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
	
	z($settings);
	
	$filename = "testCSV.csv";
	header("Content-Type:application/csv"); 
	header("Content-Disposition:attachment;filename=$filename"); 
	$output = fopen("php://output",'w');
	foreach($arrs as $arr) {
		fputcsv($output, $arr);
	}
	fclose($output);
}

// sanitize POST
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$action = $_POST['action'];

if ($action == "getProjects") {
	// given module prefix, respond with list of <option>s = projects this module is enabled on
	$prefix = $_POST['module'];
	$version = ExternalModules::getSystemSetting($prefix, ExternalModules::KEY_VERSION);
	if (empty($version)) {
		exit("<option value=''>(selected module disabled on this system)</option>");
	}
	$module = ExternalModules::getModuleInstance($prefix, $version);
	if (isset($module->framework)) {
		$pids = $module->framework->getProjectsWithModuleEnabled();
		$pids = array_map('optionizeElements', $pids);
		exit(implode($pids, "\n"));
	} else {
		exit("<option value=''>(selected module not enabled on any projects)</option>");
	}
} elseif ($action == "export") {
	$module = $_POST['module'];
	$project = $_POST['project'];
}