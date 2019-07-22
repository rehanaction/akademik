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
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	
	$r_format = $_REQUEST['format'];
	
	// pengecekan unit
	//$r_kodeunit = mUnit::passUnit($conn,$r_kodeunit);
	
	// properti halaman
	$p_title = 'Jurnal (JKPP) Yang Belum Disi Absen';
	$p_tbwidth = 900;
	$p_maxrow = 14;
	$p_namafile = 'jurnalx_'.$r_kodeunit.'_'.$r_periode;
	
	$a_data = mLaporanKelas::getJurnalBlmAbsen($conn,$r_kodeunit,$r_periode);
	
	
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
	$r = 0;
	for($c=0;$c<$m;$c++) {
		$row = $a_data[$c];
		
		
			
			// jika baris pertama
			if($r == 0) {
				include('inc_headerlap.php');
?>
<div class="div_head"><?php echo $p_title?></div>
<div class="div_subhead"><?= Akademik::getNamaPeriode($r_periode) ?></div>

<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No</th>
		<th width="60">Periode</th>
		<th width="80">Tgl. Kuliah</th>
		<th width="50">Perkuliahanke</th>
		<th width="70">Kodeunit</th>
		<th width="50">Kelas MK</th>
		<th width="70">Kode MK</th>
		<th>Nama MK</th>
		<th width="80">Kode Ruang</th>
		<th width="70">No Dosen</th>
		<th width="100">Nama Dosen</th>
		<th width="60">Jenis Kuliah</th>
	</tr>
	<tr>
<?php
			} // selesai jika baris pertama
			
			$no = $r+1;
?>
	<tr>
		<td align="center"><?= $no ?></td>
		<td align="center"><?= $row['periode'] ?></td>
		<td><?= date('d/m/Y',strtotime($row['tglkuliahrealisasi'])) ?></td>
		<td align="center"><?= $row['perkuliahanke'] ?></td>
		<td align="center"><?= $row['kodeunit'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['koderuang'] ?></td>
		<td><?= $row['nipdosenrealisasi'] ?></td>
		<td><?= $row['namadosen'] ?></td>
		<td align="center"> <?= $row['jeniskuliah'] ?></td>
	</tr>
<?php
			$r++;
			
			// cek jumlah baris
			if($r == $p_maxrow) {
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
?>
</table>
</div>
</body>
</html>
