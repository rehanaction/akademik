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
	require_once(Route::getModelPath('cuti'));
	require_once(Route::getModelPath('email'));
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg=true;
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
	$p_title = 'Data Permohonan Cuti';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mCuti;
	$p_dbtable = "pe_rwtcuti";
	$where = 'nourutcuti';
	
	//struktur view
	$a_input = array();
	if(empty($r_subkey))
		$a_input[] = array('kolom' => 'nourutsurat', 'label' => 'No. Urut Cuti', 'readonly' => true, 'default' => '<i>Digenerate otomatis</i>');
	else
		$a_input[] = array('kolom' => 'nourutsurat', 'label' => 'No. Urut Cuti', 'readonly' => true);
	$a_input[] = array('kolom' => 'idjeniscuti', 'label' => 'Jenis Cuti', 'type' => 'S', 'add' => 'onchange="getSisaCuti(this)"', 'option' => $p_model::jenisCuti($conn));
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'default' => date('Y-m-d'), 'notnull' => true);
	$a_input[] = array('kolom' => 'alasancuti', 'label' => 'Alasan Cuti', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255, 'notnull' => true);
	$a_input[] = array('kolom' => 'alamatselamacuti', 'label' => 'Alamat Selama Cuti', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'telpselamacuti', 'label' => 'Telp Selama Cuti', 'maxlength' => 30, 'size' => 30);		
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'statususulan', 'label' => 'Status', 'type' => 'R', 'readonly' => true, 'option' => $p_model::statusAjukanCuti());
	
	if($c_valid){
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	}else{
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);		
	}
	
	//detail cuti	
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tgl. Mulai', 'type' => 'D', 'add' => 'onchange="getSisaCuti(this)" onkeyup="getSisaCuti(this)"', 'notnull' => true);
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tgl. Selesai', 'type' => 'D', 'add' => 'onchange="getSisaCuti(this)" onkeyup="getSisaCuti(this)"', 'notnull' => true);
		
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		//get atasan dan unit,jabatan
		$us = $p_model::getUnitJabAtas($conn,$r_key);
		
		$record['nippejabat'] = $us['nippejabat'];
		$record['idjstruktural'] = $us['idjstruktural'];
		$record['idunit'] = $us['idunit'];
		$record['tahun'] = substr($record['tglpengajuan'],0,4);
		
		$record['sisacuti'] = is_numeric($_POST['sisacuti']) ? $_POST['sisacuti'] : 'null';
		
		//mendapatkan no surat, bila sudah divalidasi
		if($record['isvalid'] == 'Y'){
			$nosurat = $p_model::getNoSuratCuti($conn,$r_subkey,$record['tglpengajuan']);
			if($nosurat != 'null')
				$record['nosurat'] = $nosurat;
		}
		$record['idpegawai'] = $r_key;
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		//simpan cuti detail
		if(!$p_posterr and $record['tglmulai'] != 'null' and $record['tglmulai'] != 'null'){
			$record['nourutcuti'] = $r_subkey;
			list($p_posterr,$p_postmsg) = $p_model::saveCutiDetail($conn,$record,$r_key);
		}
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
				
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'ajukan' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		//get atasan dan unit,jabatan
		$us = $p_model::getUnitJabAtas($conn,$r_key);
		
		$record['nippejabat'] = $us['nippejabat'];
		$record['idjstruktural'] = $us['idjstruktural'];
		$record['idunit'] = $us['idunit'];
		
		$record['sisacuti'] = is_numeric($_POST['sisacuti']) ? $_POST['sisacuti'] : 'null';
			
		$record['idpegawai'] = $r_key;
		$record['statususulan'] = 'A';
		
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		//simpan cuti detail
		if(!$p_posterr and $record['tglmulai'] != 'null' and $record['tglmulai'] != 'null'){
			$record['nourutcuti'] = $r_subkey;
			list($p_posterr,$p_postmsg) = $p_model::saveCutiDetail($conn,$record,$r_key);
		}
		
		//email ke atasan
		if(!$p_posterr)
			mEmail::requestCuti($conn,$r_subkey);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
				
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {	
		$conn->StartTrans();
		
		//delete presensi
		list($p_posterr,$p_postmsg) = mPresensi::deleteFromCuti($conn,$r_subkey);
		
		//delete cuti detail dulu
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'pe_rwtcutidet',$where);
		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr){
			$conn->CompleteTrans();
			Route::navListpage($p_listpage,$r_key);
		}
	}
	else if($r_act == 'deletedet' and $c_delete) {
		$r_subkeyx = CStr::removeSpecial($_POST['subkeyx']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteCutiDetail($conn,$r_subkey,$r_subkeyx);
		
		if(!$p_posterr) unset($post);
	}
	
	$sql = $p_model::getDataEditPermohonanCuti($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
	//cuti detail
	if(!empty($r_subkey))
		$a_cd = $p_model::getCutiDetail($conn,$r_subkey);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'] and $t_row['id'] != 'tglmulai' and $t_row['id'] != 'tglselesai')
			$a_required[] = $t_row['id'];
		if($t_row['id'] == 'statususulan')
			$status = $t_row['realvalue'];
			
		//pengecekan hak akses utk pegawai ybs, bila sudah valid
		if($t_row['id'] == 'isvalid'){
			$isvalid = $t_row['value'];
			if(($isvalid == 'Ya' or !empty($status)) and $r_self){
				$c_edit = false;
				$c_delete = false;
			}
		}
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
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
	<script type="text/javascript" src="scripts/foreditx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post" action="<?= Route::navAddress(Route::thisPage()) ?>">
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
					
					if(empty($p_fatalerr)){
				?>
						<table border="0" cellspacing="10" align="center">
							<tr>
								<?	if($c_readlist) { ?>
								<td id="be_list" class="TDButton" onclick="goList()">
									<img src="images/list.png"> Daftar
								</td>
								<?	} if($c_insert) { ?>
								<td id="be_add" class="TDButton" onclick="goNew('<?= $r_key; ?>')">
									<img src="images/add.png"> Data Baru
								</td>
								<?	} if($c_edit) { ?>
							   <td id="be_edit" class="TDButton" onclick="goEdit()">
									<img src="images/edit.png"> Sunting
								</td>
								<td id="be_save" class="TDButton" onclick="goSave()" style="display:none">
									<img src="images/disk.png"> Simpan
								</td>
								<? if(!empty($r_subkey) and (empty($status) or !$r_self)) { ?>
								<td id="be_ajukan" class="TDButton" onclick="goAjukan()">
									<img src="images/check.png"> Ajukan
								</td>
								<?}?>
								<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
									<img src="images/undo.png"> Batal
								</td>
								<?	} if($c_delete and !empty($r_subkey)) { ?>
								<td id="be_delete" class="TDButton" onclick="goDelete()">
									<img src="images/delete.png"> Hapus
								</td>
								<?	} ?>
							</tr>
						</table>
				<?	
					}
					
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
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nourutsurat') ?></td>
							<td  class="RightColumnBG" colspan="4"><?= Page::getDataInput($row,'nourutsurat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'idjeniscuti') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'idjeniscuti') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglpengajuan') ?></td>
							<td  class="RightColumnBG" width="20%" colspan="2"><?= Page::getDataInput($row,'tglpengajuan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alasancuti') ?></td>
							<td  class="RightColumnBG" colspan="4"><?= Page::getDataInput($row,'alasancuti') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alamatselamacuti') ?></td>
							<td  class="RightColumnBG" colspan="4"><?= Page::getDataInput($row,'alamatselamacuti') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'telpselamacuti') ?></td>
							<td  class="RightColumnBG" colspan="4"><?= Page::getDataInput($row,'telpselamacuti') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'keterangan') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'keterangan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="4"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
						<?if(!empty($r_subkey)){?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statususulan') ?></td>
							<td  class="RightColumnBG" colspan="4"><?= Page::getDataInput($row,'statususulan') ?></td>
						</tr>
						<?}?>
						<tr height="30">
							<td colspan="5" class="DataBG">Detail Cuti</td>
						</tr>
						<tr id="edit" style="display:none">
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglmulai') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'tglmulai') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglselesai') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglselesai') ?></td>
							<td>
								<input type="button" value="Tambah" id="be_savedet" class="ControlStyle" onClick="goSaveDet()">
								<input type="button" value="Reset" id="be_reset" class="ControlStyle" onClick="goReset()">
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Keterangan Cuti</td>
							<td class="RightColumnBG" colspan="4"><span id="sketerangan">&nbsp;</span></td>
						</tr>
					</table>
					</div>
				</center>
				<br>
				
				<center>	
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Detail Cuti</h1>
							</div>
						</div>
					</header>
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
						<th>Tgl. Mulai</th>
						<th>Tgl. Selesai</th>
						<th>Lama Cuti</th>
						<th width="50">Aksi</th>						
						<?
							$i = 0;$detail=0;
							if(count($a_cd) > 0){
								foreach($a_cd as $rowd){
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;$detail++;
						?>
						<tr valign="top" class="<?= $rowstyle ?>">
							<td align="center"><?= CStr::formatDateInd($rowd['tglmulai'],false);?></td>
							<td align="center"><?= CStr::formatDateInd($rowd['tglselesai'],false);?></td>
							<td align="center"><?= $rowd['lamacuti'].' hari';?></td>
							<td align="center">
								<span id="show"></span>
								<span id="edit" style="display:none">
								<? if($c_delete) { ?>
									<img id="<?= $rowd['nocutidet'] ?>" title="Hapus cuti detail" src="images/delete.png" onclick="goDeleteDetail(this)" style="cursor:pointer">
								<? } ?>
								</span>
							</td>
						</tr>
						<?
								}
							}
							if($i == 0) {
						?>
						<tr>
							<td colspan="4" align="center">Data kosong</td>
						</tr>
						<?	}
						?>				
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<input type="hidden" name="subkeyx" id="subkeyx">
				<input type="hidden" name="sisacuti" id="sisacuti">
				<?	} ?>
			</form>
		</td>
	</tr>
