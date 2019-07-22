<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_jurusan = CStr::removeSpecial($_REQUEST['jurusan']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_nim = CStr::removeSpecial($_REQUEST['nim']);
	$r_frsdisetujui = CStr::removeSpecial($_REQUEST['frsdisetujui']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	$r_periode = $r_tahun.$r_semester;

	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));

	// properti halaman
	$p_title = 'Kartu Bebas Pelanggaran';
	$p_tbwidth = 800;
	$p_namafile = 'kartu_bebas_pelanggaran_'.$r_nim;
	
	$r_namajurusan = mUnit::getNamaUnit($conn,$r_jurusan);
	$r_namakabiro = mUnit::getNamaKaBiro($conn);
	
	$a_data = mLaporan::getPelanggaran($conn,$r_jurusan,$r_periode,$r_nim);
	
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
		.div_preheader { font-size: 15px; font-weight: bold }
		.div_header { font-size: 15px }
		.div_headertext { font-size: 12px; font-style: italic }

		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head { border-bottom: 1px solid black }
		.tb_head td { font-size: 10px }
		.tb_head .mark { font-size: 11px }
		.div_head { font-size: 16px; text-decoration: underline }
		.div_subhead { font-size: 14px; margin-bottom: 5px }
		.div_head, .div_subhead { font-weight: bold }

		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; padding: 1px }
		.tb_data th { background-color: #CFC; font-size: 13px }
		.tb_data td { font-size: 13px }
		.tb_data .noborder th { border-left: none; border-right: none }

		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 15px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body>


		<div align="center">
		<?php foreach($a_data as $row){ ?>

		<?php
		//if(!empty($row['jumlahpelanggaran']))
			//continue;
		//else
				include('inc_headerlap.php');
		?>
		<div class="div_head">KARTU BEBAS PELANGGARAN</div>
		</br>
			<div width="<?= $p_tbwidth ?>">
		 <table  border ="0" width="<?= $p_tbwidth ?>">
			<tr>
				<td colspan ="3">
					Dengan ini kami menyatakan mahasiswa dibawah ini:
				</td>
			</tr>
		   <tr>
				<td>Nim</td>
				<td>:</td>
				<td><?=$row['nim']?></td>
		   </tr>
		    <tr>
				<td>Nama Mahasiswa</td>
				<td>:</td>
				<td><?=$row['nama']?></td>
		   </tr>
		   <tr>
				<td>Prodi</td>
				<td>:</td>
				<td><?=$row['namaunit']?></td>
		   </tr>
		   <tr>
				<td>Poin</td>
				<td>:</td>
				<td><?=$row['poinpelanggaran'] .' - '.$row['namajenispelanggaran']?></td>
		   </tr>
		   	<tr>
		   		<td>Keterangan</td>
		   		<td>:</td>
				<?
				if(empty($row['poinpelanggaran'])){
				?>
				<td colspan ="3">
					<p>Tidak mempunyai pelanggaran pada periode akademik  <? echo Akademik::getNamaPeriode($r_periode) ?>. </p>
				</td>
				<?php } else {
				?>
				<td colspan ="3">
					<!--<p>Mempunyai pelanggaran sebanyak <?=$row['poinpelanggaran']?> pada periode akademik  <? echo Akademik::getNamaPeriode($r_periode) ?>. </p>-->
					<?php  
						if (empty($row['poinpelanggaran'])) { ?>
							<h2 style="color: green;">TIDAK MEMILIKI PELANGGARAN</h2>
					<?php	
						} else { ?>
							<h2 style="color: red;">MASIH MEMILIKI PELANGGARAN</h2>
					<?php
						}
					?>
					
				</td>

				<?php
				}?>
			</tr>
			<br>

		</table>
		<table width="<?= $p_tbwidth ?>">
		<tr>
			<td width="50%"></td>
			<td align="center">
				<p>Kepala Biro Kemahasiswaan,</p>

			<br><br><br>
			<p><u> <?= $r_namakabiro ?></u></p>
			</td>
		</tr>
		</table>
		<br>

		<div style="page-break-after:always"></div>
    </div>
 <?php } ?>
	</div>
 </body>
 </html>
