<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_idpegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_bulan = CStr::removeSpecial($_REQUEST['bulan']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('presensi'));
	
	// definisi variable halaman	
	$p_tbwidth = 1200;
	$p_col = 13;
	$p_file = 'laporankehadirankaryawan'.(!empty($r_kodeunit) ? '_'.$r_kodeunit : '');
	$p_model = 'mPresensi';
	$p_window = 'Laporan Kehadiran Karyawan';
	
	// header
	switch($r_format) {
		case 'doc';
			header("Content-Type: application/msword");
			header('Content-Disposition: attachment; filename="'.$p_file.'.doc"');
			break;
		case 'xls' :
			header("Content-Type: application/msexcel");
			header('Content-Disposition: attachment; filename="'.$p_file.'.xls"');
			break;
		default : header("Content-Type: text/html");
	}
		
    $a_data = $p_model::getLapPresensi($conn,$r_kodeunit,$r_tahun,$r_bulan,$r_idpegawai);
	
	$rs = $a_data['list'];
	$a_datapeg = $a_data['pegawai'];
	$a_jumpeg = $a_data['terima'];
	
	$arhari = $p_model::hariAbsensi();
		
	$p_title = 'Laporan Kehadiran Karyawan<br />';
	if(!empty($a_data['namaunit']))
		$p_title .= 'Unit '.$a_data['namaunit'].'<br />';
	$p_title .= 'Periode '.Date::indoMonth($r_bulan).' '.$r_tahun;
?>

