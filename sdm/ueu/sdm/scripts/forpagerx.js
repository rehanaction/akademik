var detailpage, xtdid, colparam;
var cat, str, page, sorts, filter;

function goSubmit(formid) {
	if(!formid)
		formid = "pageform";
	$("#contents").divpost({page: thispage, sent: $("#"+formid).serializeArray()});
}

function goPostAjax() {
	sent = "cat=" + cat + "&str=" + str + "&page=" + page + "&sort=" + sorts + "&filter=" + filter;
	$("#" + xtdid).divpost({page: xlist, sent: sent});
}

//start new
function goNew() {
	//sent += "cat=" + cat + "&str=" + str + "&page=" + page + "&sort=" + sorts + "&filter=" + filter;
	$("#" + xtdid).divpost({page: detailpage, sent: sent});
}

function goDetail(elem) {
	sent += "&subkey=" + elem.id +"&scroll="+$(window).scrollTop();
	$("#" + xtdid).divpost({page: detailpage, sent: sent});
}

function goDelete(elem) {
	var parent = $(elem).parent().parent();
	var classold = parent.attr('class');
	parent.removeClass(classold);
	parent.addClass("AlternateBG2");
	
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		document.getElementById("subkey").value = elem.id;
		goSubmit();
	}
	else
		parent.removeClass("AlternateBG2");
	
	parent.addClass(classold);
}

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

function goLimit() {
	document.getElementById("page").value = 1;
	goSubmit();
}
