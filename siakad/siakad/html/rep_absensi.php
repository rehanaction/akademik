<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
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
	$p_title = 'Presensi Kuliah';
	$p_tbwidth = 700;
	$p_maxrow = 46;
	$p_maxday = 16;
	$p_namafile = 'absensi_'.$r_kodeunit.'_'.$r_periode;
	
	$a_data =  mLaporanKelas::getAbsensi($conn,$r_kodeunit,$r_periode,$r_kurikulum,$r_kodemk,$r_kelasmk);
	
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
		.tb_head td, .div_head { font-size: 12px }
		.div_subhead { font-size: 11px; margin-bottom: 5px }
		.div_head { text-decoration: underline }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 10px; padding: 1px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Tahoma, Arial }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }
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
<div class="div_head">PRESENSI KULIAH</div>
<div class="div_subhead"><?= Akademik::getNamaPeriode($r_periode) ?></div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr valign="top">
		<td width="70"><strong>Mata kuliah</strong></td>
		<td align="center">:</td>
		<td colspan="4"><strong><?= $row['namamk'] ?> (<?= $row['sks'] ?> sks)</strong></td>
		<td rowspan="2" width="60"><strong>Dosen</strong></td>
		<td rowspan="2" align="center" width="10">:</td>
		<td rowspan="2"><strong><?= $row['pengajar'] ?></strong></td>
	</tr>
	<tr valign="top">
		<td width="70"><strong>Kode</strong></td>
		<td align="center" width="10">:</td>
		<td width="130"><?= $row['kodemk'] ?></td>
		<td width="30"><strong>Kelas</strong></td>
		<td align="center" width="10">:</td>
		<td width="120"><?= $row['kelasmk'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Prodi</strong></td>
		<td align="center">:</td>
		<td colspan="4"><?= $row['namaunit'] ?></td>
		<td><strong>Hari/Jam 1</strong></td>
		<td align="center">:</td>
		<td><?= $row['namahari'] ?> / <?= CStr::formatJam($row['jammulai'],'.') ?>-<?= CStr::formatJam($row['jamselesai'],'.') ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Ruang</strong></td>
		<td align="center">:</td>
		<td><?= $row['koderuang'] ?></td>
		<td><strong>Semester</strong></td>
		<td align="center">:</td>
		<td><?= $row['semmk'] ?></td>
		<td><strong>Hari/Jam 2</strong></td>
		<td align="center">:</td>
		<td><?= $row['namahari2'] ?> / <?= CStr::formatJam($row['jammulai2'],'.') ?>-<?= CStr::formatJam($row['jamselesai2'],'.') ?></td>
	</tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th rowspan="2" width="15">No</th>
		<th rowspan="2" width="55">N I M</th>
		<th>Tanggal</th>
		<? for($j=1;$j<=$p_maxday;$j++) { ?>
		<th width="20">&nbsp;</th>
		<? } ?>
	</tr>
	<tr>
		<th>Nama Mahasiswa</th>
		<? for($j=1;$j<=$p_maxday;$j++) { ?>
		<th><?= $j ?></th>
		<? } ?>
	</tr>
	<tr>
<?php
			} // selesai jika baris pertama
			
			$no = $i+1;
?>
	<tr>
		<td align="right"><?= $no ?></td>
		<td align="center"><?= $rowp['nim'] ?></td>
		<td><?= $rowp['nama'] ?></td>
		<? for($j=1;$j<=$p_maxday;$j++) { ?>
		<td>&nbsp;</td>
		<? } ?>
	</tr>
<?php
			$r++;
			
			// cek jumlah baris
			if($r == $p_maxrow)
				$r = 0;
			
			// cek footer
			if($i == $n-1) {
				$r = 0;
?>
	<tr>
		<th colspan="3">Tanda Tangan Dosen / Asisten</th>
		<? for($j=1;$j<=$p_maxday;$j++) { ?>
		<th>&nbsp;</th>
		<? } ?>
	</tr>
<?php
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
</table>
</div>
</body>
</html>
