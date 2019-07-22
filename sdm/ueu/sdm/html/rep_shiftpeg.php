<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_idpegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('presensi'));
	
	// definisi variable halaman	
	$p_tbwidth = 1200;
	$p_col = 10;
	$p_file = 'rekap_shift_pegawai_'.!empty($r_idpegawai) ? $r_idpegawai : $r_kodeunit;
	$p_model = 'mPresensi';
	$p_window = 'Daftar Rekap Shift Pegawai';
	
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
	
	$a_data = $p_model::repRekapShiftPegawai($conn,$r_kodeunit,$r_idpegawai);
	
	$rs = $a_data['list'];
	$a_shift = $a_data['shift'];
	$a_hari = $a_data['hari'];
	$namaunit = $a_data['namaunit'];
	
	$p_title = 'Daftar Rekapitulasi Shift Pegawai <br />';
	if(empty($r_idpegawai))
		$p_title .= 'Unit '.$namaunit.'<br />';
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
		while($row = $rs->FetchRow()){
			if(count($a_shift[$row['idpegawai']])>0){
	?>
	<div align="center" style="page-break-after:always">
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br>
		<br>
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0" bgcolor = "white">
			<tr>
				<td width="100">NIP</td>
				<td width="5">:</td>
				<td><?= $row['nik']?></td>
			</tr>
			<tr>
				<td width="100">NAMA PEGAWAI</td>
				<td width="5">:</td>
				<td><?= $row['namapegawai'] ?></td>
			</tr>
			<tr>
				<td width="100">UNIT KERJA</td>
				<td width="5">:</td>
				<td><?= $row['namaunit']; ?></td>
			</tr>
		</table>
		<br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<tr bgcolor = "gray">
				<th><b style = "color:#FFFFFF">No</b></th>		
				<th width="150px"><b style = "color:#FFFFFF">Tanggal Berlaku</b></th>
				<th><b style = "color:#FFFFFF">Nama Shift</b></th>
				<? if (count($a_hari) >  0){ 
						foreach($a_hari as $hari){
				?>
				<th width="100px"><b style = "color:#FFFFFF"><?= strtoupper($hari); ?></b></th>
				<? }} ?>
			</tr>			
			
			<? 
				$i=0;
				if(count($a_shift[$row['idpegawai']])>0){
					foreach ($a_shift[$row['idpegawai']] as $kode => $a_kode) {
						foreach ($a_kode as $a_tgl => $rows) {
							$i++;
			?>
			
			<tr>
				<td><?= $i; ?></td>
				<td align="center"><?= CStr::formatDateInd($rows['tglberlaku']); ?></td>
				<td><?= $rows['keterangan']; ?></td>
				<? if (count($a_hari) >  0){ 
						foreach($a_hari as $hari){
				?>
				<td><?= CStr::formatJam($rows[$hari]['datang']).' - '.CStr::formatJam($rows[$hari]['pulang']); ?></td>
				<? }} ?>
			</tr>
			
			<? }}}
				if ($i == 0){
			?>
			
			<tr>
				<td colspan="<?= $p_col; ?>" align="center">Data tidak ditemukan</td>
			</tr>
			<? } ?>
			
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
	<? }}?>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
