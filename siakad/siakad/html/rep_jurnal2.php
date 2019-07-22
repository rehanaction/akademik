<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=false;
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
		list($r_kurikulum,$r_kodemk,$r_kodeunit,$r_periode,$r_kelasmk,$r_jeniskuliah,$r_kelompok) = explode('|',$r_key);
	
	$r_format = $_REQUEST['format'];
	
	// pengecekan unit
	//$r_kodeunit = mUnit::passUnit($conn,$r_kodeunit);
	
	// properti halaman
	$p_title = 'Rencana Pembelajaran Semester';
	$p_tbwidth = 1200;
	$p_maxrow = 7;
	$p_maxday = 14;
	$p_namafile = 'RPS_'.$r_kodeunit.'_'.$r_periode;
	
	$a_data = mLaporanKelas::getJurnal($conn,$r_kodeunit,$r_periode,$r_kurikulum,$r_kodemk,$r_kelasmk,$r_jeniskuliah,$r_kelompok);
	
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
		.div_subhead { font-size: 13px; margin-bottom: 5px }
		.div_head { font-size: 14px; font-weight: bold; text-decoration: underline }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 13px; padding: 1px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Arial; height: 50px; padding:5px }
	</style>
</head>
<body>
<div align="center">
<?php
	$m = count($a_data);
	for($c=0;$c<$m;$c++) {
		$row = $a_data[$c];
		
		$r = 0;
		$n = count($row['jurnal']);
		
		for($i=0;$i<$n;$i++) {
			$rowj = $row['jurnal'][$i];
			
			// jika baris pertama
			if($r == 0) {
				include('inc_headerlap.php');
?>
<div class="div_head">JURNAL KEGIATAN PROSES PEMBELAJARAN (JKPP)</div>
<div class="div_subhead"><?= Akademik::getNamaPeriode($r_periode) ?></div>
<table class="tb_head" width="<?= $p_tbwidth-300 ?>">
	<tr valign="top">
		<td width="70"><strong>Mata kuliah</strong></td>
		<td align="center">:</td>
		<td colspan="4"><strong><?= $row['namamk'] ?> (<?= $row['sks'] ?> sks)</strong></td>
		<td width="60"><strong>Ruang</strong></td>
		<td align="center" width="10">:</td>
		<td><?= $row['koderuang'] ?></td>
	</tr>
	<tr valign="top">
		<td width="70"><strong>Kode</strong></td>
		<td align="center" width="10">:</td>
		<td width="200"><?= $row['kodemk'] ?></td>
		<td width="30"><strong>Kelas</strong></td>
		<td align="center" width="10">:</td>
		<td width="150"><?= $row['kelasmk'] ?></td>
		<td rowspan="2"><strong>Dosen</strong></td>
		<td rowspan="2" align="center">:</td>
		<td rowspan="2"><?= $row['pengajar'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Prodi</strong></td>
		<td align="center">:</td>
		<td><?= $row['namaunit'] ?></td>
		<td><strong>Semester</strong></td>
		<td align="center">:</td>
		<td><?= $row['semmk'] ?></td>
	</tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th rowspan="2" width="30">NO</th>
		<th colspan="3">Rencana perkuliahan</th>
		<th colspan="3">Pelaksanaan perkuliahan</th>
		<th colspan="2">Keaktifan Mahasiswa</th>
		<th colspan="2">Paraf</th>
	</tr>
	<tr>
		<th width="60">Hari/tgl</th>
		<th width="60">Jam ke</th>
		<th>Materi / Kegiatan</th>
		<th width="60">Hari/tgl</th>
		<th width="60">Jam ke</th>
		<th>Materi / Kegiatan</th>
		<th>Kesan Dosen</th>
		<th width="60">Jml Mhs</th>
		<th width="60">Dosen</th>
		<th width="60">Mhs</th>
	</tr>
	<tr>
<?php
			} // selesai jika baris pertama
			
			$no = $i+1;
?>
	<tr>
		<td align="center"><?= $no ?></td>
		
		<td align="center">
			<?= $rowj['hari'] ?>
			<div style="height:5px"></div>
			<?= $rowj['tanggal'] ?>
		</td>
		<td align="center"><?= $rowj['jam'] ?></td>
		<td><?= $rowj['topik'] ?></td>
		<td align="center">
			<?= $rowj['noharirealisasi'] ?>
			<div style="height:5px"></div>
			<?= $rowj['tanggalrealisasi'] ?>
		</td>
		<td align="center"><?= $rowj['jamrealisasi'] ?></td>
		<td><?= $rowj['keterangan'] ?></td>
		<td><?= $rowj['kesandosen'] ?></td>
		<td align="center"><?= $rowj['jumlahpeserta'] ?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
<?php
			$r++;
			
			// cek jumlah baris
			if($r == $p_maxrow or $i == $n-1) {
				$r = 0;
?>
</table>
<?php
				//if($c < $m-1) {
?>
<div style="page-break-after:always"></div>
<?php				
				//}
			}
		}
	}
?>
</table>
</div>
</body>
</html>
