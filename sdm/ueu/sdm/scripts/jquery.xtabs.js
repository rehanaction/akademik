(function($){
	var contentid, gsent, list, container, tabin;
	
	$.fn.xtabs = function(options) {
		var settings = $.extend({
			contentid: "contents", deftab: 0, deflink: 0, sent: ""
		}, options);
		
		contentid = settings.contentid;
		list = $(this).children("#primary");
		gsent = settings.sent;
		
		return $(this).each(function() {
			chooseTab(settings.deftab,settings.deflink);
			
			var idx, idx2, tab;
			var li = list.children("li");
			
			li.bind("chooseTab", function() {
				idx = li.index($(this));
				chooseTab(idx);
			});
			
			li.bind("hideTab", function() {
				idx = li.index($(this));
				
				tab = list.children("li:nth-child(" + (tabin+1) + ")");
				idx2 = li.index(tab);
				
				if(idx == idx2) {
					if(idx == 0)
						chooseTab(1);
					else
						chooseTab(0);
				}
				
				$(this).hide();
			});
			
			li.bind("showOnlyTab", function() {
				li.not($(this)).hide();
				$(this).trigger("chooseTab");
			});
		});
	};
	
	$.fn.chooselinx = function(linxidx, sent) {
		return chooseLinx(linxidx, sent);
	};
	
	$.fn.postpage = function(file, sent) {
		return postX(file, sent);
	};
	
	$.fn.showalltabs = function() {
		return list.children("li").show();
	};
	
	$.fn.showrealtab = function(defsent) {
		var ful = $(this).children("#primary");
		var rul = $(this).children("#primary_t");
		
		ful.hide();
		ful.attr("id","primary_t");
		rul.attr("id","primary");
		rul.show();
		
		return $(this).xtabs({sent:defsent});
	};
	
	function chooseTab(tabidx,linxidx) {
		var tab, linx;
		
		if(typeof(linxidx) == "undefined" || linxidx == '')
			linxidx = 0;
		
		list.children("li").each(function(i) {
			tab = $(this).children(":first-child");
			linx = $(this).children("#secondary");
			
			if(i == tabidx) {
				tab.replaceWith('<span id="tablink">' + tab.text() + '</span>');
				linx.show();
			}
			else {
				tab.replaceWith('<a id="tablink" href="javascript:void(0);">' + tab.text() + '</a>');
				linx.hide();
			}
		});
		
		tabin = tabidx;
		chooseLinx(linxidx, gsent);
	};
	
	function chooseLinx(linxidx, sent) {
		var idx, file, linx;
		
		linx = list.children("li:eq(" + (tabin) + ")").children("#secondary").children("li").children("a");
		linx.each(function(i) {
			$(this).unbind('click');
			
			if(i == linxidx) {
				$(this).addClass("noclick");
				file = $(this).attr("href");
				
				$(this).click(function(e) {
					e.preventDefault();
					chooseLinx(i, gsent); // opt: untuk refresh
				});
			}
			else {
				$(this).removeClass("noclick");
				
				$(this).click(function(e) {
					e.preventDefault();
					chooseLinx(i, gsent);
				});
			}
		});
		
		postX(file, sent);
		
		$("a[id='tablink']").unbind("click");
		$("a[id='tablink']").click(function() {
			li = list.children("li");
			idx = li.index($(this).parent());
			chooseTab(idx);
		});
	};
	
	function getX() {
		$("#" + contentid).waitload({divid: "progressbar"});
		$.post(file,function(text) {
			$("#" + contentid).waitload({divid: "progressbar", mode: "unload"});
			$("#" + contentid).html(text);
		});
	}
	
	function postX(file, sent) {
		$("#" + contentid).waitload({divid: "progressbar"});
		$.post(file,sent,function(text) {
			$("#" + contentid).waitload({divid: "progressbar", mode: "unload"});
			$("#" + contentid).html(text);
		});
	}
	
	function setConOpc() {
		$("#" + contentid).css("opacity",0.5);
		$("#" + contentid).css("filter","alpha(opacity=50)");
	}
	
	function unsetConOpc() {
		$("#" + contentid).css("opacity",1);
		$("#" + contentid).css("filter",null);
	}
	
	var tabEvents = ['chooseTab', 'hideTab', 'showOnlyTab'];
	for (var i = 0; i < tabEvents.length; i++) {
		$.fn[tabEvents[i]] = (function(tabEvent) {
			return function(tab) {
				return $(this).each(function() {
					var list = $(this).children("#primary");
					var li = list.children("li:nth-child(" + (tab+1) + ")");
					
					li.trigger(tabEvent);
				});
			};
		})(tabEvents[i]);
	}
})(jQuery);