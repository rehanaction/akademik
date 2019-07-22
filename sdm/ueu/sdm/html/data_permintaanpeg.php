<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_other = $a_auth['canother'];
	
	$c_validasi = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('rekrutmen'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$pkey = CStr::removeSpecial($_REQUEST['pkey']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Permintaan Pegawai Unit';
	$p_tbwidth = 900;
	$p_aktivitas = 'STRUKTUR';
	$p_listpage = Route::getListPage();
	$p_printpage = 'rep_formpengajuan';
	
	$p_model = mRekrutmen;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$p_dbtable = "re_rekrutmen";
	$p_key = "idrekrutmen";

	//jurusan
	$a_unit = mCombo::unitSave($conn,false);
	$a_jurusan = $p_model::getJurusan($conn);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'unitperekrut', 'label' => 'Unit yang Membutuhkan', 'type' => 'S', 'option' => $a_unit, 'empty' => '-- Pilih Unit yang Membutuhkan --');
	$a_input[] = array('kolom' => 'tglrekrutmen', 'label' => 'Tgl Permintaan', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglterakhir', 'label' => 'Tgl Penutupan', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglaktifbekerja', 'label' => 'Tgl. Mulai Bekerja', 'type' => 'D');
	// $a_input[] = array('kolom' => 'jnspegdirekrut', 'label' => 'Jenis Pegawai', 'type' => 'S', 'option' => $p_model::jenispegawaiRE($conn), 'empty' => true, 'notnull' => true);
	// $a_input[] = array('kolom' => 'idmilikpeg', 'label' => 'Kelompok', 'type' => 'S', 'option' => mPegawai::milikPeg($conn), 'empty' => true, 'notnull' => true);
	//$a_input[] = array('kolom' => 'posisikaryawan', 'label' => 'Posisi', 'type' => 'A', 'maxlength' => 1000, 'cols' => 30, 'rows'=>4);	

	$a_input[] = array('kolom' => 'idtipepeg', 'label' => 'Tipe Pegawai', 'type' => 'S', 'option' => mCombo::tipepegawaibaru($conn), 'empty' => true, 'notnull' => true, 'add' => 'onchange="changeJenis()"');
	$a_input[] = array('kolom' => 'idjenispeg', 'label' => 'Jenis Pegawai', 'type' => 'S', 'option' => mCombo::jenispegawaibaru($conn), 'empty' => true, 'notnull' => true, 'add' => 'onchange="changeKelompok()"');
	$a_input[] = array('kolom' => 'idkelompok', 'label' => 'Kelompok Pegawai', 'type' => 'S', 'option' => mCombo::kelompokpeg($conn), 'empty' => true, 'notnull' => true, 'add' => 'onchange="changeKelompok()"');
	$a_input[] = array('kolom' => 'kodeposisi', 'label' => 'Posisi', 'type' => 'S', 'option' => $p_model::getPosisi($conn));
	$a_input[] = array('kolom' => 'tugaskaryawan', 'label' => 'Uraian Singkat Tugas', 'type' => 'A', 'maxlength' => 255, 'cols' => 30, 'rows'=>4);
	$a_input[] = array('kolom' => 'alasanrekrutmen', 'label' => 'Alasan Rekrutmen', 'type' => 'A', 'maxlength' => 255, 'cols' => 30, 'rows'=>4);
	$a_input[] = array('kolom' => 'jenisrekrutmen', 'label' => 'Jenis Rekrutmen', 'type' => 'S', 'option' => $p_model::jenisRekrutmen(), 'notnull' => true);
	$a_input[] = array('kolom' => 'jmldibutuhkan', 'label' => 'Jumlah Karyawan yang dibutuhkan', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	//$a_input[] = array('kolom' => 'ismanpower', 'label' => 'Sesuai Man Power Planning', 'type' => 'S', 'option' => SDM::getValid());
	
	$a_input[] = array('kolom' => 'syaratusiamin', 'label' => 'Syarat Usia', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'syaratusiamax', 'label' => 'Syarat Usia Max.', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'jeniskelamin', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mBiodata::jeniskelamin(), 'empty' => true);
	$a_input[] = array('kolom' => 'syaratpengalaman', 'label' => 'Bidang Pengalaman', 'type' => 'A', 'maxlength' => 1000, 'cols' => 30, 'rows'=>4);
	$a_input[] = array('kolom' => 'syaratkeahlian', 'label' => 'Keahlian', 'type' => 'A', 'maxlength' => 1000, 'cols' => 30, 'rows'=>4);
	$a_input[] = array('kolom' => 'syaratgaji', 'label' => 'Syarat Gaji', 'maxlength' => 14, 'size' => 14, 'type' => 'N');
	$a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'maxlength' => 1000, 'cols' => 30, 'rows'=>4);
	
	$a_input[] = array('kolom' => 'idpendidikan', 'label' => 'Jenjang Pendidikan', 'type' => 'S', 'option' => $p_model::getJenjangPend($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'idjurusan', 'label' => 'Jurusan', 'type' => 'S', 'option' => $a_jurusan, 'empty' => '-- Pilih Jurusan --');
	$a_input[] = array('kolom' => 'ipk', 'label' => 'IPK', 'maxlength' => 5, 'size' => 5, 'type' => 'N,2');
	$a_input[] = array('kolom' => 'toefl', 'label' => 'TOEFL', 'maxlength' => 5, 'size' => 5, 'type' => 'N');
	
	$a_input[] = array('kolom' => 'syaratkepemimpinan', 'label' => 'Kepemimpinan', 'type' => 'A', 'maxlength' => 100, 'cols' => 30, 'rows'=>4);
	$a_input[] = array('kolom' => 'syaratketelitian', 'label' => 'Ketelitian Kerja', 'type' => 'R', 'option' => $p_model::aKetelitian());
	$a_input[] = array('kolom' => 'syaratkecepatan', 'label' => 'Kecepatan Kerja', 'type' => 'R', 'option' => $p_model::aKecepatan());
	$a_input[] = array('kolom' => 'syaratkecerdasan', 'label' => 'Kecerdasan', 'type' => 'R', 'option' => $p_model::aKecerdasan());
	$a_input[] = array('kolom' => 'syaratsosial', 'label' => 'Kontak Sosial', 'type' => 'R', 'option' => $p_model::aSosial());
	$a_input[] = array('kolom' => 'syaratketrampilan', 'label' => 'Ketrampilan', 'type' => 'A', 'maxlength' => 100, 'cols' => 30, 'rows'=>4);
	$a_input[] = array('kolom' => 'syaratbahasa', 'label' => 'Bahasa', 'type' => 'A', 'maxlength' => 100, 'cols' => 30, 'rows'=>4);
	$a_input[] = array('kolom' => 'syaratlain', 'label' => 'Lain', 'type' => 'A', 'maxlength' => 100, 'cols' => 30, 'rows'=>4);
	
	if ($c_validasi){
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Validasi Kepegawaian', 'type' => 'C', 'option' => SDM::getVerifikasi());
		$a_input[] = array('kolom' => 'isclose', 'label' => 'Status', 'type' => 'C', 'option' => SDM::getStatusClose());
	}else{
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Validasi Kepegawaian', 'type' => 'C', 'option' => SDM::getVerifikasi(), 'readonly' => true);
		$a_input[] = array('kolom' => 'isclose', 'label' => 'Status', 'type' => 'C', 'option' => SDM::getStatusClose(), 'readonly' => true);
	}

		// mengambil data detail
	$a_detail = array();
	
	$t_detail = array();
	$t_detail[] = array('kolom' => 'idproses', 'label' => 'Proses Seleksi', 'type' => 'S', 'option' => mRekrutmen::prosesSeleksi($conn));
	$t_detail[] = array('kolom' => 'urutan', 'label' => 'Urutan', 'size' => 2, 'maxlength' => 2);
	
	$a_detail['seleksi'] = array('key' => $p_model::getDetailInfo('seleksi','key'), 'data' => $t_detail);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		unset($record['unitperekrut'],$_POST['unitperekrut'],$record['idjurusan'],$_POST['idjurusan']);
		
		$conn->BeginTrans();
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,'',true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable);
		
		if(!$p_posterr){
			if(count($_POST['idunit'])>0){
				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_unit','idrekrutmen');
				
				foreach ($_POST['idunit'] as $keyunit => $idunit) {
					$recunit = array();
					$recunit['idrekrutmen'] = $r_key;
					$recunit['idunit'] = $idunit;

					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$recunit,true,'re_unit');
				}
			}
		}

		if(!$p_posterr){
			if(count($_POST['kodejurusan'])>0){
				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_jurusan','idrekrutmen');
				
				foreach ($_POST['kodejurusan'] as $keyjurusan => $kodejurusan) {
					$recjurusan = array();
					$recjurusan['idrekrutmen'] = $r_key;
					$recjurusan['kodejurusan'] = $kodejurusan;

					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$recjurusan,true,'re_jurusan');
				}
			}
		}

		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();

		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_prosesseleksi',$p_key);
		
		if(empty($p_posterr))
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_mekanisme',$p_key);

		if(empty($p_posterr))
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);

		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit) {		
		$conn->BeginTrans();
		
		if(empty($r_key)){
			list($post,$record) = uForm::getPostRecord($a_input,$_POST);
			unset($record['idjurusan'],$_POST['idjurusan']);

			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,'',true);

			if(!$p_posterr){
				if(count($_POST['kodejurusan'])>0){
					list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_jurusan','idrekrutmen');
					
					foreach ($_POST['kodejurusan'] as $keyjurusan => $kodejurusan) {
						$recjurusan = array();
						$recjurusan['idrekrutmen'] = $r_key;
						$recjurusan['kodejurusan'] = $kodejurusan;

						list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$recjurusan,true,'re_jurusan');
					}
				}
			}
		}
		
		if(!$p_posterr){
			unset($record,$post);
			$r_detail = CStr::removeSpecial($_POST['detail']);
			
			$record = array('idrekrutmen' => $r_key);
			foreach($a_detail[$r_detail]['data'] as $t_detail) {
				$t_value = $_POST[$r_detail.'_'.CStr::cEmChg($t_detail['nameid'],$t_detail['kolom'])];
				$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
			}
			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
		}
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail,'re_mekanisme');
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}

	$a_requireddet = array('seleksi_urutan');
	if(!empty($r_key)) {
		$rowd = array();
		$rowd += mRekrutmen::getProsesSeleksi($conn,$r_key,'seleksi',$post);

		//jurusan
		$a_unitrek = $p_model::getUnitRek($conn,$r_key);
		$a_jurusanrek = $p_model::getJurusanRek($conn,$r_key);
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
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/foredit.js"></script>
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
						<?	}if (!empty($r_key)){ ?>
						<td id="be_print" class="TDButton" onclick="goPrint('<?= $r_key; ?>')">
							<img src="images/small-print.png">&nbsp;Form Pengajuan
						</td>
						<? } ?>
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
					<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
						<tbody>
							<tr>
								<td width="250px"><?= Page::getDataLabel($row,'jenisrekrutmen') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'jenisrekrutmen') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tglrekrutmen') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'tglrekrutmen') ?> s/d <?= Page::getDataInput($row,'tglterakhir') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tglaktifbekerja') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'tglaktifbekerja') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'isvalid') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'isvalid') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'isclose') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'isclose') ?></td>
							</tr>
							<tr id="tr_unitperekrut">
								<td><?= Page::getDataLabel($row,'unitperekrut') ?></td>
								<td>:</td>
								<td>
									<?= Page::getDataInput($row,'unitperekrut') ?>
									<span id="edit" style="display: none">&nbsp;<img src="images/add.png" onclick="addUnit()" title="Tambah Unit yang Membutuhkan" style="cursor: pointer;"></span>
								</td>
							</tr>
							<?
								if(count($a_unitrek)){
									foreach ($a_unitrek as $kunit => $vunit) {
							?>
							<tr>
								<td>&nbsp;</td>
								<td>:</td>
								<td>
									<?= $vunit?><input type="hidden" name="idunit[]" id="idunit[]" value="<?= $kunit?>">
									<span id="edit" style="display: none">&nbsp;<img style="cursor:pointer" onclick="deleteBaris(this)" src="images/delete.png" title="Hapus baris"></span>
								</td>
							</tr>
							<?
									}
								}
							?>
							<tr height="30">
								<td colspan="4" class="DataBG">Informasi Kebutuhan</td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idtipepeg') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idtipepeg') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idjenispeg') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idjenispeg') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idkelompok') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idkelompok') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'kodeposisi') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'kodeposisi') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tugaskaryawan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'tugaskaryawan') ?></td>
							</tr>
							<?/*
							<tr>
								<td><?= Page::getDataLabel($row,'ismanpower') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'ismanpower') ?></td>
							</tr>
							*/?>
							<tr>
								<td><?= Page::getDataLabel($row,'jmldibutuhkan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'jmldibutuhkan') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'alasanrekrutmen') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'alasanrekrutmen') ?></td>
							</tr>
						</tbody>
						</table>
					</div>
				</center>					
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Syarat Dasar</a></li>
						<li><a id="tablink" href="javascript:void(0)">Syarat Pendidikan</a></li>
						<li><a id="tablink" href="javascript:void(0)">Syarat Tambahan</a></li>
						<li><a id="tablink" href="javascript:void(0)">Proses Seleksi</a></li>
					</ul>
				
					<div id="items">
						<table cellpadding="4" cellspacing="2" align="center" class="bottomline">
							<tr height="30">
								<td colspan="4" class="DataBG">Persyaratan Dasar</td>
							</tr>
							<tr>
								<td width="200px"><?= Page::getDataLabel($row,'jeniskelamin') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'jeniskelamin') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratusiamin') ?></td>
								<td>:</td>
								<td>Min <?= Page::getDataInput($row,'syaratusiamin') ?> , Max <?= Page::getDataInput($row,'syaratusiamax') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratgaji') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratgaji') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratpengalaman') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratpengalaman') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratkeahlian') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratkeahlian') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'catatan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'catatan') ?></td>
							</tr>
						</table>
					</div>
					
					<div id="items">
						<table cellpadding="4" cellspacing="2" align="center" class="bottomline">
							<tr height="30">
								<td colspan="4" class="DataBG">Persyaratan Pendidikan</td>
							</tr>
							<tr>
								<td width="200px"><?= Page::getDataLabel($row,'idpendidikan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idpendidikan') ?></td>
							</tr>
							<tr id="tr_jurusan">
								<td><?= Page::getDataLabel($row,'idjurusan') ?></td>
								<td>:</td>
								<td>
									<?= Page::getDataInput($row,'idjurusan') ?>
									<span id="edit" style="display: none">&nbsp;<img src="images/add.png" onclick="addJurusan()" title="Tambah Jurusan" style="cursor: pointer;"></span>
								</td>
							</tr>
							<?
								if(count($a_jurusanrek)){
									foreach ($a_jurusanrek as $kjurusan => $vjurusan) {
							?>
							<tr>
								<td>&nbsp;</td>
								<td>:</td>
								<td>
									<?= $a_jurusan[$vjurusan]?>
									<input type="hidden" name="kodejurusan[]" id="kodejurusan[]" value="<?= $vjurusan?>">
									<span id="edit" style="display: none">&nbsp;<img style="cursor:pointer" onclick="deleteBaris(this)" src="images/delete.png" title="Hapus baris"></span>
								</td>
							</tr>
							<?
									}
								}
							?>
							<tr>
								<td><?= Page::getDataLabel($row,'ipk') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'ipk') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'toefl') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'toefl') ?></td>
							</tr>
					</table>
					</div>					
					
					<div id="items">
						<table cellpadding="4" cellspacing="2" align="center" class="bottomline">
							<tr height="30">
								<td colspan="4" class="DataBG">Persyaratan Tambahan</td>
							</tr>
							<tr>
								<td width="200px"><?= Page::getDataLabel($row,'syaratkepemimpinan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratkepemimpinan') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratketelitian') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratketelitian') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratkecepatan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratkecepatan') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratkecerdasan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratkecerdasan') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratsosial') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratsosial') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratketrampilan') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratketrampilan') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratbahasa') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratbahasa') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'syaratlain') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'syaratlain') ?></td>
							</tr>
						</table>
					</div>
					<div id="items">
					<?= Page::getDetailTable($rowd,$a_detail,'seleksi','Proses Seleksi') ?>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="idx" id="idx">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
