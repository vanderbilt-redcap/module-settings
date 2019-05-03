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
		<div id="select">
			<h2>Select a Module</h2>
			<div>
				<p>Modules enabled on this REDCap instance:</p>
				<select id="moduleSelect">
					<?php
						define("NOAUTH", true);
						require_once("../../redcap_connect.php");
						use \ExternalModules\ExternalModules;
						
						$modules = ExternalModules::getEnabledModules();
						
						// add blank select option
						echo("<option value=''></option>");
						
						// add an option for each module
						foreach ($modules as $prefix => $version) {
							$config = ExternalModules::getConfig($prefix, $version, NULL);
							if (empty($config)) continue;
							echo("
					<option value='$prefix'>{$config['name']}</option>");
						}
					?>
				</select>
			</div>
			<div>
				<p>Include project settings, system settings, or both?</p>
				<select id="scopeSelect">
					<option value="project">Project-level only</option>
					<option value="system">System-level only</option>
					<option value="both">All module settings</option>
				</select>
			</div>
			<div id="projects" class="hidden">
				<p>Select a project that this module is enabled on:</p>
				<select id = "projectSelect"></select>
			</div>
		</div>
		<div id="actions" class="row hidden">
			<div id="export">
				<h2>Export</h2>
				<button type="button" onclick="exportCSV">Download .CSV</button>
			</div>
			<div id="import">
				<h2>Import</h2>
				<p>Choose a settings file (.csv)</p>
				<input type="file" name="settingsFile" id="settingsFile">
				<button type="button" onclick="import">Import .CSV</button>
			</div>
		</div>
		<script
			src="https://code.jquery.com/jquery-3.4.1.min.js"
			integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			crossorigin="anonymous"></script>
		<script type="text/javascript" src="js/base.js"></script>
	</body>
</html>
