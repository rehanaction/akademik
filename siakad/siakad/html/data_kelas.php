<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('detailkelas'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('jeniskuliah'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Kelas Mata Kuliah';
	$p_tbwidth = 800;
	$p_aktivitas = 'Kelas';
	$p_listpage = Route::getListPage();
	
	$p_model = mKelas;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// cek data
	$a_kodeunit = mCombo::unit($conn,false);
	$a_kurikulum = mCombo::kurikulum($conn);
	
	$a_ruang = mCombo::ruang($conn);
	
	$r_act = $_POST['act'];
	if(empty($r_key) or $r_act == 'change') {
		$post['kodeunit'] = Modul::setRequest($_POST['kodeunit'],'UNIT');
		$post['thnkurikulum'] = Modul::setRequest($_POST['thnkurikulum'],'KURIKULUM');
		
		$r_kodeunit = $post['kodeunit'];
		if(!isset($a_kodeunit[$r_kodeunit]))
			$r_kodeunit = key($a_kodeunit);
		
		$r_kurikulum = $post['thnkurikulum'];
		if(!isset($a_kurikulum[$r_kurikulum]))
			$r_kurikulum = key($a_kurikulum);
	}
	else {
		$a_cek = $p_model::getData($conn,$r_key);
		
		$r_kodeunit = $a_cek['kodeunit'];
		$r_kurikulum = $a_cek['thnkurikulum'];
	}
	$rowprodi=mUnit::jurusan($conn);
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'semester', 'label' => 'Periode', 'type' => 'S', 'option' => mCombo::semester(), 'request' => 'SEMESTER');
	$a_input[] = array('kolom' => 'tahun', 'type' => 'S', 'option' => mCombo::tahun(), 'request' => 'TAHUN');
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Pengelola', 'type' => 'S', 'option' => $a_kodeunit, 'add' => 'onchange="goChange()"', 'request' => 'UNIT');
	$a_input[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum', 'type' => 'S', 'option' => $a_kurikulum, 'add' => 'onchange="goChange()"', 'request' => 'KURIKULUM');
	$a_input[] = array('kolom' => 'kodemk','label' => 'Mata Kuliah', 'type' => 'S', 'option' => $p_model::mkKurikulum($conn,$r_kurikulum,$r_kodeunit), 'notnull' => true, 'bold' => true);
	$a_input[] = array('kolom' => 'kodemk', 'label' => 'Mata Kuliah', 'type' => 'X', 'text' => 'kodemk', 'param' => 'strpost:"f=acmatkul"');
	$a_input[] = array('kolom' => 'kelasmk', 'label' => 'Kelas', 'maxlength' => 11, 'size' => 5, 'notnull' => true, 'bold' => true);
	$a_input[] = array('kolom' => 'nama', 'label' => 'Dosen Pengajar', 'type' => 'S', 'option' => mCombo::dosen($conn,false));
	//jadwal 1
	$a_input[] = array('kolom' => 'tgljadwal1', 'label' => 'Memulai Jadwal 1','type' => 'D','add'=>'onchange="setHari1(this.value)"');
	$a_input[] = array('kolom' => 'nohari', 'type' => 'S', 'option' => Date::arrayDay(), 'empty' => true,'add'=>'enable="readonly"');
	$a_input[] = array('kolom' => 'jammulai', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'jamselesai', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'koderuang', 'label' => 'Ruang', 'type' => 'S', 'option' => $a_ruang, 'empty' => true);
	
	//jadwal 2
	$a_input[] = array('kolom' => 'tgljadwal2', 'label' => 'Memulai Jadwal 2','type' => 'D','add'=>'onchange="setHari2(this.value)"');
	$a_input[] = array('kolom' => 'nohari2', 'type' => 'S', 'option' => Date::arrayDay(), 'empty' => true,'add'=>'readonly="readonly"');
	$a_input[] = array('kolom' => 'jammulai2', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'jamselesai2', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'koderuang2', 'label' => 'Ruang 2', 'type' => 'S', 'option' => $a_ruang, 'empty' => true);
	
	//jadwal 3
	$a_input[] = array('kolom' => 'tgljadwal3', 'label' => 'Memulai Jadwal 3','type' => 'D','add'=>'onchange="setHari3(this.value)"');
	$a_input[] = array('kolom' => 'nohari3', 'type' => 'S', 'option' => Date::arrayDay(), 'empty' => true,'add'=>'readonly="readonly"');
	$a_input[] = array('kolom' => 'jammulai3', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'jamselesai3', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'koderuang3', 'label' => 'Ruang 3', 'type' => 'S', 'option' => $a_ruang, 'empty' => true);
	
	//jadwal 4
	$a_input[] = array('kolom' => 'tgljadwal4', 'label' => 'Memulai Jadwal 4','type' => 'D','add'=>'onchange="setHari4(this.value)"');
	$a_input[] = array('kolom' => 'nohari4', 'type' => 'S', 'option' => Date::arrayDay(), 'empty' => true,'add'=>'readonly="readonly"');
	$a_input[] = array('kolom' => 'jammulai4', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'jamselesai4', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'koderuang4', 'label' => 'Ruang 4', 'type' => 'S', 'option' => $a_ruang, 'empty' => true);

	$a_input[] = array('kolom' => 'dayatampung', 'label' => 'Kapasitas', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'mengulang', 'label' => 'Kelas Mengulang', 'type' => 'R', 'option' => $p_model::mengulang(), 'default' => '-1');
	$a_input[] = array('kolom' => 'isblock', 'label' => 'Jenis Kelas', 'type' => 'R', 'option' => $p_model::isblock(), 'default' => '0');
	$a_input[] = array('kolom' => 'ismkdu', 'label' => 'Kelas Bersama ?', 'type' => 'R', 'option' => $p_model::ismkdu(), 'default' => '0');
	$a_input[] = array('kolom' => 'isonline', 'label' => 'Kelas Online', 'type' => 'R', 'option' => $p_model::isOnline(), 'default' => '0');
	//$a_input[] = array('kolom' => 'keltutorial', 'label' => 'Jml. kelompok Tutorial', 'maxlength' => 1, 'size' => 1);
	//$a_input[] = array('kolom' => 'kelpraktikum', 'label' => 'Jml. Kelompok Praktikum', 'maxlength' => 1, 'size' => 1);
	$a_input[] = array('kolom' => 'sistemkuliah', 'label' => 'Basis', 'type' => 'S', 'option' => mMahasiswa::sistemKuliah($conn), 'empty' => false);
	
	// ada aksi
	if($r_act == 'save' and $c_edit) { 
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['dosen'] = $_REQUEST['nip'];
		$record['periode'] = $record['tahun'].$record['semester']; 
		$record['jammulai'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jammulai']));
		$record['jammulai2'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jammulai2']));
		$record['jammulai3'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jammulai3']));
		$record['jammulai4'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jammulai4']));
		$record['jamselesai'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jamselesai']));
		$record['jamselesai2'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jamselesai2']));
		$record['jamselesai3'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jamselesai3']));
		$record['jamselesai4'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jamselesai4']));
		//set hari berdasarkan tgl
		if($record['tgljadwal1']!='null') $record['nohari']=date('N',strtotime($record['tgljadwal1']));
		if($record['tgljadwal2']!='null') $record['nohari2']=date('N',strtotime($record['tgljadwal2']));
		if($record['tgljadwal3']!='null') $record['nohari3']=date('N',strtotime($record['tgljadwal3']));
		if($record['tgljadwal4']!='null') $record['nohari4']=date('N',strtotime($record['tgljadwal4']));
			
		//manage yang prodi pesertamku
		$form_mku=array();
		foreach($rowprodi as $kodeunit=>$namaunit) {
				if(!empty($_POST['mku_'.$kodeunit]))
					$form_mku[$kodeunit] = $kodeunit;
		}
		//print_r($form_mku);die();
		if(empty($r_key)){
			$ok=true;
			$conn->BeginTrans();
			if($form_mku)
				$ok = $p_model::saveMku($conn,$r_key,$form_mku,$record);
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			if(!$p_posterr){
				list($p_posterr,$p_postmsg)=mDetailKelas::insertNonBlock($conn,$a_input,$record,$r_key);
			}
			if($p_posterr){
				$ok=false;
				$r_key='';
			}
			$conn->CommitTrans($ok);
		}else{
			
			$ok=true;
			$conn->BeginTrans();
			$ok = $p_model::saveMku($conn,$r_key,$form_mku,$record);
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
			if(!$p_posterr and $record['isblock']==0 and mDetailKelas::ubahDetail($record,$_POST)){
				list($p_posterr,$p_postmsg)=mDetailKelas::deleteBlock($conn,$r_key.'|K|1');
				list($p_posterr,$p_postmsg)=mDetailKelas::insertNonBlock($conn,$a_input,$record,$r_key);
			}
			if($p_posterr){
				$ok=false;
			}
			$conn->CommitTrans($ok);
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		
		$ok=true;
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg)=mDetailKelas:: deleteBlock($conn,$r_key.'|K|1');
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		if($p_posterr)
				$ok=false;
		$conn->CommitTrans($ok);
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertajar' and $c_edit) {
		$record = array();
		$record['nipdosen'] = CStr::cStrNull($_POST['nip']);
		$record['ispjmk'] = $_POST['ispjmk'];
		$record['tugasmengajar'] = $_POST['tugasmengajar'];
		$record['jeniskul'] = !empty($record['jeniskul'])?$record['jeniskul']:'K';
		$record['kelompok'] = !empty($record['kelompok'])?$record['kelompok']:'1';
		if($_POST['isonline']==-1 and $_POST['tugasmengajar']==-1 and !empty($_POST['tgljadwal1'])){
			$course = mKelas::getCourseByPass($conn_moodle,$_POST['kodeunit']."".$_POST['tahun'].$_POST['semester']."".$_POST['kodemk']."".$_POST['kelasmk']);
			if(empty($course)){
				$namamk = mKelas::getNamamk($conn,$_POST['kodemk']);
				$keyall = $_POST['tahun']."|".$_POST['kodemk']."|".$_POST['kodeunit']."|".$_POST['tahun'].$_POST['semester']."|".$_POST['kelasmk']."|K|1|0|".$namamk."|".$_POST['tgljadwal1']."|".$_POST['nip'];
				if(mKelas::addCourseMoodle($keyall)){
					$mooduser = mKelas::getUserMoodle($conn,$_POST['nip']);
					if(!empty($mooduser['users'])){
						mKelas::enrolDosen($conn_moodle,$conn,$keyall);
					}else{
						$d_users=mKelas::inquiryByuserid($conn,$_POST['nip']);
						mKelas::syncUserToElearning($conn,$d_users);
						mKelas::enrolDosen($conn_moodle,$conn,$keyall);
					}
				
				}
			}
		}
		list($p_posterr,$p_postmsg) = $p_model::insertRecordMengajar($conn,$record,$r_key);
	}
	else if($r_act == 'updateajar' and $c_edit) {
		$record = array();
		$record['nipdosen'] = CStr::cStrNull($_POST['u_nip']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecordMengajar($conn,$record,$r_key);
	}
	else if($r_act == 'set' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$t_key=$r_key.'|'.$r_subkey;
		$a_tkey=explode('|',$t_key);
		if(count($a_tkey)<=6)
			$t_key=$r_key.'|K|1|'.$r_subkey;
		$record = array();
		$record['ispjmk'] = 1;
		//$record['tugasmengajar'] = "-1";
		
		list($p_posterr,$p_postmsg) = mMengajar::updateRecord($conn,$record,$t_key,true);
	}
	else if($r_act == 'unset' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$t_key=$r_key.'|'.$r_subkey;
		$a_tkey=explode('|',$t_key);
		if(count($a_tkey)<=6)
			$t_key=$r_key.'|K|1|'.$r_subkey;
		$record = array();
		$record['ispjmk'] = 0;
		//$record['tugasmengajar'] = 0;
		
		list($p_posterr,$p_postmsg) = mMengajar::updateRecord($conn,$record,$t_key,true);
		
	}else if($r_act == 'setajar' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$t_key=$r_key.'|'.$r_subkey;
		$a_tkey=explode('|',$t_key);
		if(count($a_tkey)<=6)
			$t_key=$r_key.'|K|1|'.$r_subkey;
		$record = array();
		$record['tugasmengajar'] = "-1";
		
		list($p_posterr,$p_postmsg) = mMengajar::updateRecord($conn,$record,$t_key,true);
	}else if($r_act == 'unsetajar' and $c_edit){
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$t_key=$r_key.'|'.$r_subkey;
		$a_tkey=explode('|',$t_key);
		if(count($a_tkey)<=6)
			$t_key=$r_key.'|K|1|'.$r_subkey;
		$record = array();
		//$record['ispjmk'] = 0;
		$record['tugasmengajar'] = 0;
		
		list($p_posterr,$p_postmsg) = mMengajar::updateRecord($conn,$record,$t_key,true);
	}
	else if($r_act == 'deleteajar' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$course = mKelas::getCourseByPass($conn_moodle,$_POST['kodeunit']."".$_POST['tahun'].$_POST['semester']."".$_POST['kodemk']."".$_POST['kelasmk']);
		if($_POST['isonline']==-1){
			if(!empty($course)){
				
				$mooduser = mKelas::getUserMoodle($conn,$r_subkey);
				if(!empty($mooduser['users'])){
					$key = $course."|".$mooduser['users'][0]['id'];
					mKelas::UnEnrolUser($conn_moodle,$conn,$key);
				}
			}
		}
		list($p_posterr,$p_postmsg) = $p_model::deleteMengajar($conn,$r_key,$r_subkey);
	}else if($r_act == 'insertmku' and $c_edit) {
		$record = array();
		$record['unitmku'] = CStr::cStrNull($_POST['unitmku']);
		
		
		list($p_posterr,$p_postmsg) = $p_model::insertRecordMku($conn,$record,$r_key);
	}else if($r_act == 'deletemku' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkeymku']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteMku($conn,$r_key,$r_subkey);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	if(!empty($r_key)){
		$rowp = $p_model::getDosenPengajar($conn,$r_key);
		$rowmku=$p_model::getPesertaMku($conn,$r_key);
		
	}
	
	$a_pengajar = array();
	if(!empty($rowp)) {
		$pjmk=false;
		$tugasmengajar=false;
		foreach($rowp as $t_row){
			if($t_row['ispjmk'] == '1'){
				$a_pengajar[] = $t_row['nama'].' ('.$t_row['nipdosen'].') => <b>Koordinator</b>';
				$pjmk=true;
			}else{	
				$a_pengajar[] = $t_row['nama'].' ('.$t_row['nipdosen'].')';
			}
			if($t_row['tugasmengajar'] == '-1'){
				$tugasmengajar=true;
			}
		}
	}
	$a_mku = array();
	$list_mku=array();
	if(!empty($rowmku)) {
		foreach($rowmku as $t_row){
				$a_mku[] = $t_row['namaunit'].' ('.$t_row['unitmku'].')';
				$list_mku[$t_row['unitmku']]=$t_row['namaunit'];
		}
	}
	$kelompok=mJeniskuliah::flagKelompok($conn);
	
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
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>

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
						
						$a_required = array('kodemk');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'semester,tahun') ?>
						<?= Page::getDataTR($row,'kodeunit') ?>
						<?= Page::getDataTR($row,'thnkurikulum') ?>
						<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Mata Kuliah</td>
						<td class="RightColumnBG">
						<?= Page::getDataInput($row,'kodemk') ?>
						</td>
						</tr>
						<? // = Page::getDataTR($row,'kodemk') ?>
						<?= Page::getDataTR($row,'kelasmk') ?>
						<?= Page::getDataTR($row,'sistemkuliah') ?>
						<?= Page::getDataTR($row,'dayatampung') ?>
						<?= Page::getDataTR($row,'mengulang') ?>
						<?= Page::getDataTR($row,'isblock') ?>
						<?= Page::getDataTR($row,'ismkdu') ?>
						<?= Page::getDataTR($row,'isonline') ?>
						<!--tr id="kelompok">
							<td class="LeftColumnBG">
								<?= Page::getDataLabel($row,'keltutorial') ?>
							</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'keltutorial') ?>
							</td>
						</tr>
						<tr id="kelompok">
							<td class="LeftColumnBG">
								<?= Page::getDataLabel($row,'kelpraktikum') ?>
							</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'kelpraktikum') ?>
							</td>
						</tr-->
						<?/*php foreach($kelompok as $row_kelompok) { ?>
							<tr id="kelompok">
							<td class="LeftColumnBG">
								<?='Kelompok '.$row_kelompok['namajeniskuliah'] ?>
							</td>
							<td class="RightColumnBG">
								<?= UI::createTextBox('kelompokkuliah',$t_row['nipdosen'].' - '.$t_row['nama'],'ControlStyle',0,60) ?>
							</td>
						</tr>
						<?php } */?>
						<? if(!empty($r_key) and Page::getDataValue($row,'ismkdu')==-1) { ?>
						<tr>
							<td class="DataBG" colspan="2">Setting Kuliah Bersama</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Prodi Peruntukan</td>
							<td class="RightColumnBG">
								<span id="show">
								<?= implode('<br>',$a_mku) ?>
								</span>
								<span id="edit" style="display:none">
								<? if($c_edit) { ?>
								<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
									<tr>
										<td><input type="checkbox" id="checkAll" title="Check/Uncheck All"></td>
										<td><b>Check All</b></td>
									</tr>
								<?	$i = 0;
									foreach($rowprodi as $kodeunit=>$namaunit) {
										if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
									<tr valign="top" class="<?= $rowstyle ?>">
										<td width="20"><input type="checkbox" <?=isset($list_mku[$kodeunit])?'checked':''?> name="mku_<?=$kodeunit?>" id="checklist"></td>
										<td><?= $kodeunit.' ('.$namaunit.')' ?></td>
									</tr>
								<?	} ?>
									
								</table>
								<? } else { ?>
								<?= implode('<br>',$a_mku) ?>
								<? } ?>
								</span>
							</td>
						</tr>
						<?php } ?>
						<tr id="jadwal">
							<td class="DataBG" colspan="2">Jadwal dan Pengajar</td>
						</tr>
						<!--jadwal 1 -->
						<tr id="jadwal">
							<td class="LeftColumnBG">
								<?= Page::getDataLabel($row,'tgljadwal1') ?>
							</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'tgljadwal1') ?>
								<input type="hidden" name="old_tgljadwal1" value="<?= Page::getDataValue($row,'tgljadwal1') ?>">
							</td>
						</tr>
						<tr id="jadwal">
							<td class="LeftColumnBG" style="white-space:nowrap">Jadwal 1</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'nohari') ?>, <?= Page::getDataInput($row,'jammulai') ?> - <?= Page::getDataInput($row,'jamselesai') ?>
								&nbsp; &nbsp; &nbsp; Ruang: <?= Page::getDataInput($row,'koderuang') ?>
								<input type="hidden" name="old_jammulai" value="<?= Page::getDataValue($row,'jammulai') ?>">
								<input type="hidden" name="old_jamselesai" value="<?= Page::getDataValue($row,'jamselesai') ?>">
								<input type="hidden" name="old_koderuang" value="<?= Page::getDataValue($row,'koderuang') ?>">
							</td>
						</tr>
						
						<!--jadwal 2 -->
						<tr id="jadwal">
							<td class="LeftColumnBG">
								<?= Page::getDataLabel($row,'tgljadwal2') ?>
							</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'tgljadwal2') ?>
								<input type="hidden" name="old_tgljadwal2" value="<?= Page::getDataValue($row,'tgljadwal2') ?>">
							</td>
						</tr>
						<tr id="jadwal">
							<td class="LeftColumnBG" style="white-space:nowrap">Jadwal 2</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'nohari2') ?>, <?= Page::getDataInput($row,'jammulai2') ?> - <?= Page::getDataInput($row,'jamselesai2') ?>
								&nbsp; &nbsp; &nbsp; Ruang: <?= Page::getDataInput($row,'koderuang2') ?>
								<input type="hidden" name="old_jammulai2" value="<?= Page::getDataValue($row,'jammulai2') ?>">
								<input type="hidden" name="old_jamselesai2" value="<?= Page::getDataValue($row,'jamselesai2') ?>">
								<input type="hidden" name="old_koderuang2" value="<?= Page::getDataValue($row,'koderuang2') ?>">
							</td>
						</tr>
						
						<!--jadwal 3 -->
						<tr id="jadwal">
							<td class="LeftColumnBG">
								<?= Page::getDataLabel($row,'tgljadwal3') ?>
							</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'tgljadwal3') ?>
								<input type="hidden" name="old_tgljadwal3" value="<?= Page::getDataValue($row,'tgljadwal3') ?>">
							</td>
						</tr>
						<tr id="jadwal">
							<td class="LeftColumnBG" style="white-space:nowrap">Jadwal 3</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'nohari3') ?>, <?= Page::getDataInput($row,'jammulai3') ?> - <?= Page::getDataInput($row,'jamselesai3') ?>
								&nbsp; &nbsp; &nbsp; Ruang: <?= Page::getDataInput($row,'koderuang3') ?>
								<input type="hidden" name="old_jammulai3" value="<?= Page::getDataValue($row,'jammulai3') ?>">
								<input type="hidden" name="old_jamselesai3" value="<?= Page::getDataValue($row,'jamselesai3') ?>">
								<input type="hidden" name="old_koderuang3" value="<?= Page::getDataValue($row,'koderuang3') ?>">
							</td>
						</tr>
						
						<!-- jadwal 4 -->
						<tr id="jadwal">
							<td class="LeftColumnBG">
								<?= Page::getDataLabel($row,'tgljadwal4') ?>
							</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'tgljadwal4') ?>
								<input type="hidden" name="old_tgljadwal4" value="<?= Page::getDataValue($row,'tgljadwal4') ?>">
							</td>
						</tr>
						<tr id="jadwal">
							<td class="LeftColumnBG" style="white-space:nowrap">Jadwal 4</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'nohari4') ?>, <?= Page::getDataInput($row,'jammulai4') ?> - <?= Page::getDataInput($row,'jamselesai4') ?>
								&nbsp; &nbsp; &nbsp; Ruang: <?= Page::getDataInput($row,'koderuang4') ?>
								<input type="hidden" name="old_jammulai4" value="<?= Page::getDataValue($row,'jammulai4') ?>">
								<input type="hidden" name="old_jamselesai4" value="<?= Page::getDataValue($row,'jamselesai4') ?>">
								<input type="hidden" name="old_koderuang4" value="<?= Page::getDataValue($row,'koderuang4') ?>">
							</td>
						</tr>
						<? if(!empty($r_key)) { ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Dosen Pengajar</td>
							<td class="RightColumnBG">
								<span id="show">
								<?= implode('<br>',$a_pengajar) ?>
								</span>
								<span id="edit" style="display:none">
								<? if($c_edit) { ?>
								<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
								<?	$i = 0; 
									foreach($rowp as $t_row) {
										if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
										
								?>
									<tr valign="top" class="<?= $rowstyle ?>">
										<td ><?= $t_row['nama'].' ('.$t_row['nipdosen'].')' ?><?= $t_row['ispjmk']=='1' ? '=> <b>Koordinator</b>':''?></td>
										<td>
											<input title="Set/Unset Koordinator" type="checkbox" value="<?= $t_row['idpegawai'] ?>" onclick="goSetPjmk(this)"<?= empty($t_row['ispjmk']) ? '' : ' checked' ?> <?=(empty($t_row['ispjmk']) and $pjmk) ?'disabled':''?>>
											
											<!--<img id="<? // $t_row['idpegawai'] ?>" title="Hapus Data" src="images/delete.png" onclick="goDeletePengajar(this)" style="cursor:pointer">-->
										</td>
										<td>
											<input title="Set/Unset Pengajar" type="checkbox" value="<?= $t_row['idpegawai'] ?>" onclick="goSetMengajar(this)"<?= empty($t_row['tugasmengajar']) ? '' : ' checked' ?> <?=(empty($t_row['tugasmengajar']) and $tugasmengajar) ?'disabled':''?>>
										</td>
										<td>	
											<img id="<?= $t_row['idpegawai'] ?>" title="Hapus Data" src="images/delete.png" onclick="goDeletePengajar(this)" style="cursor:pointer">
										</td>
									</tr>
									
								<?	} ?>
									<tr class="LeftColumnBG NoHover">
										<td width="100" nowrap><?= UI::createTextBox('dosen',$r_dosen,'ControlStyle',0,60) ?></td>
										<td><input type="checkbox" name="ispjmk" value="1" <?if($pjmk){ echo 'disabled';}?>> Koordinator?</td>
										<td><input type="checkbox" name="tugasmengajar" value="-1" <?if($tugasmengajar){ echo 'disabled';}?>> Pengajar?</td>
										<td><img title="Tambah Data" src="images/disk.png" onclick="goInsertPengajar()" style="cursor:pointer"></td>
									</tr>
								</table>
								<? } else { ?>
								<?= implode('<br>',$a_pengajar) ?>
								<? } ?>
								</span>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php 
									$r_keydetail=$r_key.'|K|1';
									echo '<div class="data_detailkelas">';
										require_once('data_detailkelas.php'); 
									echo '</div><br>';
								?>
							</td>
						</tr>
						<? } ?>
						
						</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="matkul" id="matkul">
				<input type="hidden" id="nip" name="nip" value="<?= $r_key ?>">
			
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="subkeymku" id="subkeymku">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

	<script src="scripts/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>	
	<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
	<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
	<script type="text/javascript">
	    $(function() {
        $.mask.definitions['~'] = "[+-]";
		$("#jammulai").mask("99:99");
		$("#jammulai2").mask("99:99");
		$("#jammulai3").mask("99:99");
		$("#jammulai4").mask("99:99");
		$("#jamselesai").mask("99:99");
		$("#jamselesai2").mask("99:99");
		$("#jamselesai3").mask("99:99");
		$("#jamselesai4").mask("99:99");
		
    });

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);

	$("#dosen").xautox({strpost: "f=acdosen", targetid: "nip"});
	$("#u_dosen").xautox({strpost: "f=acdosen", targetid: "u_nip"});
	$("#kodemk").xautox({strpost: "f=acmatkul", targetid: "matkul"});
	$("#l_unitmku").xautox({strpost: "f=acjurusan", targetid: "unitmku"});
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	var block = $("#isblock_-1").attr("checked");
	var nonblock = $("#isblock_0").attr("checked");
	if(block){
		$("[id='jadwal']").hide();
		
	}else if(nonblock){
		$("[id='jadwal']").show();
		
	}
	 $("#isblock_-1").click(function(){
		 $("[id='jadwal']").hide();
		 
	 });
	 $("#isblock_0").click(function(){
		 $("[id='jadwal']").show();
		 
	 });
	 $("[id='checkAll']").click(function() {
		var checked = $(this).attr("checked");
		if(checked)
			$("[id='checklist']").attr("checked", checked);
		else
			$("[id='checklist']").removeAttr("checked", checked);
	});
});

