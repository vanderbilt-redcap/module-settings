<?php
if (isset($_POST['settingsFile'])) {
	$import = $_POST['settingsFile'];
}

if (!isset($import)) {
	exit(json_encode("No import file found."));
}

exit(json_encode("File found!"));