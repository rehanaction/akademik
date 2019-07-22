<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_perolehan');
	
	//$c_insert = $a_auth['caninsert'];
	$c_insert = false;
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	//$conn->debug = true;
	
	// include
	require_once(Route::getModelPath('perolehanheader'));
	require_once(Route::getModelPath('perolehan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_keydet = CStr::removeSpecial($_REQUEST['keydet']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Detail Barang';
	$p_tbwidth = 600;
	$p_aktivitas = 'detail barang';
	
	$p_model = mPerolehan;
	
	//init
	$isro = false;
	
	if(!empty($r_key)){
	    $a_mdata = mPerolehan::getMData($conn,$r_keydet);
	    if($a_mdata['isverify'] == '1'){

			$isro = true;
	        $c_delete = false;
        }

	}
	
	$a_kondisi = mCombo::kondisi($conn);
	$a_dasarharga = mCombo::dasarharga($conn);
	$a_merk = mCombo::merk($conn);
	$a_coa = mCombo::coa($conn);
	
    //struktur view
	$a_input = mPerolehan::getInputAttr(array('isro' => $isro, 'kondisi' => $a_kondisi, 'dasarharga' => $a_dasarharga, 'merk' => $a_merk, 'coa' => $a_coa));

	$row = mPerolehan::getDataEdit($conn,$a_input,$r_keydet,$post);
   
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
<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:500px;overflow:auto">
<form name="pageformdet" id="pageformdet" method="post">
<? require_once('inc_databuttondet.php'); ?>
<div class="Break"></div>
<table border="0" width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" class="GridStyle" align="center">
	<tr>
		<td colspan="2" class="DataBG"><?= $p_title ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="125"><?= Page::getDataLabel($row,'barang') ?></td>
		<td class="RightColumnBG">
		    <?= Page::getDataInput($row,'barang') ?>
		    <?= Page::getDataInput($row,'idbarang1') ?>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'qty') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'qty') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'harga') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'harga') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'total') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'total') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'iddasarharga') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'iddasarharga') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idkondisi') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'idkondisi') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idcoa') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'idcoa') ?></td>
	</tr>
	<tr>
		<td colspan="2" class="DataBG">Keterangan Tambahan</td>
	</tr>
    <tr>
        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglgaransi') ?></td>
        <td class="RightColumnBG">
            Tanggal:&nbsp;<?= Page::getDataInput($row,'tglgaransi') ?>&nbsp;&nbsp;/&nbsp;&nbsp;
            Km:&nbsp;<?= Page::getDataInput($row,'kmgaransi') ?>
        </td>
    </tr>
    <tr>
        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'thnprod') ?></td>
        <td class="RightColumnBG"><?= Page::getDataInput($row,'thnprod') ?></td>
    </tr>
    <tr>
        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'merk') ?></td>
        <td class="RightColumnBG">
            <?= Page::getDataInput($row,'merk') ?> &nbsp;&nbsp;,
            <?= Page::getDataInput($row,'ukuran') ?>
        </td>
    </tr>
    <tr>
        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'spesifikasi') ?></td>
        <td class="RightColumnBG"><?= Page::getDataInput($row,'spesifikasi') ?></td>
    </tr>	
</table>

<input type="hidden" name="actdet" id="actdet">
<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
<input type="hidden" name="keydet" id="keydet" value="<?= $r_keydet ?>">

</form>
</div>

<script type="text/javascript">
var required = "<?= @implode(',',$a_required) ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";

$(document).ready(function() {
	initEditDet(<?= empty($r_keydet) ? true : false ?>);
	
	// autocomplete
	$("#barang").xautox({strpost: "f=acxbaranginv", targetid: "idbarang1"});
	
	
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
    if(confirm('Anda Yakin akan menghapus data ini ?')){
        $("#actdet").val('deletedet');
        $('#pageformdet').submit();
    }
}


</script>
</body>
</html>
