<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('dinas'));
	
	// definisi variable halaman	
	$p_tbwidth = 800;
	$p_col = 13;
	$p_file = 'ratedinas_'.$r_key;
	$p_model = 'mDinas';
	$p_window = 'Rate Perjalanan Dinas';
	
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
		
    $a_rep = $p_model::repSuratDinas($conn,$r_key);
    $a_data = $a_rep['list'];
    $a_ttd = $a_rep['ttd'];
	
	list($nomor,$romawi,$tahun) = explode('/',$a_data['nosurat']);
	$nomorsurat = $nomor.'/ESA UNGGUL/STD/'.$romawi.'/'.$tahun;
	
	$a_rate = array();
	$a_rate = $p_model::getRate($conn,$a_data['jnsrate'],$a_data['idjabatan']);
		
	$a_biayarate = array();
	$a_biayarate = $p_model::getRtBiayaDinas($conn,$r_key);
	
	if(SDM::isPegawai()){
		$r_pegawai = Modul::getIDPegawai();
		if ($r_pegawai != $a_data['pegditunjuk'])
			Route::navigate('home');
	}
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
		
		<strong>RATE PERJALANAN DINAS <br />
		<hr style="width:200px" />
		Nomor : <?= $nomorsurat; ?>
		</strong>
		<br />
		<br />
		<br />
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td valign="top" width="400px">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="3">Di Perintahkan Kepada :</td>
						</tr>			
						<tr>
							<td valign="top" width="10px">1.</td>
							<td valign="top" width="180px">Nama Lengkap</td>
							<td valign="top">: <?= $a_data['namapegawai']; ?></td>
						</tr>		
						<tr>
							<td valign="top">2.</td>
							<td valign="top">Perjalanan Sebagai/ Bagian</td>
							<td valign="top">: <?= $a_data['namajabatan'].' / '.$a_data['namaunit']; ?></td>
						</tr>		
						<tr>
							<td valign="top">3.</td>
							<td valign="top">Tujuan</td>
							<td valign="top">: <?= $a_data['alamat']; ?></td>
						</tr>		
						<tr>
							<td valign="top">4.</td>
							<td valign="top">Keperluan</td>
							<td valign="top">: <?= $a_data['dalamrangka']; ?></td>
						</tr>		
						<tr>
							<td valign="top">5.</td>
							<td valign="top">Tgl. Berangkat/Kembali</td>
							<td valign="top">: <?= CStr::formatDateInd($a_data['tglpergi']).' s/d '.CStr::formatDateInd($a_data['tglpulang']); ?></td>
						</tr>		
						<tr>
							<td valign="top">5.</td>
							<td valign="top">Jumlah Hari</td>
							<td valign="top">: <?= $a_data['lamahari']; ?></td>
						</tr>	
					</table>
				</td>
				<td>
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr height="30px">
							<td colspan="4"><strong>KETERANGAN PEJABAT KEUANGAN</strong></td>
						</tr>			
						<tr>
							<td colspan="4">Mohon dibayarkan :</td>
						</tr>	
						<? if (count($a_rate) >0 ){ 
								$i=1;
								$total = 0;
								foreach($a_rate as $col) {		
									$total += $a_biayarate[$col['idrate']];
								?>
						<tr>
							<td width="10px"><?= $i++; ?></td>
							<td width="180px"><?= $col['rateperjalanan']; ?></td>
							<td width="30px">: Rp</td>
							<td align="right"><?= CStr::formatNumber((int)$a_biayarate[$col['idrate']]); ?>&nbsp;</td>
						</tr>
						<? }} ?>
						<tr>
							<td colspan="2" align="center"><strong>JUMLAH</strong></td>
							<td style="border-top:1px  solid black">: Rp</td>
							<td align="right" style="border-top:1px  solid black"><strong><?= CStr::formatNumber($total); ?></strong>&nbsp;</td>
						</tr>
					</table>
				</td>					
			</tr>
		</table>
		<br />
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td width="50%" align="center">Jakarta, <?= CStr::formatDateInd($a_data['tglusulan']); ?></td>
				<td width="50%" align="center">Jakarta, <?= CStr::formatDateInd($a_data['tglusulan']); ?></td>
			</tr>	
			<tr height="50px">
				<td></td>
				<td></td>
				<td></td>
			</tr>		
			<tr>
				<td align="center"><?= $a_data['namakasdm']?><hr style="width:130px" /></td>
				<td align="center"><?= $a_data['namakabagkeu']?><hr style="width:130px" /></td>
			</tr>	
			<tr>
				<td align="center"><?= $a_ttd['jabkepegawaian']?></td>
				<td align="center"><?= $a_ttd['jabkeuangan']?></td>
			</tr>	
		</table>
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>