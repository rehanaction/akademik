<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_idpegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	echo $r_tahun;
	echo $r_bulan;
	
	require_once(Route::getModelPath('dinas'));
	
	// definisi variable halaman	
	$p_tbwidth = 800;
	$p_col = 13;
	$p_file = 'anggarandinas'.$r_kodeunit;
	$p_model = 'mDinas';
	$p_window = 'Rekapitulasi Anggaran Dinas';
	
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
		
    $a_data = $p_model::repAnggaranDinas($conn,$r_kodeunit,$r_tahun);
	
	$rs = $a_data['list'];
	$a_datalist = $a_data['anggaranunit'];
	$a_detail= $a_data['detailpemakaian'];
		
	$p_title = 'Laporan Kehadiran Karyawan <br />';
	if(!empty($a_data['namaunit']))
		$p_title .= 'Unit '.$a_data['namaunit'].'<br />';
	$p_title .= 'Tahun '.$r_tahun;
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
if (count($a_datalist) > 0) {
foreach($a_datalist as $row):?>
<div align="center" style="page-break-after:always">
	<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0" bgcolor = "white">
			<tr>
				<td width="100">UNIT</td>
				<td width="5">:</td>
				<td><?= $row['namaunit']?></td>
			</tr>
			<tr>
				<td width="100">ANGGARAN</td>
				<td width="5">:</td>
				<td><?= CStr::formatNumber($row['anggaran']).'&nbsp;' ?></td>
			</tr>
		</table>
		<br>
		
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">			
			<tr bgcolor = "gray">
				<th><b  style = "color:#FFFFFF">No</b></th>
				<th><b  style = "color:#FFFFFF">Pegawai</b></th>
				<th><b  style = "color:#FFFFFF">Tarif</b></th>
			</tr>
			<?php 
			if (count($a_detail[$row['idunit']]) > 0) {
				foreach($a_detail[$row['idunit']] as $r){
				$no=1;
			?>
			<tr>
				<td align="center"><?= $no++; ?></td>
				<td><?= '<b>'.$r['nik'].'</b> - '.$r['namapegawai']; ?></td>
				<td align="right"><?= CStr::formatNumber($r['tarif']).'&nbsp;'; ?></td>
			</tr>
			<?	}
			?>
			<tr>
				<td colspan="2" align="center"><b>Anggaran Terpakai</b></td>
				<td align="right"><?= CStr::formatNumber($row['anggaranterpakai']).'&nbsp;'; ?></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><b>Sisa Anggaran</b></td>
				<td align="right"><?= CStr::formatNumber($row['anggaran']-$row['anggaranterpakai']).'&nbsp;'; ?></td>
			</tr>
			<?
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
				<td width="100">UNIT</td>
				<td width="5">:</td>
				<td><?= $row['namaunit']?></td>
			</tr>
			<tr>
				<td width="100">ANGGARAN</td>
				<td width="5">:</td>
				<td><?= CStr::formatNumber($row['anggaran']).'&nbsp;' ?></td>
			</tr>
		</table>
		<br>
		
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">			
			<tr bgcolor = "gray">
				<th><b  style = "color:#FFFFFF">No</b></th>
				<th><b  style = "color:#FFFFFF">Pegawai</b></th>
				<th><b  style = "color:#FFFFFF">Tarif</b></th>
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
 
