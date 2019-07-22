// variabel global
var ajaxpage = "index.php?page=ajax";
var ajaxtimeout = 20000;
var sent="";

// pindah halaman
function goTo(page) {
	location.href = page;
}

function getPage(page) {
	return "index.php?page="+page;
}

function goView(page) {
	location.href = getPage(page);
}
// ganti role
function changeRole() {
	goView("home&act=chgrole&key="+document.getElementById("hakakses").value);
}

// image
function loadDefaultActImg(elem) {
	elem.src = "images/aktivitas/DEFAULT.png";
}

// pengecekan
function onlyNumber(e,elem,dec,point) {
	var code = e.keyCode || e.which;
	var val = elem.value;
	
	if ((code > 57 && code < 96) || code > 105 || code == 32) {
		if(code == 188 && dec) {
			if(val == "") // belum ada isinya, koma tidak boleh didepan
				return false;
			if(val.indexOf(",") > -1) // udah ada koma, tidak boleh ada lagi
				return false;
			return true;
		}
		if((point && (code == 110 || code == 190)) || code == 116 || code == 173) // refresh atau titik
			return true;
		return false;
	}
}

function charNum(elem,num) {
	var len = elem.value.length;
	if(len < num)
		return true;
	else
		return false;
}

function cutCharNum(elem,num) {
	var len = elem.value.length;
	if(len > num)
		elem.value = elem.value.substr(0,num);
}

function cfHighlight(csv) {
	var i, err = false;
	var aid = csv.split(",");
	
	for(i=0;i<aid.length;i++) {
		arre = aid[i].split(":");
		
		e = document.getElementById(arre[0]);
		if(arre.length == 2)
			el = document.getElementById(arre[1]);
		else
			el = e;
		
		if(e != null && e.value == "") {
			doHighlight(el);
			err = true;
		}
	}
	
	if(err) {
		alert("Mohon mengisi isian-isian yang berwana kuning dengan benar terlebih dahulu.");
		return false;
	}
	return true;
}

function doHighlight(elem) {
	elem.className = "ControlErr";
	elem.onfocus = function () { if(this.readOnly) this.className = "ControlRead"; else this.className = "ControlStyle"; }
}

// submit
function goSubmit() {
	var top = (document.documentElement && document.documentElement.scrollTop) || 
				document.body.scrollTop;
	
	if(document.getElementById("scroll"))
		document.getElementById("scroll").value = top;
	
	$("#contents").divpost({page: phpself, sent: $("#"+formid).serializeArray()});
}

function initRefresh() {
	$(document).keydown(function(e) {
		var ev = (window.event) ? window.event : e;
		var key = (ev.keyCode) ? ev.keyCode : ev.which;
		
		if(key == 116) {
			document.getElementById("act").value = "";
			goSubmit();
			
			return false;
		}
		
		return true;
	});
}

function initXCell() {
	$(".XCell").keydown(function(e) {
		var ev = (window.event) ? window.event : e;
		var key = (ev.keyCode) ? ev.keyCode : ev.which;
		
		if(key == 9) {
			$(this).parent().next().find(".XCell").select();
			return false;
		}
		else if(key == 13) {
			var tr = $(this).parent().parent();
			var idx = tr.find(".XCell").index(this);
			
			tr.next().find(".XCell:eq("+idx+")").select();
			return false;
		}
		
		return true;
	});
}


function goDownload(url,id) {
	if(!id && document.getElementById("subkey"))
		id = document.getElementById("subkey").value;
		
	window.open(url + "&id="+id,"_blank");
}
