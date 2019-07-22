<script type="text/javascript">
function numberOnly(evt) {
    evt = (evt) ? evt : window.event
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false
    }
    return true
} 

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	loadKotaLahir();
	loadKota();
	loadKotaOrtu();
	loadKotaKontak();
	loadKotaSMU();
	loadSMU();
	//getDetailSmu();
	loadKotaPonpes();
	loadKotaPTAsal();
	loadKotaayah();
	loadKotaibu();
	loadKotaKantor();
	disabledMhst();
	loadSistemkuliah();
	loadProdiBuka();
	loadProdiBuka2();
	
	$('#nama').upperFirstAll();

	$("#xasalsmu").xautox({strpost: "f=getSmu", targetid: "asalsmu"});


	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$("#pilihan2").attr('disabled','disabled');
});

// ajax ganti kota
function loadKota() {
	var param = new Array();
	param[0] = $("#kodepropinsi").val();
	param[1] = "<?= $r_kodekota ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekota").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax pilihan2
function loadPilihan2() {
	var param = new Array();
	param[0] = $("#pilihan1").val();
	param[1] = $("#sistemkuliah").val() ? $("#sistemkuliah").val() : null; 
	param[2] = "<?= $r_sistemkuliah ?>";
		
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optpilihan2", q: param }
				});
	
	jqxhr.done(function(data) {
		if (data != '')
			$("#pilihan2").removeAttr('disabled');
		else
			$("#pilihan2").attr('disabled','disabled');
		
		$("#pilihan2").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadKotaKantor() {
	var param = new Array();
	param[0] = $("#kodepropinsikantor").val();
	param[1] = "<?= $r_kodekotakantor ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotakantor").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKotaayah() {
	var param = new Array();
	param[0] = $("#kodepropinsiayah").val();
	param[1] = "<?= $r_kodekotaayah ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotaayah").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKotaibu() {
	var param = new Array();
	param[0] = $("#kodepropinsiibu").val();
	param[1] = "<?= $r_kodekotaibu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotaibu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}


function loadKotaLahir() {
	var param = new Array();
	param[0] = $("#kodepropinsilahir").val();
	param[1] = "<?= $r_kodekotalahir ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahir").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKotaOrtu() {
	var param = new Array();
	param[0] = $("#kodepropinsiortu").val();
	param[1] = "<?= $r_kodekotaortu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotaortu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
// ajax ganti kota
function loadKotaSMU() {
	var param = new Array();
	param[0] = $("#propinsismu").val();
	param[1] = "<?= $r_kodekotasmu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotasmu").html(data);
		loadSMU();

    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadSMU() {
	var param = new Array();
	param[0] = $("#kodekotasmu").val();
	param[1] = "<?= $r_asalsmu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optsmu", q: param }
				});
	
	jqxhr.done(function(data) {
		//getDetailSmu();
		$("#asalsmu").html(data);
		
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadKotaPonpes() {
	var param = new Array();
	param[0] = $("#propinsiponpes").val();
	param[1] = "<?= $r_kodekota ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotaponpes").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKotaPTAsal() { 
	var param = new Array();
	param[0] = $("#propinsiptasal").val();
	param[1] = "<?= $r_kodekotapt ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotapt").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKotaKontak(){
	var param = new Array();
	param[0] = $("#kodepropinsikontak").val();
	param[1] = "<?= $r_kodekotakotak?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotakotak").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

/*
function getDetailSmu(){
	
	if (document.getElementById("asalsmu").value != ''){
		var e = document.getElementById("asalsmu");
		var str = e.options[e.selectedIndex].text;

		asalsmu = str[5];
	}
	
	/*if((asalsmu == '*') || ($("#asalsmu").val() == '*')){
		document.getElementById('namasma').style.display = 'table-row';
		document.getElementById('alamatsmu').value = "";
		document.getElementById('telpsmu').value = "";
	}else{
		document.getElementById('namasma').style.display = 'none';
		var posted = "f=getDetailSmu&q[]="+$("#asalsmu").val();
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var text = text.split('#');
			document.getElementById('alamatsmu').value = text[0];
			document.getElementById('telpsmu').value = text[1];
		});
	}
}
*/
function disabledMhst(){
	if($('input[name="mhstransfer"]:checked').val() == 0){
		document.getElementById("ptasal").disabled=true;
		document.getElementById("ptjurusan").disabled=true;
		document.getElementById("ptthnlulus").disabled=true;
		document.getElementById("sksasal").disabled=true;
		document.getElementById("propinsiptasal").disabled=true;
		document.getElementById("kodekotapt").disabled=true;
		document.getElementById("negaraptasal").disabled=true;
		document.getElementById("ptipk").disabled=true;
		
		document.getElementById("ptfakultas").disabled=true;
		document.getElementById("ptthnmasuk").disabled=true;
		document.getElementById("semesterkeluar").disabled=true;
		
		
	}else{
		document.getElementById("ptasal").disabled=false;
		document.getElementById("ptjurusan").disabled=false;
		document.getElementById("ptthnlulus").disabled=false;
		document.getElementById("sksasal").disabled=false;
		document.getElementById("propinsiptasal").disabled=false;
		document.getElementById("kodekotapt").disabled=false;
		document.getElementById("negaraptasal").disabled=false;
		
		document.getElementById("ptipk").disabled=false;
		
		document.getElementById("ptfakultas").disabled=false;
		document.getElementById("ptthnmasuk").disabled=false;
		document.getElementById("semesterkeluar").disabled=false;


	}
}
 function ucfirst(str,force){
          str=force ? str.toLowerCase() : str;
          return str.replace(/(\b)([a-zA-Z])/,
                   function(firstLetter){
                      return   firstLetter.toUpperCase();
                   });
     } 

function ucwords(str,force){
  str=force ? str.toLowerCase() : str;  
  return str.replace(/(\b)([a-zA-Z])/g,
           function(firstLetter){
              return   firstLetter.toUpperCase();
           });
}
     
/* $('#nama').keyup(function(evt){

      // force: true to lower case all letter except first
      var cp_value= ucfirst($(this).val(),true) ;

      // to capitalize all words  
      //var cp_value= ucwords($(this).val(),true) ;


      $(this).val(cp_value );

   });*/
   

function loadSistemkuliah() {
	var param = new Array();
	param[0] = $("#kodekampus").val();
	param[1] = "<?= $r_kodekampus ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "sistemkuliah", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#sistemkuliah").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
} 
   
function loadProdiBuka() {
	var param = new Array();
	param[0] = $("#sistemkuliah").val();
	param[1] = "<?= $r_sistemKuliah ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "prodibuka", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#pilihan1").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
} 

function loadProdiBuka2() {
	var param = new Array();
	param[0] = $("#sistemkuliah").val();
	param[1] = "<?= $r_sistemKuliah ?>";
	param[2] = $("#pilihan1").val();
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "prodibuka", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#pilihan2").html(data);
		$("#pilihan2").prop( "disabled", false )
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
} 

</script>