</table>

<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {	
	initEdit(<?= empty($post) ? false : true ?>);
	getSisaCuti('');
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goList(){
	<?if($detail == '0' and !empty($r_subkey)){?>
		doHighlight(document.getElementById("tglmulai"));
		doHighlight(document.getElementById("tglselesai"));
		alert("Anda belum mengisi tanggal detail cuti");
	<?}else{?>
		$("#contents").divpost({page: listpage, sent: $("#pageform").serializeArray()});
	<?}?>	
}

function goUndo() {
	document.getElementById("pageform").reset();
}

function goEdit() {
	$("[id='show']").hide();
	$("[id='edit']").show();
	
	$("#be_add,#be_edit,#be_reset").hide();
	$("#be_save,#be_savedet,#be_undo").show();
	<?if(!empty($r_subkey) and (empty($status) or !$r_self)){?>
		$("#be_ajukan").show();
	<?}?>
}

function goUndo() {
	$("[id='show']").show();
	$("[id='edit']").hide();
	
	$("#be_add,#be_edit").show();
	$("#be_save,#be_savedet,#be_undo").hide();
}

function goReset(){	
	$("#tglmulai,#tglselesai").val('');
	$("#be_save,#be_savedet").show();
	$("#be_reset").hide();
	
	getSisaCuti('');
}

function goDeleteDetail(elem) {
	var parent = $(elem).parent().parent();
	var classold = parent.attr('class');
	parent.removeClass(classold);
	parent.addClass("AlternateBG2");
	
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "deletedet";
		document.getElementById("subkeyx").value = elem.id;
		goSubmit();
	}
	else
		parent.removeClass("AlternateBG2");
	
	parent.addClass(classold);
}

