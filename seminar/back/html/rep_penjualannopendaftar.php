<?
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('setting'));

	Modul::getFileAuth();

	//parameter
	$r_periode  	= $_POST['periode'];
	$r_sistemkuliah  = $_POST['sistemkuliah'];
	$r_tglawal  	= $_POST['tglawal'] ? $_POST['tglawal'] : date('d-m-Y') ;
	$r_tglakhir  	= $_POST['tglakhir'] ? $_POST['tglakhir'] : date('d-m-Y');

	//model
	$p_model=mLaporan;
	$p_title='Laporan Penjualan NoPendaftar (Harian)';
	$p_tbwidth = 950;
	
	$data=$p_model::getDataPenjualannopendaftar($conn,$r_periode,$r_sistemkuliah, $r_tglawal,$r_tglakhir);
	$namaperiode = Pendaftaran::getNamaPeriode($r_periode);
	$arrSistemkuliah = mCombo::sistemkuliah($conn);
	$p_namafile = 'penjualannopendaftar'.$periode;
	Page::setHeaderFormat($r_format,$p_namafile);
?>

<html>
	<head>
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	</head>
	
	<body onLoad="window.print();">
		<div class="book">
			<div class="page">
				<div class="subpage">
				<!-- CONTENT HERE -->
				</div>    
			</div>
		</div>
	</body>
</html>



<!DOCTYPE html>
<html>
<head>
	<title>Cetak Laporan Penjualan No Pendaftar (Harian)</title>   
	<link rel="icon" type="image/x-icon" href="image/favicon.png">
	<style>
		body{font-size:12pt;  font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;}
		th {background: #015593; color:#FFF}
	</style>
</head>
	<body style="background:white" onLoad="window.print();">		  
	<center>
		<div style="width:<?=$p_tbwidth.'px'?>;">
		LAPORAN PENJUALAN PENDAFTAR (HARIAN)
		<table width="100%">
			<tr>
				<td width="100">Basis</td>
				<td width="10">:</td>
				<td><?= $arrSistemkuliah[$r_sistemkuliah]?></td>
			</tr>
			<tr>
				<td width="100">Semester</td>
				<td width="10">:</td>
				<td><?= $namaperiode?></td>
			</tr>
			<tr>
				<td>Tanggal</td>
				<td>:</td>
				<td><?= date::indoDate(date::indoDateYmd($r_tglawal))?> s/d <?= date::indoDate(date::indoDateYmd($r_tglakhir))?> </td>
			</tr>
		</table>
		<br>
		<table border="1" width="100%" style="border-collapse:collapse;" cellpadding="3">
			<tr>
				<th>No</th>
				<th>Nopendaftar</th>
				<th>Nama</th>
				<th>Basis</th>
				<th>Jumlah (Rp)</th>
			</tr>
			<? $no=0; $t_total = 0; foreach ($data as $row){ $no++;?>
			<tr>
				<td><?= $no?></td>
				<td><?= $row['nopendaftar']?></td>
				<td><?= $row['nama']?></td>
				<td><?= $arrSistemkuliah[$row['sistemkuliah']].'('.$row['sistemkuliah'].')' ?></td>
				<td align="right"><?= cStr::formatNumber($row['nominaltarif'])?></td>
			</tr>
			<?	
			$t_total +=$row['nominaltarif'];
			}?>
			<tr>
			<td colspan="4" align="center"><b>Total</b></td>
			<td align="right"><b><?= 'Rp. '.cStr::formatNumber($t_total)?></b></td>
			</tr>
		</table>
		</div>
	</center>
	</body>
</html>