function goDeletePengajar(elem) {
	document.getElementById("act").value = "deleteajar";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goInsertPengajar() {
	document.getElementById("act").value = "insertajar";
	goSubmit();
}
function goDeleteMku(elem) {
	document.getElementById("act").value = "deletemku";
	document.getElementById("subkeymku").value = elem.id;
	if(confirm('Yakin Akan Menghapus ?'))
		goSubmit();
}

function goInsertMku() {
	document.getElementById("act").value = "insertmku";
	goSubmit();
}
function goUpdatePengajar() {
	document.getElementById("act").value = "updateajar";
	goSubmit();
}
function setHari1(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	document.getElementById("nohari").value=day;
}
function setHari2(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	document.getElementById("nohari2").value=day;
}
function setHari3(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	document.getElementById("nohari3").value=day;
}
function setHari4(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	document.getElementById("nohari4").value=day;
}
function editPjmk(elem) {
	$("#l_"+elem.id).hide();
	$("#u_"+elem.id).show();
	//alert(elem.id);
}
function goSetPjmk(elem) {
	document.getElementById("act").value = (elem.checked ? 'set' : 'unset');
	document.getElementById("subkey").value = elem.value;
	
	goSubmit();
}

function goSetMengajar(elem) {
	document.getElementById("act").value = (elem.checked ? 'setajar' : 'unsetajar');
	document.getElementById("subkey").value = elem.value;
	
	goSubmit();
}
</script>
</body>
</html>