function goSave() {
	<?if(empty($r_subkey) or $detail == 0){?>
		required += ',tglmulai,tglselesai';
	<?}else{?>
		//jika tanggal mulai diisi, maka tanggal selesai juga diisi dan sebaliknya
		if($("#tglmulai").val() != '' && $("#tglselesai").val() == ''){
			required += ',tglselesai';
		}
		if($("#tglmulai").val() == '' && $("#tglselesai").val() != ''){
			required += ',tglmulai';
		}		
	<?}?>
	
	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

function goSaveDet() {
	required += ',tglmulai,tglselesai';
	
	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

function goAjukan() {
	var pass = true;
	<?if($detail == 0){?>
		pass = false;
		alert('Silahkan isi tanggal cuti yang akan diajukan');
	<?}else{?>
		//jika tanggal mulai diisi, maka tanggal selesai juga diisi dan sebaliknya
		if($("#tglmulai").val() != '' && $("#tglselesai").val() == ''){
			required += ',tglselesai';
		}
		if($("#tglmulai").val() == '' && $("#tglselesai").val() != ''){
			required += ',tglmulai';
		}		
	<?}?>
	
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		var retval;
		retval = confirm('Anda yakin untuk mengajukan cuti ini?');
		if (retval){
			document.getElementById("act").value = "ajukan";
			goSubmit();
		}
	}
}

function getSisaCuti(elem){
	var jenis='',tglm='',tgls='';
	formattgl = /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-\d{4}$/;
	err=false;
	
	if(elem== ''){
		jenis=$("#idjeniscuti").val();
	}else{
		if(elem.id=='idjeniscuti')
			jenis=elem.value;
		else
			jenis=$("#idjeniscuti").val();
		
		if(jenis != ''){
			if(elem.id=='tglmulai')
				tglm=elem.value;
			else
				tglm=$("#tglmulai").val();
			
			if(elem.id=='tglselesai')
				tgls=elem.value;
			else
				tgls=$("#tglselesai").val();
			
			//pengecekan format tanggal
			if(tglm.match(formattgl))
				tglm = tglm;
			else
				tglm = '';
			if(tgls.match(formattgl))
				tgls = tgls;
			else
				tgls = '';
		}
	}
	
	if(tglm == '' && tgls != ''){
		doHighlight(document.getElementById("tglmulai"));
		alert("Tanggal mulai harus diisi");
		err=true;	
	}
	
	if(tglm == '')
		tglm = $("#tglpengajuan").val();
		
	if(jenis != '' && !err){
		if(tglm != '' && tglm.match(formattgl)){
			var posted = "f=gsisacuti&q[]="+$("#key").val()+"&q[]="+jenis+"&q[]="+tglm+"&q[]="+tgls+"&q[]="+$("#tglpengajuan").val();
			
			$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
				var text = text.split('|');
				if($("[id='edit']").is(":visible")){
					$("#be_save,#be_savedet").show();
					$("#be_reset").hide();
					
					<?if(!empty($r_subkey) and (empty($status) or !$r_self)){?>
						$("#be_ajukan").show();
					<?}?>
				}
				
				if(text[0] == "err"){
					$("#sketerangan").html('<font color="red">'+text[1]+'</font>');	
					$("#be_save,#be_savedet").hide();
					$("#be_reset").show();
					
					<?if(!empty($r_subkey) and (empty($status) or !$r_self)){?>
						$("#be_ajukan").hide();
					<?}?>
				}else if(text[0] == "clear"){
					$("#sketerangan").html('<font color="green">Sisa cuti anda untuk tahun ini : '+text[1]+' hari</font>');
					$("#sisacuti").val(text[1]);
				}else if(text[0] == "alert"){
					if(text[1] < 0)
						$("#sketerangan").html('<font color="red">Pengambilan cuti anda melebihi jatah cuti</font>');	
					else{
						$("#sketerangan").html('<font color="green">Sisa cuti anda untuk tahun ini : '+text[1]+' hari</font>');
						$("#sisacuti").val(text[1]);
					}
					var retval;
					retval = confirm(text[2]);
					if (!retval){		
						goReset();
					}else{
						if(text[1] < 0){
							$("#be_save,#be_savedet").hide();
							$("#be_reset").show();
							
							<?if(!empty($r_subkey) and (empty($status) or !$r_self)){?>
								$("#be_ajukan").hide();
							<?}?>
						}
					}
				}
			});
		}
	}
}
</script>
</body>
</html>
