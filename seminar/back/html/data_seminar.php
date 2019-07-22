<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	 
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$c_validate = $a_auth['canother']['V'];
	
	// include
	require_once(Route::getModelPath('seminar'));
	require_once(Route::getModelPath('jenisseminar'));
	require_once(Route::getModelPath('penyelenggaraseminar'));
	require_once(Route::getModelPath('levelseminar'));
	require_once(Route::getModelPath('jadwalseminar'));
	require_once(Route::getModelPath('seminartopeserta'));
	require_once(Route::getModelPath('pembicaraseminar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('strukturkegiatan','kemahasiswaan'));
	
	// variabel request
	if(empty($r_key))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Seminar';
	$p_tbwidth = 600;
	$p_listpage = Route::getListPage();
	
	$p_model = mSeminar;

	$uptype = 'seminar';
	$uptypeb = 'brosurseminar';
	$uptypet = 'ttdseminar';
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;

	$a_periode = mCombo::periode($conn);
	$a_jenisseminar = mJenisSeminar::getArray($conn);
	$a_ruang = mCombo::ruang($conn);
	$a_penyelenggara = mPenyelenggaraSeminar::getArray($conn);
	$a_level = mLevelSeminar::getArray($conn);
	$a_fakultas = mCombo::jurusan($conn);
	$a_sistemkuliah = mCombo::sistemKuliah($conn);
	
	$a_peserta = array('M' =>'Mahasiswa','P' =>'Pegawai','U' =>'Umum');
	$a_status = array('Diajukan' =>'Diajukan' ,'Disetujui' =>'Disetujui','Tidak Disetujui' =>'Tidak Disetujui');
	$a_wajib = array('W' =>'Wajib' ,'P' =>'Pilihan');	
	$a_type = array('M' =>'Mahasiswa' ,'D' =>'Dosen' , 'L' => 'Lain' );	
	$a_semester = array('1' =>'1' ,'2' =>'2' , '3' => '3' , '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '8 ke atas');

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode', 'type' => 'S', 'option' => $a_periode, 'empty' => false);
	$a_input[] = array('kolom' => 'kodejenisseminar', 'label' => 'Jenis Seminar', 'type' => 'S', 'option' => $a_jenisseminar, 'empty' => false);
	$a_input[] = array('kolom' => 'namaseminar', 'label' => 'Tema Seminar', 'maxlength' => 140, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tanggal Pengajuan', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglawaldaftar', 'label' => 'Tanggal Awal Daftar', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglakhirdaftar', 'label' => 'Tanggal Akhir Daftar', 'type' => 'D');
	$a_input[] = array('kolom' => 'typepengaju', 'label' => 'Penyelenggara', 'type' => 'S', 'option' => $a_penyelenggara);
	$a_input[] = array('kolom' => 'levelseminar', 'label' => 'Level Seminar', 'type' => 'S', 'option' => $a_level);

	$a_input[] = array('kolom' => 'typepeserta[]', 'label' => 'Peserta', 'type' => 'C', 'option' => $a_peserta);
	$a_input[] = array('kolom' => 'typepeserta', 'label' => 'Peserta');
	
	$a_input[] = array('kolom' => 'tarifseminarm', 'label' => 'Tarif Mahasiswa','size' => 50);
	$a_input[] = array('kolom' => 'tarifseminarp', 'label' => 'Tarif Pegawai','size' => 50);
	$a_input[] = array('kolom' => 'tarifseminaru', 'label' => 'Tarif Umum','size' => 50);

	$a_input[] = array('kolom' => 'semmhs[]', 'label' => 'MHS Semester', 'type' => 'C', 'option' => $a_semester);
	$a_input[] = array('kolom' => 'semmhs', 'label' => 'MHS Semester');

	$a_input[] = array('kolom' => 'skuliah[]', 'label' => 'Sistem Kuliah', 'type' => 'C', 'option' => $a_sistemkuliah);
	$a_input[] = array('kolom' => 'skuliah', 'label' => 'Sistem Kuliah');

	$a_input[] = array('kolom' => 'wajibpilihan', 'label' => 'Wajib/Pilihan', 'type' => 'S', 'option' => $a_wajib);
	$a_input[] = array('kolom' => 'pembicara', 'label' => 'Pembicara', 'type' => 'S', 'option' => $a_type,'notnull' => true);
	$a_input[] = array('kolom' => 'pic', 'label' => 'PIC', 'size' => 50, 'maxlength' => 30);
	$a_input[] = array('kolom' => 'nohp', 'label' => 'No Hp', 'size' => 50, 'maxlength' => 30 );
	$a_input[] = array('kolom' => 'koderuang', 'label' => 'Ruang', 'type' => 'S', 'option' => $a_ruang, 'empty' => false);
	$a_input[] = array('kolom' => 'namacp', 'label' => 'Nama CP', 'size' => 50, 'maxlength' => 40);
	$a_input[] = array('kolom' => 'cp', 'label' => 'Contact Person', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'tglkegiatan', 'label' => 'Tanggal Kegiatan', 'type' => 'D','notnull' => true);
	$a_input[] = array('kolom' => 'jammulai','label' => 'Jam Mulai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');
	$a_input[] = array('kolom' => 'jamselesai','label' => 'Jam Selesai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');
	$a_input[] = array('kolom' => 'batasbayar', 'label' => 'Batas Pembayaran', 'type' => 'D');

	// kemahasiswaan
	$a_input[] = array('kolom' => 'parentkegiatan', 'label' => 'Kegiatan', 'type' => 'S', 'option' => mStrukturKegiatan::getArrayLevel($conn,'1'), 'empty' => true, 'add' => 'onChange="loadChildKegiatan()"');
	$a_input[] = array('kolom' => 'kodekegiatan', 'label' => 'Sub Kegiatan', 'type' => 'S');

	$a_input[] = array('kolom' => 'pagumhs', 'label' => 'Kuota Mahasiswa','size' => 20);
	$a_input[] = array('kolom' => 'jmlnim', 'label' => 'Sisa Kuota','size' => 20);
	$a_input[] = array('kolom' => 'paguumum', 'label' => 'Kuota Umum','size' => 20);
	$a_input[] = array('kolom' => 'jmlnip', 'label' => 'Sisa Kuota','size' => 20);
	$a_input[] = array('kolom' => 'pagupgw', 'label' => 'Kuota Pegawai','size' => 20);
	$a_input[] = array('kolom' => 'jmlumum', 'label' => 'Sisa Kuota','size' => 20);

	if($c_validate or !empty($r_key)) {
		$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'S', 'option' => $a_status, 'readonly' => (empty($c_validate)));
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'C', 'option' => array('1' => ''), 'readonly' => (empty($c_validate)));
		$a_input[] = array('kolom' => 'isbuka', 'label' => 'Buka Absen', 'type' => 'C', 'option' => array('1' => ''), 'readonly' => (empty($c_validate)));
	}

	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Objektif', 'type' => 'A', 'rows' => 4, 'cols' => 40);
	$a_input[] = array('kolom' => 'fileposter', 'label' => 'Gambar', 'type' => 'U', 'uptype' => $uptype, 'size' => 40,'maxsize'=>'10','arrtype'=>array('png','jpg','gif'));
	$a_input[] = array('kolom' => 'filereferensi', 'label' => 'File Referensi', 'type' => 'U', 'uptype' => $uptypeb, 'size' => 40,'maxsize'=>'10','arrtype'=>array('png','doc','pdf','ppt','xls','xlsx','docx','rar','zip'));
	$a_input[] = array('kolom' => 'filettd', 'label' => 'Tanda Tangan', 'type' => 'U', 'uptype' => $uptypet, 'size' => 40,'maxsize'=>'10','arrtype'=>array('png','jpg','gif'));


	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		$namaposter = $_FILES['fileposter']['name'] ; 
		$tempposter = $_FILES['fileposter']['tmp_name'] ; 

		$referensi = $_FILES['filereferensi']['name'] ; 
		$tempreferensi = $_FILES['filereferensi']['tmp_name'] ; 
		
		// insert peserta
		if (!empty($_POST['typepeserta'])) {
			$peserta = array();
			$peserta = $_POST['typepeserta'];
		}

		$record['typepeserta'] = implode(',', $peserta);

		// insert semester
		if (!empty($_POST['semmhs'])) {
			$semmhs = array();
			$semmhs = $_POST['semmhs'];
		}

		$record['semmhs'] = implode(',', $semmhs);

		// insert sistemkuliah
		if (!empty($_POST['skuliah'])) {
			$skuliah = array();
			$skuliah = $_POST['skuliah'];
		}

		$record['skuliah'] = implode(',', $skuliah);

		$record['fileposter'] = $namaposter;
		$record['filereferensi'] = $referensi;

		// insert data seminar
		//$isJadwalExist = $p_model::getDataJadwal($conn,$record['tglkegiatan'],$record['koderuang']);
		//$isJadwalCrash = $p_model::getJadwalCrash($conn,$record['tglkegiatan'],$record['koderuang'],$r_key);
		
		// unset post berdasar type peserta
		if (!in_array("M", $peserta)) {
			unset($_POST['fakultas']); 
			unset($_POST['skuliah']); 
			unset($_POST['semmhs']); 
			unset($_POST['tarifseminarm']); 
			unset($_POST['pagumhs']);
		}

		if (!in_array("P", $peserta)) {
			unset($_POST['tarifseminarp']); 
			unset($_POST['pagupgw']);
		}

		if (!in_array("U", $peserta)) {
			unset($_POST['tarifseminaru']); 
			unset($_POST['paguumum']);
		}

		// end of unset value
		
		// set checkbox
		if(isset($record['isvalid']) and $record['isvalid'] != '1')
			$record['isvalid'] = 0;
		if(isset($record['isbuka']) and $record['isbuka'] != '1')
			$record['isbuka'] = 0;
		
		if(empty($r_key)) {
			$r_actd = 'Penambahan';
			list($p_posterr) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			
		} else {
			$r_actd = 'Pengubahan';
			list($p_posterr) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}

		/*
		if(empty($r_key)) {
			if (!empty($isJadwalExist)) {
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			} else {
				list($p_posterr,$p_postmsg) = array(true , "Jadwal Sudah di Pakai" );
			}
		} else {
			if (!empty($isJadwalCrash)) {
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);	
			} else {
				list($p_posterr,$p_postmsg) = array(true , "Jadwal Sudah di Pakai" );	
			}
			
		}*/

		// insert fakultas
		if (empty($p_posterr)) {
			if (!empty($_POST['fakultas'])) {
				$records = array(); 
				$records['idseminar'] = $r_key ;

				$fakultas = mSeminarTopeserta::getDataPeserta($conn,$r_key);

				if (!empty($fakultas)) {
					$p_posterr = mSeminarTopeserta::deleteFakultas($conn,$r_key);
				} 
				
				if(empty($p_posterr)) {
					foreach ($_POST['fakultas'] as $key => $value) {
						$records['kodeunit'] = $value ;
						if(!empty($r_key)){
							$p_posterr = mSeminarTopeserta::insertRecord($conn,$records);
							if(!empty($p_posterr))
								break;
						} 
					}
				}
			}
		}
		

		// insert pembicara 
		if (empty($p_posterr)) {
			
			if (!empty($_POST['nip']) or !empty($_POST['nim']) or !empty($_POST['umum'])) {
				$pembicara = mPembicaraSeminar::getPembicara($conn,$r_key);
			}

			if (!empty($pembicara)) {
				$p_posterr = mPembicaraSeminar::deletePembicara($conn,$r_key);
			} 
			
			// pegawai
			if(empty($p_posterr)) {
				foreach ($_POST['nip'] as $key => $value) {
					$records['idseminar'] = $r_key ;
					$records['idpembicara'] = $value ;
					$records['jenispembicara'] = 'P' ;
	
					if(!empty($r_key)){
						$p_posterr = mPembicaraSeminar::insertRecord($conn,$records);
						if(!empty($p_posterr))
							break;
					} 
				}
			}
			
			// mahasiswa
			if(empty($p_posterr)) {
				foreach ($_POST['nim'] as $key => $value) {
					$records['idseminar'] = $r_key ;
					$records['idpembicara'] = $value ;
					$records['jenispembicara'] = 'M' ;
	
					if(!empty($r_key)){
						$p_posterr = mPembicaraSeminar::insertRecord($conn,$records);
						if(!empty($p_posterr))
							break;
					} 
				}
			}

			// umum
			if(empty($p_posterr)) {
				foreach ($_POST['umum'] as $key => $value) {
					$records['idseminar'] = $r_key ;
					$records['idpembicara'] = $value ;
					$records['jenispembicara'] = 'U' ;
	
					if(!empty($r_key)){
						$p_posterr = mPembicaraSeminar::insertRecord($conn,$records);
						if(!empty($p_posterr))
							break;
					} 
				}
			}

			//file ttd pembicara
			// $namafile = $_FILES['filettd']['name'] ; 
			// $tempfile = $_FILES['filettd']['tmp_name'] ;
			
			// $recordpem['filettd'] = $namafile;

			// list($p_posterr) = mPembicaraSeminar::updateCRecord($conn,$_POST['filettd'],$recordpem,$r_key);

		}
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);

		// if(!$p_posterr) unset($post);
		
		// flash karena ada perubahan $a_input
		if(empty($p_posterr)) {
			$a_flash = array();
			$a_flash['r_key'] = $r_key;
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $r_actd.' data seminar '.(empty($p_posterr) ? 'berhasil' : 'gagal');
			
			Route::setFlashData($a_flash);
		}
	}

	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	if (!empty($r_key)) {
		$a_fakultaspeserta = mSeminarTopeserta::getDataPeserta($conn,$r_key);

		// get pembicara
		$a_pembicarap = mPembicaraSeminar::getPembicaraP($conn,$r_key);
		$a_pembicaram = mPembicaraSeminar::getPembicaraM($conn,$r_key);
		$a_pembicarau = mPembicaraSeminar::getPembicaraU($conn,$r_key);
		// $a_pembicaraup = mPembicaraSeminar::getPembicaraUp($conn,$r_key);
	}

	$r_mahasiswa = Page::getDataValue($row,'nim');
	$r_pegawai = Page::getDataValue($row,'nip');
	
	if(!empty($r_mahasiswa))
		$r_namamahasiswa = $r_mahasiswa.' - '.$p_model::getNamaMahasiswa($conn,$r_mahasiswa);	


	if(!empty($r_pegawai))
		$r_namapegawai = $r_pegawai.' - '.$p_model::getNamaPegawai($conn,$r_pegawai);
	
	if(empty($p_tbwidth))
		$p_tbwidth = 640;
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	if(!empty($row))
		$r_kegiatan = Page::getDataValue($row,'kodekegiatan');
	if(!empty($r_kegiatan))
		$v_kegiatan = mStrukturKegiatan::getData($conn,$r_kegiatan);
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
	<script type="text/javascript" src="scripts/forinplace.js"></script>
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
					/***********\*****/
					
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
						<?= Page::getDataTR($row,'periode') ?>
						<!--
							<?= Page::getDataTR($row,'temaseminar') ?>
						-->
						<?= Page::getDataTR($row,'namaseminar') ?>
						<?= Page::getDataTR($row,'kodejenisseminar') ?>
						<?= Page::getDataTR($row,'levelseminar') ?>
						<?= Page::getDataTR($row,'wajibpilihan') ?>
						<?= Page::getDataTR($row,'parentkegiatan') ?>
						<tr>
							<td class="LeftColumnBG">Sub Kegiatan</td>
							<td class="RightColumnBG">
								<span id="show"><?= $v_kegiatan['namakegiatan'] ?></span>
								<span id="edit" style="display:none"><?= Page::getDataInput($row,'kodekegiatan') ?></span>
							</td>
						</tr>
						<?= Page::getDataTR($row,'tglpengajuan') ?>
						<?= Page::getDataTR($row,'tglawaldaftar') ?>
						<?= Page::getDataTR($row,'tglakhirdaftar') ?>
						<?= Page::getDataTR($row,'typepengaju') ?>

						<!-- Type Peserta -->
						<?	$datapeserta = explode(',',Page::getDataValue($row,'typepeserta'));
							$pesertain = array();
							
							foreach($datapeserta as $key => $val){
								array_push($pesertain,$val);
							}						
						?>	

						<tr>
							<td class="LeftColumnBG">Peserta</td>
							<td class="RightColumnBG">
								<span id="show">
									<?php
										$check = '';
										foreach($a_peserta as $key => $val){
											$check .=  in_array($key,$pesertain)?'<img src="images/check.png">':'<input type="checkbox" readonly="readonly" disabled="disabled">';
											$check .= ' <label for="typepeserta[]_'.$key.'">'.$val.'</label><br>';
										}
										echo $check;

									?>
								</span>
								<span id="edit" style="display:none">
									<?php
										$check = '';
										foreach($a_peserta as $key => $val){
											if(in_array($key,$pesertain))
												$checked = 'checked=""';
											else
												$checked = '';
											$check .=  '<input type="checkbox" onchange=CheckedPeserta() name="typepeserta[]" id="typepeserta[]_'.$key.'" value="'.$key.'" '.$checked.'>';
											$check .= ' <label for="typepeserta[]_'.$key.'">'.$val.'</label><br>';
										}
										echo $check;
									?>
								</span>
							</td>
						</tr>
						<?= Page::getDataTR($row,'pic') ?>
						<?= Page::getDataTR($row,'nohp') ?>
						<?= Page::getDataTR($row,'koderuang') ?>
						<?= Page::getDataTR($row,'namacp') ?>
						<?= Page::getDataTR($row,'cp') ?>
						<?= Page::getDataTR($row,'tglkegiatan') ?> 
						<?= Page::getDataTR($row,'jammulai') ?> 
						<?= Page::getDataTR($row,'jamselesai') ?> 
						<?= Page::getDataTR($row,'keterangan') ?>
						<?= Page::getDataTR($row,'fileposter') ?>
						<?= Page::getDataTR($row,'filereferensi') ?>
						<?= Page::getDataTR($row,'filettd') ?>
						<?php if(!empty($r_key)) { ?>
						<?= Page::getDataTR($row,'status') ?>
						<?= Page::getDataTR($row,'isvalid') ?>
						<?= Page::getDataTR($row,'isbuka') ?>
						<?php } ?>
						<?= Page::getDataTR($row,'batasbayar') ?>
					</table>
				</center>

				<center>
					<div class="tabs" style="width:<?= $p_tbwidth ?>px">
						<ul>
							<li id="tmhs"><a id="tablink" href="javascript:void(0)">Mahasiswa</a></li>
							<li id="tpgw"><a id="tablink" href="javascript:void(0)">pegawai</a></li>
							<li id="tumum"><a id="tablink" href="javascript:void(0)">Umum</a></li>
							<li id="tpemb"> <a id="tablink" href="javascript:void(0)">Pembicara</a></li>
						</ul>
						
						<div id="items">
							<table cellpadding="4" cellspacing="2" align="center" id="tmahasiswa">
								<tr>
									<td class="LeftColumnBG">Fakultas Peserta</td>
									<td class="RightColumnBG">
										<span id="show">
											<?php
												$check = '';
												foreach($a_fakultas as $key => $val){
													$check .=  in_array($key,$a_fakultaspeserta)?'<img src="images/check.png">':'<input type="checkbox" readonly="readonly" disabled="disabled">';
													$check .= ' <label for="fakultas[]_'.$key.'">'.$val.'</label><br>';
												}
												echo $check;

											?>
										</span>
										<span id="edit" style="display:none">
											<?php
												$check = ''; ?>
												<input type="checkbox" name="selectall" id="selectall"  onClick="toggle(this)"/> Select All <br>
												<?php
												foreach($a_fakultas as $key => $val){
													if(in_array($key,$a_fakultaspeserta))
														$checked = 'checked=""';
													else
														$checked = '';
													$check .=  '<input type="checkbox" name="fakultas[]" id="fakultas[]_'.$key.'" value="'.$key.'" '.$checked.'>';
													$check .= ' <label for="fakultas[]_'.$key.'">'.$val.'</label><br>';
												}
												echo $check;

											?>
										</span>
									</td>
								</tr>						
								
								<!-- Get Semester -->
								<?	$valSem1 = explode(',',Page::getDataValue($row,'semmhs'));
									$valSem = array();
									foreach($valSem1 as $key => $val){
										array_push($valSem,$val);
									}						
								?>

								<tr>
									<td class="LeftColumnBG">Semester</td>
									<td class="RightColumnBG">
										<span id="show">
											<?php
												$check = '';
												foreach($a_semester as $key => $val){
													$check .=  in_array($key,$valSem)?'<img src="images/check.png">':'<input type="checkbox" readonly="readonly" disabled="disabled">';
													$check .= ' <label for="semmhs[]_'.$key.'">'.$val.'</label><br>';
												}
												echo $check;	
											?>

										</span>
										<span id="edit" style="display:none">
											<?php
												$check = '';
												foreach($a_semester as $key => $val){
													if(in_array($key,$valSem))
														$checked = 'checked=""';
													else
														$checked = '';
													$check .=  '<input type="checkbox" name="semmhs[]" id="semmhs[]_'.$key.'" value="'.$key.'" '.$checked.'>';
													$check .= ' <label for="semmhs[]_'.$key.'">'.$val.'</label><br>';
												}
												echo $check;

											?>
										</span>
									</td>
								</tr>

								<!-- Get Sistem Kuliah -->
								<?	
									$valSis1 = explode(',',Page::getDataValue($row,'skuliah'));
									$valSis = array();

									foreach($valSis1 as $key => $val){
										array_push($valSis,$val);
									}						
								?>
								<tr>
									<td class="LeftColumnBG">Sistem Kuliah</td>
									<td class="RightColumnBG">
										<span id="show">
											<?php
												$check = '';
												foreach($a_sistemkuliah as $key => $val){
													$check .=  in_array($key,$valSis)?'<img src="images/check.png">':'<input type="checkbox" readonly="readonly" disabled="disabled">';
													$check .= ' <label for="skuliah[]_'.$key.'">'.$val.'</label><br>';
												}
												echo $check;	
											?>
										</span>
										<span id="edit" style="display:none">
											<?php
												$check = '';
												foreach($a_sistemkuliah as $key => $val){
													if(in_array($key,$valSis))
														$checked = 'checked=""';
													else
														$checked = '';
													$check .=  '<input type="checkbox" name="skuliah[]" id="skuliah[]_'.$key.'" value="'.$key.'" '.$checked.'>';
													$check .= ' <label for="skuliah[]_'.$key.'">'.$val.'</label><br>';
												}
												echo $check;
											?>
										</span>
									</td>
								</tr>

								<?= Page::getDataTR($row,'tarifseminarm') ?>		
								<?= Page::getDataTR($row,'pagumhs') ;

								?> 								
								<tr>
									<td class="LeftColumnBG">Sisa Kuota</td>
									<td class="RightColumnBG">
										<span id="show">
											<?php  
												if (Page::getDataValue($row,'pagumhs') == 0) {
													echo 0;
												} else {
											?>
												<?php echo Page::getDataValue($row,'pagumhs') - Page::getDataValue($row,'jmlnim'); ?>
											<?php 
												} 
											?>
										</span>
										<span id="edit" style="display:none">
											<?php  
												if (Page::getDataValue($row,'pagumhs') == 0) {
													echo 0;
												} else {
											?>
												<?php echo Page::getDataValue($row,'pagumhs') - Page::getDataValue($row,'jmlnim'); ?>
											<?php 
												} 
											?>
										</span>
									</td>
								</tr>

							</table>
						</div>

						<div id="items">
							<table cellpadding="4" cellspacing="2" align="center">
								<?= Page::getDataTR($row,'tarifseminarp') ?>
								<?= Page::getDataTR($row,'pagupgw') ?> 
								<tr>
									<td class="LeftColumnBG">Sisa Kuota</td>
									<td class="RightColumnBG">
										<span id="show">
											<?php  
												if (Page::getDataValue($row,'pagupgw') == 0) {
													echo 0;
												} else {
											?>
												<?php echo Page::getDataValue($row,'pagupgw') - Page::getDataValue($row,'jmlnip'); ?>
											<?php 
												} 
											?>
										</span>
										<span id="edit" style="display:none">
											<?php  
												if (Page::getDataValue($row,'pagupgw') == 0) {
													echo 0;
												} else {
											?>
												<?php echo Page::getDataValue($row,'pagupgw') - Page::getDataValue($row,'jmlnip'); ?>
											<?php 
												} 
											?>
										</span>
									</td>
								</tr>
								
							</table>
						</div>

						<div id="items">
							<table cellpadding="4" cellspacing="2" align="center">
								<?= Page::getDataTR($row,'tarifseminaru') ?>
								<?= Page::getDataTR($row,'paguumum') ?> 
								<tr>
									<td class="LeftColumnBG">Sisa Kuota</td>
									<td class="RightColumnBG">
										<span id="show">
											<?php  
												if (Page::getDataValue($row,'paguumum') == 0) {
													echo 0;
												} else {
											?>
												<?php echo Page::getDataValue($row,'paguumum') - Page::getDataValue($row,'jmlumum'); ?>
											<?php 
												} 
											?>

											
										</span>
										<span id="edit" style="display:none">
											<?php  
												if (Page::getDataValue($row,'paguumum') == 0) {
													echo 0;
												} else {
											?>
												<?php echo Page::getDataValue($row,'paguumum') - Page::getDataValue($row,'jmlumum'); ?>
											<?php 
												} 
											?>
										</span>
									</td>
								</tr>
							</table>
						</div>

						<div id="items">
							<table cellpadding="4" cellspacing="2" align="center">
								<tr>
									<td class="LeftColumnBG">NIP Pegawai</td>
									<td class="RightColumnBG">
										<span id="edit" style="display: none;">
											<?= Page::getDataInputWrap($r_namapegawai,
												UI::createTextBox('nip_pembicaraseminar','','ControlStyle',30,30)) ?>
											<input type="hidden" name="nippembicaraseminar" id="nippembicaraseminar" value="<?=$r_pegawai?>">
											<button type="button" class="btn btn-primary btn-sm" onclick="addnipPembicara()"> + </button>
											<!-- <br>
											<input type="file" name="filettd" id="tandatangan" size="40" class="ControlStyle"> -->
											<span id="cetakpembicarap"></span>
										</span>

										<span id="show">
											<?php  
												foreach ($a_pembicarap as $key => $row) {
													foreach ($row as $key => $rows) { ?>
														<span>
															<?= $rows .'<br>'?>
														</span>
											<?php	}
												}
											?>
										</span>
									</td>	
								</tr>
								<tr>
									<td class="LeftColumnBG">NIM Mahasiswa</td>
									<td class="RightColumnBG">
										<span id="edit" style="display: none;">
											<?= Page::getDataInputWrap($r_namamahasiswa,
												UI::createTextBox('nim_pembicaraseminar','','ControlStyle',30,30)) ?>
											<input type="hidden" name="nimpembicaraseminar" id="nimpembicaraseminar" value="<?=$r_mahasiswa?>">
											<button type="button" class="btn btn-primary btn-sm" onclick="addnimPembicara()"> + </button>
											<!-- <br>
											<input type="file" name="filettd" id="tandatangan" size="40" class="ControlStyle"> -->
											<span id="cetakpembicaram"></span> 
										</span>

										<span id="show">
											<?php  
												foreach ($a_pembicaram as $key => $row) {
													foreach ($row as $key => $rows) { ?>
														<span>
															<?= $rows .'<br>'?>
														</span>
											<?php	}
												}
											?>
										</span>
									</td>
								</tr>
								
								<tr>
									<td class="LeftColumnBG">Pembicara Umum</td>
									<td class="RightColumnBG">
										<span id="edit" style="display: none;">
											<?= UI::createTextBox('pembicaraumum','','ControlStyle',30,30) ?>
											<button type="button" class="btn btn-primary btn-sm" onclick="addpemUmum()"> + </button>
											<!-- <br>
											<input type="file" name="filettd" id="tandatangan" size="40" class="ControlStyle"> -->
											<span id="cetakpembicarau"></span> 
										</span>

										<span id="show">
											<?php  
												foreach ($a_pembicarau as $key => $row) {
													foreach ($row as $key => $rows) { ?>
														<span>
															<?= $rows .'<br>'?>
														</span>
											<?php	}
												}
											?>
										</span>
									</td>
								</tr>	
							</table>
						</div>
	
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

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>

