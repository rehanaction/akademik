<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tglmulai = CStr::formatDate($_REQUEST['tglmulai']);
	$r_tglselesai = CStr::formatDate($_REQUEST['tglselesai']);
	$r_jenispeg = $_REQUEST['jenispeg'];
	$jenispeg = implode("','",$r_jenispeg);
	$r_jenis = $_REQUEST['jenis'];
	$jenis = implode("','",$r_jenis);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('dinas'));
	
	// definisi variable halaman	
	$p_tbwidth = 1300;
	$p_col = 10;
	$p_file = 'buktikedinasan_'.$r_kodeunit;
	$p_model = 'mDinas';
	$p_window = 'Daftar Bukti Kedinasan';
	
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
	
    $a_data = $p_model::repBuktiKedinasan($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$jenis,$jenispeg);
	
	$rs = $a_data['list'];
	$a_rate = $a_data['rate'];
	
	$p_title = 'Daftar Bukti Kedinasan <br />
				Unit '.$a_data['namaunit'].'<br />
				Periode '.CStr::formatDateInd($r_tglmulai).' s/d '.CStr::formatDateInd($r_tglselesai);	
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
			<tr bgcolor = "gray">
				<th><b style = "color:#FFFFFF">No</b></th>
				<th><b style = "color:#FFFFFF">NIP</b></th>
				<th><b style = "color:#FFFFFF">Nama Pegawai</b></th>
				<th><b style = "color:#FFFFFF">Unit Kerja</b></th>
				<th width="200px"><b style = "color:#FFFFFF">Tgl Dinas</b></th>
				<th><b style = "color:#FFFFFF">Jenis Dinas</b></th>
				<th><b style = "color:#FFFFFF">Keperluan</b></th>
				<th><b style = "color:#FFFFFF">Instansi</b></th>
				<?/*<th><b style = "color:#FFFFFF">Biaya</b></th>*/?>
				<th><b style = "color:#FFFFFF">File Materi</b></th>
			</tr>
			
			<? $i=0;
				while ($row = $rs->FetchRow()){ $i++;
			?>
			
			<tr>
				<td><?= $i; ?></td>
				<td><?= $row['nik']; ?></td>
				<td><?= $row['namapegawai']; ?></td>
				<td><?= $row['namaunit']; ?></td>
				<td align="center"><?= CStr::formatDateInd($row['tglpergi'],false).(!empty($row['tglpulang']) ? ' s/d '.CStr::formatDateInd($row['tglpulang'],false) : ''); ?></td>	
				<td><?= $row['namajenisdinas']; ?></td>
				<td><?= $row['dalamrangka']; ?></td>
				<td><?= $row['instansi']; ?></td>				
				<?/*<td align="right"><?= CStr::formatNumber($a_rate[$row['nodinas']]); ?>&nbsp;</td>*/?>
				<td><?= $row['filevalidasidinas']; ?></td>				
			</tr>
			
			<? }
				if ($i == 0){
			?>
			
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