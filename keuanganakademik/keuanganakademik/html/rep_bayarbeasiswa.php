<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('lappembayaran'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('unit'));
	
	// variabel request
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$r_sistem = CStr::removeSpecial($_REQUEST['sistemkuliah']);
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_angkatan = CStr::removeSpecial($_REQUEST['angkatan']);
	$r_statusmhs = CStr::removeSpecial($_REQUEST['statusmhs']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$r_namaunit = mUnit::getNamaUnit($conn,$r_unit);
	if(!empty($r_sistem)) {
		$r_namasistem = current(mAkademik::getArraysistemkuliah($conn,$r_sistem));
		$r_namasistem = $r_namasistem['namasistem'].' '.$r_namasistem['tipeprogram'];
	}
	else
		$r_namasistem = 'Semua';
	
	// properti halaman
	$p_title = 'Laporan Pembayaran - Beasiswa';
	$p_tbwidth = 800;
	$p_namafile = 'laporan_pembayaran_beasiswa';
	
	Page::setHeaderFormat($r_format,$p_namafile);
	
	// ambil data
	$data = mLapPembayaran::getListBeasiswa($conn,$r_periode,$r_sistem,$r_unit,$r_angkatan,$r_statusmhs);
?>
<html>
	<head>
		<title><?= $p_title ?></title>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<link rel="icon" type="image/x-icon" href="images/favicon.png">
		<link href="style/stylerep.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	<div align="center">
		<table width="<?=$p_tbwidth?>" >
			<tr>
				<td colspan="6">
				<?php require_once('inc_headrep.php') ?>
				</td>
			</tr>
            <tr>
            	<td width="10%"><strong>Periode</strong></td>
            	<td width="1%"><strong>:</strong></td>
                <td width="39%"><?= $r_periode ?></td>
				<td width="10%"><strong>Basis</strong></td>
            	<td width="1%"><strong>:</strong></td>
                <td><?= $r_namasistem ?></td>
            </tr>
			<tr>
            	<td><strong>Unit</strong></td>
            	<td><strong>:</strong></td>
                <td><?= $r_namaunit ?></td>
				<td><strong>Angkatan</strong></td>
            	<td><strong>:</strong></td>
                <td><?= empty($r_angkatan) ? 'Semua' : $r_angkatan ?></td>
            </tr>
		</table>
		<table width="<?= $p_tbwidth ?>" cellpadding="3" border="1" style="border-collapse:collapse;">
			<tr>
            	<th>NO</th>
            	<th>SMT</th>
            	<th>NIM</th>
            	<th>KD</th>
            	<th>NAMA</th>
            	<th>POT</th>
            	<th>KETERANGAN</th>
            </tr>
            <?php
				$i = 0;
				foreach($data as $row) {
					$i++;
			?>
            <tr>
            	<td align="right"><?= $i ?></td>
            	<td><?= $r_periode ?></td>
            	<td><?= $row['nim'] ?></td>
            	<td><?= $row['kodeunit'] ?></td>
				<td><?= $row['nama'] ?></td>
            	<td align="right"><?= CStr::FormatNumberRep($r_format,$row['potongan']) ?></td>
            	<td><?= $row['keteranganbeasiswa'] ?></td>
            </tr>
            <?php } ?>
		</table>
	</div>
	</body>
</html>
