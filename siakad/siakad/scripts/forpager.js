// aksi

function goNew() {
	location.href = detailpage;
}

function goDetail(elem) {
	location.href = detailpage + "&key=" + elem.id;
	//window.open(location.href,'_BLANK');
	//goSubmitBlank(location.href);
}

function goDelete(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		document.getElementById("key").value = elem.id;
		goSubmit();
	}
}

function goInsert() {
	var cek;
	if(typeof(insertreq) == "undefined")
		cek = true;
	else
		cek = cfHighlight(insertreq);
	
	if(cek) {
		document.getElementById("act").value = "insert";
		goSubmit();
	}
}
function goGenerateWali() {
	document.getElementById("act").value = "generateWali";
	goSubmit();
}

function goRefresh() {
	$("#act").val("refresh");
	goSubmit();
}

// filter

function etrFilterCombo(e) {
	var ev = (window.event) ? window.event : e;
	var key = (ev.keyCode) ? ev.keyCode : ev.which;
	
	if (key == 13)
		goFilterCombo();
}

function goFilterCombo() {
	if(cfHighlight('tfilter'))
	{
		var key = document.getElementById("cfilter").value;
		var str = document.getElementById("tfilter").value;
		
		goFilter(key,str);
	}
}

function goFilter(key,str) {
	document.getElementById("page").value = 1;
	document.getElementById("filter").value = key + ':' + str;
	goSubmit();
}

function goRemoveFilter(idx) {
	document.getElementById("page").value = 1;
	document.getElementById("filter").value = idx;
	goSubmit();
}

function initFilterTree() {
	$(".navigation").treeview({
		persist: "location"
	});
	
	$(".navigation u").click(function() {
		id1 = $(this).parents("ul.navigation:eq(0)").attr("id");
		id2 = $(this).parents("li:eq(0)").attr("id");
		
		goFilterAddTree(id1,id2);
	});
	
	var cookieval = $.cookie(cookiename);
	if(typeof(cookieval) == "undefined")
		cookieval = 0;
	
	$("#div_filtertree").accordion({
		heightStyle: "fill",
		active: parseInt(cookieval),
		activate: function(event,ui) {
			$.cookie(cookiename,$("#div_filtertree h3").index(ui.newHeader));
		}
	});
	
	$("#div_filtertree").next().find("u").click(function() {
		arrid = this.id.split(":");
		
		goFilterAddTree(arrid[0],arrid[1]);
	});
}

function goFilterTree(key,str) {
	document.getElementById("page").value = 1;
	document.getElementById("filter").value = 'tree|' + key + ':' + str;
	goSubmit();
}

function goFilterAddTree(key,str) {
	document.getElementById("page").value = 1;
	document.getElementById("filter").value = 'tree|t|' + key + ':' + str;
	goSubmit();
}

function goFilterCheckTree() {
	var filter = "";
	
	$("[id='or']:checked").each(function() {
		id = $(this).parents("li:eq(0)").attr("id");
		filter += '|' + id;
	});
	
	if(filter == "")
		filter = "|";
	
	document.getElementById("page").value = 1;
	document.getElementById("filter").value = 'tree' + filter;
	goSubmit();
}

function goRemoveFilterTree() {
	document.getElementById("page").value = 1;
	document.getElementById("filter").value = 'tree|';
	goSubmit();
}

function etrFilterAll(e) {
	var ev = (window.event) ? window.event : e;
	var key = (ev.keyCode) ? ev.keyCode : ev.which;
	
	if (key == 13)
		goFilterAll();
}

function goFilterAll() {
	var col = "";
	$("#cfilter option").each(function() {
		col += "|"+this.value;
	});
	
	document.getElementById("page").value = 1;
	document.getElementById("filter").value = 'all|' + document.getElementById("tfilter").value + col;
	goSubmit();
}

function goRemoveFilterAll() {
	document.getElementById("tfilter").value = "";
	
	goFilterAll();
}

// paging

function goFirst() {
	document.getElementById("page").value = 1;
	goSubmit();
}

function goPrev() {
	document.getElementById("page").value = (parseInt(document.getElementById("page").value) - 1);
	goSubmit();
}

function goNext() {
	document.getElementById("page").value = (parseInt(document.getElementById("page").value) + 1);
	goSubmit();
}

function goLast() {
	document.getElementById("page").value = lastpage;
	goSubmit();
}

function goPage(page) {
	document.getElementById("page").value = page;
	goSubmit();
}

// pop up

var gParam;

function goPop(idpop,elem,e) {
	gParam = elem.id;
	
	var pop = $("#"+idpop);
	
	// pop.offset({ top: e.pageY, left: e.pageX });
	
	//var x = String(e.pageX)+"px";
	var x = String(e.pageX+20)+"px";
	var y = String(e.pageY-20)+"px";
	
	pop.css("top",y);
	pop.css("left",x);
	pop.show();
	
	$(document).bind("mouseup",function(e) {
		if(pop.has(e.target).length === 0) {
			pop.hide();
		}
	});
}

function showPage(id,file) {
	// karena id bisa dimiliki tr
	if(id && typeof(gParam) != "undefined") {
		// document.getElementById(id).value = gParam;
		$("input[id='"+id+"']").val(gParam);
	}
	
	goSubmitBlank(file);
}

function submitPage(id,file) {
	// karena id bisa dimiliki tr
	if(id && typeof(gParam) != "undefined") {
		// document.getElementById(id).value = gParam;
		$("input[id='"+id+"']").val(gParam);
	}
	
	document.getElementById("pageform").action = file;
	goSubmit();
}
