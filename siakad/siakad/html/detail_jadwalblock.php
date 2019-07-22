<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getModelPath('detailkelas'));
	// variabel request
//if($_SERVER['REMOTE_ADDR']=='36.85.91.184')
//$conn->debug=true;
	$r_jadwalkey = CStr::removeSpecial($_REQUEST['key']);
	if(Akademik::isMhs())
		$r_key = Modul::getUserName();
	else
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
	
	$r_nama = Akademik::getNamaMahasiswa($conn,$r_key);
	
	// properti halaman
	$p_title = 'Jadwal Perkuliahan';
	$p_tbwidth = "100%";
	$p_aktivitas = 'JADWAL';
	
	$p_model = mKRS;
	 
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	//$a_data=$p_model::getDetailJadwal($conn, $r_jadwalkey);
 $a_data = mDetailKelas::getArrayJadwal($conn,$r_jadwalkey);

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
			<?php if(Akademik::isMhs()) require_once('inc_headermhs.php') ?>
			</center>
			<br>

			<center>
				<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
					<span>
						<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
						&nbsp;Detail Jadwal Perkuliahan
					</span>
				</div>
			</center>
			<br>
			<center>
				
			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
				<tr>
					<th width="60" rowspan="2">Pertemuan Ke</th>
					<th colspan="8">Rencana Perkualiahan</th>
					<th colspan="4">Realisasi Perkulaiahan</th>
				</tr>
				<tr>
					
					<th width="60">Hari</th>
					<th width="60">Tanggal</th>
					<th width="60">Jam Mulai</th>
					<th width="60">Jam Selesai</th>
					<th width="90">Kode MK</th>
					
					<th>Jenis</th>
					<th width="40">Kelas</th>
					<th width="80">Ruang</th>
					<th width="60">Tanggal</th>
					<th width="60">Jam Mulai</th>
					<th width="60">Jam Selesai</th>
					<th width="60">Ruangan</th>
				</tr>
			<?php
				$n = count($a_data);
				
				for($i=0;$i<$n;$i++) {
				
					$row = $a_data[$i];
			?>

			<?php

					if ($j % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $j++;
			?>
				<tr valign="top" class="<?= $rowstyle ?>">
					<td align="center"><?= $row['pertemuan']?></td>
					<td align="center"><?= Date::indoDay($row['nohari'])?></td>
					<td align="center" width="100"><?= CStr::formatDateInd($row['tglpertemuan'])?></td>
					<td align="center"><?= CStr::formatJam($row['jammulai']) ?></td>
					<td align="center"><?= CStr::formatJam($row['jamselesai']) ?></td>
					<td align="center"><?= $row['kodemk'] ?></td>
					
					<td align="center"><?= $row['jeniskul'] ?></td>
					<td align="center"><?= $row['kelasmk'] ?></td>
					<td><?= $row['koderuang'] ?></td>
					
					<td align="center" width="100"><?= CStr::formatDateInd($row['tglkuliahrealisasi'])?></td>
					<td align="center"><?= CStr::formatJam($row['waktumulairealisasi']) ?></td>
					<td align="center"><?= CStr::formatJam($row['waktuselesairealisasi']) ?></td>
					<td><?= $row['koderuangrealisasi'] ?></td>
				</tr>
				
			<?
				}
			?>
			</table>
		</div>
	</div>
</div>

</body>
</html>