<html>
<head>
<title><?= $p_window; ?></title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<style>
table { border-collapse:collapse }
div,td,th {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
}
td,th { border:1px solic black }
</style>
</head>
<body>
<?
if (count($a_jumpeg) > 0) {
foreach($a_jumpeg as $row):?>
<div align="center" style="page-break-after:always">
	<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0" bgcolor = "white">
			<tr>
				<td width="100">NIP</td>
				<td width="5">:</td>
				<td><?= $row['nik']?></td>
			</tr>
			<tr>
				<td width="100">NAMA PEGAWAI</td>
				<td width="5">:</td>
				<td><?= $row['namalengkap'] ?></td>
			</tr>
			<tr>
				<td width="100">UNIT KERJA</td>
				<td width="5">:</td>
				<td><?= $row['namaunit']; ?></td>
			</tr>
			<tr>
				<td width="100">JABATAN</td>
				<td width="5">:</td>
				<td><?= $row['jabatanstruktural']; ?></td>
			</tr>
		</table>
		<br>
		
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">			
			<tr bgcolor = "gray">
				<th rowspan = "2"><b style = "color:#FFFFFF">TGL. ABSENSI</b></th>
				<th colspan = "2"><b style = "color:#FFFFFF">JAM KERJA</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JAM MASUK</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JAM KELUAR</b></th>
				<th colspan = "2"><b style = "color:#FFFFFF">JML MENIT DATANG</b></th>
				<th colspan = "2"><b style = "color:#FFFFFF">JML MENIT PULANG</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JML JAM KERJA</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">ALASAN</b></th>
			</tr>
			<tr bgcolor ="gray">				
				<th><b style = "color:#FFFFFF">MASUK</b></th>							
				<th><b style = "color:#FFFFFF">PULANG</b></th>
				<th><b style = "color:#FFFFFF">CEPAT</b></th>							
				<th><b style = "color:#FFFFFF">TELAT</b></th>
				<th><b style = "color:#FFFFFF">CEPAT</b></th>							
				<th><b style = "color:#FFFFFF">LEMBUR</b></th>
			</tr>
			<?php 
			if (count($a_datapeg[$row['idpegawai']]) > 0) {
				foreach($a_datapeg[$row['idpegawai']] as $r):
					$jdatangc='';$mdatangc='';
					if($r['menitdatang'] > 0){
						if($r['menitdatang'] > 60){
							$jdatangc = floor($r['menitdatang']/60) .' jam ';
							$mdatangc = $r['menitdatang']%60 .' menit';
						}else{
							$mdatangc = $r['menitdatang'].' menit';
						}
					}
					
					$jdatangl='';$mdatangl='';
					if($r['menitdatang'] < 0){
						if(abs($r['menitdatang']) > 60){
							$jdatangl = floor(abs($r['menitdatang'])/60) .' jam ';
							$mdatangl = abs($r['menitdatang'])%60 .' menit';
						}else{
							$mdatangl = abs($r['menitdatang']).' menit';
						}
					}
					
					$jpulang='';$mpulang='';
					if($r['menitpulang'] > 0){
						if($r['menitpulang'] > 60){
							$jpulang = floor($r['menitpulang']/60) .' jam ';
							$mpulang = $r['menitpulang']%60 .' menit';
						}else{
							$mpulang = $r['menitpulang'].' menit';
						}
					}
					
					$stgl = strtotime($r['tglpresensi']);
					$elemen=date("w",$stgl);
			?>
			<tr>
				<td><?= $arhari[$elemen].', '.CStr::formatDateInd($r['tglpresensi']); ?></td>
				<td align="right"><?= $r['sjamdatang2']; ?></td>
				<td align="right"><?= $r['sjampulang2']; ?></td>
				<td align="right"><?= $r['jamdatang2']; ?></td>
				<td align="right"><?= $r['jampulang2']; ?></td>
				<td align="right"><?= $jdatangc.$mdatangc; ?></td>
				<td align="right"><?= $jdatangl.$mdatangl; ?></td>
				<td align="right"><?= $jpulang.$mpulang; ?></td>
				<td align="right"><?= $r['totlembur']; ?></td>
				<td align="right"><?= round($r['jamkerja'],2); ?></td>
				<td><?= $r['keterangan']; ?></td>
			</tr>
			<?php endforeach;
				}else{
			?>	
			<tr>
				<td align="center" colspan="11"><strong>Data tidak ditemukan</strong></td>
			</tr>	
			<?}?>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php');?>
</div>
 <?php endforeach;
 }else{ ?>	
<div align="center" style="page-break-after:always">
	<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0" bgcolor = "white">
			<tr>
				<td width="100">NIP</td>
				<td width="5">:</td>
				<td><?= $row['nik']?></td>
			</tr>
			<tr>
				<td width="100">NAMA PEGAWAI</td>
				<td width="5">:</td>
				<td><?= $row['namalengkap'] ?></td>
			</tr>
			<tr>
				<td width="100">UNIT KERJA</td>
				<td width="5">:</td>
				<td><?= $row['namaunit']; ?></td>
			</tr>
			<tr>
				<td width="100">JABATAN</td>
				<td width="5">:</td>
				<td><?= $row['jabatanfungsional']; ?></td>
			</tr>
		</table>
		<br>
		
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">			
			<tr bgcolor = "gray">
				<th rowspan = "2"><b style = "color:#FFFFFF">TGL. ABSENSI</th>
				<th colspan = "2"><b style = "color:#FFFFFF">JAM KERJA</th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JAM MASUK</th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JAM KELUAR</th>
				<th colspan = "2"><b style = "color:#FFFFFF">JML JAM DATANG</th>
				<th colspan = "2"><b style = "color:#FFFFFF">JML JAM PULANG</th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JML JAM KERJA</th>
				<th rowspan = "2"><b style = "color:#FFFFFF">ALASAN</th>
			</tr>
			<tr bgcolor ="gray">				
				<th><b style = "color:#FFFFFF">MASUK</th>							
				<th><b style = "color:#FFFFFF">PULANG</th>
				<th><b style = "color:#FFFFFF">CEPAT</th>							
				<th><b style = "color:#FFFFFF">TELAT</th>
				<th><b style = "color:#FFFFFF">CEPAT</th>							
				<th><b style = "color:#FFFFFF">LEMBUR</th>
			</tr>
			<tr>
				<td align="center" colspan="11"><strong>Data tidak ditemukan</strong></td>
			</tr>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php');?>
</div>
 <? } ?>
</body>
 </html>
 
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
 
