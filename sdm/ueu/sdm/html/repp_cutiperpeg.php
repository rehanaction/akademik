<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('cuti'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Daftar Riwayat Cuti Berdasarkan Nama Pegawai';
	$p_tbwidth = 600;
	$p_aktivitas = 'LAPORAN';
	$r_month = (int)date('m');
	$r_tahun = date('Y');
	
	$p_model = 'mCuti';
	$p_reportpage = 'rep_cutiperpeg';
		
	$a_input = array();
	$a_input[] = array('kolom' => 'namapegawai', 'label' => 'Nama Pegawai', 'maxlength' => 255, 'size' => 50, 'notnull' => true);
	$a_input[] = array('label' => 'Periode Pengajuan', 'input' => uCombo::bulan($r_month,true,'bulan1','',false).' '.uCombo::tahun($r_tahun,true,'tahun1','',false).' s/d '.uCombo::bulan($r_month,true,'bulan2','',false).' '.uCombo::tahun($r_tahun,true,'tahun2','',false));
	$a_input[] = array('label' => 'Jenis Cuti', 'input' => UI::createSelect('jeniscuti',$p_model::jenisCuti($conn),'','ControlStyle',true,'',true,'-- Semua Jenis --'));
	
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
					<?	$a_required = array();
						foreach($a_input as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['kolom'];
							if(empty($t_row['input']))
								$t_row['input'] = uForm::getInput($t_row);
					?>
						<tr>
							<td class="LeftColumnBG" width="200px" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '*' : '' ?>
							</td>
							<td class="RightColumnBG">
								<?= $t_row['input'] ?>
								<?if($t_row['kolom'] == 'namapegawai'){?>	
									<img id="imgpeg_c" src="images/green.gif">
									<img id="imgpeg_u" src="images/red.gif" style="display:none">
								<?}?>
							</td>
						</tr>
					<?	} ?>
						<tr>
							<input type="hidden" name="idpegawai" id="idpegawai">
							<td class="LeftColumnBG" style="white-space:nowrap">Format</td>
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
	//autocomplete
	$("#namapegawai").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgpeg", imgavail: true});
});
</script>
</body>
</html>
