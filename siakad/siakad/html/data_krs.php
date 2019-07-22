<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_npm = CStr::removeSpecial($_REQUEST['npm']);
	
	if(!empty($r_key))
		list(,,,,,$r_npm) = explode('|',$r_key);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data KRS Mahasiswa';
	$p_tbwidth = 600;
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	
	$p_model = mKRS;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_npm))
		Route::navigate('list_mahasiswa');
	else if($c_readlist)
		$p_listpage .= ('&npm='.$r_npm);
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// cek data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_npm);
	
	$a_semester = mCombo::semester();
	$a_tahun = mCombo::tahun();
	$a_kurikulum = mCombo::kurikulum($conn);
	
	$r_act = $_POST['act'];
	if(empty($r_key) or $r_act == 'change') {
		$post['semester'] = Modul::setRequest($_POST['semester'],'SEMESTER');
		$post['tahun'] = Modul::setRequest($_POST['tahun'],'TAHUN');
		$post['thnkurikulum'] = Modul::setRequest($_POST['thnkurikulum'],'KURIKULUM');
		$post['kodemk'] = Modul::setRequest($_POST['kodemk']);
		
		$r_kurikulum = $post['thnkurikulum'];
		if(!isset($a_kurikulum[$r_kurikulum]))
			$r_kurikulum = key($a_kurikulum);
		
		$r_tahun = $post['tahun'];
		if(!isset($a_tahun[$r_tahun]))
			$r_tahun = key($a_tahun);
		
		$r_semester = $post['semester'];
		if(!isset($a_semester[$r_semester]))
			$r_semester = key($a_semester);
		
		$r_periode = $r_tahun.$r_semester;
		
		$a_kodemk = $p_model::mkPeriode($conn,$r_kurikulum,$a_infomhs['kodeunit'],$r_periode);
		$a_nilai = mCombo::nAngkaKurikulum($conn,$r_kurikulum);
		
		$r_kodemk = $post['kodemk'];
		if(!isset($a_kodemk[$r_kodemk]))
			$r_kodemk = key($a_kodemk);
	}
	else {
		$a_cek = $p_model::getData($conn,$r_key);
		
		$r_periode = $a_cek['periode'];
		$r_kurikulum = $a_cek['thnkurikulum'];
		
		$a_kodemk = $p_model::mkPeriode($conn,$r_kurikulum,$a_infomhs['kodeunit'],$r_periode);
		$a_nilai = mCombo::nAngkaKurikulum($conn,$r_kurikulum);
		
		$r_kodemk = $a_cek['kodemk'];
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'semester', 'label' => 'Periode', 'type' => 'S', 'option' => $a_semester, 'add' => 'onchange="goChange()"');
	$a_input[] = array('kolom' => 'tahun', 'type' => 'S', 'option' => $a_tahun, 'add' => 'onchange="goChange()"');
	$a_input[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum', 'type' => 'S', 'option' => $a_kurikulum, 'add' => 'onchange="goChange()"');
	$a_input[] = array('kolom' => 'kodemk', 'label' => 'Mata Kuliah', 'type' => 'S', 'option' => $a_kodemk, 'add' => 'onchange="goChange()"', 'notnull' => true);
	$a_input[] = array('kolom' => 'kelasmk', 'label' => 'Kelas', 'type' => 'S', 'option' => $p_model::kelasMkPeriode($conn,$r_kurikulum,$a_infomhs['kodeunit'],$r_periode,$r_kodemk), 'notnull' => true);
	$a_input[] = array('kolom' => 'nangka', 'label' => 'Nilai', 'type' => 'S', 'option' => mCombo::nAngkaKurikulum($conn,$r_kurikulum));
	$a_input[] = array('kolom' => 'dipakai', 'label' => 'Dipakai', 'type' => 'C', 'option' => array('-1' => ''));
	
	// ada aksi
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['nim'] = $r_npm;
		$record['kodeunit'] = $a_infomhs['kodeunit'];
		$record['periode'] = $record['tahun'].$record['semester'];
		$record['nnumerik'] = 'null';
		if($record['dipakai']=='null')
			$record['dipakai']=0;
		
		$key_kelas=$record['thnkurikulum'].'|'.$record['kodemk'].'|'.$record['kodeunit'].'|'.$record['periode'].'|'.$record['kelasmk'];
		//echo $key_kelas;
		$penuh=mKelas::kelasPenuh($conn,$key_kelas);
		
		if($penuh){
		    list($p_posterr,$p_postmsg)=array(true,'Kelas/Seksi ini sudah penuh');
		}else{
		    if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		    else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
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
				<center>
				<?php require_once('inc_headermhs.php') ?>
				</center>
				<br>
				<?	/*****************/
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
						<?= Page::getDataTR($row,'semester,tahun') ?>
						<?= Page::getDataTR($row,'thnkurikulum') ?>
						<?= Page::getDataTR($row,'kodemk') ?>
						<?= Page::getDataTR($row,'kelasmk') ?>
						<?= Page::getDataTR($row,'nangka') ?>
						<?= Page::getDataTR($row,'dipakai') ?>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="npm" id="npm" value="<?= $r_npm ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
