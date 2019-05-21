<?php
define("NOAUTH", true);
require_once("../../redcap_connect.php");
use \ExternalModules\ExternalModules;

// value can be a string or array value
// path is an array where each element points to an element leading up to $arg in its parent array
function recurseSetting($arg, $path) {
	global $fields;
	global $paths;
	global $column;
	
	file_put_contents("log.txt", "entered recurseSetting with path: " . implode($path, "_") . "\n", FILE_APPEND);
	
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
			// $fields[] = array_fill(null, count($fields[0]) - 1);
			$row = count(array_keys($paths));
			$fields[$row][$column] = $arg;
		}
	}
}

// sanitize GET
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$prefix = $_GET['prefix'];
$scope = $_GET['scope'];
$pid = $_GET['pid'];

file_put_contents("log.txt", "prefix: $prefix\nscope: $scope\npid: $pid\n");

$module = ExternalModules::getModuleInstance($prefix, $version);
$settings = $module->framework->getProjectSettings($pid);
// file_put_contents("log.txt", "\ngetProjectSettings\n" . print_r($settings, true), FILE_APPEND);

$fields = [array_keys($settings)];	// $fields is the matrix starting at cell (1, 0) and goes to (n, n)
// file_put_contents("log.txt", "\nfields\n" . print_r($fields, true), FILE_APPEND);

$paths = [""];	// will become the first column of the csv contains cells (0, 0) to (n, 0)

foreach($fields[0] as $column => $field) {
	file_put_contents("log.txt", "addressing field: $field\n", FILE_APPEND);
	if ($scope != "project" and isset($settings[$field]["system_value"])) {
		file_put_contents("log.txt", "about to recurse system level values\n", FILE_APPEND);
		recurseSetting($settings[$field]["system_value"], ["system_value"]);
	}
	if ($scope != "system" and isset($settings[$field]["value"])) {
		file_put_contents("log.txt", "about to recurse project level values\n", FILE_APPEND);
		recurseSetting($settings[$field]["value"], ["value"]);
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
header("Content-Type: application/csv"); 
header("Content-Disposition: attachment; filename=$filename"); 
$output = fopen("php://output",'w');
foreach($fields as $arr) {
	fputcsv($output, $arr);
}
fclose($output);
