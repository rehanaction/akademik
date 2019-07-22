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
	require_once(Route::getModelPath('dinas'));
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai()){
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
	$p_title = 'Data Tugas Dinas';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	$p_printpage = 'rep_suratdinas';
	$p_printpagerate = 'rep_ratedinas';
	
	$p_model = mDinas;
	$p_dbtable = "pe_rwtdinas";
	$where = 'nodinas';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nosurat', 'label' => 'No Surat Dinas', 'maxlength' => 50, 'size' => 20, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'kodejenisdinas', 'label' => 'Jenis Dinas', 'type' => 'S', 'option' => $p_model::jenisDinas($conn), 'notnull' => true);
	$a_input[] = array('kolom' => 'dalamrangka', 'label' => 'Dalam Rangka', 'type' => 'A', 'maxlength' => 500, 'cols' => 60, 'rows' => 3, 'notnull' => true);
	$a_input[] = array('kolom' => 'instansi', 'label' => 'Instansi', 'maxlength' => 100, 'size' => 60, 'notnull' => true);
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'type' => 'A', 'maxlength' => 255, 'cols' => 60, 'rows' => '3', 'notnull' => true);
	$a_input[] = array('kolom' => 'jnsrate', 'label' => 'Lokasi', 'type' => 'S', 'option' => $p_model::jenisRate(), 'notnull' => true);
	$a_input[] = array('kolom' => 'idjabatan', 'label' => 'Perjalanan Dinas Sebagai', 'type' => 'S', 'option' => mMastKepegawaian::Jabatan($conn), 'notnull' => true);
	$a_input[] = array('kolom' => 'tglpergi', 'label' => 'Tanggal', 'type' => D);
	$a_input[] = array('kolom' => 'tglpulang', 'label' => 'Tgl. Pulang', 'type' => D);
	$a_input[] = array('kolom' => 'namasdm', 'label' => 'Persetujuan', 'maxlength' => 100, 'size' => 60, 'readonly' => true);
	$a_input[] = array('kolom' => 'namakeu', 'label' => '', 'maxlength' => 100, 'size' => 60, 'readonly' => true);
	$a_input[] = array('kolom' => 'namawr', 'label' => '', 'maxlength' => 100, 'size' => 60, 'readonly' => true);
	$a_input[] = array('kolom' => 'namapejabat', 'label' => 'Pejabat Menugaskan', 'maxlength' => 100, 'size' => 60, 'readonly' => true);
	$a_input[] = array('kolom' => 'tglusulan', 'label' => 'Tgl. Pengajuan', 'type' => D, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'filekedinasan', 'label' => 'File Kedinasan', 'type' => 'U', 'uptype' => 'filekedinasan', 'size' => 40);
	$a_input[] = array('kolom' => 'pejabatpenugas', 'label' => 'Pejabat Pemberi Tugas', 'maxlength' => 100, 'size' => 60);
	$a_input[] = array('kolom' => 'idpegawaitugas', 'type' => 'H');
	
	if (!empty($r_subkey))
		$a_input[] = array('kolom' => 'filevalidasidinas', 'label' => 'File Materi Dinas', 'type' => 'U', 'uptype' => 'filevalidasidinas', 'size' => 40);
	
	if ($c_valid){
		$a_input[] = array('kolom' => 'issetujuatasan', 'label' => 'Atasan', 'type' => 'R', 'option' => SDM::statusPersetujuan());
		//$a_input[] = array('kolom' => 'issetujuwarek2', 'label' => 'Warek 2', 'type' => 'R', 'option' => SDM::statusPersetujuan());
		$a_input[] = array('kolom' => 'issetujukasdm', 'label' => 'Kepala SDM', 'type' => 'R', 'option' => SDM::statusPersetujuan());
		$a_input[] = array('kolom' => 'issetujukabagkeu', 'label' => 'Ka. Bag. Keuangan', 'type' => 'R', 'option' => SDM::statusPersetujuan());
		$a_input[] = array('kolom' => 'idunit', 'label' => 'Anggaran Unit', 'type' => 'S', 'option' => mCombo::unitSave($conn,false));
		$a_input[] = array('kolom' => 'jmldicairkan', 'label' => 'Jumlah Realisasi Biaya', 'maxlength' => 14, 'size' => 14, 'type' => 'N');
		$a_input[] = array('kolom' => 'tgldicairkan', 'label' => 'Tgl. Realisasi', 'type' => D);
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Validasi File Materi', 'type' => 'R', 'option' => SDM::getValid());
		
	
		$c_detailedit = true;
		$c_detaildelete = true;
	}else{	
		$a_input[] = array('kolom' => 'issetujuatasan', 'label' => 'Atasan', 'type' => 'R', 'option' => SDM::statusPersetujuan(), 'readonly' => true);
		//$a_input[] = array('kolom' => 'issetujuwarek2', 'label' => 'Warek 2', 'type' => 'R', 'option' => SDM::statusPersetujuan(), 'readonly' => true);
		$a_input[] = array('kolom' => 'issetujukasdm', 'label' => 'Kepala SDM', 'type' => 'R', 'option' => SDM::statusPersetujuan(), 'readonly' => true);
		$a_input[] = array('kolom' => 'issetujukabagkeu', 'label' => 'Ka. Bag. Keuangan', 'type' => 'R', 'option' => SDM::statusPersetujuan(), 'readonly' => true);
		$a_input[] = array('kolom' => 'idunit', 'label' => 'Anggaran Unit', 'type' => 'S', 'option' => mCombo::unitSave($conn,false), 'readonly' => true);
		$a_input[] = array('kolom' => 'jmldicairkan', 'label' => 'Jumlah Realisasi Biaya', 'maxlength' => 14, 'size' => 14, 'readonly' => true);
		$a_input[] = array('kolom' => 'tgldicairkan', 'label' => 'Tgl. Realisasi', 'type' => D, 'readonly' => true);
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Validasi File Materi', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);
		
		$c_detailedit = false;
		$c_detaildelete = false;
	}
	
	$a_detail = array();
	
	$t_detail = array();
	$t_detail[] = array('kolom' => 'idrate', 'label' => 'Rate Perjalanan', 'type' => 'S', 'option' => $p_model::jenisRatePerjalanan($conn));
	$t_detail[] = array('kolom' => 'nominal', 'label' => 'Nominal', 'size' => 14, 'maxlength' => 14, 'type' => 'N', 'align' => 'right');
	
	$a_detail['rateperjalanan'] = array('key' => $p_model::getDetailInfo('rateperjalanan','key'), 'data' => $t_detail);		
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = CStr::removeSpecial($_POST['actdet']);
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$conn->BeginTrans();
		$record['pegditunjuk'] = $r_key;
		
		if(empty($r_subkey)){
			$record['tglusulan'] = date("Y-m-d");			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
			
		}else{			
			if ($record['issetujuatasan'] == 'S'){				
				if($record['nosurat'] == 'null')
					$record['nosurat'] = $p_model::getNoSurat($conn,$record['tglusulan']);
			}
			if ($record['issetujukasdm'] == 'S'){
				$record['issetujukabagkeu'] = 'S';
				$record['issetujuwarek2'] = 'S';
			}

			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
			
			//simpan ke presensi
			if (!$p_posterr){
				$isvalidpre = $p_model::isValidDinas($conn, $r_subkey);
				//simpan ke presensi
				if ($isvalidpre)
					list($p_posterr,$p_postmsg) = mPresensi::saveFormDinas($conn,$r_subkey);
			}
		}
		
		//jika status dinas pejabat		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::saveBiayaDinas($conn,$r_subkey);

		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);

		if($ok) 
			unset($post);
		else
			Route::setFlashDataPost($post);
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();	
	}
	else if($r_act == 'delete' and $c_delete) {
		$conn->StartTrans();
		
		$isvalidpre = $p_model::isValidDinas($conn, $r_subkey);
		if ($isvalidpre)
			list($p_posterr,$p_postmsg) = mPresensi::deleteFormDinas($conn, $r_subkey);

		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'pe_biayadinas','nodinas');
			
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where,'','filekedinasan,filevalidasidinas');
		
		$conn->CompleteTrans();
		
		if(!$p_posterr)
			Route::navListpage($p_listpage,$r_key);
	}
	else if($r_act == 'deletefile' and $c_delete) {
		$r_file = CStr::removeSpecial($_POST['file']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,$r_file,$where);				
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();	
	}
	else if($r_act == 'insertdet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		$record = array('nodinas' => $r_subkey);
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_value = $_POST[$r_detail.'_'.CStr::cEmChg($t_detail['nameid'],$t_detail['kolom'])];
			$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
		}
		
		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);		
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();	
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkeydet = CStr::removeSpecial($_POST['subkeydet']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkeydet,$r_detail,'pe_biayadinas');		
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();
	}
	else if($r_act == 'upload') {
		$a_inputup = array();
		$a_inputup[] = array('kolom' => 'filevalidasidinas', 'label' => 'File Materi Dinas', 'type' => 'U', 'uptype' => 'filevalidasidinas', 'size' => 40);
		
		list($post,$record) = uForm::getPostRecord($a_inputup,$_POST);
		
		$where = "nodinas";
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_inputup,$record,$r_subkey,$p_dbtable,$where);				
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();
	}
	else if($r_act == 'valid') {		
		$record = array();
		$record['isvalid'] = 'Y';
		
		$where = "nodinas";
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,true,$p_dbtable,$where);				
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();
	}
	else if($r_act == 'unvalid') {		
		$record = array();
		$record['isvalid'] = 'null';
		
		$where = "nodinas";
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,true,$p_dbtable,$where);			
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();
	}
	
	if (!empty($r_subkey)){
		$a_role = $p_model::getValidasiDinas($conn, $r_subkey);
				
		if (($a_role['issetujuatasan'] == 'T' or  $a_role['issetujukasdm'] == 'T' or $a_role['issetujuwarek2'] == 'T' or $a_role['issetujukabagkeu'] == 'T') and !$c_valid){
			$c_edit = false;
			$c_delete = false;
		}
		
		if (($a_role['issetujuatasan'] == 'S' or  $a_role['issetujukasdm'] == 'S' or $a_role['issetujuwarek2'] == 'S' or $a_role['issetujukabagkeu'] == 'S') and !$c_valid){
			$c_edit = false;
			$c_delete = false;
		}
	}
		
	$p_postmsg = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : $p_postmsg;
	$p_posterr = !empty($_REQUEST['err']) ? $_REQUEST['err'] : $p_posterr;
	if($p_posterr)
		$post = Route::getFlashDataPost();
	
	$sql = $p_model::getDataEditRDinas($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	$a_rate = array();
	$a_rate = $p_model::jenisRatePerjalanan($conn);
	
	if(!empty($r_subkey)) {
		$a_persetujuan = SDM::statusPersetujuan();
				
		$rowd = array();
		$rowd += $p_model::getBiayaDinas($conn,$r_subkey,'rateperjalanan',$post);
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
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foreditx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post" action="<?= Route::navAddress(Route::thisPage()) ?>" enctype="multipart/form-data">
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
						<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
							<img src="images/undo.png"> Batal
						</td>
						<?	} if($c_delete and !empty($r_subkey)) { ?>
						<td id="be_delete" class="TDButton" onclick="goDelete()">
							<img src="images/delete.png"> Hapus
						</td>
						<? }
						if (!empty($r_subkey)) {
						?>
						<td id="be_print" class="TDButton" onclick="goPrint()">
							<img src="images/small-print.png">Surat Dinas
						</td>
						<td id="be_printrate" class="TDButton" onclick="goPrintRt()">
							<img src="images/small-print.png">Rate Perjalanan
						</td>
						<? } ?>
					</tr>
				</table>		
					
				<?	}
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
						<? if (!empty($r_subkey)) {?>
						<tr>
							<td class="LeftColumnBG" width="200"><?= Page::getDataLabel($row,'nosurat') ?></td>
							<td class="RightColumnBG"><?=  (Page::getDataValue($row,'nosurat') != '' ? Page::getDataInput($row,'nosurat') : 'Belum disetujui'); ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglusulan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglusulan') ?></td>
						</tr>
						<? } ?>
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
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filekedinasan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'filekedinasan') ?></td>
						</tr>
						<? if (!empty($r_subkey)) {?>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filevalidasidinas') ?></td>
							<td class="RightColumnBG">
								<? 
								$filevalid = Page::getDataValue($row,'filevalidasidinas');
								if (!empty($filevalid)) {?>
								<a href="javascript:goDownload('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('filevalidasidinas') ?> ')" class="ULink"><?= Page::getDataValue($row,'filevalidasidinas') ?></a>
								<div class="Break"></div>
								<u class="ULink" onclick="goDeleteFile('filevalidasidinas')">Hapus file</u>
								<? }else{ 
									if (!$c_edit and $c_kepeg){
								?>
								<input id="filevalidasidinas" class="ControlStyle" type="file" size="40" name="filevalidasidinas">
								<input type="text" name="tname" id="tname" value="" style="display:none" />
								<input type="button" name="bup" id ="bup" value="Upload" class="ControlStyle" onClick="goUpload()" />
								<? }} ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td class="RightColumnBG">
							<? 
								$isvalid = Page::getDataValue($row, 'isvalid');
								if ($isvalid != 'Y'){
									if ($c_valid){
							?>
							<input type="button" name="badd" id ="badd" value="Validasi" class="ControlStyle" onClick="goValid()" />
							<? }
							}else{ ?>
							<img src="images/check.png" title="Valid" />
							<input type="button" name="badd" id ="badd" value="Un Validasi" class="ControlStyle" onClick="goUnValid()" />
							<? } ?>
							</td>
						</tr>
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
										<td class="RightColumnBG"><?= ($c_valid) ? Page::getDataInput($row,'issetujuatasan') : $a_persetujuan[Page::getDataValue($row,'issetujuatasan')]; ?></td>
										<td><strong><?= Page::getDataInput($row,'namapejabat') ?></strong></td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'issetujukasdm') ?></td>
										<td class="RightColumnBG"><?= ($c_valid) ? Page::getDataInput($row,'issetujukasdm') : $a_persetujuan[Page::getDataValue($row,'issetujukasdm')]; ?></td>
										<td><strong><?= Page::getDataInput($row,'namasdm') ?></strong></td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'issetujukabagkeu') ?></td>
										<td class="RightColumnBG"><?= ($c_valid) ? Page::getDataInput($row,'issetujukabagkeu') : $a_persetujuan[Page::getDataValue($row,'issetujukabagkeu')]; ?></td>
										<td><strong><?= Page::getDataInput($row,'namakeu') ?></strong></td>
									</tr>
									<?/*<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'issetujuwarek2') ?></td>
										<td class="RightColumnBG"><?= ($c_valid) ? Page::getDataInput($row,'issetujuwarek2') : $a_persetujuan[Page::getDataValue($row,'issetujuwarek2')]; ?></td>
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
				<? 
				if(!empty($r_subkey) and $c_valid) { ?>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a href="javascript:void(0)">Biaya Rate</a></li>
						<li><a href="javascript:void(0)">Realisasi</a></li>
					</ul>					
										
					<div id="items">
					<?= Page::getDetailTable($rowd,$a_detail,'rateperjalanan','Tarif Rate Perjalanan',$c_detailedit,$c_detaildelete) ?>
					</div>
					
					<div id="items">
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
							<tr class="DataBG" height="30">
								<td colspan="2">Data Realisasi</td>
							</tr>
							<tr>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunit') ?></td>
								<td class="RightColumnBG"><?= Page::getDataInput($row,'idunit') ?></td>
							</tr>
							<tr>
								<td class="LeftColumnBG" width="200"><?= Page::getDataLabel($row,'jmldicairkan') ?></td>
								<td class="RightColumnBG"><?= Page::getDataInput($row,'jmldicairkan') ?></td>
							</tr>
							<tr>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tgldicairkan') ?></td>
								<td class="RightColumnBG"><?= Page::getDataInput($row,'tgldicairkan') ?></td>
							</tr>
						</table>
					</div>
						
				</center>
				<? } ?>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<input type="hidden" name="subkeydet" id="subkeydet">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="file" id="file">
				<?	} ?>
			</form>
		</td>
	</tr>
</table>

<iframe name="upload_iframe" style="display:none;"></iframe>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var detform = "<?= Route::navAddress('pop_filekedinasan') ?>";
var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {	
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$("input[name='pejabatpenugas']").xautox({strpost: "f=acpejabat", targetid: "idpegawaitugas", imgchkid: "imgid", imgavail: true});
	
	initTab(); 
});

function openDetail(pkey, pkeydet){
    $.ajax({
        url: detform,
        type: "POST",
        data: {key : pkey, subkey : pkeydet},
        success: function(data){
            $.facybox(data);
        }
    });
}


function goSave() {
	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		document.getElementById("pageform").target = "upload_iframe";
		document.getElementById("act").value = "save";
		document.getElementById("pageform").submit();
	}
}

function goUpload() {
	document.getElementById("pageform").target = "upload_iframe";
	document.getElementById("act").value = "upload";
	document.getElementById("pageform").submit();
}


function goPrint() {
	window.open("<?= Route::navAddress($p_printpage) ?>"+"&key=<?= $r_subkey?>","_blank");
}

function goPrintRt() {
	window.open("<?= Route::navAddress($p_printpagerate) ?>"+"&key=<?= $r_subkey?>","_blank");
}

function goValid() {
	var valid = confirm('Apakah anda yakin untuk validasi bukti kedinasan ?');
	if (valid){
		document.getElementById("pageform").target = "upload_iframe";
		document.getElementById("act").value = "valid";
		document.getElementById("pageform").submit();
	}
}

function goUnValid() {
	var valid = confirm('Apakah anda yakin untuk membatalkan validasi bukti kedinasan ?');
	if (valid){
		document.getElementById("pageform").target = "upload_iframe";
		document.getElementById("act").value = "unvalid";
		document.getElementById("pageform").submit();
	}
}

</script>
</body>
</html>
