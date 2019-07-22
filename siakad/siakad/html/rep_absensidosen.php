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
	
	$a_data =  mLaporanKelas::getAbsensi($conn,$r_kodeunit,$r_periode,$r_kurikulum,$r_kodemk,$r_kelasmk);
	$pjmk=mLaporanKelas::getPjmk($conn,$r_key);
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
		.div_head { font-size: 13pt }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 12px; padding: 1px }
		.tb_data th { font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Tahoma, Arial;height:50px }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 12px }
	</style>
</head>
<body>
<div align="center">
<?php
	foreach($a_data as $row){
		$semester=Akademik::semester(true);
		$t_semester = substr($r_periode,-1);
		$t_tahun = substr($r_periode,0,4);
		include('inc_headerlap.php');
?>
<div class="div_head">DAFTAR HADIR DOSEN</div>
<br>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr valign="top">
		<td width="100"><strong>Matakuliah</strong></td>
		<td width="10" align="center">:</td>
		<td width="100"><strong><?= $row['namamk'] ?></strong></td>
		<td width="70"><strong>Alokasi Waktu / SKS</strong></td>
		<td width="10" align="center">:</td>
		<td width="100"><strong><?=$row['sks']*50?> Menit (<?= $row['sks'] ?> sks)</strong></td>
	</tr>
	<tr valign="top">
		<td><strong>Tingkat / Semester</strong></td>
		<td align="center">:</td>
		<td ><strong><?= $semester[$t_semester] ?></strong></td>
		<td><strong>Tahun Akademik</strong></td>
		<td align="center">:</td>
		<td ><strong><?=(int)$t_tahun.' / '.($t_tahun+1)?></strong></td>
	</tr>
	<tr valign="top">
		<td><strong>Koordinator</strong></td>
		<td align="center">:</td>
		<td colspan="4"><strong><?= $pjmk['pjmk'] ?></strong></td>
		
	</tr>
	<tr valign="top">
		<td><strong>Fakultas / Program Studi</strong></td>
		<td align="center">:</td>
		<td colspan="4"><strong><?= strtoupper($row['fakultas']).'/'.$row['namaunit'] ?></strong></td>
		
	</tr>
</table>
<br>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr align="center">
		<th rowspan="2">No</th>
		<th rowspan="2">Tanggal</th>
		<th rowspan="2">Pertemuan Ke</th>
		<th rowspan="2">Jenis Kegiatan (Metode : T,P,K) <br> Pokok Bahasan</th>
		<th rowspan="2">Nama & Tanda Tangan</th>
		<th colspan="2">Jam Hadir</th>
	</tr>
	<tr align="center">
		<th>Datang</th>
		<th>Pulang</th>
	</tr>
	<?php for($i=1;$i<=10;$i++) { ?>
	<tr>
		<td align="center"><?=$i?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<?php } ?>
</table>
<br>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	
	<tr>
		<td width="65%">&nbsp;</td>
		<td><strong>Jakarta, <?= str_repeat('.',40) ?></strong></td>
	</tr>
	<tr>
		<td><strong>&nbsp;</strong></td>
		<td><strong>Koordinator</strong></td>
	</tr>
	<tr height="30">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td><strong>&nbsp;</strong></td>
		<td><strong><u><?= $pjmk['pjmk'] ?></u></strong></td>
	</tr>
	<tr>
		<td><strong>&nbsp;</strong></td>
		<td><strong>NIK <?= $pjmk['nik'] ?></strong></td>
	</tr>
</table>

<div style="page-break-after:always"></div>
<?php } ?>
</div>
</body>
</html>
