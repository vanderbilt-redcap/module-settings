function getProjectOptions(modulePrefix) {
	$.ajax({
		url: "ajax.php",
		data: {
			action: "getProjects",
			module: modulePrefix
		},
		dataType: "text",
		method: "POST",
		success : function(data) {
			console.log(data);
			$("#projectSelect").html(data);
		},
		fail : function(data) {
			// $("#error").html("<pre>There was an error:\n" + data + "</pre>")
		}
	})
}

function exportCSV(modulePrefix, projectID) {
	$.ajax({
		url: "ajax.php",
		data: {
			action: "getProjects",
			module: modulePrefix
		},
		// dataType: "text",
		method: "POST",
		success : function(data) {
			console.log(data);
			$("#projectSelect").html(data);
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
			$(this).next().hide();
			$("#projectSelect").html("");
		} else {
			getProjectOptions(prefix);
			$(this).next().show();
		}
	});
})