var detailreq = "<?= @implode(',',$a_requireddet) ?>";

$(document).ready(function() {
	initEdit(<?= (empty($post) and $_POST['idx'] != '3') ? false : true ?>);
	initTab('<?= $_POST['idx']?>');
	changeJenis();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function changeJenis() {
	var posted = "f=optjenispegawaibaru&q[]="+$("#idtipepeg").val()+"&q[]="+$("#idjenispeg").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#idjenispeg").html(text);
		changeKelompok();
	});
}

function changeKelompok() {
	var posted = "f=optkelompok&q[]="+$("#idjenispeg").val()+"&q[]="+$("#idkelompok").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#idkelompok").html(text);
	});
}

function addUnit(){
	if(cfHighlight('unitperekrut')){
		var str = '';
		str += '<tr>';
		str += '<td>&nbsp;</td>';
		str += '<td>:</td>';
		str += '<td>'+$("#unitperekrut option:selected").text().trim()+'<input type="hidden" name="idunit[]" id="idunit[]" value="'+$("#unitperekrut").val()+'">';
		str += '&nbsp;<img style="cursor:pointer" onclick="deleteBaris(this)" src="images/delete.png" title="Hapus baris">';
		str += '</td>';
		str += '</tr>';

		$(str).insertAfter($("#tr_unitperekrut").closest("tr"));
	}
}

function addJurusan(){
	if(cfHighlight('idjurusan')){
		var str = '';
		str += '<tr>';
		str += '<td>&nbsp;</td>';
		str += '<td>:</td>';
		str += '<td>'+$("#idjurusan option:selected").text()+'<input type="hidden" name="kodejurusan[]" id="kodejurusan[]" value="'+$("#idjurusan").val()+'">';
		str += '&nbsp;<img style="cursor:pointer" onclick="deleteBaris(this)" src="images/delete.png" title="Hapus baris">';
		str += '</td>';
		str += '</tr>';

		$(str).insertAfter($("#tr_jurusan").closest("tr"));
	}
}

function deleteBaris(img) {
	var retval;
	retval = confirm("Anda yakin untuk menghapus kolom ini?");
	if (retval) {
		$(img).parent().parent().replaceWith("");
	}
}

function goPrint(id) {
	goShowPage(id,'<?= Route::navAddress($p_printpage) ?>');
}

</script>
</body>
</html>
