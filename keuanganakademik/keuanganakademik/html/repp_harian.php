<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('bank'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Pembayaran Rutin';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	       
	$a_input = array();
	$a_input['sistemkuliah'] = array('kolom' => 'sistemkuliah', 'label' => 'Basis', 'type' => 'S', 'option' => array('' => 'Semua') + mAkademik::getArraySistemKuliahCombo($conn));
	$a_input['unit'] = array('kolom' => 'kodeunit', 'label' => 'Jurusan', 'type' => 'S', 'option' => mAkademik::getArrayunit($conn));
	$a_input['angkatan'] = array('kolom' => 'angkatan', 'label' => 'Angkatan', 'type' => 'S', 'option' => array('' => 'Semua') + mCombo::tahun_angkatan());
	$a_input['bank'] = array('kolom' => 'bank', 'label' => 'Bank', 'type' => 'S', 'option' => array('' => 'Semua') + mBank::arrQuery($conn,true));
	$a_input['tglmulai'] = array('kolom' => 'tglmulai', 'label' => 'Tanggal', 'type' => 'D');
	$a_input['tglakhir'] = array('kolom' => 'tglakhir', 'label' => 'Tanggal', 'type' => 'D');
	
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
						<?	$i = 0;
						$a_required = array();
						foreach($a_input as $t_row) {
							if($i++ > 3)
								break;
							
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
							if(empty($t_row['input']))
								$t_row['input'] = uForm::getInput($t_row);
						?>
						<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
							</td>
							<td class="RightColumnBG">
								<?= $t_row['input'] ?>
							</td>
						</tr>
						<?	} ?>
                        <tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								Tanggal
							</td>
							<td class="RightColumnBG">
								<?= uForm::getInput($a_input['tglmulai']) ?>
                                s/d 
                                <?= uForm::getInput($a_input['tglakhir']) ?>
							</td>
						</tr>
                        <? /* <tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								Online / Offline
							</td>
							<td class="RightColumnBG">
								<input type="checkbox" name="jenish2h[]" id="jenish2h" checked="checked" value="0"/> H2H
								<input type="checkbox" name="jenish2h[]" id="jenish2h" checked="checked" value="1"/> Non H2H
							</td>
						</tr> */ ?>
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
