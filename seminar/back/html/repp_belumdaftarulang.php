<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
        // properti halaman
	$p_title = 'Laporan Rekapitulasi Data Pendaftar';
	$p_tbwidth = 550;
	$p_aktivitas = 'LAPORAN';
	
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode');
	$l_prodi = uCombo::unitakademik($conn,$r_prodi,'','kodeunit','',false);
	$paramawal= array('kolom' => 'tglawal', 'label' => 'Tanggal Tagihan(Mulai)', 'type' => 'D');
	$paramakhir= array('kolom' => 'tglakhir', 'label' => 'Tanggal Tagihan(Mulai)', 'type' => 'D');
	$i_tanggalmulai=uForm::getInput($paramawal);
	$i_tanggalselesai=uForm::getInput($paramakhir);


	$a_input = array();

	$a_input[] = array('label' => 'Periode','input' => $l_periode);
	$a_input[] = array('label' => 'Jurusan','input' => $l_prodi);
	$a_input[] = array('label' => 'Tanggal Tagihan (awal)','input' => $i_tanggalmulai,'text'=>'Filter Untuk laporan telah daftar ulang');
	$a_input[] = array('label' => 'Tanggal Tagihan (Akhir)','input' => $i_tanggalselesai,'text'=>'Filter Untuk Laporan telah daftar ulang');

	
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
								<?= $t_row['text'] ?>
								
							</td>
						</tr>
					<?	} ?>
						<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">Format</td>
							<td class="RightColumnBG"><?= uCombo::format() ?></td>
						</tr>
					</table>
					<div class="Break"></div>
					<input type="button" value="Tampilkan (belum daftar ulang)" class="ControlStyle" onclick="goReport()"> <br><br>
					<input type="button" value="Tampilkan (telah daftar ulang dan atau memiliki tunggakan)" class="ControlStyle" onclick="goReport2()">
					</div>
				</center>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var reportpage = "<?= Route::navAddress($p_reportpage) ?>";
var reportpage2 = "<?= Route::navAddress('rep_sudahdaftarulang') ?>";
var required = "<?= @implode(',',$a_required) ?>";

function goReport2() {
	var form = document.getElementById("pageform");
	
	form.action = reportpage2;
	form.target = "_blank";
	
	goSubmit();
	
	form.action = "";
	form.target = "";
}


</script>
</body>
</html>


