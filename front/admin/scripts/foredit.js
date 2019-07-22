// aksi

function initEdit(isedit) {
	if(!isedit)
		isedit = false;
	
	if(isedit)
		goEdit();
	else if(document.getElementById("key"))
		if(document.getElementById("key").value == "")
			goEdit();
}

function goChange() {
	document.getElementById("act").value = "change";
	goSubmit();
}

function goList() {
	location.href = listpage;
}

function goNew() {
	location.href = thispage;
}

function goEdit() {
	$("[id='show']").hide();
	$("[id='edit']").show();
	
	$("#be_add,#be_edit").hide();
	$("#be_save,#be_undo").show();
}

function goSave() {
	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

function goUndo() {
	location.reload();
}

function goDelete(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		goSubmit();
	}
}

// detail

function goInsertDetail(detail) {
	document.getElementById("detail").value = detail;
	document.getElementById("act").value = "insertdet";
	
	goSubmit();
}

function goDeleteDetail(detail,elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("detail").value = detail;
		document.getElementById("subkey").value = elem.id;
		document.getElementById("act").value = "deletedet";
		goSubmit();
	}
}

// tab pane

function initTab() {
	$("div.tabs a[id='tablink']").click(function() {
		index = $("div.tabs a").index(this);
		
		$("div.tabs li").removeAttr("class");
		$(this).parent("li").attr("class","selected");
		
		$("div[id='items']").hide();
		$("div[id='items']").eq(index).show();
	});
	
	chooseTab(0);
}

function chooseTab(idx) {
	$("div.tabs a").eq(idx).triggerHandler("click");
}