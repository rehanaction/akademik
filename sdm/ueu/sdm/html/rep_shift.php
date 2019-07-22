<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_mulai = CStr::removeSpecial($_REQUEST['tglmulai']);
	$r_selesai = CStr::removeSpecial($_REQUEST['tglselesai']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('presensi'));
	
	// definisi variable halaman	
	$p_tbwidth = 1200;
	$p_col = 12;
	$p_file = 'rekapshift_'.$r_kodeunit;
	$p_model = 'mPresensi';
	$p_window = 'Rekapitulasi Shift Pegawai';
	
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
	
	
    $a_data = $p_model::repRekapShift($conn,$r_kodeunit,CStr::formatDate($r_mulai),CStr::formatDate($r_selesai));
	
	$rs = $a_data['list'];
	$a_hari = $a_data['hari'];
	$a_kelkerja = $a_data['kelkerja'];
	$a_shift = $a_data['shift'];
	$namaunit = $a_data['namaunit'];
	
	$p_title = 'Rekapitulasi Shift Pegawai <br />
				Unit '.$namaunit.'<br />
				Tanggal '.$r_mulai.' s/d '.$r_selesai;
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
<div align="center">
		<? require_once($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<? if (count($a_kelkerja) > 0){ 
				foreach($a_kelkerja as $kelompok => $keterangan){
					if (count($rs[$kelompok]) > 0){
			?>
			<tr bgcolor = "gray">
				<th><b style = "color:#FFFFFF">No</b></th>
				<th><b style = "color:#FFFFFF">NIP</b></th>
				<th><b style = "color:#FFFFFF">Nama Pegawai</b></th>
				<th><b style = "color:#FFFFFF">Unit Kerja</b></th>
				<? if (count($a_hari) >  0){ 
						foreach($a_hari as $hari){
				?>
				<th width="75px"><?= strtoupper($hari); ?></b></th>
				<? }} ?>
			</tr>
			<tr bgcolor = "#ECDFDF">
				<td colspan="4"><?= $keterangan; ?></td>
				<? if (count($a_hari) >  0){ 
						foreach($a_hari as $hari){
				?>
				<td align="center" width="100px">
					<?= ($a_shift[$kelompok][$hari]['datang'] <> '') ? $a_shift[$kelompok][$hari]['datang'].' - '.$a_shift[$kelompok][$hari]['pulang'] : 'OFF'; ?>
				</td>
				<? }} ?>
			</tr>
			<? 
					}
				$i=0;
				if (count($rs[$kelompok]) > 0){
				foreach($rs[$kelompok] as $row){ $i++;
			?>
			
			<tr>
				<td><?= $i; ?></td>
				<td><?= $row['nik']; ?></td>
				<td><?= $row['namapegawai']; ?></td>
				<td colspan="<?= count($a_hari)+1; ?>"><?= $row['namaunit']; ?></td>
			</tr>
			<? }
			} ?>
			<? }}else{	?>
			
			<tr>
				<td colspan="<?= $p_col; ?>" align="center">Data tidak ditemukan</td>
			</tr>
			<? } ?>
			
		</table>
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
