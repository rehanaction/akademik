<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	// combo
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Rekap Penggunaan Ruang (Jam)';
	$p_tbwidth = 700;
	$p_aktivitas = 'UNIT';
	
	// mendapatkan data
	$a_data = mKelas::getRekapRuang($conn,$r_periode);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	
	// combo hari
	$a_hari = Date::arrayDay();
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfilter.php'); ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tr>
						<th>Ruang</th>
						<?	foreach($a_hari as $t_no => $t_hari) { ?>
						<th width="70"><?= $t_hari ?></th>
						<?	} ?>
					</tr>
				<?	foreach($a_data as $t_ruang => $t_data) { ?>
					<tr class="NoHover">
						<td><?= $t_ruang ?></td>
						<?	foreach($a_hari as $t_no => $t_hari) { ?>
						<td align="right"><?= CStr::cStrNBSP(round($t_data[$t_no]/60,2)) ?></td>
						<?	} ?>
					</tr>
				<?	} ?>
				</table>
			</form>
		</div>
	</div>
</div>
</body>
</html>
