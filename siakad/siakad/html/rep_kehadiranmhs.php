<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporankelas'));
	require_once(Route::getModelPath('sistemkuliah'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_sistemkuliah = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	$r_jeniskuliah = Modul::setRequest($_POST['jeniskuliah'],'JENISKULIAH');
	$r_nim = Modul::setRequest($_POST['username'],'USERNAME');
	$r_starttgl = Modul::setRequest($_POST['starttgl'],'STARTTGL');
	$r_endtgl = Modul::setRequest($_POST['endtgl'],'ENDTGL');
	
	// tambahan
	$r_starttgl=date('Y-m-d',strtotime($r_starttgl));
	$r_endtgl=date('Y-m-d',strtotime($r_endtgl));
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Jumlah Kehadiran mahasiswa';
	$p_tbwidth = 1000;
	
	$a_jeniskuliah=mKuliah::jenisKuliah($conn);
	$a_data =  mLaporanKelas::getAbsensiMhs($conn,$r_unit,$r_periode,$r_jeniskuliah,$r_sistemkuliah,$r_starttgl,$r_endtgl,$r_nim);
	$a_prodi = mCombo::jurusan($conn);
	$a_sistemkuliah=mSistemkuliah::getArray($conn,true);
	
	$a_datadosen =  mLaporanKelas::getAbsensiDosen($conn,$r_unit,$r_periode,$r_jeniskuliah,$r_sistemkuliah,$r_starttgl,$r_endtgl);
	
	$a_absendosen=array();
	foreach($a_datadosen as $data){
		$idx=$data['thnkurikulum'].$data['kodeunit'].$data['kodemk'].$data['kelasmk'];
		$a_absendosen[$idx]+=$data['jumlahrealisasi'];
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
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head { border-bottom: 1px solid black }
		.tb_head td { font-size: 10px }
		.tb_head .mark { font-size: 11px }
		.div_head { font-size: 16px; text-decoration: underline }
		.div_subhead { font-size: 14px; margin-bottom: 5px }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; padding: 1px }
		.tb_data th { background-color: #CFC; font-size: 11px }
		.tb_data td { font-size: 10px }
		.tb_data .noborder th { border-left: none; border-right: none }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 11px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body>
<div align="center">
	<?=strtoupper('Laporan Jumlah Kehadiran mahasiswa '.$namaunit.' '.Akademik::getNamaPeriode($r_periode))?><br>
	<?=strtoupper('Basis '.$a_sistemkuliah[$r_sistemkuliah].' Jenis '.$a_jeniskuliah[$r_jeniskuliah].' Tanggal '.CStr::formatDateInd($r_starttgl).' - '.CStr::formatDateInd($r_endtgl))?>
	<table class="tb_data" width="<?= $p_tbwidth ?>">
		<thead>
		<tr>
			<th width="5">No</th>
			<th width="90">Semester</th>
			<th width="100">Prodi</th>
			<th width="50">Nim</th>
			<th>Nama Mahasiswa</th>
			<th width="30">Kode MK</th>
			<th width="200">Nama Matakuliah</th>
			<th width="30">Kelas</th>
			<th width="5">Hadir</th>
			<th width="5">Jlm Hadir Dosen</th>
		</tr>
		</thead>
		<tbody>
		<?	
			$i = 0;
			foreach($a_data as $row) {
				$idx=$row['thnkurikulum'].$row['kodeunit'].$row['kodemk'].$row['kelasmk'];
			$i++;
			$a_jadwal=array();
			if(!empty($row['namahari']))
				$a_jadwal[] = $row['namahari'].', '.CStr::formatJam($row['jammulai']).' - '.CStr::formatJam($row['jamselesai'])
		?>
		<tr>
			<td align="center"><?=$i?></td>
			<td><?=Akademik::getNamaPeriode($row['periode'],true)?></td>
			<td><?=$a_prodi[$row['kodeunit']]?></td>
			<td align="center"><?=$row['nim']?></td>
			<td><?=$row['nama']?></td>
			<td align="center"><?=$row['kodemk']?></td>
			<td ><?=$row['namamk']?></td>
			<td align="center"><?=$row['kelasmk']?></td>
			<td align="center"><?=$row['jumlah']?></td>
			<td align="center"><?=(int)$a_absendosen[$idx]?></td>
		</tr>
		
		<?	}
			if($i == 0) {
		?>
		<tr>
			<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
		</tr>
		<?	} ?>
		</tbody>
	</table>				
</div>
</body>
</html>
