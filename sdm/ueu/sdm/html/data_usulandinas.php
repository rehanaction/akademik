<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('dinas'));
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mDinas;
		
	$p_tbwidth = "800";
	$p_title = "Data Usulan Dinas Unit";
	$p_header = "Data Pelaksanaan Tugas Dinas";
	$p_headerdetail = "Daftar Peserta Kedinasan";
	$p_listpage = Route::getListPage();
	$p_dbtable = "pe_rwtdinas";
	$p_aktivitas = 'DOCUMENT';
	$p_key = "refid";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'tglusulan', 'label' => 'Tgl. Pengajuan', 'type' => D, 'default' => date('Y-m-d'));
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => mCombo::unitSave($conn,false), 'notnull' => true);
	$a_input[] = array('kolom' => 'kodejenisdinas', 'label' => 'Jenis Dinas', 'type' => 'S', 'option' => $p_model::jenisDinas($conn), 'notnull' => true);
	$a_input[] = array('kolom' => 'dalamrangka', 'label' => 'Dalam Rangka', 'type' => 'A', 'maxlength' => 500, 'cols' => 60, 'rows' => '3', 'notnull' => true);
	$a_input[] = array('kolom' => 'instansi', 'label' => 'Instansi', 'maxlength' => 100, 'size' => 60, 'notnull' => true);
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'type' => 'A', 'maxlength' => 255, 'cols' => 60, 'rows' => '3', 'notnull' => true);
	$a_input[] = array('kolom' => 'jnsrate', 'label' => 'Lokasi', 'type' => 'S', 'option' => $p_model::jenisRate(), 'notnull' => true);
	$a_input[] = array('kolom' => 'tglpergi', 'label' => 'Tanggal', 'type' => D);
	$a_input[] = array('kolom' => 'tglpulang', 'label' => 'Tgl. Pulang', 'type' => D);
	$a_input[] = array('kolom' => 'pejabatpenugas', 'label' => 'Pejabat Pemberi Tugas', 'maxlength' => 100, 'size' => 60);
	$a_input[] = array('kolom' => 'idpegawaitugas', 'type' => 'H');
	
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {		
		$id = $_POST['id'];
		$r_nosurat = $_POST['nosurat'];
		
		if (count($id) > 0){				
			foreach ($id as $key => $idpegawai){
				$record = array();
				list($post,$record) = uForm::getPostRecord($a_input,$_POST);
				if(empty($r_key))
					$record['refid'] = $p_model::getKodeKolektif($conn);
				else
					$record['refid'] = $r_key;
					
				$r_subkey = CStr::removeSpecial($_POST['no_'.$idpegawai]);
				$record['pegditunjuk'] = $idpegawai;
				$record['idjabatan'] = $_POST['idj'][$key];
				$record['issetujukasdm'] = 'S';
				$record['issetujuatasan'] = 'S';
				$record['issetujukabagkeu'] = 'S';
				$record['issetujuwarek2'] = 'S';

				if(empty($r_nosurat))
					$record['nosurat'] = $p_model::getNoSurat($conn,$record['tglusulan']);
				else
					$record['nosurat'] = $r_nosurat;
				
				if (empty($r_subkey))
					list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,'nodinas',true);
				else{
					unset($record['nosurat'],$record['idjabatan']);
					list($p_posterr,$p_postmsg) =  $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,'nodinas');
				}

				if (!$p_posterr and $record['issetujukasdm'] == 'S'){
					list($p_posterr,$p_postmsg) = mPresensi::saveFormDinas($conn,$r_subkey);
				}

				//jika status dinas pejabat		
				if(!$p_posterr){
					list($p_posterr,$p_postmsg) = $p_model::saveBiayaDinas($conn,$r_subkey);
				}
			}
		}
				
		if(!$p_posterr){
			unset($post);
			if(empty($r_key))
				$r_key = $record['refid'];
				
			$p_key = "refid";
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		$a_nodinas = $p_model::getNoDinas($conn,$r_key);

		foreach ($a_nodinas as $r_subkey => $value) {
			list($p_posterr,$p_postmsg) = mPresensi::deleteFormDinas($conn, $r_subkey);
		
			if(!$p_posterr)
				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'pe_biayadinas','nodinas');

			if(!$p_posterr)
				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey, $p_dbtable,'nodinas');
		}
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'deletepeserta' and $c_delete) {
		$p_key = "nodinas";
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);	

		list($p_posterr,$p_postmsg) = mPresensi::deleteFormDinas($conn, $r_subkey);
		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'pe_biayadinas','nodinas');

		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey, $p_dbtable,'nodinas');
		
		$isExist = $p_model::isExistDinas($conn, $r_key);
		
		if(!$p_posterr){
			if ($isExist){
				$p_key = 'refid';
			}else
				Route::navigate($p_listpage);
		}else
			$p_key = 'refid';
	}
		
	$sql = $p_model::getEditDinasKol($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	if (!empty($r_key)){
		$a_peserta = $p_model::listQueryPeserta($conn,$r_key);
		$r_nosurat = $p_model::noSurat($conn,$r_key);
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
		
	$a_required = array('thnanggaran','idunit','anggaran');
	$a_jabatan = mMastKepegawaian::Jabatan($conn);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
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
									require_once('inc_databutton.php');
								
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
											<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_header ?></h1>
										</div>
									</div>
								</header>
								<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
									<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
										<tbody>
											<tr>
												<td class="LeftColumnBG" width="150px"><?= Page::getDataLabel($row,'tglusulan') ?></td>
												<td class="RightColumnBG"><?= Page::getDataInput($row,'tglusulan') ?></td>
											</tr>
											<tr>
												<td class="LeftColumnBG" width="150px"><?= Page::getDataLabel($row,'idunit') ?></td>
												<td class="RightColumnBG"><?= Page::getDataInput($row,'idunit') ?></td>
											</tr>
											<tr>
												<td class="LeftColumnBG"><?= Page::getDataLabel($row,'dalamrangka') ?></td>
												<td class="RightColumnBG"><?= Page::getDataInput($row,'dalamrangka') ?></td>
											</tr>
											<tr>
												<td class="LeftColumnBG"><?= Page::getDataLabel($row,'kodejenisdinas') ?></td>
												<td class="RightColumnBG"><?= Page::getDataInput($row,'kodejenisdinas') ?></td>
											</tr>
											<tr>
												<td class="LeftColumnBG"><?= Page::getDataLabel($row,'instansi') ?></td>
												<td class="RightColumnBG"><?= Page::getDataInput($row,'instansi') ?></td>
											</tr>
											<tr>
												<td class="LeftColumnBG"><?= Page::getDataLabel($row,'alamat') ?></td>
												<td class="RightColumnBG"><?= Page::getDataInput($row,'alamat') ?></td>
											</tr>
											<tr>
												<td class="LeftColumnBG"><?= Page::getDataLabel($row,'jnsrate') ?></td>
												<td class="RightColumnBG"><?= Page::getDataInput($row,'jnsrate') ?></td>
											</tr>
											<tr>
												<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpergi') ?></td>
												<td class="RightColumnBG"><?= Page::getDataInput($row,'tglpergi') ?> s/d <?= Page::getDataInput($row,'tglpulang') ?></td>
											</tr>
											<tr>
												<td class="LeftColumnBG"><?= Page::getDataLabel($row,'pejabatpenugas') ?></td>
												<td class="RightColumnBG"><?= Page::getDataInput($row,'pejabatpenugas') ?>
												<span id="show" style="display: inline;"></span>
												<span id="edit" style="display: none;">
												<img id="imgid_c" src="images/green.gif"><img id="imgid_u" src="images/red.gif" style="display:none">
												</span>
												<?= Page::getDataInput($row,'idpegawaitugas') ?></td>
											</tr>
										</tbody>
									</table>
								</div>
								<br />
								
								<header style="width:<?= $p_tbwidth ?>px">
									<div class="inner">
										<div class="left title">
											<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_headerdetail ?></h1>
										</div>
									</div>
								</header>
								<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
									<table width="100%" cellspacing="0" cellpadding="4" class="GridStyle">
										<thead>
											<tr>
												<th width="100">No</th>
												<th>Pegawai</th>
												<th>Dinas Sebagai</th>
												<th width="50">Aksi</th>
											</tr>
										</thead>
										<tbody>
										<? if($c_edit){?>
											<tr id="template_1">
												<td>&nbsp;</td>
												<td><span id="edit" style="display:none"><?= UI::createTextBox('peserta','','ControlAuto',60,60,$c_edit)?>
												<input type="hidden" name="idpegawai" id="idpegawai" /></span>
												</td>
												<td><?= UI::createSelect('idjabatan',$a_jabatan,'ControlStyle','',true)?></td>
												<td><span id="edit" style="display:none">
												<input type="button" name="bsave" id="bsave" onClick="goAddPeserta(1)" class="ControlStyle" value="Tambah" /></span>
												</td>
											</tr>
										<? }?>
											<? if (!empty($r_key) and count($a_peserta) >0) {
													$ip = 0;
													foreach($a_peserta as $row){
														$ip++;
											?>
											<tr>
												<td align="center"><?= $ip; ?>
												<input type="hidden" name="id[]" id="id[]" value="<?= $row['idpegawai']; ?>" />
												<input type="hidden" name="no_<?= $row['idpegawai']; ?>" value="<?= $row['nodinas']; ?>"/></td>
												<td><?= $row['namalengkap']; ?></td>
												<td><?= $row['namajabatan']; ?></td>
												<td align="center"><img src="images/delete.png" onClick="goRemovePeserta('<?= $row['nodinas']; ?>', '<?= $row['namalengkap']; ?>')" style="cursor:pointer" /></td>
											</tr>
											<? }} ?>
										</tbody>
									</table>
								</div>
							</center>
							<? } ?>
							<input type="hidden" name="act" id="act">
							<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
							<input type="hidden" name="subkey" id="subkey">
							<input type="hidden" name="nosurat" id="nosurat" value="<?= $r_nosurat?>">
						</form>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	//autocomplete
	$("input[name='pejabatpenugas']").xautox({strpost: "f=acpejabat", targetid: "idpegawaitugas", imgchkid: "imgid", imgavail: true});
	$("#peserta").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgpeg", imgavail: true});
});

