// aksi

function goReport(page) {
	var form = document.getElementById("pageform");
	
	if(typeof(page) == "undefined")
		form.action = reportpage;
	else
		form.action = getPage(page);
	
	form.target = "_blank";
	
	goSubmit();
	
	form.action = "";
	form.target = "";
}