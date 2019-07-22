<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	$r_parent = CStr::removeSpecial($_REQUEST['parent']);
	$p_dbtable = "pa_formsubyektifdet";
	$where = "kodeformsubyektif,nouraian";
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Detail Form Penilaian';
	$p_tbwidth = 700;
	$p_aktivitas = 'Detail Penilaian';
	
	$p_model = mPa;
	
	//struktur view
	$a_inputdet = array();	
	$a_inputdet[] = array('kolom' => 'nomor', 'label' => 'Nomor', 'maxlength' => 3, 'size' => 3, 'notnull' => true);
	$a_inputdet[] = array('kolom' => 'uraian', 'label' => 'Ukuran Penilaian', 'type' => 'A', 'maxlength' => 1000, 'cols' => 60, 'rows' => '4', 'notnull' => true);
	$a_inputdet[] = array('kolom' => 'isdinilai', 'label' => 'Pertanyaan ?', 'type' => 'S', 'option' => SDM::getValid(), 'notnull' => true);
	$a_inputdet[] = array('kolom' => 'parentnouraian', 'label' => 'Nomor','type' => H);
	
	$row = $p_model::getDataEdit($conn,$a_inputdet,($r_key.'|'.$r_subkey),$post,$p_dbtable,$where);
		
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	if (empty($r_parent))
		$r_parent = Page::getDataValue($row,'parentnouraian');
	
	if (!empty($r_parent))
		$a_parent = $p_model::getSoalPA($conn, $r_key, $r_parent);
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
<? require_once('inc_databuttonpop.php'); ?>
<div class="Break"></div>
<table border="0" width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" class="GridStyle" align="center">
	<tr>
		<td colspan="4" class="DataBG" style="height:25px;border-top-left-radius:4px;border-top-right-radius:4px;border:none;"><?= $p_title ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'nomor') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'nomor') ?></td>
	</tr>
	<? if (!empty($r_parent)){
	?>
	<tr>
		<td class="LeftColumnBG">Parent</td>
		<td class="RightColumnBG"><?= $a_parent['uraian']; ?></td>
	</tr>
	<? } ?>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'uraian') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'uraian') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'isdinilai') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'isdinilai') ?></td>
	</tr>
</table>

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
