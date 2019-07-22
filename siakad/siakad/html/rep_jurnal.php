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
	$r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));
	
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
	$p_title = 'DAFTAR HADIR DOSEN DAN BERITA ACARA PERKULIAHAN';
	$p_tbwidth = "100%";
	$p_maxrow = 8;
	$p_maxday = 16;
	$p_namafile = 'rps_'.$r_kodeunit.'_'.$r_periode;
	
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

		.tb_data2 { border-collapse: collapse }
		.tb_data2 th, .tb_data2 td { border: 0px solid black; font-size: 13px; padding: 1px }
		.tb_data2 th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data2 td { font-family: Arial; height: 50px; padding:5px }
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
		for($i=0;$i<=$n;$i++) {
			$rowj = $row['jurnal'][$i];
			
			// jika baris pertama
			if($r == 0) {
				include('inc_headerlap.php');
?>
<div class="div_head">DAFTAR HADIR DOSEN DAN BERITA ACARA PERKULIAHAN</div>
<div class="div_subhead"><?= Akademik::getNamaPeriode($r_periode) ?></div>
<table class="tb_head" width="<?= $p_tbwidth ?>" >
	<tr valign="top">
		<td width="50">Mata Kuliah</td>
		<td align="center">:</td>
		<td width="200"><strong><?= $row['namamk'] ?></strong></td>
		<td width="50">Bobot SKS</td>
		<td align="center" width="10">:</td>
		<td><?= $row['sks'] ?> SKS (<?= $row['kodemk'] ?>)</td>
		
	</tr>
	<tr valign="top">
		<td>Prodi/Smt/Kls</td>
		<td align="center" width="10">:</td>
		<td width="200"><?= $row['namaunit'] ?>/ <?= $row['semmk'] ?>/ <?= $row['kelasmk'] ?></td>
		<td>Hari/Jam</td>
		<td align="center">:</td>
		<td><?= $rowj['hari'] ?>/ <?= $rowj['jam'] ?></td>

	</tr>
	<tr valign="top">
		<td>Dosen</td>
		<td align="center">:</td>
		<td width="300"><?= $row['pengajar'] ?></td>
		<td width="60">Ruang</td>
		<td align="center" width="10">:</td>
		<td><?= $row['koderuang'] ?></td>
	</tr>
</table>
<br>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th rowspan="2" width="30">Tatap<br>muka<br>Ke-</th>
		<th colspan="3">Rencana perkuliahan</th>
		<th colspan="3">Pelaksanaan perkuliahan</th>
		<th rowspan="2">Jml<br>Mhs</th>
		<th colspan="3">Paraf</th>
	</tr>
	<tr>
		<th width="60">Hari/tgl</th>
		<th width="60">Jam ke</th>
		<th>Materi / Kegiatan</th>
		<th width="60">Hari/tgl</th>
		<th width="60">Jam ke</th>
		<th>Materi / Kegiatan</th>
		
		<th width="60">Dosen</th>
		<th width="60">Mhs</th>
		<th width="60">Petugas</th>
	</tr>
	<tr>
<?php
			} // selesai jika baris pertama
			$no = $i+1;
?>

	
<?php if($n==16){ ?>
	<tr>
		<?php if ($no == 8) { ?>
			<td align="center"><?= $no ?></td>
			<td colspan="10" align="center"><b>UTS</b></td>
		<?php }elseif($no == 16){ ?>
			<td align="center"><?= $no ?></td>
			<td colspan="10" align="center"><b>UAS</b></td>
		<?php }else{ ?>
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
			<td align="center"><?= $rowj['jumlahpeserta'] ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		<?php } ?>
</tr>
<?php }else{ ?>
<tr>

	<td align="center"><?= $rowj['perkuliahanke'] ?></td>
		
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
		<td align="center"><?= $rowj['jumlahpeserta'] ?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>


<?php } ?>
		
	</tr>
	
<?php


			$r++;
			if($n==16){
				$p_maxrow = 8;
			}else{
				$p_maxrow = 7;
			}
			
			// cek jumlah baris
			if($r == $p_maxrow or $i == $n) {
				$r = 0;
?>

</table>
<br>
<table class="tb_data2" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="2">Catatan :<br>&nbsp;&nbsp;&nbsp;&nbsp;1. Perhitungan honorarium dimulai tanggal 21 sampai dengan tanggal 20 bulan berikutnya.<br>&nbsp;&nbsp;&nbsp;&nbsp;2. Daftar hadir dosen / Time Sheet harus diisi dengan jelas dan lengkap.</td>
	</tr>
	<tr>
		<td align="center" width="300">Wakil Ketua Bidang Akademik,<br><br><br><br><br><br><b>(Drs. RIYANDI NUR SUMAWIDJAYA , M.M)</b></td>
		<td></td>
	</tr>
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
