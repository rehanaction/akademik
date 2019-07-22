var ajaxtimeout = 20000;

(function ($) {
	$(document).ajaxError(function(event,request,settings,error) {
		if(error == "timeout")
			alert("Pengambilan data timeout");
		else
			alert("Terjadi kesalahan dalam pengambilan data");
	});
	
	// untuk mendapatakan option select
	$.fn.xhrSetOption = function (param, empty, callback) {
		var ajaxurl = xhrfGetURL(param);
		
		if(!empty) empty = false;
		
		return $(this).each(function() {
			var jqelem = $(this);
			
			var jqxhr = $.ajax({
				dataType: "json",
				url: ajaxurl,
				timeout: ajaxtimeout
			});
			jqxhr.done(function(data) {
				if(empty === false)
					jqelem.empty();
				else if(empty === true)
					jqelem.html('<option value=""></option>');
				else if(data.length == 0)
					jqelem.html('<option value="">-- ' + empty + ' tidak ditemukan --</option>');
				else
					jqelem.html('<option value="">-- Pilih ' + empty + ' --</option>');
				
				jQuery.each(data,function(k,v) {
					jqelem.append('<option value="' + k + '">' + v + '</option>');
				});
				
				// panggil fungsi callback
				if (typeof(callback) == "function")
					callback();
			});
		});
	}
})(jQuery);

// mendapatkan data
function xhrfGetURL(param) {
	var ajaxurl = "index.php?page=ajax";
	
	if(param) {
		if (typeof(param) == "object")
			param = param.join("&");
		
		ajaxurl += "&" + param;
	}
	
	return ajaxurl;
}

function xhrfGetStr(param, callback, ctimeout) {
	var ajaxurl = xhrfGetURL(param);
	
	var jqxhr = $.ajax({
		url: ajaxurl,
		timeout: (typeof(ctimeout) == "undefined" ? ajaxtimeout : ctimeout)
	});
	jqxhr.done(function(data) {
		// panggil fungsi callback
		if (typeof(callback) == "function")
			callback(data);
	});
}

function xhrfGetData(param, callback) {
	var ajaxurl = xhrfGetURL(param);
	
	var jqxhr = $.ajax({
		dataType: "json",
		url: ajaxurl,
		timeout: ajaxtimeout
	});
	jqxhr.done(function(data) {
		// panggil fungsi callback
		if (typeof(callback) == "function")
			callback(data);
	});
}

// post data
function xhrfPostData(url, param, callback, ctimeout) {
	var jqxhr = $.ajax({
		dataType: "json",
		method: "POST",
		data: param,
		url: url,
		timeout: (typeof(ctimeout) == "undefined" ? ajaxtimeout : ctimeout)
	});
	jqxhr.done(function(data) {
		// panggil fungsi callback
		if (typeof(callback) == "function")
			callback(data);
	});
}
