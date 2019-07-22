<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	//koneksi dengan akademik = sdm.ms_pegawai
	$connsia = Query::connect('akad');
	if($_SERVER['REMOTE_ADDR'] == "36.85.91.184") //ip public sevima
		$connsia->debug=true;
	
	// include
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getModelPath('honor'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	if(SDM::isPegawai())
		$r_self = 1;
	
	if($c_kepeg){
		$c_edit = $a_auth['canupdate'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	//konfigurasi halaman
	$p_model = mPegawai;
	$p_tbwidth = "800";
	$p_title = "Data Pegawai";
	$dirfoto = 'fotopeg';
	$p_aktivitas = 'BIODATA';
	$p_foto = uForm::getPathImageFoto($conn,$r_key,$dirfoto);
	
	$a_input = array();
	$a_input[] = array('kolom' => 'idpegawai', 'label' => 'ID System', 'readonly' => true);
	$a_input[] = array('kolom' => 'gelardepan', 'maxlength' => 25, 'size' => 15);
	$a_input[] = array('kolom' => 'gelarbelakang', 'maxlength' => 25, 'size' => 15);
	$a_input[] = array('kolom' => 'namadepan', 'label' => 'Depan', 'maxlength' => 100, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'namatengah', 'label' => 'Tengah', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'namabelakang', 'label' => 'Belakang', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'jeniskelamin', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => $p_model::jenisKelamin($conn), 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'idagama', 'label' => 'Agama', 'type' => 'S', 'option' => $p_model::agama($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tempat Lahir', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl. Lahir', 'type' => 'D','notnull' => true);
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => $p_model::statusNikah());
	
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => mCombo::unitSave($conn,false), 'notnull' => true);
	$a_input[] = array('kolom' => 'idstatusaktif', 'label' => 'Status Aktif', 'type' => 'S', 'option' => mCombo::statusaktif($conn), 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'idtipepeg', 'label' => 'Tipe Pegawai', 'type' => 'S', 'option' => mCombo::tipepegawai($conn), 'empty' => true, 'notnull' => true, 'add' => 'onchange="changeJenis(this.value)"');
	$a_input[] = array('kolom' => 'idjenispegawai', 'label' => 'Jenis Pegawai', 'type' => 'S', 'option' => mCombo::jenispegawai($conn), 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'idhubkerja', 'label' => 'Hubungan Kerja', 'type' => 'S', 'option' => mCombo::hubungankerja($conn), 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodejabatanatasan', 'label' => 'Jabatan Atasan', 'type' => 'S', 'option' => mCombo::strukturalSave($conn,false), 'empty' => true);
	$a_input[] = array('kolom' => 'npwp', 'label' => 'NPWP', 'maxlength' => 25, 'size' => 30);	
	$a_input[] = array('kolom' => 'nippns', 'label' => 'NIPPNS', 'maxlength' => 25, 'size' => 30);
	
	//field-field utk dosen
	$a_input[] = array('kolom' => 'nidn', 'label' => 'NIDN', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'nodosen', 'label' => 'No. Dosen', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'kodekeldosen', 'label' => 'Kelompok Dosen', 'type' => 'S', 'option' => $p_model::kelDosen($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodebidang', 'label' => 'Rumpun Bidang Dosen', 'type' => 'S', 'option' => mCombo::bidangSave($conn,false), 'empty' => true);	
	$a_input[] = array('kolom' => 'mkthn', 'label' => 'Masa Kerja Mengajar', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'mkbln', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'issesuaibidang', 'label' => 'Kesesuaian Bidang', 'type' => 'C', 'option' => array('Y' => 'Sesuai Bidang Dosen'));
	$a_input[] = array('kolom' => 'idstatusaktifhomebase', 'label' => 'Status Aktif Homebase', 'type' => 'S', 'option' => $p_model::statusAktifHomebase($conn), 'empty' => true);

	$a_input[] = array('kolom' => 'idmilikpeg', 'label' => 'Kelompok', 'type' => 'S', 'option' => $p_model::milikPeg($conn), 'empty' => true, 'notnull' => true);	
	$a_input[] = array('kolom' => 'nik', 'label' => 'N I K', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'tglmasuk', 'label' => 'Tgl. Masuk Kerja', 'type' => 'D');
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 100, 'size' => 50, 'infoedit' => 'Email Esa Unggul');
	$a_input[] = array('kolom' => 'usernameldap', 'label' => 'Username LDAP', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'nohandkey', 'label' => 'No. Handkey', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'niphkey', 'label' => 'NIP. Handkey', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'idfinger', 'label' => 'ID Finger', 'maxlength' => 10, 'size' => 10);
	
	$a_input[] = array('kolom' => 'nodapen', 'label' => 'No. Dapen', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'tgldapen', 'label' => 'Tgl. Dapen', 'type' => 'D');
	$a_input[] = array('kolom' => 'statusdapen', 'label' => 'Status Dapen', 'type' => 'S', 'option' => mCombo::isAktif(), 'empty' => true);
	
	$a_input[] = array('kolom' => 'idbank', 'label' => 'Nama Bank', 'type' => 'S', 'option' => $p_model::bank($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'norekening', 'label' => 'No. Rekening', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'anrekening', 'label' => 'Atas Nama Rekening', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'cabangbank', 'label' => 'Cabang Bank', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'idbankhonor', 'label' => 'Nama Bank', 'type' => 'S', 'option' => $p_model::bank($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'norekeninghonor', 'label' => 'No. Rekening', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'anrekeninghonor', 'label' => 'Atas Nama Rekening', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'cabangbankhonor', 'label' => 'Cabang Bank', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);	
	
	$r_act = $_POST['act'];
	$tipepeg = $p_model::getTipePegawai($conn,$r_key);
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		if($record['namadepan'] != 'null')
			$record['namadepan'] = strtoupper($record['namadepan']);
		if($record['namatengah'] != 'null')
			$record['namatengah'] = strtoupper($record['namatengah']);
		if($record['namabelakang'] != 'null')
			$record['namabelakang'] = strtoupper($record['namabelakang']);
		if($record['tmplahir'] != 'null')
			$record['tmplahir'] = strtoupper($record['tmplahir']);
		if($record['anrekening'] != 'null')
			$record['anrekening'] = strtoupper($record['anrekening']);
		if($record['cabangbank'] != 'null')
			$record['cabangbank'] = strtoupper($record['cabangbank']);
		if($record['anrekeninghonor'] != 'null')
			$record['anrekeninghonor'] = strtoupper($record['anrekeninghonor']);
		if($record['cabangbankhonor'] != 'null')
			$record['cabangbankhonor'] = strtoupper($record['cabangbankhonor']);
		
		if($record['nik'] != 'null'){
			$nip = $p_model::getNIP($conn,$r_key);
			if($nip != $record['nik'])
				$record['nip'] = $record['nik'];
		}
		
		if($record['mkthn'] == 'null' and $record['mkbln'] == 'null')
			$record['mkmengajar'] = 'null';
		else{
			if($record['mkthn'] == 'null')
				$record['mkthn'] = '0';
			if($record['mkbln'] == 'null')
				$record['mkbln'] = '0';
			$record['mkmengajar'] = str_pad($record['mkthn'],2,'0', STR_PAD_LEFT).str_pad($record['mkbln'],2,'0', STR_PAD_LEFT);
		}
		
		//pengecekan untuk membuat no. dosen
		if(($record['idtipepeg'] == 'D' or $record['idtipepeg'] == 'AD') and $record['nodosen'] == 'null')
			$record['nodosen'] = $p_model::createNoDosen($conn);
				
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr){
			$p_posterr = mIntegrasi::saveRoleGate($conn,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penyimpanan User Role ke Gate gagal';
		}
		
		if(!$p_posterr){
			if($record['idtipepeg'] == 'D' or $record['idtipepeg'] == 'AD'){
				$p_posterr = mIntegrasi::saveDosenSyncAkad($conn,$connsia,$r_key);
			}else{
				if($tipepeg == 'D' or $tipepeg == 'AD'){
					$p_posterr = mIntegrasi::saveDosenSyncAkad($conn,$connsia,$r_key,'TA');
				}

				//$p_posterr = mIntegrasi::saveNonDosenSyncAkad($conn,$connsia,$r_key);
			}
			
			if($p_posterr)
				$p_postmsg = 'Penyimpanan User ke Akademik gagal';
		}
		
		if(!$p_posterr){
			if($record['idtipepeg'] == 'D' or $record['idtipepeg'] == 'AD'){
				list($p_posterr,$p_postmsg) = mGaji::setRateHonor($conn,$r_key);
				if(!$p_posterr)
					list($p_posterr,$p_postmsg) = mHonor::setPPHHonor($conn,$r_key);
			}else{
				if($tipepeg == 'D' or $tipepeg == 'AD')
					$p_posterr = mGaji::unValidRateHonor($conn,$r_key);
				
				if($p_posterr)
					$p_postmsg = 'Penyimpanan Unvalid honor gagal';
			}
		}
		
		if(!$p_posterr){
			$r_periodegaji = mGaji::getLastPeriodeGaji($conn);
			mGaji::tarikData($conn,$r_periodegaji,'',$r_key);
		}

		if(!$p_posterr){
			$info = $p_model::getSimplePegawai($conn,$r_key);
			$r_namalengkap = $info['namalengkap'];
			$p_label = $r_namalengkap.(!empty($info['nik']) ? ' | '.$info['nik'] : '');
		?>
		<script type="text/javascript">
			window.parent.$("#labelpeg").html('<?= $p_label?>');
		</script>
		<?php
		}
		
		$ok = Query::isOK($p_posterr);
		if($ok){
			$conn->CommitTrans($ok);				
			unset($post);
		}else
			$conn->RollbackTrans();
	}
	else if($r_act == 'savefoto' and $c_edit) {		
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,200,200);
			
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
		
		uForm::reloadImageFoto($conn,$r_key,$dirfoto,$msg);
	}
	else if($r_act == 'deletefoto' and $c_edit) {
		@unlink($p_foto);
		
		uForm::reloadImageFoto($conn,$r_key,$dirfoto);
	}
	
	$sql = $p_model::getDataEditKepegawaian($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,'','',$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
			
		//pengecekan hak akses utk pegawai ybs, bila sudah valid
		if($t_row['id'] == 'isvalid'){
			$isvalid = $t_row['value'];
			if($isvalid and $r_self)
				$c_edit = false;
		}
		
		if($t_row['id'] == 'nidn')
			$nidn = $t_row['value'];
		if($t_row['id'] == 'nodosen')
			$nodosen = $t_row['value'];
		if($t_row['id'] == 'kodekeldosen')
			$kodekeldosen = $t_row['value'];
		if($t_row['id'] == 'kodebidang')
			$bidang = $t_row['realvalue'];
		if($t_row['id'] == 'issesuaibidang')
			$sesuai = $t_row['realvalue'];
		if($t_row['id'] == 'idstatusaktifhomebase')
			$statushomebase = $t_row['realvalue'];
		if($t_row['id'] == 'mkthn')
			$mkthn = $t_row['value'];
		if($t_row['id'] == 'mkbln')
			$mkbln = $t_row['value'];
	}
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
	<script type="text/javascript" src="scripts/foreditx.js"></script>
	<style>
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
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
						
						<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
						<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
							<tr>
								<td width="180"><?= Page::getDataLabel($row,'idpegawai') ?></td>
								<td width="10">:</td>
								<td><?= Page::getDataInput($row,'idpegawai') ?></td>
								<td rowspan="8" width="200" align="center" valign="top">
									<?= uForm::getImageFoto($conn,$r_key,$dirfoto,$c_edit) ?>
								</td>
							</tr>
							<tr>
								<td>Gelar</td>
								<td>:</td>
								<td>
									<table cellspacing="0" cellpadding="4" c>
										<tr><td>Depan<td><td>:</td><td><?= Page::getDataInput($row,'gelardepan') ?></td></tr>
										<tr><td>Belakang<td><td>:</td><td><?= Page::getDataInput($row,'gelarbelakang') ?></td></tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>Nama</td>
								<td>:</td>
								<td>
									<table cellspacing="0" cellpadding="4" c>
										<tr><td><?= Page::getDataLabel($row,'namadepan') ?><td><td>:</td><td><?= Page::getDataInput($row,'namadepan') ?></td></tr>
										<tr><td><?= Page::getDataLabel($row,'namatengah') ?><td><td>:</td><td><?= Page::getDataInput($row,'namatengah') ?></td></tr>
										<tr><td><?= Page::getDataLabel($row,'namabelakang') ?><td><td>:</td><td><?= Page::getDataInput($row,'namabelakang') ?></td></tr>
									</table>
								</td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'jeniskelamin') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'jeniskelamin') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idagama') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idagama') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tmplahir') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'tmplahir') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tgllahir') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'tgllahir') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'statusnikah') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'statusnikah') ?></td>
							</tr>
						</table>
						</div>
					</center>
					<br>
					<center>
					<div class="tabs" style="width:<?= $p_tbwidth ?>px">
						<ul>
							<li><a href="javascript:void(0)">Data Kepegawaian</a></li>
							<li><a href="javascript:void(0)">Dana Pensiun</a></li>
							<li><a href="javascript:void(0)">Pelengkap</a></li>
						</ul>
						
						<div id="items">
						<table cellpadding="4" cellspacing="0" align="center" class="bottomline">
							<tr>
								<td width="180"><?= Page::getDataLabel($row,'idunit') ?></td>
								<td width="10">:</td>
								<td><?= Page::getDataInput($row,'idunit') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idstatusaktif') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idstatusaktif') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idtipepeg') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idtipepeg') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idjenispegawai') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idjenispegawai') ?></td>
							</tr>	
							<tr>
								<td><?= Page::getDataLabel($row,'idhubkerja') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idhubkerja') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'kodejabatanatasan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'kodejabatanatasan') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'npwp') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'npwp') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'nippns') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'nippns') ?></td>
							</tr>
							<tr id="tr_nodosen">
								<td><?= Page::getDataLabel($row,'nodosen') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'nodosen') ?></td>
							</tr>
							<tr id="tr_nidn">
								<td><?= Page::getDataLabel($row,'nidn') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'nidn') ?></td>
							</tr>
							<tr id="tr_keldosen">
								<td><?= Page::getDataLabel($row,'kodekeldosen') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'kodekeldosen') ?></td>
							</tr>
							<tr id="tr_bidang">
								<td><?= Page::getDataLabel($row,'kodebidang') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'kodebidang') ?></td>
							</tr>
							<tr id="tr_sesuai">
								<td><?= Page::getDataLabel($row,'issesuaibidang') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'issesuaibidang') ?></td>
							</tr>
							<tr id="tr_statushomebase">
								<td><?= Page::getDataLabel($row,'idstatusaktifhomebase') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idstatusaktifhomebase') ?></td>
							</tr>
							<tr id="tr_mkmengajar">
								<td><?= Page::getDataLabel($row,'mkthn') ?></td>
								<td>:</td>
								<td>
									<?= Page::getDataInput($row,'mkthn') ?>&nbsp;tahun
									<?= Page::getDataInput($row,'mkbln') ?>&nbsp;bulan
								</td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idmilikpeg') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idmilikpeg') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'nik') ?></td>
								<td>:</td>
								<td>
									<?= Page::getDataInput($row,'nik') ?>
									<span id="show"></span>
									<span id="edit" style="display:none">
										<input type="button" value="NIP Terakhir" title="Lihat NIP Terakhir" class="ControlStyle" onClick="showLastNIP()">&nbsp;&nbsp;
										<span id="lastnik"></span>
									</span>
								</td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tglmasuk') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'tglmasuk') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'email') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'email') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'usernameldap') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'usernameldap') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'nohandkey') ?></td>
								<td>:</td>
								<td>
									<?= Page::getDataInput($row,'nohandkey') ?>
									&nbsp;&nbsp;&nbsp;NIP Handkey&nbsp;:&nbsp;
									<?= Page::getDataInput($row,'niphkey') ?>
								</td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idfinger') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idfinger') ?></td>
							</tr>
						</table>
						</div>
						
						<div id="items">
						<table cellpadding="4" cellspacing="0" align="center" class="bottomline">
							<tr>
								<td width="180"><?= Page::getDataLabel($row,'nodapen') ?></td>
								<td width="10">:</td>
								<td><?= Page::getDataInput($row,'nodapen') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tgldapen') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'tgldapen') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'statusdapen') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'statusdapen') ?></td>
							</tr>
						</table>
						</div>
						
						<div id="items">
						<table cellpadding="4" cellspacing="0" align="center" class="bottomline">
							<tr height="30">
								<td colspan="3" class="DataBG">Rekening Penggajian</td>
							</tr>
							<tr>
								<td width="180"><?= Page::getDataLabel($row,'idbank') ?></td>
								<td width="10">:</td>
								<td><?= Page::getDataInput($row,'idbank') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'norekening') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'norekening') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'anrekening') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'anrekening') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'cabangbank') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'cabangbank') ?></td>
							</tr>
							<tr height="30">
								<td colspan="3" class="DataBG">Rekening Honor</td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idbankhonor') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idbankhonor') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'norekeninghonor') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'norekeninghonor') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'anrekeninghonor') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'anrekeninghonor') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'cabangbankhonor') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'cabangbankhonor') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'keterangan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'keterangan') ?></td>
							</tr>
						</table>
						</div>
					</div>
					</center>
					
					<? } ?>
					<input type="hidden" name="act" id="act">
					<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
					<input type="hidden" name="detail" id="detail">
				</form>
			</td>
		</tr>
	</table>
	
