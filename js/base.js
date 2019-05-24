function getProjectOptions(modulePrefix) {
	$.ajax({
		url: "ajax.php",
		data: {
			action: "getProjects",
			module: modulePrefix
		},
		dataType: "html",
		method: "POST",
		success : function(data) {
			$("#projects select").html(data);
		},
		fail : function(data) {
			// $("#error").html("<pre>There was an error:\n" + data + "</pre>")
		}
	})
}

function importSettings() {
	let prefix = $("#moduleSelect").children("option:selected").val();
	let scope = $("#scopeSelect").children("option:selected").val();
	let pid = $("#projectSelect").children("option:selected").val();
	let myFormData = new FormData();
	myFormData.append('settingsFile', settingsFile.files[0]);
	myFormData.append("prefix", prefix);
	myFormData.append("scope", scope);
	myFormData.append("pid", pid);
	
	console.log('firing import ajax...');
	
	$.ajax({
		url: 'import.php',
		type: 'POST',
		processData: false,
		contentType: false,
		dataType : 'json',
		data: myFormData,
		error: function(req, status, err) {
			console.log('ajax error');
			console.log(req);
			console.log(status);
			console.log(err);
		},
		success : function(data) {
			console.log('ajax successful');
			console.log(data);
		},
		fail : function(data) {
			console.log('ajax failed');
			console.log(data);
		}
	})
}

function exportCSV() {
	let prefix = $("#moduleSelect").children("option:selected").val();
	let scope = $("#scopeSelect").children("option:selected").val();
	let pid = $("#projectSelect").children("option:selected").val();
	
	window.location.href = "/redcap/plugins/module-settings/export.php?prefix=" + prefix + "&scope=" + scope + "&pid=" + pid;
}

$(function() {
	$("#moduleSelect").change(function() {
		let prefix = $(this).children("option:selected").val();
		if (prefix == "") {
			$("#actions").hide();
			$("#projects").hide();
			$("#projects select").html("");
		} else {
			getProjectOptions(prefix);
			$("#projects").show();
		}
	});
	$("#scopeSelect").change(function() {
		let scope = $(this).children("option:selected").val();
		if (scope == "system") {
			$("#actions").show();
			$("#projects").hide();
		} else {
			let pid = $("#projectSelect").children("option:selected").val();
			$("#projects").show();
			if (pid == "" || pid == null) {
				$("#actions").hide();
			} else {
				$("#actions").show();
			}
		}
	});
	$("#projectSelect").change(function() {
		let pid = $(this).children("option:selected").val();
		if (pid == "" || pid == null) {
			$("#actions").hide();
		} else {
			$("#actions").show();
		}
	});
})