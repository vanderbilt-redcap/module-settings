<?php
require_once("../../redcap_connect.php");
?>
<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<link rel="stylesheet" href="css/base.css">
		<title>Module Settings Import/Export</title>
	</head>
	<body>
		<div id="select" class="card m-3">
			<h2>Select a Module</h2>
			<div class="card-body">
				<p>Modules enabled on this REDCap instance:</p>
				<select id="moduleSelect">
					<?php
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
		<div id="actions" class="row card hidden m-3">
			<div id="export" class="card-body">
				<h2>Export</h2>
				<p>Generate a settings file (.csv)</p>
				<button class="btn btn-primary" onclick="exportCSV()" type="submit">Download .CSV</button>
			</div>
			<div id="import" class="card-body">
				<h2>Import</h2>
				<p>Choose a settings file (.csv)</p>
				<input type="file" class="btn-primary" name="settingsFile" id="settingsFile">
				<button type="button" class="btn btn-primary" onclick="importSettings()">Import .CSV</button>
			</div>
		</div>
		<div id="results" class="card hidden m-3">
			<div class="card-body">
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		<script
			src="https://code.jquery.com/jquery-3.4.1.min.js"
			integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			crossorigin="anonymous"></script>
		<script type="text/javascript" src="js/base.js"></script>
	</body>
</html>
