<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_nim = CStr::removeSpecial($_REQUEST['nim']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('mahasiswa'));

	// properti halaman
	$p_title = 'Kartu Aktivitas Mahasiswa';
	$p_tbwidth = 950;
	$p_namafile = 'kartu_aktivitas_mahasiswa';

	$a_info = mMahasiswa::getDataSingkat($conn,$r_nim);
	$a_data = mLaporan::getAktivitasByMhs($conn,$r_nim);

	$r_namakabiro = mUnit::getNamaKaBiro($conn);
	$a_jenisaktivitas = array('E'=>'External','I'=>'Internal');

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
<?php
	include('inc_headerlap.php');
?>
<div class="div_head">KARTU AKTIVITAS MAHASISWA</div>
</br>
	<table class="tb_head" width="<?= $p_tbwidth ?>">
		<tr>
			<td width="70">Nim</td>
			<td>:</td>
			<td><?=$a_info['nim']?></td>
		</tr>
		<tr>
			<td>Nama Mahasiswa</td>
			<td>:</td>
			<td><?=$a_info['nama']?></td>
		</tr>
		<tr>
			<td>Unit</td>
			<td>:</td>
			<td><?=$a_info['jurusan']?></td>
		</tr>
   </table>
   <br>
	<table class="tb_data" width="<?= $p_tbwidth ?>">
		<tr>
			<th>No</th>
			<th>Tgl Pengajuan</th>
			<th>Tgl Kegiatan</th>
			<th>Jenis Aktivitas</th>
			<th>Nama Aktivitas</th>
			<th>Penyelenggara</th>
			<th>Jenis kegiatan</th>
			<th>kegiatan</th>
			<th>Validasi</th>
			<th>Poin</th>
		</tr>
		<?php
		$no = 0 ;
		foreach($a_data as $row){
		?>
		<tr>
			<td><?= ++$no ?></td>
			<td><?=date::indoDate($row['tglpengajuan'])?></td>
			<td><?=date::indoDate($row['tglkegiatan'])?></td>
			<td><?=$a_jenisaktivitas[$row['jenisaktivitas']]?></td>
			<td><?=$row['namakegiatan']?></td>
			<td><?=$row['penyelenggara']?></td>
			<td><?=$row['namakegiatan']?></td>
			<td><?=$row['namakodekegiatan']?></td>
			<td><?=(!empty($row['isvalid']))?'valid' : '' ?></td>
			<td><?=$row['isvalid'] ? $row['poinkegiatan'] : '0' ?></td>

		</tr>
		<?php } ?>
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
	</div>
 </body></html>
