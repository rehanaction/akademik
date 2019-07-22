<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
		
	$c_update = $a_auth['canupdate'];
	$c_other = $a_auth['canother'];
	
	//role tambahan
	$c_validasi = $c_other['K'];
	
	// include
	require_once(Route::getModelPath('dinas'));
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Data Persetujuan Tugas Kedinasan';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_dbtable = 'pe_rwtdinas';
	$p_key = 'nodinas';
	$p_listpage = Route::getListPage();
	$p_printpage = 'rep_suratdinas';
	$p_printpagerate = 'rep_ratedinas';
	
	$p_model = mDinas;
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	//role persetujuan
	$a_role = $p_model::getValidasiDinas($conn, $r_key);
	
	if (($a_role['issetujuatasan'] == 'T' or $a_role['issetujukasdm'] == 'T' or $a_role['issetujuwarek2'] == 'T' or $a_role['issetujukabagkeu'] == 'T') and Modul::getRole() != 'A')
		$c_edit = false;
	
	if (($a_role['issetujuatasan'] == 'S' and $a_role['issetujukasdm'] == 'S' and $a_role['issetujuwarek2'] == 'S' and $a_role['issetujukabagkeu'] == 'S') and Modul::getRole() != 'A')
		$c_edit = false;
		
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nosurat', 'label' => 'No Surat Dinas', 'maxlength' => 50, 'size' => 20, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'namapegawai', 'label' => 'Pegawai', 'maxlength' => 50, 'size' => 20, 'readonly' => true);
	$a_input[] = array('kolom' => 'namaunit', 'label' => 'Unit', 'maxlength' => 50, 'size' => 20, 'readonly' => true);
	$a_input[] = array('kolom' => 'kodejenisdinas', 'label' => 'Jenis Dinas', 'type' => 'S', 'option' => $p_model::jenisDinas($conn), 'readonly' => true);
	$a_input[] = array('kolom' => 'dalamrangka', 'label' => 'Dalam Rangka', 'type' => 'A', 'maxlength' => 500, 'cols' => 60, 'rows' => 3, 'readonly' => true);
	$a_input[] = array('kolom' => 'instansi', 'label' => 'Instansi', 'maxlength' => 100, 'size' => 60, 'notnull' => true, 'readonly' => true);
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'type' => 'A', 'maxlength' => 255, 'cols' => 60, 'rows' => '3', 'readonly' => true);
	$a_input[] = array('kolom' => 'jnsrate', 'label' => 'Lokasi', 'type' => 'S', 'option' => $p_model::jenisRate(), 'readonly' => true);
	$a_input[] = array('kolom' => 'idjabatan', 'label' => 'Perjalanan Dinas Sebagai', 'type' => 'S', 'option' => mMastKepegawaian::Jabatan($conn), 'readonly' => true);
	$a_input[] = array('kolom' => 'tglpergi', 'label' => 'Tanggal', 'type' => D, 'readonly' => true);
	$a_input[] = array('kolom' => 'tglpulang', 'label' => 'Tgl. Pulang', 'type' => D, 'readonly' => true);
	$a_input[] = array('kolom' => 'namasdm', 'label' => 'Persetujuan', 'maxlength' => 100, 'size' => 60, 'readonly' => true);
	$a_input[] = array('kolom' => 'namakeu', 'label' => '', 'maxlength' => 100, 'size' => 60, 'readonly' => true);
	$a_input[] = array('kolom' => 'namawr', 'label' => '', 'maxlength' => 100, 'size' => 60, 'readonly' => true);
	$a_input[] = array('kolom' => 'namapejabat', 'label' => 'Pejabat Menugaskan', 'maxlength' => 100, 'size' => 60, 'readonly' => true);
	$a_input[] = array('kolom' => 'tglusulan', 'label' => 'Tgl. Pengajuan', 'type' => D, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'pejabatpenugas', 'label' => 'Pejabat Pemberi Tugas', 'maxlength' => 100, 'size' => 60, 'readonly' => true);
	$a_input[] = array('kolom' => 'idpegawaitugas', 'type' => 'H');
	
	if ($c_validasi)
		$a_input[] = array('kolom' => 'filekedinasan', 'label' => 'File Kedinasan', 'type' => 'U', 'uptype' => 'filekedinasan', 'size' => 40);
	else
		$a_input[] = array('kolom' => 'filekedinasan', 'label' => 'File Kedinasan', 'type' => 'U', 'uptype' => 'filekedinasan', 'size' => 40, 'readonly' => true);
	
	$c_detailedit = $c_detaildelete = false;
	
	//emailatasan
	if (Modul::getRole() == 'Jab' or $c_validasi)
		$a_input[] = array('kolom' => 'issetujuatasan', 'label' => 'Atasan', 'type' => 'R', 'option' => SDM::statusPersetujuan());
	else
		$a_input[] = array('kolom' => 'issetujuatasan', 'label' => 'Atasan', 'type' => 'R', 'option' => SDM::statusPersetujuan(), 'readonly' => true);
	
	/*if ($a_role['emailwarek2'] == Modul::getUserEmail() or $c_validasi)
		$a_input[] = array('kolom' => 'issetujuwarek2', 'label' => 'Warek 2', 'type' => 'R', 'option' => SDM::statusPersetujuan());
	else
		$a_input[] = array('kolom' => 'issetujuwarek2', 'label' => 'Warek 2', 'type' => 'R', 'option' => SDM::statusPersetujuan(), 'readonly' => true);*/
	
	if ($a_role['emailkasdm'] == Modul::getUserEmail() or $c_validasi){		
		$c_detailedit = $c_detaildelete = true;
		$a_input[] = array('kolom' => 'issetujukasdm', 'label' => 'Kepala SDM', 'type' => 'R', 'option' => SDM::statusPersetujuan());
		$a_input[] = array('kolom' => 'idunit', 'label' => 'Anggaran Unit', 'type' => 'S', 'option' => mCombo::unitAll($conn,false));
		$a_input[] = array('kolom' => 'jmldicairkan', 'label' => 'Jumlah Realisasi Biaya', 'maxlength' => 14, 'size' => 14);
	}else{
		$a_input[] = array('kolom' => 'issetujukasdm', 'label' => 'Kepala SDM', 'type' => 'R', 'option' => SDM::statusPersetujuan(), 'readonly' => true);
		$a_input[] = array('kolom' => 'idunit', 'label' => 'Anggaran Unit', 'type' => 'S', 'option' => mCombo::unitAll($conn,false), 'readonly' => true);
		$a_input[] = array('kolom' => 'jmldicairkan', 'label' => 'Jumlah Realisasi Biaya', 'maxlength' => 14, 'size' => 14, 'readonly' => true);
	}
	
	if ($a_role['emailkabagkeu'] == Modul::getUserEmail() or $c_validasi){
		$c_detailedit = $c_detaildelete = true;
		$a_input[] = array('kolom' => 'issetujukabagkeu', 'label' => 'Ka. Bag. Keuangan', 'type' => 'R', 'option' => SDM::statusPersetujuan());
		$a_input[] = array('kolom' => 'tgldicairkan', 'label' => 'Tgl. Realisasi', 'type' => D);
	}else{
		$a_input[] = array('kolom' => 'issetujukabagkeu', 'label' => 'Ka. Bag. Keuangan', 'type' => 'R', 'option' => SDM::statusPersetujuan(), 'readonly' => true);
		$a_input[] = array('kolom' => 'tgldicairkan', 'label' => 'Tgl. Realisasi', 'type' => D, 'readonly' => true);
	}
	
	$a_detail = array();
	
	$t_detail = array();
	$t_detail[] = array('kolom' => 'idrate', 'label' => 'Rate Perjalanan', 'type' => 'S', 'option' => $p_model::jenisRatePerjalanan($conn));
	$t_detail[] = array('kolom' => 'nominal', 'label' => 'Nominal', 'size' => 14, 'maxlength' => 14, 'type' => 'N', 'align' => 'right');
	
	$a_detail['rateperjalanan'] = array('key' => $p_model::getDetailInfo('rateperjalanan','key'), 'data' => $t_detail);
		
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if ($record['issetujuatasan'] == 'S'){				
			if($record['nosurat'] == 'null')
				$record['nosurat'] = $p_model::getNoSurat($conn,$record['tglusulan']);
		}
		if ($record['issetujukasdm'] == 'S'){
			$record['issetujukabagkeu'] = 'S';
			$record['issetujuwarek2'] = 'S';
		}
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if (!$p_posterr){
			$isvalidpre = $p_model::isValidDinas($conn, $r_key);
			//simpan ke presensi
			if ($isvalidpre)
				list($p_posterr,$p_postmsg) = mPresensi::saveFormDinas($conn,$r_key);
		}
		
		if(!$p_posterr){ 
			//role persetujuan
			$a_role = $p_model::getValidasiDinas($conn, $r_key);
			
			if (($a_role['issetujuatasan'] == 'T' or $a_role['issetujukasdm'] == 'T' or $a_role['issetujuwarek2'] == 'T' or $a_role['issetujukabagkeu'] == 'T') and Modul::getRole() != 'A')
				$c_edit = false;
				
			unset($post);
		}
	}
	else if($r_act == 'insertdet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		$record = array('nodinas' => $r_key);
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_value = $_POST[$r_detail.'_'.CStr::cEmChg($t_detail['nameid'],$t_detail['kolom'])];
			$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
		}
		
		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail,'pe_biayadinas');
	}
	
	$sql = $p_model::getDataEditRDinas($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	$a_rate = array();
	$a_rate = $p_model::jenisRatePerjalanan($conn);
	
	if(!empty($r_key)) {
		$a_persetujuan = SDM::statusPersetujuan();
				
		$rowd = array();
		$rowd += $p_model::getBiayaDinas($conn,$r_key,'rateperjalanan',$post);
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
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
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
					
					if(empty($p_fatalerr)){ ?>
				<table border="0" cellspacing="10" align="center">
					<tr>
						<?	if($c_readlist) { ?>
						<td id="be_list" class="TDButton" onclick="goList()">
							<img src="images/list.png"> Daftar
						</td>
						<?	} if($c_insert) { ?>
						<td id="be_add" class="TDButton" onclick="goNew()">
							<img src="images/add.png"> Data Baru
						</td>
						<?	} if($c_edit) { ?>
					   <td id="be_edit" class="TDButton" onclick="goEdit()">
							<img src="images/edit.png"> Sunting
						</td>
						<td id="be_save" class="TDButton" onclick="goSave()" style="display:none">
							<img src="images/disk.png"> Simpan
						</td>
						<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
							<img src="images/undo.png"> Batal
						</td>
						<?	} if($c_delete and !empty($r_key)) { ?>
						<td id="be_delete" class="TDButton" onclick="goDelete()">
							<img src="images/delete.png"> Hapus
						</td>
						<?	} ?>
						<td id="be_print" class="TDButton" onclick="goPrint('<?= $r_key; ?>')">
							<img src="images/small-print.png">Surat Dinas
						</td>
						<td id="be_printrate" class="TDButton" onclick="goPrintRt('<?= $r_key; ?>')">
							<img src="images/small-print.png">Rate Perjalanan
						</td>
					</tr>
				</table>
				<? }
						
					
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
						<tr class="DataBG" height="30">
							<td colspan="2">Data Kedinasan</td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'namapegawai') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namapegawai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'namaunit') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namaunit') ?></td>
						</tr>
						<? if (!empty($r_key)) {?>
						<tr>
							<td class="LeftColumnBG" width="200"><?= Page::getDataLabel($row,'nosurat') ?></td>
							<td class="RightColumnBG"><?=  (Page::getDataValue($row,'issetujuatasan') == 'S' ? Page::getDataInput($row,'nosurat') : 'Belum disetujui'); ?></td>
						</tr>
						<? } ?>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglusulan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglusulan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'kodejenisdinas') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodejenisdinas') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'dalamrangka') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'dalamrangka') ?></td>
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
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idjabatan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idjabatan') ?></td>
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
						<? if((!empty($r_key) and $c_detailedit) or (!empty($r_key) and $c_validasi)) { ?>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunit') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idunit') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'jmldicairkan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'jmldicairkan') ?></td>
						</tr>
						<? } ?>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filekedinasan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'filekedinasan') ?></td>
						</tr>
						<? if (!empty($r_key)) {?>
						<tr class="DataBG" height="30">
							<td colspan="2">Status Persetujuan</td>
						</tr>
						<tr>
							<td colspan="2">
								<table width="100%" class="GridStyle" cellpadding="3">
									<tr>
										<th class="HeaderBG" width="200px">Pejabat</th>
										<th class="HeaderBG" width="200px">Status</th>
										<th class="HeaderBG">Nama</th>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'issetujuatasan') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'issetujuatasan'); ?></td>
										<td><strong><?= Page::getDataInput($row,'namapejabat') ?></strong></td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'issetujukasdm') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'issetujukasdm'); ?></td>
										<td><strong><?= Page::getDataInput($row,'namasdm') ?></strong></td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'issetujukabagkeu') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'issetujukabagkeu'); ?></td>
										<td><strong><?= Page::getDataInput($row,'namakeu') ?></strong></td>
									</tr>
									<?/*<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'issetujuwarek2') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'issetujuwarek2'); ?></td>
										<td><strong><?= Page::getDataInput($row,'namawr') ?></strong></td>
									</tr>*/?>
								</table>
							</td>
						</tr>
						<? } ?>
					</table>
					</div>
				</center>
				<br>
				<? if(!empty($r_key)) { ?>
				<center>
				<div class="tabs" id="tablink"style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Biaya Rate</a></li>
					</ul>					
										
					<div id="items">
					<?= Page::getDetailTable($rowd,$a_detail,'rateperjalanan','Tarif Rate Perjalanan',true,$c_detailedit,$c_detaildelete); ?>
					</div>
				</center>
				<? } ?>
				<?	} ?>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="detail" id="detail">
			</form>
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
	
	$("input[name='pejabatpenugas']").xautox({strpost: "f=acpejabat", targetid: "idpegawaitugas", imgchkid: "imgid", imgavail: true});
	
	initTab(); 
});


function goPrint(id) {
	goShowPage(id,'<?= Route::navAddress($p_printpage) ?>');
}

function goPrintRt(id) {
	goShowPage(id,'<?= Route::navAddress($p_printpagerate) ?>');
}
</script>
</body>
</html>
