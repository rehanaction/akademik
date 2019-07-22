<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_update = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('pekerjaan'));
	require_once(Route::getModelPath('email'));
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pengajuan Cuti';
	$p_tbwidth = 600;
	$p_aktivitas = 'DATA';
	$p_dbtable = 'pe_rwtberobat';
	$p_key = 'nourutberobat';
	$p_listpage = Route::getListPage();
	
	$p_model = mPekerjaan;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nosurat', 'label' => 'No. Surat', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'readonly' => true);
	$a_input[] = array('kolom' => 'nik', 'label' => 'NPP', 'readonly' => true);
	$a_input[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'readonly' => true);
	$a_input[] = array('kolom' => 'status', 'label' => 'Pengobatan Bagi', 'type' => 'S', 'option' => $p_model::jenisStatus($conn,$r_key),'readonly' => true);
	$a_input[] = array('kolom' => 'nama', 'label' => 'Nama', 'readonly' => true);
	$a_input[] = array('kolom' => 'keluhan', 'label' => 'Keluhan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255,'readonly' => true);
	$a_input[] = array('kolom' => 'biayaberobat', 'label' => 'Biaya Berobat', 'maxlength' => 12, 'size' => 20, 'type' => 'N');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	$a_input[] = array('kolom' => 'setujuatasan', 'label' => 'Setuju Atasan', 'type' => 'R', 'option' => $p_model::statusSetujuiBerobat());
	$a_input[] = array('kolom' => 'setujuklinik', 'label' => 'Setuju Klinik', 'type' => 'R', 'option' => $p_model::statusSetujuiKlinik());
	$a_input[] = array('kolom' => 'catatanklinik', 'label' => 'Cat. Klinik', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	if(Modul::getRole() == 'A' or Modul::getRole() == 'AP')
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	else
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit){
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) unset($post);
	}
	
	$sql = $p_model::getDataEditPermohonanBerobat($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
		if($t_row['id'] == 'status')
			$status = $t_row['realvalue'];
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
						<?= Page::getDataTR($row,'nosurat') ?>
						<?= Page::getDataTR($row,'nik') ?>
						<?= Page::getDataTR($row,'tglpengajuan') ?>
						<?= Page::getDataTR($row,'namalengkap') ?>
						<?= Page::getDataTR($row,'keluhan') ?>
						<?= Page::getDataTR($row,'status') ?>
						<? 
						if($status != 'K')
							echo Page::getDataTR($row,'nama');
						?>
						<?= Page::getDataTR($row,'biayaberobat') ?>
						<?= Page::getDataTR($row,'keterangan') ?>
						<?= Page::getDataTR($row,'isvalid') ?>
						<?= Page::getDataTR($row,'setujuatasan') ?>
						<?= Page::getDataTR($row,'setujuklinik') ?>
						<?= Page::getDataTR($row,'catatanklinik') ?>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
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