<script type="text/javascript">

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "namaseminar,pembicara,tglkegiatan";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	// autocomplete
	$("#nim_pembicaraseminar").xautox({strpost: "f=acmahasiswa", targetid: "nimpembicaraseminar"});
	$("#nip_pembicaraseminar").xautox({strpost: "f=acpegawai", targetid: "nippembicaraseminar"});

	// format jam
	$.mask.definitions['~'] = "[+-]";
	$("#i_jammulai").mask("99:99");
	$("#i_jamselesai").mask("99:99");
	$(".jam").mask("99:99");

	//
	$("#tmahasiswa").hide();

	CheckedPeserta();
	initTab();

	loadChildKegiatan();
});

function addnipPembicara(){
	var nip = $('#nip_pembicaraseminar').val(); // isi nip - nama
	var idnip = $('#nippembicaraseminar').val(); // isi id 

	if (nip.length > 0) {
		if ($('#list_'+idnip).length > 0) {
			return true ;
		} else {
			if (idnip.length > 1) {
				var pegawai = '<a href="javascript:void(0)" id="link_'+idnip+'" onclick="remPem('+idnip+')"><br>'+nip+'\t<b>X</b></a>' ;

				$("#cetakpembicarap").append(pegawai);
				$("#cetakpembicarap").append('<input type="hidden" id="list_'+idnip+'" name="nip[]" value="'+nip+'" ></input>');
				$("#cetakpembicarap").append('<input type="hidden" name="idpeg[]" value="'+idnip+'" ></input>');
			}
		}
	} else {d
		return true ;
	}	
}

