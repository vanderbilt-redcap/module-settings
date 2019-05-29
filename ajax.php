<?php
define("NOAUTH", true);
require_once("../../redcap_connect.php");
use \ExternalModules\ExternalModules;

function optionizeElements($element) {
	$p = new \Project($element);
	return "<option value='$element'>{$p->project['app_title']}</option>";
	// unset($p);
}

// value can be a string or array value
// path is the
function recurseSetting($arg, $path) {
	global $fields;
	global $paths;
	global $column;
	
	if (gettype($arg) == 'array') {
		foreach($arg as $key => $val) {
			$newpath = $path;
			$newpath[] = $key;
			recurseSetting($val, $newpath);
		}
	} else {
		$masterkey = implode($path, "_");
		$row = array_search($masterkey, $paths, true);
		if ($row === FALSE) {
			$paths[] = $masterkey;
			$fields[] = create_array(count($fields[0]) - 1);
			$row = count(array_keys($paths));
			$fields[$row][$column] = $arg;
		}
	}
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
	// copypasta from framework->getProjectsWithModuleEnabled so even non-frameworked external modules can get import/export support
	$results = $module->query("
		select project_id
		from redcap_external_modules m
		join redcap_external_module_settings s
			on m.external_module_id = s.external_module_id
		where
			m.directory_prefix = '$prefix'
			and s.value = 'true'
			and s.`key` = 'enabled'
	");

	$pids = [];
	while($row = $results->fetch_assoc()) {
		$pids[] = $row['project_id'];
	}
	
	if (!empty($pids)) {
		$pids = array_map('optionizeElements', $pids);
		array_unshift($pids, "<option value=''></option>");
		exit(implode($pids, "\n"));
	} else {
		exit("<option value=''>(selected module not enabled on any projects)</option>");
	}
} elseif ($action == "export") {
	$prefix = $_POST['prefix'];
	$scope = $_POST['scope'];
	$pid = $_POST['pid'];
	
	// file_put_contents("log.txt", "prefix: $prefix\nscope: $scope\npid: $pid\n");
	
	$module = ExternalModules::getModuleInstance($prefix, $version);
	$settings = $module->framework->getProjectSettings($pid);
	// file_put_contents("log.txt", "\ngetProjectSettings\n" . print_r($settings, true), FILE_APPEND);
	
	$fields = [array_keys($settings)];	// $fields is the matrix starting at cell (1, 0) and goes to (n, n)
	// file_put_contents("log.txt", "\nfields\n" . print_r($fields, true), FILE_APPEND);
	
	$paths = [""];	// will become the first column of the csv contains cells (0, 0) to (n, 0)
	
	foreach($fields as $column => $field) {
		if ($scope != "project" and isset($field["system_value"])) {
			recurseArray($field["system_value"], ["system_value"]);
		}
		if ($scope != "system" and isset($field["value"])) {
			recurseArray($field["value"], ["value"]);
		}
	}
	
	/*
	foreach($fields as $column => $field) {
		if ($settings[$field]['system_value'] != null) {
			if (!isset($arrs['system_value'])) $arrs['system_value']
		}
		if value is array then
			$path = 'value'
			recurse value array
				for each $index, $item in array
					$key = $path . "_$index"
					if $arrs[$key] then add item to that array in correct column
					else create $arrs[$key] and add item in correct column
		else
			write value
		end
	}
	*/
	
	// combine paths column with the rest of the cells ($fields matrix)
	foreach($fields as $i => $arr) {
		array_unshift($arr, $paths[$i]);
	}
	
	$filename = "testCSV.csv";
	header("Content-Type:application/csv"); 
	header("Content-Disposition:attachment;filename=$filename"); 
	$output = fopen("php://output",'w');
	foreach($fields as $arr) {
		fputcsv($output, $arr);
	}
	fclose($output);
}