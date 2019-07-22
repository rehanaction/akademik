<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_idpegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_tahun1 = CStr::removeSpecial($_REQUEST['tahun1']);
	$r_bulan1 = CStr::removeSpecial($_REQUEST['bulan1']);
	$r_tahun2 = CStr::removeSpecial($_REQUEST['tahun2']);
	$r_bulan2 = CStr::removeSpecial($_REQUEST['bulan2']);
	$r_jeniscuti = CStr::removeSpecial($_REQUEST['jeniscuti']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('cuti'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_col = 13;
	$p_file = 'riwayatcuti_'.$r_idpegawai;
	$p_model = 'mCuti';
	$p_window = 'Daftar Riwayat Cuti';
	
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
	
	$a_data = $p_model::getLapCutiPeg($conn,$r_idpegawai,$r_tahun1,$r_bulan1,$r_tahun2,$r_bulan2,$r_jeniscuti);
	
	$rs = $a_data['list'];
	$a_det = $a_data['det'];
	
	$p_title = 'Daftar Riwayat Cuti <br />
				'.$a_data['namapegawai'].'<br />
				Periode '.Date::indoMonth($r_bulan).' '.$r_tahun;
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
				<th style = "color:#FFFFFF">No</th>		
				<th style = "color:#FFFFFF">No. Urut Cuti</th>
				<th style = "color:#FFFFFF">Tgl Pengajuan</th>	
				<th style = "color:#FFFFFF">Jenis Cuti</th>
				<th style = "color:#FFFFFF">Tgl Cuti</th>	
				<th style = "color:#FFFFFF">Lama Cuti</th>						
				<th style = "color:#FFFFFF">Sisa Cuti</th>
				<th style = "color:#FFFFFF">Keterangan</th>
				<th style = "color:#FFFFFF">Status</th>
			</tr>
			
			
			<? $i=0;
				while ($row = $rs->FetchRow()){ $i++;
			?>
			
			<tr>
				<td><?= $i; ?></td>
				<td><?= $row['nosurat']; ?></td>
				<td><?= CStr::formatDateInd($row['tglpengajuan']); ?></td>
				<td><?= $row['jeniscuti']; ?></td>
				<td align="center">
					<?if(count($a_det[$row['nourutcuti']])>0){?>
					<table>
						<? foreach($a_det[$row['nourutcuti']] as $kdet => $det){?>
						<tr>
							<td align="center" style="border:none" width="150px"><?= CStr::formatDateInd($det['tglmulai']); ?></td>
							<td align="center" style="border:none" width="10px"> - </td>
							<td align="center" style="border:none" width="150px"><?= CStr::formatDateInd($det['tglselesai']); ?></td>
						</tr>
						<?}	?>
					</table>
					<?}?>
				</td>
				<td><?= $row['lama']; ?></td>
				<td><?= $row['sisacuti']; ?></td>			
				<td><?= $row['keterangan'].' '.$row['alasancuti']; ?></td>				
				<td><?= $row['status']; ?></td>
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
