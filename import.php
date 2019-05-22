<?php
file_put_contents("log.txt", "log start --\n");
file_put_contents("log.txt", "\$_POST:\n" . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents("log.txt", "\$_FILES:\n" . print_r($_FILES, true) . "\n", FILE_APPEND);

$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
if (isset($_FILES['settingsFile'])) {
	$info = $_FILES['settingsFile'];
}
if (!isset($info)) {
	exit(json_encode("No import file found."));
}

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

$settings = [];
$csv = array_map('str_getcsv', file($info["tmp_name"]));
file_put_contents("log.txt", "csv array:\n" . print_r($csv, true) . "\n", FILE_APPEND);

foreach ($csv[0] as $col => $fieldname) {
	if ($col == 0) continue;
	$settings[$fieldname] = [];
	foreach ($csv as $row => $array) {
		if ($row == 0) continue;
		$key = $array[0];	// like "system_value" or "value_0_0" etc
		if (isset($array[$col])) {
			// there is a value here so json decode and put it in $settings array
			
		}
	}
}