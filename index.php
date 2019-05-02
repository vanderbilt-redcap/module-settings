<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<link rel="stylesheet" href="css/base.css">
		<title>Module Settings Import/Export</title>
	</head>
	<body>
		<div id="export">
			<h2>Export</h2>
			<form>
				<p>Select a module:</p>
				<select id="moduleSelect" name="modulePrefix">
					<?php
						define("NOAUTH", true);
						require_once("../../redcap_connect.php");
						use \ExternalModules\ExternalModules;
						
						$modules = ExternalModules::getEnabledModules();
						
						// add blank select option
						echo("
					<option value=''></option>");
						
						// add an option for each module
						foreach ($modules as $prefix => $version) {
							$config = ExternalModules::getConfig($prefix, $version, NULL);
							if (empty($config)) continue;
							echo("
					<option value='$prefix'>{$config['name']}</option>");
						}
					?>
				</select>
				<div class="hidden">
					<p>Select a project:</p>
					<select id="projectSelect" name="project"></select>
					<button type="button" onclick="exportCSV">Export to .CSV</button>
				</div>
			</form>
		</div>
		<div id="import">
			<h2>Import</h2>
			<form>
				<p>Choose a settings file (.csv):</p>
				<input type="file" name="settingsFile" id="settingsFile">
				<button type="button" onclick="import">Import .CSV</button>
			</form>
		</div>
		<script
			src="https://code.jquery.com/jquery-3.4.1.min.js"
			integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			crossorigin="anonymous"></script>
		<script type="text/javascript" src="js/base.js"></script>
	</body>
</html>
