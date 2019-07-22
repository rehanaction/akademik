<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('tugas'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	// combo
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// untuk admin
	if(!Akademik::isMhs() and !Akademik::isDosen()) {
		$r_kodeunit = Modul::setRequest($_POST['unit'],'UNIT');
		$l_kodeunit = uCombo::unit($conn,$r_kodeunit,'unit','onchange="goSubmit()"',false);
	}
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Forum Tugas';
	$p_tbwidth = 640;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = 'list_tugaskelas';
	$p_subpage = 'set_upnilaitugas';
	
	$p_model = mTugas;
	
	// mendapatkan data
	if(Akademik::isMhs())
		$a_data = $p_model::getKelasMhs($conn,$r_periode,Modul::getUserName());
	else if(Akademik::isDosen())
		$a_data = $p_model::getKelasDosen($conn,$r_periode,Modul::getUserName());
	else
		$a_data = $p_model::getKelas($conn,$r_periode,$r_kodeunit);
	
	// membuat filter
	$a_filtercombo = array();
	if(!empty($r_kodeunit)) $a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_kodeunit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
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
						<th>Mata Kuliah</th>
						<th width="60">Peserta</th>
						<th width="60">Tugas</th>
						<th width="150">Posting Terakhir</th>
					</tr>
					<?	$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							
							$t_key = mKelas::getKeyRow($row);
							$t_label = $row['label'];
							
							if(empty($t_label)) {
								$t_id = '';
								$t_waktu = '';
								$t_topik = '';
							}
							else {
								$pos = strpos($t_label,'|');
								$pos2 = strpos($t_label,'|',$pos+1);
								
								$t_waktu = substr($t_label,0,$pos);
								$t_id = substr($t_label,$pos+1,$pos2-$pos-1);
								$t_topik = substr($t_label,$pos2+1);
								
								if(strlen($t_topik) > 30)
									$t_topik = substr($t_topik,0,27).'...';
							}
					?>
					<tr class="<?= $rowstyle ?>">
						<td><u class="ForumLink" id="<?= $t_key ?>" onclick="goDetail(this)"><?= $row['kodemk'] ?> - <?= $row['namamk'] ?> (<?= $row['kelasmk'] ?>)</u></td>
						<td align="center"><?= (int)$row['jumlahpeserta'] ?></td>
						<td align="center"><?= $row['jumlah'] ?></td>
						<td align="center">
						<?	if(!empty($t_id)) { ?>
							<?= CStr::formatDateDiff($t_waktu) ?><br>
							<u class="ULink" onclick="goSubDetail(<?= $t_id ?>)"><?= $t_topik ?></u>
						<?	} ?>
						</td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="4" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

function goDetail(elem) {
	location.href = detailpage + "&kelas=" + elem.id;
}

function goSubDetail(key) {
	goView("<?= $p_subpage ?>&key="+key);
}

</script>
</body>
</html>