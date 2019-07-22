<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	//$conn->debug = false;
	
	// include
	require_once(Route::getModelPath('public'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$p_dbtable = "pe_pengumuman";
	$where = "idpengumuman";
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Detail Pengumuman';
	$p_tbwidth = 800;
	$p_aktivitas = 'NEWS';
	
	$p_model = mPublic;
	
	$a_data = $p_model::getDetailPengumuman($conn, $r_key);
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
<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:500px;overflow:auto">
<form name="pageformdet" id="pageformdet" method="post">
	<div id="wrapper" style="padding-left:20px">
		<div class="SideItem" style="width:70%;">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/NEWS.png" onerror="loadDefaultActImg(this)"> Pengumuman
			</div>
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideSubTitle" colspan="2">Daftar Pengumuman</td>
				</tr>
				<tr>
					<td align="right" colspan="2">Posting : <?= CStr::formatDateInd($a_data['tglmulai']) ?></td>
				</tr>
				<tr>
					<td colspan="2">
					<img src="<?= Route::navAddress('img_datathumb&type=pengumuman&id='.$a_data['idpengumuman']) ?>">
					<div class="Break"></div>
					<div class="SideTitle"><?= $a_data['judulpengumuman'] ?></div>
					<div class="Break"></div>
					<div class="NewsContent"><?= $a_data['isipengumuman'] ?></div>
					</td>
				</tr>
			</table>
		</div>
	</div>
<input type="hidden" name="actdet" id="actdet">
<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey ?>">
<input type="hidden" name="parent" id="parent" value="<?= $r_parent ?>">

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
