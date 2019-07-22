<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	//koneksi dengan mutu
	//$connmutu = Query::connect('mutu');
	//if($_SERVER['REMOTE_ADDR'] == "36.85.91.184") //ip public sevima
	//	$connmutu->debug=true;

	// include
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getModelPath('integrasi'));
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
	$p_title = 'Data Unit Kerja';
	$p_tbwidth = 900;
	$p_aktivitas = 'UNIT';
	$p_dbtable = 'ms_unit';
	$p_key = 'idunit';
	$p_listpage = Route::getListPage();
	
	$p_model = mMastKepegawaian;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//cek bila add child, lalu ambil field dari parent
	if(!empty($pkey)){
		$pcat = $p_model::pUnit($conn,$pkey);
	}
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Kode Unit', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'namasingkat', 'label' => 'Nama Singkat', 'maxlength' => 6, 'size' => 10);
	
	//default dari parent
	if(!empty($pkey))
		$a_input[] = array('kolom' => 'parentunit', 'label' => 'Parent Unit', 'type' => 'S', 'empty' => true, 'notnull' => true, 'option' => mCombo::unitSave($conn,false),'default' => $pkey);
	else
		$a_input[] = array('kolom' => 'parentunit', 'label' => 'Parent Unit', 'type' => 'S', 'empty' => true, 'option' => mCombo::unitSave($conn,false));
		
	$a_input[] = array('kolom' => 'idunitanggaran', 'label' => 'Unit Anggaran<br><font size="1px">(digunakan laporan penggajian)</font>', 'type' => 'S', 'option' => mCombo::unitSave($conn,false),'empty'=>true);
	$a_input[] = array('kolom' => 'namapimpinan', 'label' => 'Pimpinan', 'maxlength' => 100, 'size' => 60, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'nippimpinan', 'type' => 'H');
	$a_input[] = array('kolom' => 'kodeprogram', 'label' => 'Program Pendidikan', 'type' => 'S', 'empty' => true, 'option' => $p_model::listProgramPendidikan($conn));
	
	//default dari parent
	if(!empty($pkey)){
		$a_input[] = array('kolom' => 'isakademik', 'label' => 'is Akademik?', 'type' => 'S', 'empty' => true, 'option' => $p_model::isAkademik(),'default' => $pcat['isakademik']);
		$a_input[] = array('kolom' => 'isaktif', 'label' => 'is Aktif?', 'type' => 'S', 'option' => mCombo::isAktif(),'default' => $pcat['isaktif']);
	}else{
		$a_input[] = array('kolom' => 'isakademik', 'label' => 'is Akademik?', 'type' => 'S', 'empty' => true, 'option' => $p_model::isAkademik());
		$a_input[] = array('kolom' => 'isaktif', 'label' => 'is Aktif?', 'type' => 'S', 'option' => mCombo::isAktif());
	}

	//keperluan mutu
	$a_input[] = array('kolom' => 'noskpendirian', 'label' => 'SK Pendirian', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglskpendirian', 'label' => 'Tgl. SK Pendirian', 'type' => 'D');
	$a_input[] = array('kolom' => 'pejabatttdsk', 'label' => 'Pejabat TTD SK', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'bulan', 'label' => 'Periode Mulai', 'empty' => true, 'type' => 'S', 'option' => Date::arrayMonth());
	$a_input[] = array('kolom' => 'tahun', 'maxlength' => 4, 'size' => 4);
	$a_input[] = array('kolom' => 'nomorskizin', 'label' => 'SK Izin', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglskizin', 'label' => 'Tgl. SK Izin', 'type' => 'D');
	$a_input[] = array('kolom' => 'statusakreditasi', 'label' => 'Status Akreditasi', 'maxlength' => 2, 'size' => 2);
	$a_input[] = array('kolom' => 'noskbanpt', 'label' => 'SK BANPT', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglskbanpt', 'label' => 'Tgl. SK BANPT', 'type' => 'D');
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'telp', 'label' => 'Telp', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'fax', 'label' => 'Fax', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'homepage', 'label' => 'Homepage', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 100, 'size' => 50);

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		if($record['bulan'] != 'null' and $record['tahun'] != 'null')
			$record['blnthnmulai'] = $record['tahun'].str_pad($record['bulan'],2,'0', STR_PAD_LEFT);
		
		$recmutu = array();
		$recmutu = $record;
		$recmutu['idunitparent'] = $record['parentunit'];
		$recmutu['program'] = $record['kodeprogram'];

		if($record['namapimpinan'] != 'null'){
			$recmutu['ketuaunit'] = $record['nippimpinan'];

			list($nik,$nama) = explode(" - ", $record['namapimpinan']);
			$recmutu['namaketuaunit'] = $nama;
			$recmutu['nik'] = $nik;
		}else{
			$recmutu['ketuaunit'] = 'null';
			$recmutu['namaketuaunit'] = 'null';
			$recmutu['nik'] = 'null';
		}

		$conn->BeginTrans();
		
		//untuk infoleft dan inforight	
		$recadd = $p_model::saveInfoUnit($conn,$record['parentunit'],$r_key);
		//list($recaddmutu,$p_posterr) = $p_model::saveInfoLain($connmutu,'mutu','ms_unit',$p_key,'kodeunit',$r_key,'idunitparent','parentkodeunit',$record['parentunit']);
		
		if(count($recadd) > 0)
			$record = array_merge($record,$recadd);

		if(count($recaddmutu) > 0)
			$recmutu = array_merge($recmutu,$recaddmutu);
		
		if(empty($r_key)){
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
			/*if(!$p_posterr){
				$recmutu['idunit'] = $r_key;
				$p_posterr = Query::recInsert($connmutu,$recmutu,'mutu.ms_unit');
				
				if($p_posterr)
					$p_postmsg = 'Penyimpanan Unit Kerja Mutu gagal';
			}*/
		}
		else{
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
			/*if(!$p_posterr){
				$p_posterr = Query::recUpdate($connmutu,$recmutu,'mutu.ms_unit',$p_model::getCondition($r_key,$p_key));
				
				if($p_posterr)
					$p_postmsg = 'Penyimpanan Unit Kerja Mutu gagal';
			}*/
		}
var_dump($r_key);
		//integrasi dengan sim gate dan aset
		if(!$p_posterr){
			$p_posterr = mIntegrasi::saveUnit($conn,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penyimpanan Unit Kerja Integrasi gagal';
		}

		//integrasi dengan sim gate mutu
		//if(!$p_posterr)
		//	list($p_posterr,$p_postmsg) = mIntegrasi::saveUnitLain($connmutu,$r_key);
		
		$ok = Query::isOK($p_posterr);
		if($ok){
			//mengganti right
			Modul::changeRight($conn,Modul::getUnit(),Modul::getRole(),Modul::getUserID());
			
			$a_input = array();
			$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Kode Unit', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
			$a_input[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
			$a_input[] = array('kolom' => 'namasingkat', 'label' => 'Nama Singkat', 'maxlength' => 6, 'size' => 10);
			
			//default dari parent
			if(!empty($pkey))
				$a_input[] = array('kolom' => 'parentunit', 'label' => 'Parent Unit', 'type' => 'S', 'empty' => true, 'notnull' => true, 'option' => mCombo::unitSave($conn,false),'default' => $pkey);
			else
				$a_input[] = array('kolom' => 'parentunit', 'label' => 'Parent Unit', 'type' => 'S', 'empty' => true, 'option' => mCombo::unitSave($conn,false));
				
			$a_input[] = array('kolom' => 'idunitanggaran', 'label' => 'Unit Anggaran<br><font size="1px">(digunakan laporan penggajian)</font>', 'type' => 'S', 'option' => mCombo::unitSave($conn,false),'empty'=>true);
			$a_input[] = array('kolom' => 'namapimpinan', 'label' => 'Pimpinan', 'maxlength' => 100, 'size' => 60);
			$a_input[] = array('kolom' => 'nippimpinan', 'type' => 'H');
			$a_input[] = array('kolom' => 'kodeprogram', 'label' => 'Program Pendidikan', 'type' => 'S', 'empty' => true, 'option' => $p_model::listProgramPendidikan($conn));
			
			//default dari parent
			if(!empty($pkey)){
				$a_input[] = array('kolom' => 'isakademik', 'label' => 'is Akademik?', 'type' => 'S', 'empty' => true, 'option' => $p_model::isAkademik(),'default' => $pcat['isakademik']);
				$a_input[] = array('kolom' => 'isaktif', 'label' => 'is Aktif?', 'type' => 'S', 'option' => mCombo::isAktif(),'default' => $pcat['isaktif']);
			}else{
				$a_input[] = array('kolom' => 'isakademik', 'label' => 'is Akademik?', 'type' => 'S', 'empty' => true, 'option' => $p_model::isAkademik());
				$a_input[] = array('kolom' => 'isaktif', 'label' => 'is Aktif?', 'type' => 'S', 'option' => mCombo::isAktif());
			}

			//keperluan mutu
			$a_input[] = array('kolom' => 'noskpendirian', 'label' => 'SK Pendirian', 'maxlength' => 50, 'size' => 30);
			$a_input[] = array('kolom' => 'tglskpendirian', 'label' => 'Tgl. SK Pendirian', 'type' => 'D');
			$a_input[] = array('kolom' => 'pejabatttdsk', 'label' => 'Pejabat TTD SK', 'maxlength' => 100, 'size' => 50);
			$a_input[] = array('kolom' => 'bulan', 'label' => 'Periode Mulai', 'empty' => true, 'type' => 'S', 'option' => Date::arrayMonth());
			$a_input[] = array('kolom' => 'tahun', 'maxlength' => 4, 'size' => 4);
			$a_input[] = array('kolom' => 'nomorskizin', 'label' => 'SK Izin', 'maxlength' => 50, 'size' => 30);
			$a_input[] = array('kolom' => 'tglskizin', 'label' => 'Tgl. SK Izin', 'type' => 'D');
			$a_input[] = array('kolom' => 'statusakreditasi', 'label' => 'Status Akreditasi', 'maxlength' => 2, 'size' => 2);
			$a_input[] = array('kolom' => 'noskbanpt', 'label' => 'SK BANPT', 'maxlength' => 50, 'size' => 30);
			$a_input[] = array('kolom' => 'tglskbanpt', 'label' => 'Tgl. SK BANPT', 'type' => 'D');
			$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
			$a_input[] = array('kolom' => 'telp', 'label' => 'Telp', 'maxlength' => 25, 'size' => 30);
			$a_input[] = array('kolom' => 'fax', 'label' => 'Fax', 'maxlength' => 25, 'size' => 30);
			$a_input[] = array('kolom' => 'homepage', 'label' => 'Homepage', 'maxlength' => 100, 'size' => 50);
			$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 100, 'size' => 50);
		
			$conn->CommitTrans($ok);
			unset($post);
			$pkey = '';
		}else
			$conn->RollbackTrans();
	}
	else if($r_act == 'delete' and $c_delete) {	
		$conn->BeginTrans();
		
		$p_posterr = $p_model::deleteInfoUnit($conn,$r_key);
		if($p_posterr)
			$p_postmsg = 'Penghapusan Unit Kerja gagal';

	/*	if(!$p_posterr){
			$p_posterr = $p_model::deleteInfoLain($connmutu,'mutu','ms_unit',$p_key,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penghapusan Unit Kerja Mutu gagal';
		}*/
			
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);

		/*if(!$p_posterr){
			$p_posterr = Query::qDelete($connmutu,'mutu.ms_unit',$p_model::getCondition($r_key,$p_key));
			if($p_posterr)
				$p_postmsg = 'Penghapusan Unit Kerja Mutu gagal';
		}*/
			
		//delete juga integrasi sim gate dan aset
		if(!$p_posterr){
			$p_posterr = mIntegrasi::deleteUnit($conn,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penghapusan Unit Kerja Integrasi gagal';
		}
			
		//delete juga integrasi mutu
		/*if(!$p_posterr){
			$p_posterr = mIntegrasi::deleteUnitLain($connmutu,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penghapusan Unit Kerja Mutu gagal';
		}*/
			
		$ok = Query::isOK($p_posterr);
		if($ok){
			//mengganti right
			Modul::changeRight($conn,Modul::getUnit(),Modul::getRole(),Modul::getUserID());
			
			$conn->CommitTrans($ok);					
			Route::navigate($p_listpage);
		}else
			$conn->RollbackTrans();
	}
	
	$sql = $p_model::getDataUnit($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
			
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
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeunit') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodeunit') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaunit') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namaunit') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namasingkat') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namasingkat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'parentunit') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'parentunit') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idunitanggaran') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idunitanggaran') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namapimpinan') ?></td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'namapimpinan') ?>
								<?= Page::getDataInput($row,'nippimpinan') ?>	
								<?/*<span id="edit" style="display:none">
									<img id="imgpeg_c" src="images/green.gif">
									<img id="imgpeg_u" src="images/red.gif" style="display:none">
								</span>*/?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeprogram') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodeprogram') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isakademik') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'isakademik') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isaktif') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'isaktif') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'noskpendirian') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'noskpendirian') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglskpendirian') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglskpendirian') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pejabatttdsk') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'pejabatttdsk') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'bulan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'bulan') ?>&nbsp;<?= Page::getDataInput($row,'tahun') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nomorskizin') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nomorskizin') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglskizin') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglskizin') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statusakreditasi') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'statusakreditasi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'noskbanpt') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'noskbanpt') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglskbanpt') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglskbanpt') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alamat') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'alamat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'telp') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'telp') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'fax') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'fax') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'homepage') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'homepage') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'email') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'email') ?></td>
						</tr>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="pkey" id="pkey" value="<?= $pkey ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<?/*
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
*/?>
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
	// $("#namapimpinan").xautox({strpost: "f=acnamapegawai", targetid: "nippimpinan", imgchkid: "imgpeg", imgavail: true});
});

</script>
</body>
</html>
