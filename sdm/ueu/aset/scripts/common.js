// variabel global
var ajaxpage = "index.php?page=ajax";
var ajaxtimeout = 20000;

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

function goOpen(page) {
	win = window.open(getPage(page));
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
		if((point && (code == 110 || code == 190)) || code == 116) // refresh atau titik
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

function formatNumber(str) {
	if(str != "") {
		str = str.replace(/\./g,'');
		str = str.replace(/,/g,'.');
	}
	
	if(str == "")
		return 0;
	else if(isNaN(str))
		return 0;
	else
		return parseFloat(str);
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
	
	document.getElementById("pageform").submit();
}

function goSubmitBlank(file) {
	var action = document.getElementById("pageform").action;
	var target = document.getElementById("pageform").target;
	
	document.getElementById("pageform").action = file;
	document.getElementById("pageform").target = "_blank";
	
	goSubmit();
	
	document.getElementById("pageform").action = action;
	document.getElementById("pageform").target = target;
}

function goReload() {
	location.reload();
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

// download
function goDownload(jenis,id) {
	if(!id && document.getElementById("key"))
		id = document.getElementById("key").value;
	
	goView("download&type="+jenis+"&id="+id);
}

function goDownloadUG(url,id) {
	if(!id && document.getElementById("key"))
		id = document.getElementById("key").value;
	
	window.open(url + "&id="+id,"_blank");
}

// jquery
(function($){
	$.fn.showWait = function() {
		var foto = $("#imgfoto");
		var offset = foto.offset();
		
		var borderleft = foto.css("border-left-width");
		var bordertop = foto.css("border-top-width");
		
		if(borderleft == "")
			borderleft = 0;
		else
			borderleft = parseFloat(borderleft.substr(0,borderleft.length-2));
		
		if(bordertop == "")
			bordertop = 0;
		else
			bordertop = parseFloat(bordertop.substr(0,bordertop.length-2));
		
		return $(this).each(function() {
			var dark = $("#"+this.id+"_dark");
			var light = $("#"+this.id+"_light");
			
			if(dark.length > 0) {
				dark.css("position","absolute");
				
				dark.css("left",offset.left+borderleft);
				dark.css("top",offset.top);
				dark.css("width",foto.width());
				dark.css("height",foto.height());
				
				dark.show();
			}
			if(light.length > 0) {
				light.css("position","absolute");
				light.css("padding-top",(foto.height()/2)-5);
				
				light.css("left",offset.left+borderleft);
				light.css("top",offset.top);
				light.css("width",foto.width());
				light.css("height",foto.height());
				
				light.show();
			}
		});
	};
	
	$.fn.hideWait = function() {
		return $(this).each(function() {
			$("#"+this.id+"_light").hide();
			$("#"+this.id+"_dark").hide();
		});
	};
})(jQuery);