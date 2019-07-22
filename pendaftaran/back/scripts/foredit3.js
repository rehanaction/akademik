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
		
		div = $("div[id='items']").eq(index);
		
		div.xLoad();
		div.show();
	});
	
	chooseTab(0);
}

function chooseTab(idx) {
	$("div.tabs a").eq(idx).triggerHandler("click");
}

function setKeyPop(label,elem) {
	$("#key_"+label).val(elem.id);
}

function goShowPop(label) {
	$("#div_dark_"+label).show();
	
	// atur posisi
	var padtop = $(window).height()/4;
	$("#div_light_"+label).css("padding-top",padtop);
	
	// cek edit
	if($("#key_"+label).val() == "") {
		$("#be_delete_"+label).hide();
		
		// langsung edit
		goEditPop(label);
	}
	else
		$("#be_delete_"+label).show();
	
	$("#div_light_"+label).show();
}

function goWaitPop(label) {
	// cek popup
	if($("#div_light_"+label).is(":hidden"))
		goShowPop(label);
	
	$("#div_content_"+label).children().css("visibility","hidden");
	$("#div_info_"+label).css("visibility","visible");
	
	$("#div_load_"+label).show();
}

function goUnwaitPop(label) {
	$("#div_content_"+label).children().css("visibility","visible");
	$("#div_info_"+label).css("visibility","hidden");
	
	$("#div_error_"+label).hide();
	$("#div_success_"+label).hide();
}

function goSuccessPop(label,msg) {
	$("#div_load_"+label).hide();
	
	$("#div_success_"+label).html(msg);
	$("#div_success_"+label).show();
	
	setTimeout('goClosePop("'+label+'"); $("#div_light_'+label+'").parent().xLoad();',1500);
}

function goErrorPop(label,msg) {
	$("#div_load_"+label).hide();
	
	$("#div_error_"+label).html(msg);
	$("#div_error_"+label).show();
	
	setTimeout('goUnwaitPop("'+label+'");',1500);
}

function goNewPop(label) {
	$("[id$='_"+label+"']").val("");
	
	if($("#div_light_"+label).is(":hidden"))
		goShowPop(label);
}

function goDetailPop(label,elem) {
	setKeyPop(label,elem);
	goWaitPop(label);
	
	var post = "act=data&label="+label+"&key_"+label+"="+elem.id;
	
	var jqxhr = $.ajax({
					url: "index.php?page="+ajax_pop[label],
					timeout: ajaxtimeout,
					data: post
				});

	jqxhr.done(function(data) {
		data = jQuery.parseJSON(data);
		
		var cvalue, clabel;
		for(key in data) {
			cdetail = true;
			if(!data[key])
				cdetail = false;
			else if(typeof(data[key].value) == "undefined")
				cdetail = false;
			
			if(cdetail) {
				cvalue = data[key].value;
				clabel = data[key].label;
			}
			else {
				cvalue = data[key];
				clabel = data[key];
			}
			
			$("#"+key+"_"+label).val(cvalue);
			$("#span_"+key+"_"+label).html(clabel);
		}
		
		goUnwaitPop(label);
	});
	jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function goEditPop(label) {
	$("[id='show_"+label+"']").hide();
	$("[id='edit_"+label+"']").show();
	
	$("#be_add_"+label+",#be_edit_"+label+"").hide();
	$("#be_save_"+label+",#be_undo_"+label+"").show();
}

function goSavePop(label) {
	var pass = true;
	if(typeof(required_pop[label]) != "undefined") {
		if(!cfHighlight(required_pop[label]))
			pass = false;
	}
	
	if(pass) {
		goWaitPop(label);
		
		var post = "act=save&label="+label+"&key="+$("#key").val()+"&";
		post += $("[id$='_"+label+"']").serialize();
		
		var jqxhr = $.ajax({
						url: "index.php?page="+ajax_pop[label],
						timeout: ajaxtimeout,
						type: "POST",
						data: post
					});
	
		jqxhr.done(function(data) {
			data = jQuery.parseJSON(data);
			
			if(data.error == "0")
				goSuccessPop(label,data.message);
			else
				goErrorPop(label,data.message);
		});
		jqxhr.fail(function(xhr,status) {
			alert(status);
		});
	}
}

function goUndoPop(label) {
	if($("#key_"+label).val() != "") {
		$("[id='edit_"+label+"']").hide();
		$("[id='show_"+label+"']").show();
		
		$("#be_save_"+label+",#be_undo_"+label+"").hide();
		$("#be_add_"+label+",#be_edit_"+label+"").show();
	}
}

function goDeletePop(label) {
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		goWaitPop(label);
		
		var post = "act=delete&label="+label+"&key_"+label+"="+$("#key_"+label).val();
		
		var jqxhr = $.ajax({
						url: "index.php?page="+ajax_pop[label],
						timeout: ajaxtimeout,
						type: "POST",
						data: post
					});
	
		jqxhr.done(function(data) {
			data = jQuery.parseJSON(data);
			
			if(data.error == "0")
				goSuccessPop(label,data.message);
			else
				goErrorPop(label,data.message);
		});
		jqxhr.fail(function(xhr,status) {
			alert(status);
		});
	}
}

function goClosePop(label) {
	$("#div_light_"+label).hide();
	$("#div_dark_"+label).hide();
	
	// kembalikan posisi
	goUndoPop(label);
	goUnwaitPop(label);
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