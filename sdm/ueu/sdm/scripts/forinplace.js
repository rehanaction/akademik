// aksi

function etrInsert(e) {
	var ev = (window.event) ? window.event : e;
	var key = (ev.keyCode) ? ev.keyCode : ev.which;
	
	if (key == 13)
		goInsert();
}

function goInsert() {
	if(cfHighlight(insertreq)) {
		document.getElementById("act").value = "insert";
		goSubmit();
	}
}

function goEdit(elem) {
	document.getElementById("act").value = "edit";
	document.getElementById("key").value = elem.id;
	goSubmit();
}

function etrUpdate(e,elem) {
	var ev = (window.event) ? window.event : e;
	var key = (ev.keyCode) ? ev.keyCode : ev.which;
	
	if (key == 13)
		goUpdate(elem);
}

function goUpdate(elem) {
	if(cfHighlight(updatereq)) {
		document.getElementById("act").value = "update";
		document.getElementById("key").value = elem.id;
		goSubmit();
	}
}

function goDelete(elem) {
	var parent = $(elem).parent().parent();
	var classold = parent.attr('class');
	parent.removeClass(classold);
	parent.addClass("AlternateBG2");
	
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		document.getElementById("key").value = elem.id;
		goSubmit();
	}
	else
		parent.removeClass("AlternateBG2");
	
	parent.addClass(classold);
}
