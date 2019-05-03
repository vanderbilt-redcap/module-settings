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

function exportCSV() {
	$.ajax({
		url: "ajax.php",
		data: {
			action: "export",
			prefix: $("#moduleSelect").children("option:selected").val(),
			scope: $("#scopeSelect").children("option:selected").val(),
			pid: $("#projectSelect").children("option:selected").val()
		},
		dataType: "text",
		method: "POST",
		success : function(data) {
			console.log(data);
			window.location.href = data;
		},
		fail : function(data) {
			// $("#error").html("<pre>There was an error:\n" + data + "</pre>")
		}
	})
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