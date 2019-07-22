<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_periode=$r_tahun.$r_semester;
	// properti halaman
	$p_title = 'Laporan Kelas';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	$a_input[] = array('label' => 'Prodi', 'input' => uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false));
	
	$a_laporan = array();
	$a_laporan['rep_absensikuliah'] = 'Absensi Perkuliahan';
	$a_laporan['rep_absensitutorial'] = 'Absensi Tutorial';
	$a_laporan['rep_absensipraktikum'] = 'Absens Praktikum';
	$a_laporan['rep_absensidosen'] = 'Absensi Dosen';
	//$a_laporan['rep_absensiuas&uts=1'] = 'Absensi UTS';
	//$a_laporan['rep_absensiuas'] = 'absensi UAS';
	
	//require_once($conf['view_dir'].'inc_repp.php');
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
					<?	$a_required = array();
						foreach($a_input as $t_row) {
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
						<td class="LeftColumnBG" width="100" style="white-space:nowrap">Mata Kuliah & Kelas</td>
						<td class="RightColumnBG">
							<?= UI::createTextBox('namamk','','ControlStyle',50,40, true, '', 'Kode - Nama Matakuliah') ?>
							<input type="hidden" id="kodemk" name="kodemk">	
						</td>
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
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var reportpage = "<?= Route::navAddress($p_reportpage) ?>";
var required = "<?= @implode(',',$a_required) ?>";
$(document).ready(function() {
	$("#namamk").xautox({strpost: "f=rep_acmatkul&periode=<?=$r_periode?>&kodeunit=<?=$r_unit?>", targetid: "kodemk"});
});
</script>
</body>
</html>
