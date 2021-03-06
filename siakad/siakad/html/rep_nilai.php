<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporankelas'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('setting'));
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	$r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));
	$r_format = $_REQUEST['format'];
	
	if(!empty($r_key))
		list($r_thnkurikulum,$r_kodemk,$r_kodeunit,$r_periode,$r_kelasmk) = explode('|',$r_key);
	
	// properti halaman
	$p_title = 'Nilai Akhir';
	$p_tbwidth = 700;
	$p_maxrow = 46;
	$p_footrow = 7;
	$p_namafile = 'nilai_akhir_'.$r_kodeunit.'_'.$r_periode;
	
	$a_data = mLaporanKelas::getNilaiAkhir($conn,$r_kodeunit,$r_periode,$r_thnkurikulum,$r_kodemk,$r_kelasmk);
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key,true,$key[5]);
	$p_pesan = mSetting::getPesanPengesahan($conn);
	
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
		.tb_data th { font-family: Arial; font-weight: bold }
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
<div class="div_head">NILAI AKHIR</div>
<div class="div_subhead"><?= Akademik::getNamaPeriode($r_periode) ?></div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
		<td>Matakuliah</td>
		<td>:</td>
		<td><?= $a_infokelas['namamk'] ?> (<?= $a_infokelas['kodemk'] ?>)</td>
		<td>Program Studi</td>
		<td>:</td>
		<td>
			<?php
				if ($a_infokelas['kodeunit'] == "4302161201") {
					echo "S1 - Manajemen";
				}elseif ($a_infokelas['kodeunit'] == "4302162201") {
					echo "S1 - Akuntansi";
				}elseif ($a_infokelas['kodeunit'] == "4302161101") {
					echo "Magister Manajemen";
				}else{
					echo "STIE INABA";
				}

			?>
				
		</td>
	</tr>
	<tr>
		<td>Jadwal</td>
		<td>:</td>
		<td>
			<?= $a_infokelas['jadwal'] ?>
			<? if(!empty($a_infokelas['nohari2'])) { ?>
			<br><?= $a_infokelas['jadwal2'] ?>
			<? } ?> Kelas : <?= $a_infokelas['kelasmk'] ?>
		</td>
	</tr>
	<tr>
		<td>Dosen</td>
		<td>:</td>
		<td><?= $a_infokelas['pengajar'] ?></td>
	</tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="15" rowspan="2">No</th>
		<th width="55" rowspan="2">N I M</th>
		<th rowspan="2">Nama Mahasiswa</th>
<?php
				foreach($row['unsur'] as $rowu) {
?>
		<th><?= $rowu['nama'] ?></th>
<?php
				}
?>
		<th width="40" rowspan="2">NA</th>
		<th colspan="2">Nilai Akhir</th>
	</tr>
	<tr>
<?php
				foreach($row['unsur'] as $rowu) {
?>
		<th width="40"><?= $rowu['prosentase'] ?> %</th>
<?php
				}
?>
		<th width="40">Angka</th>
		<th width="40">Huruf</th>
	</tr>
<?php
			} // selesai jika baris pertama
			
			$no = $i+1;
?>
	<tr>
		<td align="center"><?= $no ?></td>
		<td align="center"><?= $rowp['nim'] ?></td>
		<td><?= $rowp['nama'] ?></td>
<?php
			foreach($row['unsur'] as $rowu) {
?>
		<td align="center"><?= CStr::formatNumberRep($r_format,$rowp['nilai'][$rowu['id']],2,true) ?></th>
<?php
			}
?>
		<td align="center"><?= CStr::formatNumberRep($r_format,$rowp['nnumerik'],2,true) ?></td>
		<td align="center"><?= CStr::formatNumberRep($r_format,$rowp['nangka'],2) ?></td>
		<td align="center"><?= $rowp['nhuruf'] ?></td>
	</tr>
<?php
			$r++;
			
			// cek jumlah baris
			if($r == $p_maxrow)
				$r = 0;
			
			// cek footer
			if($i == $n-1) {
				$r = 0;
				if($r > $p_maxrow-$p_footrow) {
					// mundur untuk mencetak footer
					$i--;
				}
				else {
?>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="2">Ket: &nbsp; PF = Performance &nbsp; TGS = Tugas</td>
	</tr>
	<tr>
		<td colspan="2"><?= str_replace('<br>','',$p_pesan) ?></td>
	</tr>
	<tr>
		<td width="65%"><strong>Mengetahui,</strong></td>
		<td><strong>Bandung, <?= str_repeat('.',40) ?></strong></td>
	</tr>
	<tr>
		<td><strong>Ketua Program Studi</strong></td>
		<td><strong>Dosen Pembina / Pengawas,</strong></td>
	</tr>
	<tr height="30">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td><strong><u><?= $row['ketua'] ?></u></strong></td>
		<td>
			<strong>
			<? if(empty($row['nippengajar'])) { ?>
			<?= str_repeat('.',50) ?>
			<? } else { ?>
			<u><?= $row['pengajar'] ?></u>
			<? } ?>
			</strong>
		</td>
	</tr>
	<tr>
		<td>NIP. <?= $row['nipketua'] ?></td>
		<td>
			<? if(!empty($row['nippengajar'])) { ?>
			NIP. <?= $row['nippengajar'] ?>
			<? } ?>
		</td>
	</tr>
<?php
				}
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
</div>
</body>
</html>