<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	$p_dbtable = "pe_suratlembur";
	$where = "idsuratlembur";
	
	if((empty($r_subkey) and $c_insert) or (!empty($r_subkey) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Detail Lembur';
	$p_tbwidth = 700;
	$p_aktivitas = 'TIME';
	
	$p_model = mPresensi;
	
	//struktur view
	$a_inputdet = array();	
	$a_inputdet[] = array('kolom' => 'pegawai', 'label' => 'Nama Pegawai', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_inputdet[] = array('kolom' => 'idpegawai','type' => 'H');
	$a_inputdet[] = array('kolom' => 'tgllembur', 'label' => 'Tgl. Lembur', 'type' => 'D', 'notnull' => true);
	$a_inputdet[] = array('kolom' => 'jamawal', 'label' => 'Jam Lembur', 'maxlength' => 4, 'size' => 4);
	$a_inputdet[] = array('kolom' => 'jamakhir', 'maxlength' => 4, 'size' => 4);
	$a_inputdet[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'type' => 'A', 'maxlength' => 500, 'cols' => 50, 'rows' => 2);
	
	$sql = $p_model::getDataLemburDetail($conn,$r_subkey);
	$row = $p_model::getDataEdit($conn,$a_inputdet,$r_subkey,$post,$p_dbtable,$where,$sql);
		
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:500px;overflow:auto">
<form name="pageformdet" id="pageformdet" method="post">
<? require_once('inc_databuttonpop.php'); ?>
<div class="Break"></div>
<table border="0" width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" class="GridStyle" align="center">
	<tr>
		<td colspan="4" class="DataBG" style="height:25px;border-top-left-radius:4px;border-top-right-radius:4px;border:none;"><?= $p_title ?></td>
	</tr>
	<tr>
		<td width="150px" class="LeftColumnBG"><?= Page::getDataLabel($row,'pegawai') ?></td>
		<td class="RightColumnBG">
			<?= Page::getDataInput($row,'pegawai') ?>
			<?= Page::getDataInput($row,'idpegawai') ?>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tgllembur') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'tgllembur') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'jamawal') ?></td>
		<td class="RightColumnBG">
			<?= Page::getDataInput($row,'jamawal') ?> s/d <?= Page::getDataInput($row,'jamakhir') ?>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'lokasi') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'lokasi') ?></td>
	</tr>
</table>

<input type="hidden" name="actdet" id="actdet">
<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey ?>">

</form>
</div>

<script type="text/javascript">
var required = "<?= @implode(',',$a_required) ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";

$(document).ready(function() {
	initEditDet(<?= empty($r_keydet) ? true : false ?>);
	
	//autocomplete
	$("#pegawai").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai"});
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
    var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
        $("#actdet").val('savedet');
        $('#pageformdet').submit();
    }
}

function goDeleteDet(){
    $("#actdet").val('deletedet');
    $('#pageformdet').submit();
}
</script>
</body>
</html>
