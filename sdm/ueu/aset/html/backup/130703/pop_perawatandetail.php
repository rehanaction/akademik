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
	$p_aktivitas = 'detail perawatan';
	
	$p_model = mRawatDetail;
	
	//user
    $r_role = Modul::getRole();
    if(in_array($r_role, Aset::getRoleRM()))
        $c_rawat = true;
	
	//init
	$isro = false;
	if(!empty($r_key)){
	    $a_mdata = mRawat::getMData($conn,$r_key);
	    $r_isok1 = $a_mdata['isok1'];
	    $r_idunit = $a_mdata['idunit'];
	    $r_idbarang = $a_mdata['idbarang1'];

	    if(!empty($r_idbarang))
	        $r_namabarang = mBarang::getNamaBarang($conn, $r_idbarang);
	    
	    if($r_isverify == '1'){
	        $isro = true;
	    }
	}

	$a_jenisrawat = mCombo::jenisrawat($conn);
	
	//struktur view
	$a_input = $p_model::getInputAttr(array('isro' => $isro, 'a_jenisrawat' => $a_jenisrawat));
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_keydet,$post);

	//ambil data
	$i_noseri = Page::getDataInput($row,'noseri');
	$i_idjenisrawat = Page::getDataInput($row,'idjenisrawat');
	$i_keluhan = Page::getDataInput($row,'keluhan');
	if(!empty($r_keydet)){
        $a_mdata = mRawat::getMData($conn,$r_key);
        
        if($a_mdata['isverify'] == '1'){
            $i_noseri = Page::getDataValue($row,'noseri');
            
            $r_idjenisrawat = Page::getDataValue($row,'idjenisrawat');
        	$i_idjenisrawat = $a_jenisrawat[$r_idjenisrawat].'<input type="hidden" name="idjenisrawat" value="'.$r_idjenisrawat.'">';

            $r_keluhan = Page::getDataValue($row,'keluhan');
        	$i_keluhan = $r_keluhan.'<input type="hidden" name="keluhan" value="'.$r_keluhan.'">';
        }
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
		<td class="LeftColumnBG" width="125">Barang</td>
		<td class="RightColumnBG"><?= $r_idbarang.' - '.$r_namabarang ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'noseri') ?></td>
		<td class="RightColumnBG">
		    <?= $i_noseri; //Page::getDataInput($row,'noseri') ?>
    	    <?= Page::getDataInput($row,'idseri') ?>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'spesifikasi') ?></td>
		<td class="RightColumnBG"><span id="span_spesifikasi"><?= Page::getDataInput($row,'spesifikasi') ?></span></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglperolehan') ?></td>
		<td class="RightColumnBG"><span id="span_tglperolehan"><?= Page::getDataInput($row,'tglperolehan') ?></span></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglgaransi') ?></td>
		<td class="RightColumnBG"><span id="span_tglgaransi"><?= Page::getDataInput($row,'tglgaransi') ?></span></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idjenisrawat') ?></td>
		<td class="RightColumnBG"><?= $i_idjenisrawat; //Page::getDataInput($row,'idjenisrawat') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'keluhan') ?></td>
		<td class="RightColumnBG"><?= $i_keluhan; //Page::getDataInput($row,'keluhan') ?></td>
	</tr>
	<?  if($c_rawat){ ?>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'biaya') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'biaya') ?></td>
	</tr>
	<?  } ?>
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
    $('#noseri').autocomplete(
	    ajaxpage, 
	    {
		    parse: function(data){ 
			    var parsed = [];
			    for (var i=0; i < data.length; i++) {
				    parsed[i] = {
					    data: data[i],
					    value: data[i].noseri // nama field yang dicari
				    };
			    }
			    return parsed;
		    },
		    formatItem: function(data,i,max){
			    var str = '';
			    str += '<div class="search_content">';
			    str += data.noseri +'<br>';
			    str += data.merk +'<br>';
			    str += data.spesifikasi +'<br>';
			    str += '</div>';
			    return str;
		    },
		    extraParams: {
		        f:'acnoseri',
		        idunit: '<?= $r_idunit ?>',
		        idbarang1: '<?= $r_idbarang ?>'
		    },
		    width: 500,
		    dataType: 'json'
	    })
	    .result(
		    function(event,data,formated){
                $('#noseri').val(data.noseri).focus();
                $('#idseri').val(data.idseri);
                $('#span_merk').html(data.merk);
                $('#span_spesifikasi').html(data.spesifikasi);
                $('#span_tglperolehan').html(data.tglperolehan);
                $('#span_tglgaransi').html(data.tglgaransi);
		    }
	    ).focus();
	    
    $('#namasupplier').autocomplete(
	    ajaxpage, 
	    {
		    parse: function(data){ 
			    var parsed = [];
			    for (var i=0; i < data.length; i++) {
				    parsed[i] = {
					    data: data[i],
					    value: data[i].namasupplier // nama field yang dicari
				    };
			    }
			    return parsed;
		    },
		    formatItem: function(data,i,max){
			    var str = '';
			    str += '<div class="search_content">';
			    str += data.namasupplier +'<br>';
			    str += '</div>';
			    return str;
		    },
		    extraParams: {
		        f:'acsupplier'
		    },
		    width: 500,
		    dataType: 'json'
	    })
	    .result(
		    function(event,data,formated){
                $('#namasupplier').val(data.namasupplier).focus();
                $('#idsupplier').val(data.idsupplier);
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
