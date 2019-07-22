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
		$r_jenis = CStr::removeSpecial($_REQUEST['jenis']);
		if(!empty($_REQUEST['tglawalujian']))
			$r_tglawalujian = CStr::removeSpecial(date('Y-m-d',strtotime($_REQUEST['tglawalujian'])));
		if(!empty($_REQUEST['tglakhirujian']))
			$r_tglakhirujian = CStr::removeSpecial(date('Y-m-d',strtotime($_REQUEST['tglakhirujian'])));
		$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	}
	else
		list($r_kurikulum,$r_kodemk,$r_kodeunit,$r_periode,$r_kelasmk) = explode('|',$r_key);
	
	$r_format = $_REQUEST['format'];
	
	// pengecekan unit
	$r_kodeunit = mUnit::passUnit($conn,$r_kodeunit);
	
	// properti halaman
	$p_title = 'Presensi Kuliah';
	$p_tbwidth = 750;
	$p_maxrow = 46;
	$p_maxday = 16;
	$p_namafile = 'absensi_'.$r_kodeunit.'_'.$r_periode;
	
	$a_data =  mLaporanKelas::getJadwalUjian($conn,$r_kodeunit,$r_periode,$r_tglawalujian,$r_tglakhirujian,$r_jenis);
	
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
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 10px; padding: 2px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Tahoma, Arial }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }
	</style>
</head>
<body>
<div align="center">
<?php if(!empty($a_data)) include('inc_headerlap.php'); ?>
<?php foreach($a_data as $tglujian=>$a_jadwal){ 

?>
	<div class="div_head">JADWAL PELAKSANAAN <?=strtoupper($_GET['jenis'])?></div>
	<div class="div_subhead"><?= Akademik::getNamaPeriode($r_periode) ?> </div>
	<table class="tb_head" width="<?= $p_tbwidth ?>">
		
		<tr valign="top">
			<td><strong>Prodi</strong></td>
			<td align="center">:</td>
			<td colspan="4"><?= mUnit::getNamaUnit($conn,$r_kodeunit) ?></td>
			
		</tr>
		<tr valign="top">
			<td width="70"><strong>Tanggal <?=strtoupper($_GET['jenis'])?></strong></td>
			<td align="center" width="10">:</td>
			<td width="130"><?=Date::indoDate($tglujian)?></td>
			
		</tr>
	</table>
	<table class="tb_data" width="<?= $p_tbwidth ?>">
		<thead>
		<tr>
			<th  width="15">No</th>
			<th  >Nama Prodi</th>
			<th  >Mata Kuliah</th>
			<th  width="20">Sesi</th>
			<th  width="20">Basis</th>
			<th  width="150">Dosen</th>
			<th  width="30">Tgl Ujian</th>
			<th  width="30">Waktu</th>
			<th  width="20">Ruang</th>
			<th  width="10">Kel.</th>
			<th  width="10">Jum. Peserta</th>
		</tr>
		</thead>
		<tbody>
		<?php $i=1;foreach($a_jadwal as $row){ 
			$html=CStr::countHtmlTag($row['namapengajar']);
			?>
			<tr>
				<td align="center"><?=$i++?></td>
				<td align="left"><?=$row['jurusan']?></td>
				<td align="left"><?=$row['kodemk']?> <?=$row['namamk']?></td>
				<td align="center"><?=$row['kelasmk']?></td>
				<td align="center"><?=$row['sistemkuliah']?></td>
				<td align="left"><?=$html['div']>1?$row['namakoordinator']:$row['namapengajar']?></td>
				<td align="center"><?=Date::indoDate($row['tglujian'])?></td>
				<td align="center"><?=CStr::formatJam($row['waktumulai'])?>-<?=CStr::formatJam($row['waktuselesai'])?></td>
				<td align="center"><?=$row['koderuang']?></td>
				<td align="center"><?=$row['kelompok']?></td>
				<td align="center"><?=$row['jum_peserta']?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<div style="page-break-after:always"></div>
<?php } ?>
</div>
</body>
</html>
