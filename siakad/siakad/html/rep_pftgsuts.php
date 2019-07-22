<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporankelas'));
	require_once(Route::getModelPath('unit'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(empty($r_key)) {
		$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
		$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	}
	else
		list($r_kurikulum,$r_kodemk,$r_kodeunit,$r_periode,$r_kelasmk) = explode('|',$r_key);
	
	$r_format = $_REQUEST['format'];
	
	// pengecekan unit
	$r_kodeunit = mUnit::passUnit($conn,$r_kodeunit);
	
	// properti halaman
	$p_title = 'Form Absensi';
	$p_tbwidth = 700;
	$p_maxrow = 38;
	$p_footrow = 5;
	$p_namafile = 'pf_tgs_uts_'.$r_kodeunit.'_'.$r_periode;
	
	$a_data = mLaporanKelas::getPFTgsUTS($conn,$r_kodeunit,$r_periode,$r_kurikulum,$r_kodemk,$r_kelasmk);
	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		.tab_header { border-bottom: 1px solid black; margin-bottom: 5px }
		.div_headeritem { float: left }
		.div_preheader, .div_header { font-family: "Times New Roman" }
		.div_preheader { font-size: 10px; font-weight: bold }
		.div_header { font-size: 12px }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head td { font-size: 10px }
		.div_head, .div_subhead { font-size: 12px; font-weight: bold }
		.div_head { text-decoration: underline }
		.div_subhead { margin-bottom: 5px }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 10px; padding: 1px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Tahoma, Arial }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 11px }
	</style>
</head>
<body>
<div align="center">
<?php
	$m = count($a_data);
	for($c=0;$c<$m;$c++) {
		$row = $a_data[$c];
		
		$r = 0;
		$n = count($row['peserta']);
		
		for($i=0;$i<$n;$i++) {
			$rowp = $row['peserta'][$i];
			
			// jika baris pertama
			if($r == 0) {
				include('inc_headerlap.php');
?>
<div class="div_head">NILAI PERFORMANCE-TUGAS-UTS</div>
<div class="div_subhead"><?= Akademik::getNamaPeriode($r_periode) ?></div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr valign="top">
		<td width="65"><strong>Matakuliah</strong></td>
		<td align="center" width="10">:</td>
		<td width="350"><strong><?= $row['namamk'] ?> (<?= $row['sks'] ?> sks)</strong></td>
		<td width="30"><strong>Kode</strong></td>
		<td align="center" width="10">:</td>
		<td width="100"><?= $row['kodemk'] ?></td>
		<td width="40"><strong>Kelas</strong></td>
		<td><strong><?= $row['kelasmk'] ?></strong></td>
	</tr>
	<tr valign="top">
		<td><strong>Dosen</strong></td>
		<td align="center">:</td>
		<td><?= $row['pengajar'] ?></td>
		<td><strong>Hari/Jam</strong></td>
		<td align="center">:</td>
		<td colspan="3"><?= Date::indoDay($row['nohari']) ?> / <?= CStr::formatJam($row['jammulai']) ?> - <?= CStr::formatJam($row['jamselesai']) ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Prodi</strong></td>
		<td align="center">:</td>
		<td><?= $row['namaunit'] ?></td>
		<td><strong>Ruang</strong></td>
		<td align="center">:</td>
		<td colspan="3"><strong><?= $row['koderuang'] ?></strong></td>
	</tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20" rowspan="2">No.</th>
		<th width="60" rowspan="2">NIM</th>
		<th rowspan="2">NAMA MAHASISWA</th>
		<th colspan="3">PERFORMANCE</th>
		<th colspan="3">TUGAS</th>
		<th width="50" rowspan="2">UTS</th>
	</tr>
	<tr>
		<th width="50">ABSENSI</th>
		<th width="50">PARTISIPASI<br>DISKUSI</th>
		<th width="50">AKHLAQ</th>
		<th width="40">1</th>
		<th width="40">2</th>
		<th width="40">3</th>
	</tr>
<?php
			} // selesai jika baris pertama
			
			$no = $i+1;
?>
	<tr>
		<td align="center"><?= $no ?></td>
		<td align="center"><?= $rowp['nim'] ?></td>
		<td><?= $rowp['nama'] ?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
<?php
			$r++;
			
			// cek jumlah baris
			if($r == $p_maxrow)
				$r = 0;
			
			// cek footer
			if($i == $n-1) {
				$r = 0;
				if($r > $p_maxrow-$p_footrow) {
					// mundur untuk mencetak footer
					$i--;
				}
				else {
?>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td width="65%"><strong>Mengetahui,</strong></td>
		<td align="center"><strong>Jakarta, <?= str_repeat('.',40) ?></strong></td>
	</tr>
	<tr>
		<td><strong>Ketua Program Studi</strong></td>
		<td align="center"><strong>Dosen Pembina / Pengawas,</strong></td>
	</tr>
	<tr height="30">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td><strong><u><?= $row['ketua'] ?></u></strong></td>
		<td align="center"><strong>(<?= str_repeat('.',50) ?>)</strong></td>
	</tr>
	<tr>
		<td colspan="2">NIP. <?= $row['nipketua'] ?></td>
	</tr>
<?php
				}
			}
			
			if($r == 0) {
?>
</table>
<?php
				if($c < $m-1) {
?>
<div style="page-break-after:always"></div>
<?php				
				}
			}
		}
	}
?>
</div>
</body>
</html>