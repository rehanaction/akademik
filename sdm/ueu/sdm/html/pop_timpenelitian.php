<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_readlist = true;		
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('pengembangan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_edit = $a_auth['canupdate'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
		
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	$r_keydet = CStr::removeSpecial($_REQUEST['keydet']);
	$p_dbtable = "pe_timpenelitian";
	$where = "idpenelitian,notimpenelitian";
	$p_listpage = 'data_gpenelitian';
	
	// properti halaman
	$p_title = 'Data Tim Penelitian';
	$p_tbwidth = 600;
	$p_aktivitas = 'DATA';
	
	$p_model = mPengembangan;
	
	//struktur view
	$a_inputdet = array();	
	$a_inputdet[] = array('kolom' => 'statustim', 'label' => 'Jenis Tim', 'type' => 'R', 'option' => $p_model::statusTim(), 'default' => 'P', 'add' => 'onchange="changeJenis(this.value)"');
	$a_inputdet[] = array('kolom' => 'pegawai', 'label' => 'Pegawai', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_inputdet[] = array('kolom' => 'idpegawai', 'type' => 'H');
	$a_inputdet[] = array('kolom' => 'namatim', 'label' => 'Personil Luar', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_inputdet[] = array('kolom' => 'timkontributorke', 'label' => 'Kontributor Ke', 'type' => 'S', 'option' => $p_model::kontributor());
	
	$sql = $p_model::getDataEditTimPenelitian($conn,$r_keydet);
	$row = $p_model::getDataEdit($conn,$a_inputdet,($r_subkey.'|'.$r_keydet),$post,'','',$sql);
		
?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>
<body>
	<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:250px;overflow:auto">
		<form name="pageformdet" id="pageformdet" method="post" action=""<?= Route::navAddress($p_listpage) ?>"">				
			<? require_once('inc_databuttonpop.php'); ?>
			<div class="Break"></div>
						
			<table class="box-content" style="padding:0px" width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" align="center">
				<tr>
					<td colspan="4" class="DataBG" style="border:none;height:25px;"><?= $p_title ?></td>
				</tr>
				<tr>
					<td class="LeftColumnBG" width="150px" style="white-space:nowrap"><?= Page::getDataLabel($row,'statustim') ?></td>
					<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'statustim') ?></td>
				</tr>
				<tr id="tr_peg">
					<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pegawai') ?></td>
					<td  class="RightColumnBG" colspan="3">
						<?= Page::getDataInput($row,'pegawai') ?>
						<?= Page::getDataInput($row,'idpegawai') ?>	
						<span id="edit" style="display:none">
							<img id="imgpeg_c" src="images/green.gif">
							<img id="imgpeg_u" src="images/red.gif" style="display:none">
						</span>
					</td>
				</tr>
				<tr id="tr_luar" style="display:none">
					<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namatim') ?></td>
					<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'namatim') ?></td>
				</tr>
				<tr>
					<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'timkontributorke') ?></td>
					<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'timkontributorke') ?></td>
				</tr>
			</table>	
			
			<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey ?>">
			<input type="hidden" name="keydet" id="keydet" value="<?= $r_keydet ?>">
		</form>
	</div>
	
<script type="text/javascript">
var required;
var ajaxpage = "<?= Route::navAddress('ajax') ?>";

$(document).ready(function() {
	initEditDet(<?= empty($r_keydet) ? true : false ?>);
	changeJenis($("input[name='statustim']::checked").val());
	
	//autocomplete
	$("#pegawai").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgpeg", imgavail: true});
});

function initEditDet(isedit) {
	if(!isedit)
		isedit = false;
	
	if(isedit)
		goEditDet();
	else if(document.getElementById("subkey"))
		if(document.getElementById("subkey").value == "")
			goEditDet();
}

function goEditDet() {
	$("#pageformdet").find("[id='show']").hide();
	$("#pageformdet").find("[id='edit']").show();
	
	$("#pageformdet").find("#be_editdet").hide();
	$("#pageformdet").find("#be_savedet,#be_undodet").show();
}

function goUndoDet(){
    $('.close').click();    
}

function goSaveDet(){
	sts = $("input[name='statustim']::checked").val();
    var pass = true;
    var err = false;
	if(sts == 'P' && $("#idpegawai").val() == ''){
		doHighlight(document.getElementById("pegawai"));
		alert("Silahkan memilih nama pegawai terlebih dahulu");
		pass = false;
	}
	
	if(pass) {
        kont = $("#timkontributorke").val();
		namakont = document.getElementById('timkontributorke').options[document.getElementById('timkontributorke').selectedIndex].text;
        
        if(sts == 'L'){
			nama = $("#namatim").val();
			id = '';
			jenis = 'Personil Luar';
		}else{
			nama = $("#pegawai").val();
			id = $("#idpegawai").val();
			jenis = 'Pegawai';
		}
		
		$("[id='kontributorke']").each(function() {
			var tim = $(this).val().split('::');
			if(kont == $(this).val() || kont == tim[0]){
				alert("Kontributor ke "+kont+" sudah ada!");
				err = true;
			}
		});
		
		if(sts == 'P'){
			if('<?= $r_key?>' == id){
				alert("Ma'af, isian ini khusus untuk anggota tim, bukan untuk anda");
				err = true;
			}		
		
			$("[name='"+sts+"[]']").each(function() {
				var tim = $(this).val().split('::');
				if(id == tim[3]){
					alert("Nama anggota sudah ada!");
					err = true;
				}
			});
		}
		
		if(!err){
			baris = '<tr>' + "\n" +
					'<td>' + "\n" +
						'<font color="green">' + nama + "</font>\n" +
						'<input type="hidden" id="kontributorke" name="'+sts+'[]" value="'+kont+'::'+nama+'::'+sts+'::'+id+'">' + "\n" +
					'</td>' + "\n" +
					'<td align="center">' + "\n" +
						'<font color="green">' + namakont + "</font>\n" +
					'</td>' + "\n" +
					'<td align="center">' + "\n" +
						'<font color="green">' + jenis + "</font>\n" +
					'</td>' + "\n" +
					'<td align="center">' + "\n" +
						'<span id="show" style="display:none"></span>' + "\n" +
						'<span id="edit">' + "\n" +
							'<img src="images/delete.png" style="cursor:pointer" onclick="deleteBaris(this)" title="Hapus Tim">' + "\n" +
						'</span>' + "\n" +
					'</td>' + "\n" +
				'</tr>';
				
			$("#tbl_tim").append(baris);
			$('.close').click();
		}
    }    
}

function goDeleteDet(){
    $("#actdet").val('deletedet');
    $('#pageformdet').submit();
}

function changeJenis(jenis){
	if(jenis == 'L'){
		$("#tr_luar").show();
		$("#tr_peg").hide();
		required ='namatim';
	}else{
		$("#tr_luar").hide();
		$("#tr_peg").show();
		required = 'pegawai';
	}
}

</script>
</body>
</html>
