// aksi

function initEdit(isedit) {
	if(!isedit)
		isedit = false;
	
	if(isedit)
		goEdit();
	else if(document.getElementById("key"))
		if(document.getElementById("key").value == "")
			goEdit();
	
	initFoto();
}

function initFoto() {
	
	// upload foto
	$('html').click(function() {
		hidePopFoto();
	});
	$("#imgfoto").click(function(e) {
		e.stopPropagation();
		var offset = $(this).offset();
		
		var x = e.pageX;
		var y = e.pageY;
		
		showPopFoto(x,y);
	});
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

function goReset() {
	$("#pageform")[0].reset();
}

function goDelete() {
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		goSubmit();
	}
}

function goDeleteFile(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus file ini?");
	if(hapus) {
		document.getElementById("act").value = "deletefile";
		goSubmit();
	}
}

// detail

function goInsertDetail(detail) {
	document.getElementById("detail").value = detail;
	document.getElementById("act").value = "insertdet";
	
	goSubmit();
}

function goUpdateDetail(detail,elem) {
	// nonaktifkan selain yang dipost
	$("[id='tr_detail']").find("input,select,textarea").attr("disabled","disabled");
	$(elem).parents("tr:eq(0)").find("input,select,textarea").removeAttr("disabled");
	
	document.getElementById("detail").value = detail;
	document.getElementById("subkey").value = elem.id;
	document.getElementById("act").value = "updatedet";
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

// upload foto

var upload;

function showPopFoto(x,y) {
	var pop = $("#popFoto");
	
	pop.css("left",x);
	pop.css("top",y);
	
	pop.show();
}

function hidePopFoto() {
	$("#popFoto").hide();
}

function showChooseFile() {
	$("#foto").click();
}

function chooseFile() {
	if(upload)
		goUploadFoto();
}

function setUpload() {
	upload = true;
	showChooseFile();
}

function goUploadFoto() {
	
	var form = $("#pageform");
	var target = form.attr("target");
	
	$("#imgfoto").showWait();
	form.attr("target","upload_iframe");
	
	document.getElementById("act").value = "savefoto";
	goSubmit();
	
	form.attr("target",target);
	
}

function goHapusFoto() {
	var hapus = confirm("Apakah anda yakin akan menghapus foto ini?");
	if(hapus) {
		var form = $("#pageform");
		var target = form.attr("target");
		
		$("#imgfoto").showWait();
		form.attr("target","upload_iframe");
		
		document.getElementById("act").value = "deletefoto";
		goSubmit();
		
		form.attr("target",target);
	}
}
