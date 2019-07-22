<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporanmhs'));
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_angkatan = (int)$_REQUEST['angkatan'];
	$r_format = $_REQUEST['format'];
	
	if(Akademik::isMhs())
		$r_npm = Modul::getUserName();
	else
		$r_npm = CStr::removeSpecial($_REQUEST['npm']);
	
	// properti halaman
	$p_title = 'Daftar Nilai Mahasiswa';
	$p_tbwidth = 720;
	$p_maxrow = 48;	
	
	if(empty($r_npm)) {
		$p_namafile = 'nilai_'.$r_kodeunit.'_'.$r_angkatan;
		$a_data = mLaporanMhs::getNilaiUnit($conn,$r_kodeunit,$r_angkatan);
	}
	else {
		$p_namafile = 'nilai_'.$r_npm;
		$a_data = mLaporanMhs::getNilai($conn,$r_npm);
	}
	
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
		
		.tb_head td, .div_head { font-family: "Times New Roman" }
		.tb_head td { font-size: 12px }
		.div_head { font-size: 16px; font-weight: bold; margin-bottom: 5px; text-decoration: underline }
		
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; padding: 2px }
		.tb_data th { background-color: #CFC; font-size: 12px }
		.tb_data td { font-size: 11px }
		.tb_data .noborder th { border-left: none; border-right: none }
	</style>
</head>
<body>
<div align="center">
<?php
	$m = count($a_data);
	for($c=0;$c<$m;$c++) {
		$row = $a_data[$c];
		
		$r = 0;
		$n = count($row['nilai']);
		
		$t_tsks = 0;
		$t_tnsks = 0;
		
		for($i=0;$i<$n;$i++) {
			$rowk = $row['nilai'][$i];
			
			$t_nangka = $rowk['nangka'];
			$t_sks = $rowk['sks'];
			
			$t_nsks = $t_nangka*$t_sks;
			$t_tsks += $t_sks;
			$t_tnsks += $t_nsks;
			
			// jika baris pertama
			if($r == 0) {
				include('inc_headerlap.php');
?>
<div class="div_head">DAFTAR NILAI MAHASISWA</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr valign="top">
		<td width="80"><strong>Nama Mhs</strong></td>
		<td align="center" width="10">:</td>
		<td width="230"><?= $row['nama'] ?></td>
		<td width="100"><strong>Fakultas / Jurusan</td>
		<td align="center" width="10">:</td>
		<td><?= $row['fakultas'] ?> / <?= $row['namaunit'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>N I M</strong></td>
		<td align="center">:</td>
		<td><?= $row['nim'] ?></td>
		<td><strong>Periode Daftar</strong></td>
		<td align="center">:</td>
		<td><?= $row['periodemasuk'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Jenis Kelamin</strong></td>
		<td align="center">:</td>
		<td><?= $row['sex'] ?></td>
		<td><strong>Semester Mhs</strong></td>
		<td align="center">:</td>
		<td><?= $row['semestermhs'] ?></td>
	</tr>
</table>
<div style="height:5px"></div>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="30">No.</th>
		<th width="70">Kode</th>
		<th>Nama Matakuliah</th>
		<th width="60">SKS</th>
		<th width="60">Nilai</th>
		<th width="60">N.K.</th>
	</tr>
<?php
			} // selesai jika baris pertama
			
			$no = $i+1;
?>
	<tr>
		<td align="center"><?= $no ?></td>
		<td align="center"><?= $rowk['kodemk'] ?></td>
		<td><?= $rowk['namamk'] ?></td>
		<td align="center"><?= $t_sks ?></td>
		<td align="center"><?= $rowk['nhuruf'] ?></td>
		<td align="center"><?= $t_nsks ?></td>
	</tr>
<?php
			$r++;
			
			// cek jumlah baris
			if($r == $p_maxrow or $i == $n-1) {
				$r = 0;
?>
	<tr class="noborder">
		<th colspan="3">J U M L A H</th>
		<th><?= $t_tsks ?></th>
		<th>&nbsp;</th>
		<th><?= $t_tnsks ?></th>
	</tr>
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