<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporankelas'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Isi Nilai';
	$p_tbwidth = 800;
	

	
	$a_data = mLaporanKelas::statusPenilaian($conn,$r_unit,$r_periode);
	$a_prodi = mCombo::jurusan($conn);
	$namaunit=mUnit::getNamaUnit($conn,$r_unit);
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
	<?=strtoupper('Laporan status penilaian '.$namaunit.' '.Akademik::getNamaPeriode($r_periode))?><br><br>
	<table class="tb_data" width="<?= $p_tbwidth ?>">
		<thead>
		<tr>
			<th colspan=""></th>
		</tr>
		<tr>
			<th>No</th>
			<th>Semester</th>
			<th>Prodi</th>
			<th>Kode MK</th>
			<th>Kurikulum</th>
			<th>Nama Matakuliah</th>
			<th>Kelas</th>
			<th>SKS</th>
			<th>Dosen Pengajar</th>
			<th>Nilai</th>
			<th>Dipakai</th>
			<th>Sahkan</th>
			<th>Kunci</th>
		</tr>
		</thead>
		<tbody>
		<?	
			$i = 0;
			foreach($a_data as $row) {	
			$i++;
		?>
		<tr>
			<td align="center"><?=$i?></td>
			<td><?=Akademik::getNamaPeriode($row['periode'],true)?></td>
			<td><?=$a_prodi[$row['kodeunit']]?></td>
			<td align="center"><?=$row['kodemk']?></td>
			<td align="center"><?=$row['thnkurikulum']?></td>
			<td><?=$row['namamk']?></td>
			<td align="center"><?=$row['kelasmk']?></td>
			<td align="center"><?=$row['sks']?></td>
			<td><?=$row['namapengajar']?></td>
<!--			
			<td align="center"><?= ($row['dinilai']>=$row['jum_mhs']) ? '<img src="images/check.png">':'' ?></td>
			<td align="center"><?= ($row['dipakai']>=$row['jum_mhs']) ? '<img src="images/check.png">' : '' ?></td>
                        <td align="center"><?= ($row['nilaimasuk']=='-1') ? '<img src="images/check.png">':'' ?></td>
                        <td align="center"><?= empty($row['kuncinilai']) ? '' : '<img src="images/check.png">' ?></td>



-->
			<td align="center"><?= ($row['dinilai']>=$row['jum_mhs']) ? 'X':'' ?></td>
			<td align="center"><?= ($row['dipakai']>=$row['jum_mhs']) ? 'X' : '' ?></td>
			<td align="center"><?= ($row['nilaimasuk']=='-1') ? 'X':'' ?></td>
			<td align="center"><?= empty($row['kuncinilai']) ? '' : 'X' ?></td>
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