function addnimPembicara(){
	var nim = $('#nim_pembicaraseminar').val(); // isi nim - nama
	var idnim = $('#nimpembicaraseminar').val(); // isi id

	if (nim.length > 0) {
		if ($('#list_'+idnim).length > 0) {
			return true ;
		} else {
			if (idnim.length > 1) {
				var mahasiswa = '<a href="javascript:void(0)" id="link_'+idnim+'" onclick="remPem('+idnim+')"><br>'+nim+'<b>X</b></a>' ;
					
				$("#cetakpembicaram").append(mahasiswa);
				$("#cetakpembicaram").append('<input type="hidden" id="list_'+idnim+'" name="nim[]" value="'+nim+'" ></input>');
				$("#cetakpembicaram").append('<input type="hidden" name="idmhs[]" value="'+idnim+'" ></input>');
			}
		}
	} else {
		return true ;
	}	
}

function addpemUmum(){
	var i = 0;

	var idumum = i++ ;
	var namapembicara = $('#pembicaraumum').val();
	
	var pembicara = '<a href="javascript:void(0)" id="link_'+idumum+'" onclick="remPem('+idumum+')"><br>'+namapembicara+'<b>X</b></a>' ;
		
	$("#cetakpembicarau").append(pembicara);
	$("#cetakpembicarau").append('<input type="hidden" id="list_'+idumum+'" name="umum[]" value="'+namapembicara+'" ></input>');	
}

function remPem(id){
	$('#link_'+id).remove();
	$('#list_'+id).remove();
}

function CheckedPeserta() {
	var x = document.getElementById("typepeserta[]_M").checked ;
    var y = document.getElementById("typepeserta[]_P").checked ;
    var z = document.getElementById("typepeserta[]_U").checked ;
    
    if (x) {
    	$("#tmhs").show();
    	$("#tmahasiswa").show();
    } else {
    	$("#tmhs").hide();
    }
    if (y) {
    	$("#tpgw").show();
    } else {
    	$("#tpgw").hide();
    }
    if (z) {
    	$("#tumum").show();
    } else {
    	$("#tumum").hide();

    }

}

function toggle(source) {
  checkboxes = document.getElementsByName('fakultas[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}

function chooseTab(idx) {
	$("div.tabs li:visible").eq(idx).find("a").triggerHandler("click");
}

// ajax ganti kegiatan
function loadChildKegiatan() {
	var param = new Array();
	param[0] = $("#parentkegiatan").val();
	param[1] = "<?= $v_kegiatan['kodekegiatan'] ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "getkegiatanchild", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodekegiatan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

</script>
</body>
</html>
