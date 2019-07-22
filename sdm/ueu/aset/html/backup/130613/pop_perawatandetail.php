<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_perawatan');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$conn->debug = false;
	
	// include
	require_once(Route::getModelPath('rawat'));
	require_once(Route::getModelPath('rawatdetail'));
	require_once(Route::getModelPath('barang'));
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
	$p_title = 'Data Detail Perawatan';
	$p_tbwidth = 500;
	$p_aktivitas = 'Detail Perawatan';
	
	$p_model = mRawatDetail;
	
	//init
	$isro = false;
	if(!empty($r_key)){
	    $a_acpt = mRawat::getDataAcpt($conn,$r_key);
	    $r_isok1 = $a_acpt['isok1'];
	    if($r_isok1 == '1'){
	        $isro = true;
	    }
	}
	
	//struktur view
	$a_input = $p_model::getInputAttr(array('isro' => $isro));
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_keydet,$post);
    //print_r($row);
	//ambil data
	if(!empty($r_keydet)){
	    /*$r_idseri = Page::getDataValue($row,'idseri');
	    if(!empty($r_idseri)){
	        require_once(Route::getModelPath('seri'));
    	    $seri = mSeri::getData($conn, $r_idseri);
	    }*/
	}
	
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
	<link href="style/jquery.autocomplete.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:500px;overflow:auto">
<form name="pageformdet" id="pageformdet" method="post">
<? require_once('inc_databuttondet.php'); ?>
<div class="Break"></div>
<table border="0" width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" class="GridStyle" align="center">
	<tr>
		<td colspan="4" class="DataBG" style="height:25px;border-top-left-radius:4px;border-top-right-radius:4px;border:none;"><?= $p_title ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="125"><?= Page::getDataLabel($row,'labelbarang') ?></td>
		<td class="RightColumnBG">
			<?= Page::getDataInput($row,'labelbarang') ?>
		    <?= Page::getDataInput($row,'idseri') ?>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'noseri') ?></td>
		<td class="RightColumnBG">
		    <span id="span_noseri"><?= Page::getDataInput($row,'noseri') ?></span>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'merk') ?></td>
		<td class="RightColumnBG">
		    <span id="span_merk"><?= Page::getDataInput($row,'merk') ?></span>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'spesifikasi') ?></td>
		<td class="RightColumnBG">
		    <span id="span_spesifikasi"><?= Page::getDataInput($row,'spesifikasi') ?></span>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'biaya') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'biaya') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'catatan') ?></td>
	</tr>
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
	
	// autocomplete
    $('#labelbarang').autocomplete(
	    ajaxpage, 
	    {
		    parse: function(data){ 
			    var parsed = [];
			    for (var i=0; i < data.length; i++) {
				    parsed[i] = {
					    data: data[i],
					    value: data[i].barang // nama field yang dicari
				    };
			    }
			    return parsed;
		    },
		    formatItem: function(data,i,max){
			    var str = '';
			    str += '<div class="search_content">';
			    str += data.idbarang1 + '.' + data.noseri + ' - ' + data.namabarang +'<br>';
			    str += data.merk +'<br>';
			    str += data.spesifikasi +'<br>';
			    str += '</div>';
			    return str;
		    },
		    extraParams: {
		        f:'acseri'
		    },
		    width: 500,
		    dataType: 'json'
	    })
	    .result(
		    function(event,data,formated){
                $('#labelbarang').val(data.idbarang1+' - '+data.namabarang).focus();
                $('#idseri').val(data.idseri);
                $('#span_noseri').html(data.noseri);
                $('#span_merk').html(data.merk);
                $('#span_spesifikasi').html(data.spesifikasi);
		    }
	    ).focus();
	    

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
