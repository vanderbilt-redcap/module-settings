<?php
file_put_contents("log.txt", "log start --\n");
// file_put_contents("log.txt", "\$_POST:\n" . print_r($_POST, true) . "\n", FILE_APPEND);
// file_put_contents("log.txt", "\$_FILES:\n" . print_r($_FILES, true) . "\n", FILE_APPEND);

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
if (isset($_FILES['settingsFile'])) {
	$info = $_FILES['settingsFile'];
}
if (empty($info)) exit(json_encode([
	"error" => "No import file found."
]));

// make sure prefix, scope, pid are provided as necessary
if (empty($_POST['scope']) or empty($_POST['prefix'])) exit(json_encode([
	"error" => "Scope or module prefix not defined."
]));
if ($_POST['scope'] != "system" and empty($_POST['pid'])) exit(json_encode([
	"error" => "pid parameter not supplied in POST. pid required when scope is 'both' or 'project'."
]));

// idea is to turn csv array into something like:
/*
$settings = [
	[field_name] => Array
		(
			[system_value] => whatever,
			[value] => Array
				(
					[0] => another_value,
					[1] => etc
				)

		)
]
*/

// and then import via $module->setProjectSettings($settings, $pid);
$prefix = $_POST['prefix'];
$scope = $_POST['scope'];
$pid = $_POST['pid'];
define("NOAUTH", true);
require_once("../../redcap_connect.php");
use \ExternalModules\ExternalModules;
$module = ExternalModules::getModuleInstance($prefix);

$settings = [];
$csv = array_map('str_getcsv', file($info["tmp_name"]));
// file_put_contents("log.txt", "csv array:\n" . print_r($csv, true) . "\n", FILE_APPEND);

foreach ($csv[0] as $col => $fieldname) {
	if ($col == 0) continue;
	$settings[$fieldname] = [];
	foreach ($csv as $row => $array) {
		if ($row == 0) continue;
		if (!empty($array[$col])) {
			// there is a value here so json decode and put it in $settings array
			
			// build $settings if necessary
			$key = $array[0];	// like "system_value" or "value_0_0" etc
			$keylist = explode("_", $key);
			if ($keylist[0] == 'system' and $keylist[1] == 'value') {
				array_shift($keylist);
				$keylist[0] = 'system_value';
			}
			
			$settingsChain = &$settings[$fieldname];
			foreach ($keylist as $i => $key) {
				if ($i == count($keylist) - 1) {
					$settingsChain[$key] = json_decode($array[$col]);	// last key holds not an array but a value
				} else {
					if (!isset($settingsChain[$key])) $settingsChain[$key] = [];
				}
				$settingsChain = &$settingsChain[$key];
			}
		}
	}
}

// file_put_contents("log.txt", "\nget class methods\n" . print_r(get_class_methods($module), true), FILE_APPEND);

// $module->framework->setProjectSettings($settings, $pid);
$newSettings = $module->framework->getProjectSettings($pid);

file_put_contents("log.txt", "\$settings level2b system_value 1 0: {$newSettings['level2b']['system_value'][1][0]}\n", FILE_APPEND);
file_put_contents("log.txt", "\$settings subtest value 1 0: {$newSettings['subtest']['value'][1][0]}\n", FILE_APPEND);
file_put_contents("log.txt", "\$settings organizationid value: {$newSettings['organizationid']['value']}\n", FILE_APPEND);

// $module = ExternalModules::getModuleInstance($prefix);
// $module->framework->getProjectSettings($pid);
file_put_contents("log.txt", "\ngetProjectSettings\n" . print_r($newSettings, true), FILE_APPEND);
file_put_contents("log.txt", "\n\nimport finished", FILE_APPEND);

exit(json_encode([
	"message" => "Success! Settings imported to $prefix."
]));
