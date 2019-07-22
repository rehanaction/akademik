<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Rekap Statistik Alumni';
	$p_tbwidth = 400;
	$p_aktivitas = 'LAPORAN';
	
	
	$a_input = array();
	// combo
	$l_periode = uCombo::periodeWisuda($conn,$r_periode,'periode','',false);
	$l_periode2 = uCombo::periodeWisuda($conn,$r_periode2,'periode2','',false);
	$l_prodi = uCombo::jurusan_yudisium($conn,$r_jurusan,'','jurusan','',false,false);

	// $a_input[] = array('label' => 'Prodi', 'input' => uCombo::jurusan($conn,$r_jurusan,$r_fakultas,'jurusan','',false));
	// $a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	// $a_input[] = array('label' => 'Periode', 'input' =>  uCombo::periodeWisuda($conn,$r_periode,'periode','onchange="goSubmit()"',false));
	
	// require_once($conf['view_dir'].'inc_repp.php');
	if(empty($p_reportpage))
		$p_reportpage = Route::getReportPage();
?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forreport.js"></script>
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
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth+22 ?>px">
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
					<div class="box-content" style="width:<?= $p_tbwidth ?>px">
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">Program Studi</td>
							<td class="RightColumnBG"><?= $l_prodi ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">Periode Wisuda</td>
							<td class="RightColumnBG"><?= $l_periode ?> - <?= $l_periode2?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">Format</td>
							<td class="RightColumnBG"><?= uCombo::format() ?></td>
						</tr>
					</table>
					<div class="Break"></div>
					<?	if(empty($a_laporan)) { ?>
					<input type="button" value="Tampilkan" class="ControlStyle" onclick="goReport()">
					<?	} else {
							foreach($a_laporan as $t_file => $t_label) { ?>
					<input type="button" value="<?= $t_label ?>" class="ControlStyle" onclick="goReport('<?= $t_file ?>')">
					<?		}
						} ?>
					</div>
				</center>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var reportpage = "<?= Route::navAddress($p_reportpage) ?>";
var required = "<?= @implode(',',$a_required) ?>";

</script>
</body>
</html>