function goAddPeserta(id){
	var tr = $("#template_"+id);
	var idpegawai = tr.find("input[name=idpegawai]").val();
	el = document.getElementById('peserta');
	
	var err = false;
	if(el != null && el.value == "") {
		doHighlight(el);
		err = true;
	}
	
	if (err){
		alert("Mohon mengisi input yang berwarna kuning!!!");
	}else{
		if (idpegawai == "")
			alert("Mohon mengisi data valid!!!");
		else{
			var newtr = '<tr><td>&nbsp;</td><td>'+el.value+'<input type="hidden" name="id[]" id="id[]" value="'+idpegawai+'" /></td><td>'+$("#idjabatan option:selected" ).text()+'<input type="hidden" name="idj[]" id="idj[]" value="'+document.getElementById('idjabatan').value+'" /></td><td align="center"><img src="images/delete.png" onClick="goRmPeserta(this)" style="cursor:pointer" /></td></tr>';
			tr.after(newtr);
			$("#peserta").val("");
		}
	}
}

function goSave() {
	if($("input[name='id[]']").length > 0){
		var pass = true;
		if(typeof(required) != "undefined") {
			if(!cfHighlight(required))
				pass = false;
		}
		
		if(pass) {
			document.getElementById("act").value = "save";
			goSubmit();
		}
	}else
		alert("Silahkan masukkan data peserta dinas");
}

function goRmPeserta(elem) {
	$(elem).parents("tr").eq(0).replaceWith("");
}

function goRemovePeserta(id,nama){
	var hapus = confirm("Apakah anda yakin akan menghapus peserta " + nama + " ?");
	if(hapus) {
		document.getElementById('subkey').value = id;
		document.getElementById('act').value = 'deletepeserta';
		goSubmit();
	}
}

</script>
</body>
</html>
</html>