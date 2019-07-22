// aksi

function goReport() {
	var form = document.getElementById("pageform");
	
	form.action = reportpage;
	form.target = "_blank";
	
	goSubmit();
	
	form.action = "";
	form.target = "";
}