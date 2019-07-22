<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
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
	
	$r_uts = (int)$_REQUEST['uts'];
	$r_format = $_REQUEST['format'];
	
	// pengecekan unit
	$r_kodeunit = mUnit::passUnit($conn,$r_kodeunit);
	
	// properti halaman
	$p_title = 'Absensi '.($r_uts ? 'UTS' : 'UAS');
	$p_tbwidth = 700;
	$p_maxrow = 46;
	$p_footrow = 6;
	$p_namafile = 'absensi_uas_'.$r_kodeunit.'_'.$r_periode;
	$r_uas=$r_uts?false:true;
	$a_data =  mLaporanKelas::getAbsensiUAS($conn,$r_kodeunit,$r_periode,$r_kurikulum,$r_kodemk,$r_kelasmk,$r_uts,$r_uas);
	
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
<div class="div_head">ABSENSI <?= $r_uts ? 'UTS' : 'UAS' ?></div>
<div class="div_subhead"><?= Akademik::getNamaPeriode($r_periode) ?></div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr valign="top">
		<td width="70"><strong>Kode</strong></td>
		<td align="center" width="10">:</td>
		<td width="130"><?= $row['kodemk'] ?></td>
		<td width="30"><strong>Kelas</strong></td>
		<td align="center" width="10">:</td>
		<td width="150"><?= $row['kelasmk'] ?></td>
		<td width="50"><strong>Dosen</strong></td>
		<td align="center" width="10">:</td>
		<td><?= $row['pengajar'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Matakuliah</strong></td>
		<td align="center">:</td>
		<td colspan="4"><strong><?= $row['namamk'] ?></strong></td>
		<td><strong>Hari, Tgl</strong></td>
		<td align="center">:</td>
		<td><?= str_repeat('.',15) ?>, <?= str_repeat('.',30) ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Prodi</strong></td>
		<td align="center">:</td>
		<td colspan="4"><?= $row['namaunit'] ?></td>
		<td colspan="3"><strong>Jam</strong>: <?= str_repeat('.',10) ?> / <strong>Ruang</strong>: <?= str_repeat('.',30) ?></td>
	</tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="15">No</th>
		<th width="55">N I M</th>
		<th>Nama Mahasiswa</th>
		<th width="200">Tanda Tangan Mahasiswa</th>
	</tr>
	<tr>
<?php
			} // selesai jika baris pertama
			
			$no = $i+1;
?>
	<tr>
		<td align="right"><?= $no ?></td>
		<td align="center"><?= $rowp['nim'] ?></td>
		<td><?= $rowp['nama'] ?></td>
		<td align="<?= $i%2 == 0 ? 'left' : 'right' ?>" style="padding-bottom:0"><?= $no.' '.str_repeat('.',30) ?></td>
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
		<td width="65%"><strong>Mengetahui,</strong></td>
		<td><strong>Jakarta, <?= str_repeat('.',40) ?></strong></td>
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
		<td><strong><?= str_repeat('.',50) ?></strong></td>
	</tr>
	<tr>
		<td colspan="2">NIP. <?= $row['nipketua'] ?></td>
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
