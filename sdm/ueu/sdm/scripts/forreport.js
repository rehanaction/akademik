// aksi

function goReport() {
	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		var form = document.getElementById("pageform");
		
		form.action = reportpage;
		form.target = "_blank";
		
		goSubmit();
		
		form.action = "";
		form.target = "";
	}
}