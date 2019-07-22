<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	
	// koneksi database
	$connh = Query::connect('h2h');
	$connh->debug = $conn->debug;
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	// combo
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Rekapitulasi SPP Mahasiswa';
	$p_tbwidth = 700;
	$p_aktivitas = 'SPP';
	$p_colnum = 5;
	
	$p_model = mPerwalian;
	
	// mendapatkan data
	$a_fakjur = mUnit::getListFakJur($conn);
	list($a_bayar,$a_belumbayar) = $p_model::getDataBayar($conn,$r_periode);
	
	$a_datac = array();
	foreach($a_bayar as $row) {
		$t_kodeunit = $row['kodeunit'];
		
		$a_datac[$t_kodeunit]['jumlahmhs']++;
		$a_datac[$t_kodeunit]['jumlahmhsbayar']++;
	}
	foreach($a_belumbayar as $row) {
		$t_kodeunit = $row['kodeunit'];
		
		$a_datac[$t_kodeunit]['jumlahmhs']++;
		$a_datac[$t_kodeunit]['jumlahmhsblmbayar']++;
	}
	
	// mendapatkan data h2h
	$a_h2h = mTagihan::getRekapHerUnit($connh,$r_periode);
	
	// membuat filter
	$a_filtercombo = array();
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
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th rowspan="2">Prodi</th>
						<th colspan="3">Jumlah Mahasiswa (perwalian)</th>
					</tr>
					<tr>
						<th>Jumlah Mahasiswa</th>
						<th>Jumlah Sudah Bayar</th>
						<th>Jumlah Belum Bayar</th>
						
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_fakjur as $t_data) {
							$t_tjumlahmhs = 0;
							$t_tjumlahmhsbayar = 0;
							$t_tjumlahmhsblmbayar = 0;
							$t_tjumlahbayar = 0;
							$t_tcountmhsbayar=0;
							$t_tcountmhsblmbayar=0;
					?>
					<tr valign="top" class="GreenBG">
						<td colspan="<?= $p_colnum ?>"><strong><?= $t_data['namaunit'] ?></strong></td>
						
						
					</tr>
					<?		foreach($t_data['child'] as $t_kodeunit => $row) {
								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								
								$rowc = $a_datac[$t_kodeunit];
								
								$t_jumlahmhs = (int)$rowc['jumlahmhs'];
								$t_jumlahmhsbayar = (int)$rowc['jumlahmhsbayar'];
								$t_jumlahmhsblmbayar = (int)$rowc['jumlahmhsblmbayar'];
								$t_jumlahbayar = (float)$a_h2h[$t_kodeunit]['bayar']+(float)$a_h2h[$t_kodeunit]['belum'];
								
								$t_countmhsbayar = (float)$a_h2h[$t_kodeunit]['bayar'];
								$t_countmhsbelumbayar = (float)$a_h2h[$t_kodeunit]['belum'];
								 
								$t_tjumlahmhs += $t_jumlahmhs;
								$t_tjumlahmhsbayar += $t_jumlahmhsbayar;
								$t_tjumlahmhsblmbayar += $t_jumlahmhsblmbayar;
								$t_tjumlahbayar += $t_jumlahbayar;
								$t_tcountmhsbayar+=$t_countmhsbayar;
								$t_tcountmhsblmbayar+=$t_countmhsbelumbayar;
								
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['namaunit'] ?></td>
						<td align="right"><?= CStr::formatNumber($t_jumlahmhs) ?></td>
						<td align="right"><?= CStr::formatNumber($t_jumlahmhsbayar) ?></td>
						<td align="right"><?= CStr::formatNumber($t_jumlahmhsblmbayar) ?></td>
					</tr>
					<?		} ?>
					<tr valign="top" class="YellowBG">
						<td><strong>Total <?= $t_data['namaunit'] ?></strong></td>
						<td align="right"><?= CStr::formatNumber($t_tjumlahmhs) ?></td>
						<td align="right"><?= CStr::formatNumber($t_tjumlahmhsbayar) ?></td>
						<td align="right"><?= CStr::formatNumber($t_tjumlahmhsblmbayar) ?></td>
						
					</tr>
					<?		$t_ttjumlahmhs += $t_tjumlahmhs;
							$t_ttjumlahmhsbayar += $t_tjumlahmhsbayar;
							$t_ttjumlahmhsblmbayar += $t_tjumlahmhsblmbayar;
							$t_ttjumlahbayar += $t_tjumlahbayar;
							$t_ttcountmhsbayar += $t_tcountmhsbayar;
							$t_ttcountmhsblmbayar +=$t_tcountmhsblmbayar;
						}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						else { ?>
					<tr valign="top" class="YellowBG">
						<td><strong>Total</strong></td>
						<td align="right"><?= CStr::formatNumber($t_ttjumlahmhs) ?></td>
						<td align="right"><?= CStr::formatNumber($t_ttjumlahmhsbayar) ?></td>
						<td align="right"><?= CStr::formatNumber($t_ttjumlahmhsblmbayar) ?></td>
						</tr>
					<?	} ?>
				</table>
			</form>
		</div>
	</div>
</div>

</body>
</html>