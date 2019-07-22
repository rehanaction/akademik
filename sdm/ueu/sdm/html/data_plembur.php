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
	require_once(Route::getModelPath('presensi'));
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
		$c_delete = $a_auth['candelete'];
	}
		
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
			
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	// properti halaman
	$p_title = 'Data Surat Lembur';
	$p_tbwidth = 800;
	$p_aktivitas = 'TIME';
	$p_listpage = Route::getListPage();
	
	$p_model = mPresensi;
	$p_dbtable = "pe_suratlembur";
	$where = 'idsuratlembur';
	
	$p_atas = $p_model::getAtasanLembur($conn,$r_key);
	
	//struktur view
	$a_input = array();
	if(empty($r_subkey)){
		$a_input[] = array('kolom' => 'pejabat', 'label' => 'Pejabat Atasan', 'maxlength' => 255, 'size' => 60, 'notnull' => true, 'default' => $p_atas['pejabat']);
		$a_input[] = array('kolom' => 'pejabatatasan', 'type' => 'H', 'default' => $p_atas['idpegawai']);
		$a_input[] = array('kolom' => 'idjstruktural', 'type' => 'H', 'default' => $p_atas['idjstruktural']);
	}else{
		$a_input[] = array('kolom' => 'pejabat', 'label' => 'Pejabat Atasan', 'maxlength' => 255, 'size' => 60, 'notnull' => true);
		$a_input[] = array('kolom' => 'pejabatatasan', 'type' => 'H');
		$a_input[] = array('kolom' => 'idjstruktural', 'type' => 'H');
	}
	$a_input[] = array('kolom' => 'namaunit', 'label' => 'Unit Lembur', 'maxlength' => 255, 'size' => 60, 'notnull' => true);
	$a_input[] = array('kolom' => 'idunit', 'type' => 'H');
	$a_input[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'maxlength' => 60, 'size' => '60');
	$a_input[] = array('kolom' => 'tglpenugasan', 'label' => 'Tgl. Penugasan', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'tgllembur', 'label' => 'Tgl. Lembur', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'jamawal', 'label' => 'Jam Lembur', 'maxlength' => 4, 'size' => 4, 'notnull' => true);
	$a_input[] = array('kolom' => 'jamakhir', 'label' => 'Jam Selesai', 'maxlength' => 4, 'size' => 4, 'notnull' => true);

	if($c_valid)
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	else
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['jamawal'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jamawal']));
		$record['jamakhir'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jamakhir']));
		
		$record['idpegawai'] = $r_key;
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
				
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	
	$sql = $p_model::getDataEditLembur($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
		
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
			
		//pengecekan hak akses utk pegawai ybs, bila sudah valid
		if($t_row['id'] == 'isvalid'){
			$isvalid = $t_row['value'];
			if($isvalid == 'Ya' and $r_self){
				$c_edit = false;
				$c_delete = false;
			}
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
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/jquery.autocomplete.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foreditx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
						require_once('inc_databuttonajax.php');
					
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
				
					if(empty($p_fatalerr)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'pejabat');?></td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'pejabat');?>
								<?= Page::getDataInput($row,'pejabatatasan');?>
								<?= Page::getDataInput($row,'idjstruktural');?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaunit');?></td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'namaunit');?>
								<?= Page::getDataInput($row,'idunit');?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'lokasi');?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'lokasi');?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglpenugasan');?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglpenugasan');?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tgllembur');?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tgllembur');?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jamawal');?></td>
							<td class="RightColumnBG">
								<span id="show"><?= $row[7]['value'] ?></span>
								<span id="edit" style="display:none">
									<input type="text" size="3" maxlength="4" class="ControlStyle" value="<?= str_pad($row[7]['realvalue'],4,'0',STR_PAD_LEFT)?>" id="jamawal" name="jamawal">
								</span>
								s/d
								<span id="show"><?= $row[8]['value'] ?></span>
								<span id="edit" style="display:none">
									<input type="text" size="3" maxlength="4" class="ControlStyle" value="<?= str_pad($row[8]['realvalue'],4,'0',STR_PAD_LEFT)?>" id="jamakhir" name="jamakhir">
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<?	} ?>
			</form>
		</td>
	</tr>
</table>

<script type="text/javascript" src="scripts/jquery.autocomplete.js"></script>
<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {

	initEdit(<?= empty($post) ? false : true ?>);
	
	$("#jamawal").mask("12:34");
	$("#jamakhir").mask("12:34");
	
	// autocomplete
    $('#pejabat').autocomplete(
	    ajaxpage, 
	    {
		    parse: function(data){ 
			    var parsed = [];
			    for (var i=0; i < data.length; i++) {
				    parsed[i] = {
					    data: data[i],
					    value: data[i].pejabat // nama field yang dicari
				    };
			    }
			    return parsed;
		    },
		    formatItem: function(data,i,max){
			    var str = '';
			    str += '<div class="search_content">';
			    str += data.pejabat +'<br>';
			    str += '</div>';
			    return str;
		    },
		    extraParams: {
		        f:'acpejabatatasan'
		    },
		    width: 400,
		    dataType: 'json'
	    })
	    .result(
		    function(event,data,formated){
                $('#pejabat').val(data.pejabat).focus();
                $('#pejabatatasan').val(data.idpegawai);
                $('#idjstruktural').val(data.idjstruktural);
		    }
	    ).focus();
	    
	$('#namaunit').autocomplete(
	    ajaxpage, 
	    {
		    parse: function(data){ 
			    var parsed = [];
			    for (var i=0; i < data.length; i++) {
				    parsed[i] = {
					    data: data[i],
					    value: data[i].namaunit // nama field yang dicari
				    };
			    }
			    return parsed;
		    },
		    formatItem: function(data,i,max){
			    var str = '';
			    str += '<div class="search_content">';
			    str += data.namaunit +'<br>';
			    str += '</div>';
			    return str;
		    },
		    extraParams: {
		        f:'acunit'
		    },
		    width: 400,
		    dataType: 'json'
	    })
	    .result(
		    function(event,data,formated){
                $('#namaunit').val(data.namaunit).focus();
                $('#idunit').val(data.idunit);
		    }
	    ).focus();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});


function cekJam(){
	var jawal,jakhir,err=false;
	formatjam = /^[0-1][0-9]|2[0-4]:[0-5][0-9]/;
	
	jawal=$("#jamawal").val();
	jakhir=$("#jamakhir").val();
	
	jamawal=jawal.substring(0,2);
	menitawal=jawal.substring(3,5);
	jamakhir=jakhir.substring(0,2);
	menitakhir=jakhir.substring(3,5);
	
	if(jamawal=='24' && menitawal!='00'){
		jawal='';
	}
	else {
		if(jawal.match(formatjam))
			jawal = jawal;
		else
			jawal='';
	}
	if(jamakhir=='24' && menitakhir!='00'){
		jakhir='';
	}
	else{
		if(jakhir.match(formatjam))
			jakhir = jakhir;
		else
			jakhir='';
	}
	
	if(jawal == ''){
		doHighlight(document.getElementById("jamawal"));
		alert("Format Jam tidak sesuai");
		err=true;	
	}
	if(jakhir == ''){
		doHighlight(document.getElementById("jamakhir"));
		alert("Format Jam tidak sesuai");
		err=true;	
	}
	
	return err;
}

function goSave() {
	var err;
	err = cekJam();

	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass && !err) {
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

</script>
</body>
</html>

