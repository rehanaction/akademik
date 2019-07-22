<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_upload = $a_auth['canother']['U'];
	
	// include
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_self = (int)$_REQUEST['self'];
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getUserIDPegawai();
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	// properti halaman
	$p_title = 'Data Pegawai';
	$p_tbwidth = 800;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
	$p_foto = uForm::getPathImagePegawai($conn,$r_key);
	
	$p_model = mPegawai;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
		// struktur view
	$a_input = array();
	if (Akademik::isAdmin() || Akademik::isKepegawaian()) {
		$a_input[] = array('kolom' => 'idpegawai', 'label' => 'ID Pegawai', 'maxlength' => 25, 'size' => 20);
		$a_input[] = array('kolom' => 'username', 'label'=>'Username Login');
		$a_input[] = array('kolom' => 'nik', 'label' => 'N I K', 'maxlength' => 25, 'size' => 20, 'notnull' => false);
		$a_input[] = array('kolom' => 'gelardepan', 'maxlength' => 20, 'size' => 20);
		$a_input[] = array('kolom' => 'idjenispegawai', 'label'=>'Jenis Pegawai', 'type' => 'S', 'option' => $p_model::jenisPegawai2($conn), 'empty' => true);
		$a_input[] = array('kolom' => 'namadepan', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 50, 'notnull' => false);
		//$a_input[] = array('kolom' => 'namatengah', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
		//$a_input[] = array('kolom' => 'namabelakang', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
		$a_input[] = array('kolom' => 'gelarbelakang', 'maxlength' => 20, 'size' => 20);
		$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' =>  $p_model::kodeUnitsdm($conn), 'empty'=> true);
		$a_input[] = array('kolom' => 'idtipepeg', 'label' => 'Tipe Pegawai', 'type' => 'S', 'option' => $p_model::tipePegawai($conn), 'empty' => true, 'notnull' => false);
		$a_input[] = array('kolom' => 'jabatan', 'label' => 'Jabatan');
		$a_input[] = array('kolom' => 'idhubkerja', 'label' => 'Status Tetap', 'type' => 'S', 'option' => $p_model::statusTetap($conn));
		$a_input[] = array('kolom' => 'idstatusaktif', 'label' => 'Status Pegawai', 'type' => 'S', 'option' => $p_model::statusPegawai($conn));
		$a_input[] = array('kolom' => 'isdosen', 'label' => 'Mengajar', 'type' => 'C', 'option' => array('-1' => 'Pegawai mengajar kuliah'));
	}else{
		$a_input[] = array('kolom' => 'idpegawai', 'label' => 'ID Pegawai', 'maxlength' => 25, 'size' => 20, 'readonly' => true);
		$a_input[] = array('kolom' => 'username', 'label'=>'Username Login', 'readonly' => true);
		$a_input[] = array('kolom' => 'nik', 'label' => 'N I K', 'maxlength' => 25, 'size' => 20, 'notnull' => false, 'readonly' => true);
		$a_input[] = array('kolom' => 'gelardepan', 'maxlength' => 20, 'size' => 20, 'readonly' => true);
		$a_input[] = array('kolom' => 'idjenispegawai', 'label'=>'Jenis Pegawai', 'type' => 'S', 'option' => $p_model::jenisPegawai2($conn), 'empty' => true, 'readonly' => true);
		$a_input[] = array('kolom' => 'namadepan', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 50, 'notnull' => false, 'readonly' => true);
		//$a_input[] = array('kolom' => 'namatengah', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
		//$a_input[] = array('kolom' => 'namabelakang', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
		$a_input[] = array('kolom' => 'gelarbelakang', 'maxlength' => 20, 'size' => 20, 'readonly' => true);
		$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' =>  $p_model::kodeUnitsdm($conn), 'empty'=> true, 'readonly' => true);
		$a_input[] = array('kolom' => 'idtipepeg', 'label' => 'Tipe Pegawai', 'type' => 'S', 'option' => $p_model::tipePegawai($conn), 'empty' => true, 'notnull' => false, 'readonly' => true);
		$a_input[] = array('kolom' => 'jabatan', 'label' => 'Jabatan', 'readonly' => true);
		$a_input[] = array('kolom' => 'idhubkerja', 'label' => 'Status Tetap', 'type' => 'S', 'option' => $p_model::statusTetap($conn), 'readonly' => true);
		$a_input[] = array('kolom' => 'idstatusaktif', 'label' => 'Status Pegawai', 'type' => 'S', 'option' => $p_model::statusPegawai($conn), 'readonly' => true);
		$a_input[] = array('kolom' => 'isdosen', 'label' => 'Mengajar', 'type' => 'C', 'option' => array('-1' => 'Pegawai mengajar kuliah'), 'readonly' => true);
	}
	$a_input[] = array('kolom' => 'idpegawai', 'label' => 'ID Pegawai', 'maxlength' => 25, 'size' => 20);
	$a_input[] = array('kolom' => 'username', 'label'=>'Username Login');
	$a_input[] = array('kolom' => 'nik', 'label' => 'N I K', 'maxlength' => 25, 'size' => 20, 'notnull' => false);
	$a_input[] = array('kolom' => 'gelardepan', 'maxlength' => 20, 'size' => 20);
	$a_input[] = array('kolom' => 'idjenispegawai', 'label'=>'Jenis Pegawai', 'type' => 'S', 'option' => $p_model::jenisPegawai2($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'namadepan', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 50, 'notnull' => false);
	//$a_input[] = array('kolom' => 'namatengah', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
	//$a_input[] = array('kolom' => 'namabelakang', 'label' => 'Nama Pegawai', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
	$a_input[] = array('kolom' => 'gelarbelakang', 'maxlength' => 20, 'size' => 20);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' =>  $p_model::kodeUnitsdm($conn), 'empty'=> true);
	$a_input[] = array('kolom' => 'idtipepeg', 'label' => 'Tipe Pegawai', 'type' => 'S', 'option' => $p_model::tipePegawai($conn), 'empty' => true, 'notnull' => false);
	$a_input[] = array('kolom' => 'jabatan', 'label' => 'Jabatan');
	$a_input[] = array('kolom' => 'idhubkerja', 'label' => 'Status Tetap', 'type' => 'S', 'option' => $p_model::statusTetap($conn));
	$a_input[] = array('kolom' => 'idstatusaktif', 'label' => 'Status Pegawai', 'type' => 'S', 'option' => $p_model::statusPegawai($conn));
	$a_input[] = array('kolom' => 'isdosen', 'label' => 'Mengajar', 'type' => 'C', 'option' => array('-1' => 'Pegawai mengajar kuliah'));
	
	$a_input[] = array('kolom' => 'noktp', 'label' => 'No. KTP', 'maxlength' => 20, 'size' => 20);
	$a_input[] = array('kolom' => 'npwp', 'label' => 'NPWP', 'maxlength' => 25, 'size' => 25);
	$a_input[] = array('kolom' => 'jeniskelamin', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => $p_model::jenisKelamin());
	$a_input[] = array('kolom' => 'idagama', 'label' => 'Agama', 'type' => 'S', 'option' => $p_model::agama($conn), 'empty' => '-- Pilih Agama --');
	$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tmp & Tgl Lahir', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl Lahir', 'type' => 'D');
	$a_input[] = array('kolom' => 'goldarah', 'label' => 'Gol Darah', 'type' => 'S', 'option' => $p_model::golonganDarah(), 'empty' => '-- Pilih Golongan Darah --');
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'maxlength' => 150, 'size' => 50);
	$a_input[] = array('kolom' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'kecamatan', 'label' => 'Kecamatan', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'kodepropinsi', 'label' => 'Propinsi', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'add' => 'onchange="loadKota()"', 'empty' => '-- Pilih Propinsi --');
	$a_input[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'type' => 'S', 'option' => mCombo::kota($conn), 'empty' => true, 'empty' => '-- Pilih Kota --');
	$a_input[] = array('kolom' => 'kodepos', 'label' => 'Kode Pos', 'type' => 'NP', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'telepon', 'label' => 'Telp', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'telp2', 'label' => 'Telp 2', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'nohp', 'label' => 'HP', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'hp2', 'label' => 'HP 2', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'emailpribadi', 'label' => 'Email 2', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => $p_model::statusNikah($conn));
	$a_input[] = array('kolom' => 'idkewarganegaraan', 'label' => 'Kewarganegaraan', 'type' => 'S', 'option' => $p_model::wargaNegara($conn));
	//$a_input[] = array('kolom' => 'tinggi', 'label' => 'Tinggi (cm)', 'maxlength' => 50, 'size' => 50);
	//$a_input[] = array('kolom' => 'beratbadan', 'label' => 'Berat Badan (kg)', 'maxlength' => 50, 'size' => 50);
	//$a_input[] = array('kolom' => 'rambut', 'label' => 'Rambut', 'maxlength' => 50, 'size' => 50);
	//$a_input[] = array('kolom' => 'bentukmuka', 'label' => 'Bentuk Muka', 'maxlength' => 50, 'size' => 50);
	//$a_input[] = array('kolom' => 'warnakulit', 'label' => 'Warna Kulit', 'maxlength' => 50, 'size' => 50);
	//$a_input[] = array('kolom' => 'cirikhas', 'label' => 'Ciri-ciri Khas', 'maxlength' => 100, 'size' => 50);
	//$a_input[] = array('kolom' => 'cacattubuh', 'label' => 'Cacat Tubuh', 'maxlength' => 100, 'size' => 50);
	//$a_input[] = array('kolom' => 'hobi', 'label' => 'Kegemaran (Hobi)', 'type' => 'A', 'rows' => 5, 'cols' => 50);
	
	$a_input[] = array('kolom' => 'nidn', 'label' => 'N I D N / N U P N', 'maxlength' => 10, 'size' => 20);
	$a_input[] = array('kolom' => 'semesteraktif', 'label' => 'Diangkat Dosen', 'type' => 'S', 'option' => mCombo::semester(), 'empty' => true);
	$a_input[] = array('kolom' => 'tahunaktif', 'type' => 'S', 'option' => mCombo::tahun(), 'empty' => true);
	$a_input[] = array('kolom' => 'semesterajar', 'label' => 'Aktif Mengajar', 'type' => 'S', 'option' => mCombo::semester(), 'empty' => true);
	$a_input[] = array('kolom' => 'tahunajar', 'type' => 'S', 'option' => mCombo::tahun(), 'empty' => true);
	$a_input[] = array('kolom' => 'adaijinmengajar', 'label' => 'Surat Ijin Mengajar', 'type' => 'C', 'option' => array('-1' => 'Pegawai memiliki surat ijin mengajar'));
	$a_input[] = array('kolom' => 'adasertifikat', 'label' => 'Sertifikasi Dosen', 'type' => 'C', 'option' => array('-1' => 'Pegawai memiliki sertifikat dosen'));
	$a_input[] = array('kolom' => 'aktamengajar', 'label' => 'Akta Mengajar', 'maxlength' => 50, 'size' => 30);

	$a_input[] = array('kolom' => 'idjfungsional', 'label' => 'Jabatan Fungsional', 'type' => 'S', 'option' => $p_model::jaFung($conn), 'empty' => true);

	//$a_input[] = array('kolom' => 'gaji', 'label' => 'Rate Honor', 'type' => 'S', 'option' => $p_model::rateHonor($conn), 'empty' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if($record['tahunaktif'] == 'null' or $record['semesteraktif'] == 'null')
			$record['periodeaktif'] = 'null';
		else
			$record['periodeaktif'] = $record['tahunaktif'].$record['semesteraktif'];
		
		if($record['tahunajar'] == 'null' or $record['semesterajar'] == 'null')
			$record['periodeajar'] = 'null';
		else
			$record['periodeajar'] = $record['tahunajar'].$record['semesterajar'];
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		//if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'savefoto' and $c_upload) {
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,800,600);
			switch($err) {
				case -1:
				case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
				case -3: $msg = 'foto tidak bisa disimpan'; break;
				default: $msg = false;
			}
			if($msg !== false)
				$msg = 'Upload gagal, '.$msg;

		}
		else
			$msg = Route::uploadErrorMsg($_FILES['foto']['error']);

		uForm::reloadImagePegawai($conn,$r_key,$msg);
	}
		
	else if($r_act == 'deletefoto' and $c_edit) {
		@unlink($p_foto);
		
		uForm::reloadImagePegawai($conn,$r_key);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key);
	$rate2 = Page::getDataValue($row,'idjfungsional');
	$rate = mPegawai::getRate($conn, $rate2);

							
	$r_dosen = Page::getDataValue($row,'isdosen');
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<link href="style/modal.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
	<style type="text/css">
		#imgfoto{
			width: 100%;
		}
	</style>
	
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
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idpegawai') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idpegawai') ?></td>
							<td align="center" valign="top" rowspan="12">
							<?	if(!empty($r_key)) { ?>
								<?= uForm::getImagePegawai($conn,$r_key,$c_upload) ?>
								<?php if($c_upload){ ?>
								<br><span>klik gambar untuk upload/hapus Photo</span>
								<br>Pas Foto formal<br>Ukuran 4x6 (berwarna)
								<?php } ?>
							<?	} ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'username') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'username') ?></td>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nik') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nik') ?></td>
							
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Nama Lengkap</td>
							<td class="RightColumnBG" width="50%">
								<table>
								<tr>
									<td>Gelar Depan</td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'gelardepan') ?></td>
								</tr>
								<tr>
									<td>Nama</td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'namadepan') ?></td>
								</tr>
								<tr>
									<td>Gelar Belakang</td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'gelarbelakang') ?></td>
								</tr>
								</table>
							</td>
						<tr>
						<?= Page::getDataTR($row,'idunit') ?>
						<?= Page::getDataTR($row,'idtipepeg') ?>
						<?= Page::getDataTR($row,'idjenispegawai') ?>
						<?= Page::getDataTR($row,'jabatan') ?>
						<?= Page::getDataTR($row,'idhubkerja') ?>
						<?= Page::getDataTR($row,'idstatusaktif') ?>
						<?= Page::getDataTR($row,'isdosen') ?>
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li class="active"><a id="tablink" href="javascript:void(0)">Biodata</a></li>
						<?	if(!empty($r_key)) {
								if(!empty($r_dosen)) { ?>
									<li><a id="tablink" href="javascript:void(0)">Dosen</a></li>
									<? } ?>

						<?	} ?>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="items">
							<table cellpadding="4" cellspacing="2" align="center">
								<?= Page::getDataTR($row,'noktp') ?>
								<?= Page::getDataTR($row,'npwp') ?>
								<?= Page::getDataTR($row,'tmplahir,tgllahir',', ') ?>
								<?= Page::getDataTR($row,'jeniskelamin') ?>
								<?= Page::getDataTR($row,'idagama') ?>
								<?= Page::getDataTR($row,'goldarah') ?>
								<?= Page::getDataTR($row,'statusnikah') ?>
								<?= Page::getDataTR($row,'idkewarganegaraan') ?>
								<?= Page::getDataTR($row,'telepon') ?>
								<?= Page::getDataTR($row,'nohp') ?>
								<?= Page::getDataTR($row,'email,emailpribadi','<div class="Break"></div>') ?>
								<tr>
									<td class="LeftColumnBG" style="white-space:nowrap">Alamat</td>
									<td class="RightColumnBG">
										<table>
											<tr>
												<td><?= Page::getDataLabel($row,'kodepropinsi') ?></td>
												<td>:</td>
												<td><?= Page::getDataInput($row,'kodepropinsi') ?></td>
											</tr>
											<tr>
												<td><?= Page::getDataLabel($row,'kodekota') ?></td>
												<td>:</td>
												<td><?= Page::getDataInput($row,'kodekota') ?></td>
											</tr>
											<tr>
												<td><?= Page::getDataLabel($row,'kelurahan') ?></td>
												<td>:</td>
												<td><?= Page::getDataInput($row,'kelurahan') ?></td>
											</tr>
											<tr>
												<td><?= Page::getDataLabel($row,'kecamatan') ?></td>
												<td>:</td>
												<td><?= Page::getDataInput($row,'kecamatan') ?></td>
											</tr>
											<tr>
												<td width="100"><?= Page::getDataLabel($row,'alamat') ?></td>
												<td width="5">:</td>
												<td><?= Page::getDataInput($row,'alamat') ?></td>
											</tr>
											<tr>
												<td><?= Page::getDataLabel($row,'kodepos') ?></td>
												<td>:</td>
												<td><?= Page::getDataInput($row,'kodepos') ?></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>	
						<?	if(!empty($r_key)) {
							if(!empty($r_dosen)) { ?>
						<div id="items">
						<table cellpadding="4" cellspacing="2" align="center">
							<?= Page::getDataTR($row,'nidn') ?>
							<?= Page::getDataTR($row,'idjfungsional') ?>
							<?= Page::getDataTR($row,'semesteraktif,tahunaktif') ?>
							<?= Page::getDataTR($row,'semesterajar,tahunajar') ?>
							<?= Page::getDataTR($row,'adaijinmengajar') ?>
							<?= Page::getDataTR($row,'adasertifikat') ?>
							<?= Page::getDataTR($row,'aktamengajar') ?>
							<? if(Akademik::isAdmin()) { ?>
								<tr>
									<td>Rate Honor</td>
									<td><?= $rate ?></td>
								</tr>
							<? } ?>
							
							
						</table>
						</div>
								<?		} ?>				
						
						</div>
					<?	} ?>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	loadKota();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

// ajax ganti kota
function loadKota() {
	var param = new Array();
	param[0] = $("#kodepropinsi").val();
	param[1] = "<?= $r_kodekota ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekota").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}


function sukses(msg){
	$(".DivSuccess").html(msg);
	$(".DivSuccess").show();
	$(".DivSuccess").fadeOut(2000);
}
function gagal(msg){
	$(".DivError").html(msg);
	$(".DivError").show();
	$(".DivError").fadeOut(2000);
}

</script>
</body>
</html>
