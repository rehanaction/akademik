<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$arr_bulan = Date::arrayMonth(true);
	for($i = date('Y')-10;$i<= date('Y')+20;$i++ ){
		$arr_tahun[$i] = $i;
		}
	
	// properti halaman
	$p_title = 'Rekap Pembelian Token (Harian)';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	       
	$a_input = array();
	$a_input['periodedaftar'] = array('kolom' => 'periodedaftar', 'label' => 'Periode Pendaftaran', 'type' => 'S', 'option' => mAkademik::getPeriodedaftar($conn));
	$a_input['bulan'] = array('kolom' => 'bulan', 'label' => 'Bulan', 'type' => 'S', 'option' => $arr_bulan,'value' => date('m'));
	$a_input['tahun'] = array('kolom' => 'tahun', 'label' => 'Tahun', 'type' => 'S', 'option' => $arr_tahun,'value' => date('Y'));
	
	$a_jenis = mCombo::arrJenish2h();
?>

<?php
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
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								Periode Daftar
							</td>
							<td class="RightColumnBG">
								<?= uForm::getInput($a_input['periodedaftar']) ?>
							</td>
						</tr>
                        <tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								Bulan Tahun
							</td>
							<td class="RightColumnBG">
								<?= uForm::getInput($a_input['bulan']) ?>
                                <?= uForm::getInput($a_input['tahun']) ?>
							</td>
						</tr>
                        <tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								Online / Offline
							</td>
							<td class="RightColumnBG">
								<?php foreach($a_jenis as $k => $v) { ?>
								<input type="checkbox" name="jenish2h[]" id="jenish2h_<?php echo $k ?>" checked="checked" value="<?php echo $k ?>"/>
								<label for="jenish2h_<?php echo $k ?>"><?php echo $v ?></label>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">Format</td>
							<td class="RightColumnBG"><?= uCombo::format() ?></td>
						</tr>
					</table>
					<div class="Break"></div>
					<?	if(empty($a_laporan)) { ?>
					<input type="button" value="Tampilkan" class="ControlStyle" onClick="goReport()">
					<?	} else {
							foreach($a_laporan as $t_file => $t_label) { ?>
					<input type="button" value="<?= $t_label ?>" class="ControlStyle" onClick="goReport('<?= $t_file ?>')">
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