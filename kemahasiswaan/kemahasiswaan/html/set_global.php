<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('settingmhs'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Setting Kemahasiswaan';
	$p_tbwidth = 500;
	$p_aktivitas = 'SETTING';
	$p_model = mSettingMhs;
	
	$r_key = 1;
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nipkabiro', 'label' => 'NIP Kabiro Kemahasiswaan', 'size' => 38, 'maxlength' => 25);
	$a_input[] = array('kolom' => 'namakabiro', 'label' => 'Nama Kabiro Kemahasiswaan', 'size' => 38, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'nipbeasiswa', 'label' => 'NIP Kabiro Pengembangan<br> kerjasama institusi', 'size' => 38, 'maxlength' => 25);
	$a_input[] = array('kolom' => 'namabeasiswa', 'label' => 'Nama Kabiro Pengembangan<br> kerjasama institusi', 'size' => 38, 'maxlength' => 100);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
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
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem" >
			<?	if(!empty($p_postmsg)) { ?>
			<center>
			<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:950px">
				<?= $p_postmsg ?>
			</div>
			</center>
			<div class="Break"></div>
			<?	} ?>
			<center>
			<div>
				<header style="width:<?= $p_tbwidth-50 ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Setting Kemahasiswaan</h1>
						</div>
					</div>
				</header>
				<?	/********/
					/* DATA */
					/********/
				?>
				<form name="pageform" id="pageform" method="post">
					<div class="box-content" style="width:<?= $p_tbwidth-72 ?>px">
						<table width="100%" cellpadding="4" cellspacing="2" align="center" style="margin-bottom:10px">
							<?= Page::getDataTR($row,'nipkabiro',null,true) ?>
							<?= Page::getDataTR($row,'namakabiro',null,true) ?>
							<?= Page::getDataTR($row,'nipbeasiswa',null,true) ?>
							<?= Page::getDataTR($row,'namabeasiswa',null,true) ?>
						</table>
						<input type="button" value="Simpan Setting Kemahasiswaan" class="ControlStyle" onclick="goSubmit()">
					</div>
					<input type="hidden" name="act" id="act" value="save">
				</form>
			</div>
			</center>
		</div>
	</div>
</div>
</body>
</html>