<script type="text/javascript">
	
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	changeJenis();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function changeJenis() {
	var posted = "f=optjenispegawai&q[]="+$("#idtipepeg").val()+"&q[]="+$("#idjenispegawai").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#idjenispegawai").html(text);
	});
	
	if($("#idtipepeg").val() == 'D' || $("#idtipepeg").val() == 'AD'){
		$("#nodosen").val('<?= $nodosen?>');
		$("#nidn").val('<?= $nidn?>');
		$("#kodekeldosen").val('<?= $kodekeldosen?>');
		$("#kodebidang").val('<?= $bidang?>');
		$("#issesuaibidang").val('<?= $sesuai?>');
		$("#idstatusaktifhomebase").val('<?= $statushomebase?>');
		$("#mkthn").val('<?= $mkthn?>');
		$("#mkbln").val('<?= $mkbln?>');
		$("#tr_nodosen").show();
		$("#tr_nidn").show();
		$("#tr_keldosen").show();
		$("#tr_bidang").show();
		$("#tr_sesuai").show();
		$("#tr_statushomebase").show();
		$("#tr_mkmengajar").show();
	}else{
		$("#nodosen").val("");
		$("#nidn").val("");
		$("#kodekeldosen").val("");
		$("#kodebidang").val("");
		$("#issesuaibidang").val("");
		$("#idstatusaktifhomebase").val("");
		$("#mkthn").val("");
		$("#mkbln").val("");
		$("#tr_nodosen").hide();
		$("#tr_nidn").hide();
		$("#tr_keldosen").hide();
		$("#tr_bidang").hide();
		$("#tr_sesuai").hide();
		$("#tr_statushomebase").hide();
		$("#tr_mkmengajar").hide();
	}
}

function showLastNIP(){
	var posted = "f=gnik&q="+$("#idmilikpeg").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var text = text.split('|');
		if(text[0] == '1'){
			$("#lastnik").html('NIP Terakhir : <b>'+text[2]+'</b>');
			if($("#nik").val() == ''){
				$("#nik").val(text[1]);
			}
		}else{
			$("#lastnik").html(text[2]);
		}
	});
}
</script>
</body>
</html>