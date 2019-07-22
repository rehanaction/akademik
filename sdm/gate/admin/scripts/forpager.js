// aksi

function goNew() {
	location.href = detailpage;
}

function goDetail(elem) {
	location.href = detailpage + "&key=" + elem.id;
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

// pop up

var gParam;

function goPop(idpop,elem,e) {
	gParam = elem.id;
	
	var pop = $("#"+idpop);
	
	// pop.offset({ top: e.pageY, left: e.pageX });
	
	var x = String(e.pageX)+"px";
	var y = String(e.pageY)+"px";
	
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
	
	var action = document.getElementById("pageform").action;
	var target = document.getElementById("pageform").target;
	
	document.getElementById("pageform").action = file;
	document.getElementById("pageform").target = "_blank";
	
	goSubmit();
	
	document.getElementById("pageform").action = action;
	document.getElementById("pageform").target = target;
}