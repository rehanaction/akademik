<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_mutasi');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$conn->debug = false;
	
	// include
	require_once(Route::getModelPath('mutasi'));
	require_once(Route::getModelPath('mutasidetail'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_keydet = CStr::removeSpecial($_REQUEST['keydet']);

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Detail Mutasi';
	$p_tbwidth = 700;
	$p_aktivitas = 'Detail Mutasi';
	
	$p_model = mMutasiDetail;
	
	//init
	$isro = false;
	if(!empty($r_key)){
	    $a_mdata = mMutasi::getMData($conn,$r_key);

    	$seri = $p_model::getSeri($conn,$a_mdata,$r_key);

	    if($a_mdata['isverify'] == '1'){
	        $isro = true;
	        $c_delete = false;
	    }
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/jquery.autocomplete.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:500px;overflow:auto">
<form name="pageformdet" id="pageformdet" method="post">
<? require_once('inc_databuttondet.php'); ?>
<div class="Break"></div>
<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
    <tr>
        <td colspan="6" class="DataBG"><?= $p_title ?></td>
    </tr>
    <tr>
        <th width="30">&nbsp;</th>
        <th width="50">No. Seri</th>
        <th>Barang</th>
        <th width="80">Merk</th>
        <th width="150">Spesifikasi</th>
        <th width="80">Tgl. Perolehan</th>
    </tr>
    <?
    if(count($seri) > 0){
        foreach($seri as $id => $val){
    ?>
    <tr valign="top">
        <td align="center"><input type="checkbox" name="seri[]" value="<?= $val['idseri'] ?>"></td>
        <td><?= Aset::formatNoSeri($val['noseri']) ?></td>
        <td><?= $val['barang'] ?></td>
        <td><?= $val['merk'] ?></td>
        <td><?= $val['spesifikasi'] ?></td>
        <td><?= CStr::formatDateInd($val['tglperolehan'],false) ?></td>
    </tr>
    <?  }
    }else{
    ?>
    <tr>
        <td colspan="6" align="center"><b>-- Data tidak ditemukan --</b></td>
    </tr>
    <?
    }
    ?>
</table>

<input type="hidden" name="actdet" id="actdet">
<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
<input type="hidden" name="keydet" id="keydet" value="<?= $r_keydet ?>">

</form>
</div>

<script type="text/javascript" src="scripts/jquery.autocomplete.js"></script>
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
	else if(document.getElementById("keydet"))
		if(document.getElementById("keydet").value == "")
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
