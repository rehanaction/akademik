<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	Modul::getFileAuth();
	
	
	// include
	require_once(Route::getModelPath('laporankelas'));
	require_once(Route::getModelPath('detailkelas'));
	require_once(Route::getModelPath('unit'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(empty($r_key)) {
	
		$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
		$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
		
		if(!empty($_REQUEST['namamk'])){
			$arr_mk=explode(' - ',$_REQUEST['namamk']);
			$arr_mk=str_replace(')','',$arr_mk);
			$arr_mk=str_replace('(','',$arr_mk);
			$r_kurikulum=$arr_mk[2];
			$r_kodemk=$arr_mk[0];
			$r_kelasmk=$arr_mk[3];
			$r_key=$r_kurikulum.'|'.$r_kodemk.'|'.$r_kodeunit.'|'.$r_periode.'|'.$r_kelasmk;
		}
	}
	else
		list($r_kurikulum,$r_kodemk,$r_kodeunit,$r_periode,$r_kelasmk) = explode('|',$r_key);
	
	$r_format = $_REQUEST['format'];
	
	// pengecekan unit
	$r_kodeunit = mUnit::passUnit($conn,$r_kodeunit);
	
	
	//detail jadwal
	$detail_kelas = mDetailKelas::getArray($conn,$r_key);
	
	// properti halaman
	$p_title = 'Presensi Kuliah';
	$p_tbwidth = 700;
	$p_maxrow = 46;
	$p_maxday = count($detail_kelas)/2;
	if(empty($p_maxday) or $p_maxday<1)
		$p_maxday=7;
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
		.div_header { font-size: 12pt }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head td, .div_head { font-size: 12px }
		.div_subhead { font-size: 11px; margin-bottom: 5px }
		.div_head { text-decoration: underline }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 10px; padding: 2px }
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
						//include('inc_headerlap.php');
		?>
		<table class="tab_header" width="<?= $p_tbwidth ?>">
		<thead>
			<tr>
				<td width="70" align="center">
					<img src="images/logo.jpg" width="65">
				</td>
				<td valign="middle" align="center">
					<div class="div_header">DAFTAR HADIR MAHASISWA <?= strtoupper($row['namaunit'])?> KELAS <b><?= strtoupper($row['kelasmk']) ?></b></div>
					<div class="div_header"><b>SEKOLAH TINGGI ILMU EKONOMI INABA </b></div>
					
				</td>
			</tr>
			</thead>
		</table>


		<table class="tb_head" width="<?= $p_tbwidth ?>" >
			<tr valign="top">
				<td width="100"><strong>Mata kuliah</strong></td>
				<td align="center" width="10">:</td>
				<td width="200"><strong><?= $row['namamk'] ?> (<?= $row['sks'] ?> sks)</strong></td>
				<td>&nbsp;</td>
				<td width="100"><strong>Hari & Jam</strong></td>
				<td align="center" width="10">:</td>
				<td width="200"><strong><?= $row['namahari'] ?>, <?= CStr::formatjam($row['jammulai']) ?> - <?= CStr::formatjam($row['jamselesai']) ?></strong></td>
				<td>&nbsp;</td>
			</tr>
			<tr valign="top">
				<td width="150"><strong>Tahun Akademik</strong></td>
				<td align="center" width="10">:</td>
				<td width="200"><b><?= Akademik::getNamaPeriodeAbsen($r_periode) ?></b></td>
				<td>&nbsp;</td>
				<td width="150"><strong>Ruang</strong></td>
				<td align="center" width="10">:</td>
				<td width="200"><b><?= $row['koderuang'] ?></b></td>
				<td>&nbsp;</td>
			</tr>
			<tr valign="top">
				<td width="100"><strong>Dosen</strong></td>
				<td align="center" width="10">:</td>
				<td width="500"><strong><?= $row['pengajar'] ?></strong></td>
				<td>&nbsp;</td>
			</tr>
			
		</table>
		<br>
		<table class="tb_data" width="<?= $p_tbwidth ?>">
			<tr>
				<th rowspan="3" width="10">No</th>
				<th rowspan="3">Nama Mahasiswa</th>
				<th rowspan="3" width="55">N I M</th>
				
				<th colspan="<?=$p_maxday?>"><b>TANGGAL DAN JAM</b></th>
				
			</tr>
			
			<tr>
				<? for($j=0;$j<$p_maxday;$j++) { ?>
				<th width="50">&nbsp;</th>
				<?   } ?>
			</tr>
			<tr>
				
				<? for($j=0;$j<$p_maxday;$j++) { ?>
				<th width="50">&nbsp;</th>
				<? } ?>
			</tr>
			
			<tr>
		<?php
					} // selesai jika baris pertama
					
					$no = $i+1;
		?>
			<tr>
				<td align="right"><?= $no ?></td>
				<td><?= $rowp['nama'] ?></td>
				<td align="center"><?= $rowp['nim'] ?></td>
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
			<tr align="left">
				<th colspan="3">Jumlah Hadir</th>
				<? for($j=1;$j<=$p_maxday;$j++) { ?>
				<th>&nbsp;</th>
				<? } ?>
			</tr>
			<tr align="left">
				<th colspan="3">Jumlah Tidak Hadir</th>
				<? for($j=1;$j<=$p_maxday;$j++) { ?>
				<th>&nbsp;</th>
				<? } ?>
			</tr>
			<tr align="left">
				<th colspan="3">Paraf Dosen / Nama Dosen</th>
				<? for($j=1;$j<=$p_maxday;$j++) { ?>
				<th>&nbsp;</th>
				<? } ?>
			</tr>
		<?php
					}
					
					if($r == 0) {
		?>
		</table>
		<br>
		<table width="<?= $p_tbwidth ?>">
			<tr>
				<td>
					<span style="font-size: 14px;">** Mahasiswa yang tidak tercantum namanya tidak diperbolehkan mengikuti perkuliahan<br>** Mahasiswa yang tidak tercantum namanya segera melapor ke Bagian Akademik/Dosen Wali<br></span>
				</td>
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
		</table>
	</div>
	<div style="page-break-after:always"></div>

</body>
</html>
