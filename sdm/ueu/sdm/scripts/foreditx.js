/* fungsi umum */
var sent="";

function goSubmit(formid) {
	if(!formid)
		formid = "pageform";
	$("#contents").divpost({page: thispage, sent: $("#"+formid).serializeArray()});
}

function goList(formid) {
	if(!formid)
		formid = "pageform";
	$("#contents").divpost({page: listpage, sent: $("#"+formid).serializeArray()});
}

function goNew(key) {
	sent = "key="+key;
	$("#contents").divpost({page: detailpage, sent: sent});
}

function goPost(file,sent) {
	$("#contents").divpost({page: file, sent: sent});
}

function goUndo() {
	document.getElementById("pageform").reset();
}

function goEdit() {
	$("[id='show']").hide();
	$("[id='edit']").show();
	
	$("#be_add,#be_edit").hide();
	$("#be_save,#be_undo").show();
}

function goUndo() {
	$("[id='show']").show();
	$("[id='edit']").hide();
	
	$("#be_add,#be_edit").show();
	$("#be_save,#be_undo").hide();
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

function initEdit(isedit) {
	if(!isedit)
		isedit = false;
	
	if(isedit)
		goEdit();
	else if(document.getElementById("subkey"))
		if(document.getElementById("subkey").value == "")
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
		if(document.getElementById("file"))
			document.getElementById("file").value = elem;
		document.getElementById("act").value = "deletefile";
		goSubmit();
	}
}

// tab pane
function initTab() {
	$("div.tabs a").click(function() {
		var index = $("div.tabs a").index(this);
		
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
	form.submit();
	
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
		form.submit();
		
		form.attr("target",target);
	}
}

function goInsertDetail(detail) {
	document.getElementById("detail").value = detail;
	document.getElementById("act").value = "insertdet";
	
	goSubmit();
}

function goDeleteDetail(detail,elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("detail").value = detail;
		document.getElementById("subkeydet").value = elem.id;
		document.getElementById("act").value = "deletedet";
		goSubmit();
	}
}

function goShowPage(id,file){
	document.getElementById("subkey").value = id;
	
	var action = document.getElementById("pageform").action;
	var target = document.getElementById("pageform").target;
	
	document.getElementById("pageform").action = file;
	document.getElementById("pageform").target = "_blank";
	
	goSubmit();
	
	document.getElementById("pageform").action = action;
	document.getElementById("pageform").target = target;
}

