<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('lappembayaran'));
	require_once(Route::getModelPath('bank'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('unit'));
	
	// variabel request
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$r_sistem = CStr::removeSpecial($_REQUEST['sistemkuliah']);
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_angkatan = CStr::removeSpecial($_REQUEST['angkatan']);
	$r_statusmhs = CStr::removeSpecial($_REQUEST['statusmhs']);
	$r_bank = CStr::removeSpecial($_REQUEST['bank']);
	$r_tgltagihan = CStr::removeSpecial($_REQUEST['tgltagihan']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$r_namaunit = mUnit::getNamaUnit($conn,$r_unit);
	$r_tgltagihan = CStr::formatDate($r_tgltagihan);
	
	if(!empty($r_sistem)) {
		$r_namasistem = current(mAkademik::getArraysistemkuliah($conn,$r_sistem));
		$r_namasistem = $r_namasistem['namasistem'].' '.$r_namasistem['tipeprogram'];
	}
	else
		$r_namasistem = 'Semua';
	
	if(!empty($r_statusmhs)) {
		$a_statusmhs = mAkademik::getArrayStatusMhs($conn);
		$r_namastatus = $a_statusmhs[$r_statusmhs];
	}
	else
		$r_namastatus = 'Semua';
	
	if(!empty($r_bank)) {
		$a_bank = mBank::arrQuery($conn,true);
		$r_namabank = $a_bank[$r_bank];
	}
	else
		$r_namabank = 'Semua';
	
	// properti halaman
	$p_title = 'Laporan Pembayaran - Mahasiswa Lunas';
	$p_tbwidth = 800;
	$p_namafile = 'laporan_pembayaran_lunas';
	
	Page::setHeaderFormat($r_format,$p_namafile);
	
	// ambil data
	$data = mLapPembayaran::getListLunas($conn,$r_periode,$r_sistem,$r_unit,$r_angkatan,$r_statusmhs,$r_bank,$r_tgltagihan);
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
			<tr>
            	<td><strong>Bank</strong></td>
            	<td><strong>:</strong></td>
                <td><?= $r_namabank ?></td>
				<td><strong>Status Mhs</strong></td>
            	<td><strong>:</strong></td>
                <td><?= $r_namastatus ?></td>
            </tr>
			<?php if(!empty($r_tgltagihan)) { ?>
			<tr>
            	<td><strong>Tagihan s.d.</strong></td>
            	<td><strong>:</strong></td>
                <td colspan="4"><?= CStr::formatDateInd($r_tgltagihan) ?></td>
            </tr>
			<?php } ?>
		</table>
		<table width="<?= $p_tbwidth ?>" cellpadding="3" border="1" style="border-collapse:collapse;">
			<tr>
            	<th>&nbsp;</th>
            	<th>FAKULTAS</th>
            	<th>LUNAS</th>
            	<th>TOTAL</th>
            	<th>HUTANG</th>
            	<th>%</th>
            </tr>
            <?php
				$i = 0;
				$t_lunas = $t_total = $t_hutang = 0;
				foreach($data as $row) {
					$i++;
					
					// total
					$t_selisih = $row['total']-$row['lunas'];
					
					$t_lunas += $row['lunas'];
					$t_total += $row['total'];
					$t_hutang += $t_selisih;
					
					// rasio
					if(empty($row['total']))
						$t_ratio = 100;
					else
						$t_ratio = round(($t_selisih*100)/$row['total']);
			?>
            <tr>
            	<td align="right"><?= $i ?></td>
            	<td><?= $row['namaunit'] ?></td>
            	<td align="right"><?= CStr::formatNumberRep($r_format,$row['lunas'],0,false,true) ?></td>
				<td align="right"><?= CStr::formatNumberRep($r_format,$row['total'],0,false,true) ?></td>
				<td align="right"><?= CStr::formatNumberRep($r_format,$t_selisih,0,false,true) ?></td>
				<td align="right"><?= $t_ratio ?> %</td>
            </tr>
            <?php
				}
				
				// rasio
				if(empty($t_total))
					$t_ratio = 100;
				else
					$t_ratio = round(($t_hutang*100)/$t_total);
			?>
			<tr>
            	<th>&nbsp;</th>
            	<th>TOTAL UNIVERSITAS</th>
            	<th align="right"><?= CStr::formatNumberRep($r_format,$t_lunas,0,false,true) ?></th>
				<th align="right"><?= CStr::formatNumberRep($r_format,$t_total,0,false,true) ?></th>
				<th align="right"><?= CStr::formatNumberRep($r_format,$t_hutang,0,false,true) ?></th>
				<th align="right"><?= $t_ratio ?> %</th>
            </tr>
		</table>
	</div>
	</body>
</html>
