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
	require_once(Route::getModelPath('pengembangan'));
	require_once(Route::getModelPath('riwayat'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai())
		$r_self = 1;
	
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
	$p_title = 'Data Studi Lanjut';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mPengembangan;
	$p_dbtable = "pe_tugasbelajar";
	$where = 'nouruttugas';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tgl. Mulai', 'type' => 'D', 'notnull' => true,'add' => 'onchange="getDurasi();"');
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tgl. Selesai', 'type' => 'D', 'notnull' => true,'add' => 'onchange="getDurasi();"');
	$a_input[] = array('kolom' => 'durasitugas', 'label' => 'Durasi', 'maxlength' => 3, 'size' => 3, 'type' => 'N', 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'jenistugas', 'label' => 'Jenis Studi', 'type' => 'S', 'empty' => true, 'option' => $p_model::jenisStudi(), 'default' => 'D', 'add' => 'onchange="getMasaIkatan()"');
	$a_input[] = array('kolom' => 'idpendidikan', 'label' => 'Jenjang', 'type' => 'S', 'option' => mRiwayat::jenjangPendidikan($conn));
	$a_input[] = array('kolom' => 'gelar', 'label' => 'Gelar', 'maxlength' => 25, 'size' => 30);
		
	$a_input[] = array('kolom' => 'nosurattugas', 'label' => 'No. Surat', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'tglsurattugas', 'label' => 'Tgl. Surat Tugas', 'type' => 'D');
	$a_input[] = array('kolom' => 'totalsks', 'label' => 'Total SKS', 'maxlength' => 6, 'size' => 5, 'type' => 'N');
	$a_input[] = array('kolom' => 'nilaitoefl', 'label' => 'Nilai Toefl', 'maxlength' => 10, 'size' => 10, 'type' => 'N');
	$a_input[] = array('kolom' => 'nilaigmat', 'label' => 'Nilai Gmat', 'maxlength' => 10, 'size' => 10, 'type' => 'N');
	$a_input[] = array('kolom' => 'nilaigre', 'label' => 'Nilai GRE', 'maxlength' => 10, 'size' => 10, 'type' => 'N');
	$a_input[] = array('kolom' => 'nilailain', 'label' => 'Nilai Lain', 'maxlength' => 10, 'size' => 10, 'type' => 'N');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	$a_input[] = array('kolom' => 'filestudilanjut', 'label' => 'File Studi Lanjut', 'type' => 'U', 'uptype' => 'filestudilanjut', 'size' => 40);
	
	$a_input[] = array('kolom' => 'pt', 'label' => 'Nama Institusi', 'maxlength' => 255, 'size' => 100, 'notnull' => true);	
	$a_input[] = array('kolom' => 'kodept', 'type' => 'H');
	$a_input[] = array('kolom' => 'fakultas', 'label' => 'Fakultas', 'maxlength' => 255, 'size' => 40);
	$a_input[] = array('kolom' => 'kodefakultas', 'type' => 'H');
	$a_input[] = array('kolom' => 'jurusan', 'label' => 'Jurusan', 'maxlength' => 255, 'size' => 40);
	$a_input[] = array('kolom' => 'kodejurusan', 'type' => 'H');
	$a_input[] = array('kolom' => 'bidang', 'label' => 'Bidang', 'maxlength' => 255, 'size' => 40);
	$a_input[] = array('kolom' => 'kodebidang', 'type' => 'H');
	$a_input[] = array('kolom' => 'isln', 'label' => 'Lokasi', 'type' => 'S', 'option' => mRiwayat::lokasiPendidikan());
	$a_input[] = array('kolom' => 'alamatuniv', 'label' => 'Alamat Instansi', 'maxlength' => 100, 'size' => 70);
	$a_input[] = array('kolom' => 'telpuniv', 'label' => 'Telp.', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'websiteuniv', 'label' => 'Website', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'cp1', 'label' => 'Contact Person 1', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'cp2', 'label' => 'Contact Person 2', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'email1', 'label' => 'Email 1', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'email2', 'label' => 'Email 2', 'maxlength' => 50, 'size' => 50);
		
	$a_input[] = array('kolom' => 'nonota', 'label' => 'No. Kontrak', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglnota', 'label' => 'Tgl. Kontrak', 'type' => 'D');
	$a_input[] = array('kolom' => 'sponsor', 'label' => 'Nama Sponsor', 'maxlength' => 100, 'size' => 70);
	$a_input[] = array('kolom' => 'idbiaya', 'label' => 'Jenis Pembiayaan', 'type' => 'S', 'empty' => true, 'notnull' => true, 'option' => $p_model::jenisPembiayaan($conn), 'add' => 'onchange="getMasaIkatan()"');
	$a_input[] = array('kolom' => 'pembiayaansendiri', 'label' => 'Biaya Pribadi', 'maxlength' => 18, 'size' => 15, 'type' => 'N');
	$a_input[] = array('kolom' => 'pembiayaaninternal', 'label' => 'Biaya Kampus', 'maxlength' => 18, 'size' => 15, 'type' => 'N');
	$a_input[] = array('kolom' => 'pembiayaanpihaklain', 'label' => 'Biaya Sponsor', 'maxlength' => 18, 'size' => 15, 'type' => 'N');
	$a_input[] = array('kolom' => 'masaikatandinas', 'label' => 'Masa Ikatan Dinas', 'maxlength' => 3, 'size' => 3, 'type' => 'N', 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'statususulan', 'label' => 'Status Usulan', 'type' => 'S', 'empty' => true, 'option' => $p_model::statusUsulan());
	
	$a_cek = $p_model::cekList($conn,$r_subkey);
		
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		//simpan ke ceklist	
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::saveCeklist($conn,$_POST,$r_subkey);
		
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
		//delete ceklist	
		list($p_posterr,$p_postmsg) = $p_model::deleteCeklist($conn,$r_subkey);
		if(!$p_posterr)		
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where,'','filestudilanjut');
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	else if($r_act == 'deletefile' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,'filestudilanjut',$where);				
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
		
	$p_postmsg = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : $p_postmsg;
	$p_posterr = !empty($_REQUEST['err']) ? $_REQUEST['err'] : $p_posterr;
	if($p_posterr)
		$post = Route::getFlashDataPost();
	
	$sql = $p_model::getDataEditStudiLanjut($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
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
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
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
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglmulai') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'tglmulai') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglselesai') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglselesai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'durasitugas') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'durasitugas') ?>&nbsp;bulan</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jenistugas') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'jenistugas') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idpendidikan') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'idpendidikan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'gelar') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'gelar') ?></td>
						</tr>
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a href="javascript:void(0)">Info Studi</a></li>
						<li><a href="javascript:void(0)">Institusi</a></li>
						<li><a href="javascript:void(0)">Pembiayaan</a></li>
						<li><a href="javascript:void(0)">Kelengkapan</a></li>
					</ul>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'nosurattugas') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'nosurattugas') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglsurattugas') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglsurattugas') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'totalsks') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'totalsks') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nilaitoefl') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nilaitoefl') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nilaigmat') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nilaigmat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nilaigre') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nilaigre') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nilailain') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nilailain') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'keterangan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'keterangan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filestudilanjut') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filestudilanjut') ?></td>
						</tr>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pt') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'pt') ?>
								<?= Page::getDataInput($row,'kodept') ?>	
								<span id="edit" style="display:none">
									<img id="imgpt_c" src="images/green.gif">
									<img id="imgpt_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'fakultas') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'fakultas') ?>
								<?= Page::getDataInput($row,'kodefakultas') ?>	
								<span id="edit" style="display:none">
									<img id="imgfak_c" src="images/green.gif">
									<img id="imgfak_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'jurusan') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'jurusan') ?>
								<?= Page::getDataInput($row,'kodejurusan') ?>	
								<span id="edit" style="display:none">
									<img id="imgjur_c" src="images/green.gif">
									<img id="imgjur_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'bidang') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'bidang') ?>
								<?= Page::getDataInput($row,'kodebidang') ?>	
								<span id="edit" style="display:none">
									<img id="imgbid_c" src="images/green.gif">
									<img id="imgbid_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isln') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isln') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alamatuniv') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'alamatuniv') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'telpuniv') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'telpuniv') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'websiteuniv') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'websiteuniv') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'cp1') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'cp1') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'cp2') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'cp2') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'email1') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'email1') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'email2') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'email2') ?></td>
						</tr>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'nonota') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'nonota') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglnota') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglnota') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'sponsor') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'sponsor') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idbiaya') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idbiaya') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pembiayaansendiri') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'pembiayaansendiri') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pembiayaaninternal') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'pembiayaaninternal') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pembiayaanpihaklain') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'pembiayaanpihaklain') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'masaikatandinas') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'masaikatandinas') ?>&nbsp;bulan</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statususulan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'statususulan') ?></td>
						</tr>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?
						if(count($a_cek) > 0){
							foreach($a_cek as $key => $val){
						?>
						<tr>
							<td class="LeftColumnBG" width="10px">
								<span id="show">
									<?= $val['status'] == 'S' ? '<img src="images/check.png">' : '';?>
								</span>
								<span id="edit" style="display:none">
									<input type="checkbox" id="ceklist_<?= $val['noceklist']?>" value="1" name="ceklist_<?= $val['noceklist']?>" <?= $val['status'] == 'S' ? 'checked' : '';?>>									
									<input type="hidden" id="ceklist" name="ceklist[]" value="<?= $val['noceklist']?>">
								</span>
							</td>
							<td class="RightColumnBG"><label for="ceklist<?= $val['noceklist']?>"><?= $val['ceklist']?></label></td>
						</tr>
						<?
							}
						}
						?>
					</table>
					</div>
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

<iframe name="upload_iframe" style="display:none"></iframe>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {	
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	//autocomplete
	$("#pt").xautox({strpost: "f=acpt", targetid: "kodept", imgchkid: "imgpt", imgavail: true});
	$("#fakultas").xautox({strpost: "f=acfakultas", targetid: "kodefakultas", imgchkid: "imgfak", imgavail: true});
	$("#jurusan").xautox({strpost: "f=acjurusan", targetid: "kodejurusan", imgchkid: "imgjur", imgavail: true});
	$("#bidang").xautox({strpost: "f=acbidang", targetid: "kodebidang", imgchkid: "imgbid", imgavail: true});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

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

function getDurasi(){
	var posted = "f=gdurasi&q[]="+$("#tglmulai").val()+"&q[]="+$("#tglselesai").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#durasitugas").val(text);
		getMasaIkatan();
	});
}

function getMasaIkatan(){
	var posted = "f=gmasaikatan&q[]="+$("#jenistugas").val()+"&q[]="+$("#idbiaya").val()+"&q[]="+$("#durasitugas").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#masaikatandinas").val(text);
	});
}
</script>
</body>
</html>
