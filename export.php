<?php
define("NOAUTH", true);
require_once("../../redcap_connect.php");
use \ExternalModules\ExternalModules;

// value can be a string or array value
// path is an array where each element points to an element leading up to $arg in its parent array
function recurseSetting($arg, $path) {
	global $fields;
	global $field;
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
			$fields[] = array_fill(0, count($fields[0]), null);
			$row = count(array_keys($paths)) - 1;
		}
		// $fields[$row][$column] = json_encode($arg);
		$fields[$row][$column] = json_encode($arg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
		// file_put_contents("log.txt", "recursing $field\_" . implode($path, "_") . " -- writing value (type: " . gettype($arg) . ") to $row, $column : $arg\n", FILE_APPEND);
	}
}

// sanitize GET
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$prefix = $_GET['prefix'];
$scope = $_GET['scope'];
$pid = $_GET['pid'];

// file_put_contents("log.txt", "prefix: $prefix\nscope: $scope\npid: $pid\n");

$module = ExternalModules::getModuleInstance($prefix, $version);
$settings = $module->framework->getProjectSettings($pid);
// file_put_contents("log.txt", "\ngetProjectSettings\n" . print_r($settings, true), FILE_APPEND);

$fields = [array_keys($settings)];	// $fields is the matrix starting at cell (1, 0) and goes to (n, n)

$paths = [""];	// will become the first column of the csv contains cells (0, 0) to (n, 0)

foreach($fields[0] as $column => $field) {
	if ($scope != "project" and isset($settings[$field]["system_value"])) {
		recurseSetting($settings[$field]["system_value"], ["system_value"]);
	}
	if ($scope != "system" and isset($settings[$field]["value"])) {
		recurseSetting($settings[$field]["value"], ["value"]);
	}
}

// combine paths column with the rest of the cells ($fields matrix)
foreach($fields as $i => &$arr) {
	array_unshift($arr, $paths[$i]);
}

$filename = implode([$prefix, $scope, $pid, 'settings', 'export'], "_") . ".csv";
header("Content-Type: application/csv"); 
header("Content-Disposition: attachment; filename=$filename"); 
$output = fopen("php://output",'w');
foreach($fields as $row) {
	fputcsv($output, $row);
}
fclose($output);