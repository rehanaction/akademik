(function($){
	var acdivh = 200; // tinggi div autocomplete
	var acx, actdidx, actdnum;
	var strac = new Array();
	
	$.fn.xautox = function(options) {
		var tdpeak, tdmore;
		var settings = $.extend({
			ajaxpage: ajaxpage, strpost: "", targetid: "", postid: "", imgchkid: "", imgavail: false, acdivpos: 1, posset: 1
		}, options);
		
		$(document.body).click(function(e) {
			$("#div_autocomplete").hide();
		});
		
		return $(this).each(function() {
			// karena target bisa beda-beda jadi ditaruh sini
			var target = "";
			var parent = $(this).parent();
			var imgchk_c, imgchk_u;
			
			if(settings.targetid == "") {
				target = $(this);
				
				imgchk_c = parent.children("[id='"+settings.imgchkid+"_c']");
				imgchk_u = parent.children("[id='"+settings.imgchkid+"_u']");
			}
			else {
				target = $("[id='"+settings.targetid+"']");
				if(target.length > 1) {
					target = parent.children("[id='"+settings.targetid+"']");
				
					imgchk_c = parent.children("[id='"+settings.imgchkid+"_c']");
					imgchk_u = parent.children("[id='"+settings.imgchkid+"_u']");
				}
				else {
					imgchk_c = $("#"+settings.imgchkid+"_c");
					imgchk_u = $("#"+settings.imgchkid+"_u");
				}
			}
			
			$(this).attr("autocomplete","off");
			$(this).attr("class","ControlAuto");
			if(target.val() != "")
				strac[target.attr("id")] = $(this).val();
			
			$(this).focus(function(e) {
				this.select();
			});
			
			$(this).keyup(function(e) {
				if($(this).val() != "" && (strac[target.attr("id")] != "" && $(this).val() != strac[target.attr("id")]))
				{
					$(this).attr("class","ControlAuto");
					imgchk_c.hide();
					imgchk_u.show();
					
					if(settings.targetid != "")
						target.val("");
				}
				else if($(this).val() == "")
				{
					$(this).attr("class","ControlAuto");
					imgchk_c.hide();
					if(settings.imgavail)
						imgchk_u.show();
					else
						imgchk_u.hide();
					
					if(settings.targetid != "")
						target.val("");
				}
				
				if((e.keyCode < 38 || e.keyCode > 40) && e.keyCode != 13) {
					showAutoComplete($(this),target,imgchk_c,imgchk_u,settings);
				}
			});
			
			$(this).keydown(function(e) {
				if(e.keyCode == 40 || e.keyCode == 38) {
					if(e.keyCode == 40 && actdidx < (actdnum-1)) {
						actdidx++;
						tdpeak = $("#tab_autocomplete td:eq("+actdidx+")").offset().top + $("#tab_autocomplete td:eq("+actdidx+")").height();
						tdmore = tdpeak - $("#div_autocomplete").offset().top;
						if(tdmore > acdivh)
							$("#div_autocomplete").get(0).scrollTop += (tdmore-acdivh);
					}
					else if(e.keyCode == 38 && actdidx > 0) {
						actdidx--;
						tdpeak = $("#tab_autocomplete td:eq("+actdidx+")").offset().top;
						if(tdpeak < $("#div_autocomplete").offset().top && $("#div_autocomplete").get(0).scrollTop > 0)
							$("#div_autocomplete").get(0).scrollTop -= ($("#div_autocomplete").offset().top - tdpeak);
					}
					else if(e.keyCode == 38 && actdidx < 0 && settings.acdivpos == 2)
						actdidx++;
					updateAutoCompleteLight();
				}
				else if((e.keyCode == 39 || e.keyCode == 13) && actdidx >= 0) {
					execAutoCompleteLight($(this),target,imgchk_c,imgchk_u,$("#tab_autocomplete td:eq("+actdidx+")"));
					return false;
				}
			});
		});
	};
	
	$.fn.unxauto = function() {
		$(this).unbind("focus");
		$(this).unbind("keyup");
		$(this).unbind("keydown");
	}
	
	function showAutoComplete(jqtbox,jqkode,jqimgc,jqimgu,settings) {
		var toffset;
		var srch = jqtbox.val();
		
		var ajaxpage = settings.ajaxpage;
		var strpost = settings.strpost;
		var postid = settings.postid;
		var acdivpos = settings.acdivpos;
		var posset = settings.posset;
		
		actdidx = -1;
		
		if($("#tab_autocomplete").width() < jqtbox.width())
			$("#tab_autocomplete").width(jqtbox.width());
		
		var getc = "q";
		if(postid != "") {
			strpost += "&q[]="+$("#"+postid).val();
			getc += "[]";
		}
		
		actdnum = 0;
		acx = $.getJSON(ajaxpage,strpost+"&"+getc+"="+jqtbox.val(),function(data) {
			// alert('DATA: '+data);
			$("#tab_autocomplete").empty();
			
			$.each(data, function(i,item) {
				$("#tab_autocomplete").append('<tr><td id="'+item.key+'" nowrap style="cursor:pointer">'+item.label+'</td></tr>');
				actdnum++;
			});
			
			if(actdnum > 0) {
				$("#tab_autocomplete td").mouseover(function() {
					actdidx = $("#tab_autocomplete td").index($(this));
					updateAutoCompleteLight();
				});
				
				$("#tab_autocomplete td").click(function() {
					execAutoCompleteLight(jqtbox,jqkode,jqimgc,jqimgu,$(this));
				});
				
				if(posset == 1)
					toffset = jqtbox.offset();
				else
					toffset = jqtbox.position();
				
				$("#div_autocomplete").css("left",toffset.left+1);
				if(acdivpos == 1)
					$("#div_autocomplete").css("top",toffset.top+jqtbox.height()+5);
				$("#div_autocomplete").show();
				
				$("#div_autocomplete").get(0).scrollTop = 0;
				if($("#tab_autocomplete").height() > acdivh) {
					$("#div_autocomplete").height(acdivh);
					$("#div_autocomplete").width($("#tab_autocomplete").width()+19);
					if(acdivpos == 2)
						$("#div_autocomplete").css("top",toffset.top-acdivh-5);
				}
				else {
					$("#div_autocomplete").height($("#tab_autocomplete").height());
					$("#div_autocomplete").width($("#tab_autocomplete").width());
					if(acdivpos == 2)
						$("#div_autocomplete").css("top",toffset.top-$("#tab_autocomplete").height()-5);
				}
			}
			else
				$("#div_autocomplete").hide();
		});
	}
	
	function updateAutoCompleteLight() {
		$("#tab_autocomplete td").css("background-color","#FFFFFF");
		$("#tab_autocomplete td:eq("+actdidx+")").css("background-color","#C4CDE0");
	}
	
	function execAutoCompleteLight(jqtbox,jqkode,jqimgc,jqimgu,jqtd) {
		var ptext = jQuery.trim(jqtd.text());
		
		strac[jqkode.attr("id")] = ptext;
		jqtbox.val(ptext);
		jqkode.val(jqtd.attr("id"));
		jqtbox.attr("class","ControlAuto");
		if(jqimgc)
			jqimgc.show();
		if(jqimgu)
			jqimgu.hide();
		
		$("#div_autocomplete").hide();
		
		// after effect
		jqtbox.change();
		jqkode.change();
	}
})(jQuery);