(function($){
	$.fn.divpost = function(options) {
		var settings = $.extend({
			page: "", sent: ""
		}, options);
		
		return $(this).each(function() {
			xdiv = $(this);
			
			xdiv.waitload({divid: "progressbar"});
			$.post(settings.page,settings.sent,function(text) {
				xdiv.waitload({divid: "progressbar", mode: "unload"});
				xdiv.html(text);
			});
		});
	};
	
	$.fn.onlyNum = function(options) {
		var settings = $.extend({
			point: true
		}, options);
		
		$(this).keydown(function (e) {
			code = e.keyCode || e.which;
			if ((code > 57 && code < 96) || code > 105 || code == 32) {
				if((code == 190 || code == 110) && settings.point) {
					if($(this).val() == "") // belum ada isinya, titik tidak boleh didepan
						return false;
					if($(this).val().indexOf(".") > -1) // udah ada titik, tidak boleh ada lagi
						return false;
					return true;
				}
				return false;
			}
		});
	};
	
	$.fn.showPopup = function(options) {
		var settings = $.extend({
			popup: "", x: 0, y: 0
		}, options);
		
		popup = $("#" + settings.popup);
		
		return $(this).each(function() {
			popup.css("left",settings.x);
			popup.css("top",settings.y);
			popup.css("visibility","visible");
		});
	};
	
	$.fn.waitload = function(options) {
		var settings = $.extend({
			divid: "roller", mode: "load"
		}, options);
		
		return $(this).each(function() {
			div = $("#" + settings.divid);
			
			if(settings.mode == "load") {
				$(this).css("opacity",0.5);
				$(this).css("filter","alpha(opacity=50)");
				
				ctrofs = $(this).offset();
				divleft = ctrofs.left + ($(this).width()/2) - (div.width()/2);
				divtop = ctrofs.top + ($(this).height()/2) - (div.height()/2);
				
				div.css("left",divleft);
				div.css("top",divtop);
				div.css("visibility","visible");
			}
			else { // sebenarnya unload
				$(this).css("opacity",1);
				$(this).css("filter",null);
				div.css("visibility","hidden");
			}
		});
	};
	
	$.fn.textNumberFormat = function(num) {
		var i, j, ret;
		
		return $(this).each(function() {
			num = String(num);
			j = 0; ret = "";
			for(i=num.length-1;i>=0;i--) {
				if(j == 3) {
					ret = "." + ret;
					j = 0;
				}
				ret = num.charAt(i) + ret;
				j++;
			}
			
			$(this).text(ret);
		});
	};
	
	$.fn.txtFieldDateFormat = function(options) {
		var settings = $.extend({
			point: true
		}, options);
		
		$(this).change(function() {
			dtString = $(this).val();
			if(dtString.length == 8) {
				var newdate = dtString.substring(0,2)+'-'+dtString.substring(2,4)+'-'+dtString.substring(4,8);
				$(this).val(newdate);
			}
			else if(dtString.length == 10) {
				var newdate = dtString.substring(0,2)+'-'+dtString.substring(3,5)+'-'+dtString.substring(6,10);
				$(this).val(newdate);
			};
		});
	};
	
	
})(jQuery);