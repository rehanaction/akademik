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
	$r_date = CStr::removeSpecial($_REQUEST['subdate']);
	$r_keydet = $r_subkey;
	
	$p_dbtable = "pe_rwtshiftdet";
	$where = "kodeshift,tglshift";
	
	if((empty($r_subkey) and $c_insert) or (!empty($r_subkey) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Detail Shift Bulanan '.CStr::formatDateInd($r_date);
	$p_tbwidth = 700;
	$p_aktivitas = 'TIME';
	
	$p_model = mPresensi;
	
	//struktur view
	$a_inputdet = array();	
	$a_inputdet[] = array('kolom' => 'jamdatang', 'label' => 'Jam', 'maxlength' => 4, 'size' => 4, 'notnull' => true);
	$a_inputdet[] = array('kolom' => 'jampulang', 'label' => 'Jam Pulang', 'maxlength' => 4, 'size' => 4, 'notnull' => true);
	$a_inputdet[] = array('kolom' => 'kodeshift', 'label' => 'Nomor','type' => H);
	
	$row = $p_model::getDataEdit($conn,$a_inputdet,($r_subkey.'|'.$r_date),$post,$p_dbtable,$where);
		
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
</head>
<body>
<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:200px;overflow:auto">
<form name="pageformdet" id="pageformdet" method="post">
<? require_once('inc_databuttonpop.php'); ?>
<div class="Break"></div>
<table border="0" width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" class="GridStyle" align="center">
	<tr>
		<td colspan="4" class="DataBG" style="height:25px;border-top-left-radius:4px;border-top-right-radius:4px;border:none;"><?= $p_title ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="150px"><?= Page::getDataLabel($row,'jamdatang') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'jamdatang') ?> s/d <?= Page::getDataInput($row,'jampulang') ?> Contoh : 08:00-09:00 menjadi 0800 - 0900
		<input type="hidden" name="tglshift" value="<?= $r_date; ?>" />
		</td>
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
