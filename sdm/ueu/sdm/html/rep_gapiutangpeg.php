<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_idpegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_jenispinjaman = $_POST['jenispinjaman'];
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('pinjaman'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_file = 'angsuranpinjaman_'.$r_kodeunit;
	$p_model = 'mPinjaman';
	$p_window = 'Kartu Piutang Pegawai';
	
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
	
	//bila diunduh bentuk doc atau excel, koma rupiah dihilangkan
	$dot = true;
	if($r_format == 'doc' or $r_format == 'xls')
		$dot = false;
	
	if(empty($r_jenispinjaman))
		$sqljenis = '';
	else if(count($r_jenispinjaman) == 1) {
		if(is_array($r_jenispinjaman)) $r_jenispinjaman = $r_jenispinjaman[0];
		$sqljenis = "and pj.kodejnspinjaman = '".CStr::cAlphaNum($r_jenispinjaman)."' ";
		$where = "where kodejnspinjaman = '".CStr::cAlphaNum($r_jenispinjaman)."' ";
	}
	else {
		for($i=0;$i<count($r_jenispinjaman);$i++)
			$r_jenispinjaman[$i] = CStr::cAlphaNum($r_jenispinjaman[$i]);
		$i_jenispinjaman = implode("','",$r_jenispinjaman);
		$sqljenis = "and pj.kodejnspinjaman in ('$i_jenispinjaman') ";
		$where = "where kodejnspinjaman in ('$i_jenispinjaman') ";
	}
	
	//mendapatkan data gaji
    $a_data = $p_model::repLapPiutangPeg($conn,$r_kodeunit,$sqljenis,$r_idpegawai);
	
	$rs = $a_data['list'];
	$jenispinjaman = $a_data['jenispinjaman'];
	$a_angsuran = $a_data['angsuran'];
	$a_pinjaman = $a_data['pinjaman'];
	
	$p_title = 'Kartu Piutang Pegawai<br />';
	if(!empty($a_data['namaunit']))
		$p_title .= 'Unit '.$a_data['namaunit'].'<br />';
	
	$p_title .= $jenispinjaman.'<br />';
	
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
	$no=0;
	while($row = $rs->FetchRow()){
		$no++;
?>
	<div align="center" style="page-break-after:always">
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0">
			<tr>
				<td width="100">Nama</td>
				<td width="10">:</td>
				<td><b><?= $row['namapegawai'] ?></b></td>
			</tr>
			<tr>
				<td width="100">Bagian</td>
				<td width="10">:</td>
				<td><?= $row['namaunit'] ?></td>
			</tr>
		</table>
		</br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">			
			<tr bgcolor ="gray">				
				<th width="80"><b style = "color:#FFFFFF">NO ANGSURAN</b></th>
				<th><b style = "color:#FFFFFF">TANGGAL</b></th>
				<th><b style = "color:#FFFFFF">URAIAN</b></th>						
				<th><b style = "color:#FFFFFF">REFF</b></th>						
				<th><b style = "color:#FFFFFF">PINJAMAN</b></th>
				<th><b style = "color:#FFFFFF">JUMLAH ANGSURAN</b></th>								
				<th><b style = "color:#FFFFFF">PEMBAYARAN</b></th>
				<th><b style = "color:#FFFFFF">SALDO</b></th>
				<th><b style = "color:#FFFFFF">KETERANGAN</b></th>
			</tr>
			
			<?
			if(count($a_angsuran[$row['idpeminjam']])>0){
				foreach($a_angsuran[$row['idpeminjam']] as $rowa){
					$no=1;
					if(count($rowa>0)){
						foreach($rowa as $rowda){
							if($no==1){
							$saldo = $rowda['totalpinjaman'];
							?>
							<tr>
								<td rowspan="2"></td>
								<td><?= CStr::formatDateInd($rowda['tgldicairkan'])?></td>
								<td><b><?= $rowda['jnspinjaman']?></b></td>
								<td><b><?= $rowda['nobuktidicairkan']?></b></td>
								<td align="right"><b><?= CStr::formatNumber($rowda['jmldisetujui'],0,$dot)?></b></td>
								<td></td>
								<td></td>
								<td align="right"><?= CStr::formatNumber($rowda['jmldisetujui'],0,$dot)?></td>
								<td></td>
							</tr>
							<tr>
								<td><?= CStr::formatDateInd($rowda['tgldicairkan'])?></td>
								<td><b>BY. ADM</b></td>
								<td><b><?= $row['nobuktidicairkan']?></b></td>
								<td align="right"><b><?= CStr::formatNumber($rowda['biayaadministrasi'],0,$dot)?></b></td>
								<td></td>
								<td></td>
								<td align="right"><?= CStr::formatNumber($rowda['totalpinjaman'],0,$dot)?></td>
								<td></td>
							</tr>
							<?}
								if($rowda['isdibayar']=='Y'){
									$saldo = $saldo - $rowda['jmlangsuran'];
								}
						?>
								<tr>
									<td align="center"><?= $no++?></td>
									<td><?= CStr::formatDateInd($rowda['tglbayar'])?></td>
									<td>Angsuran</td>
									<td><b><?= $rowda['nobkm']?></b></td>
									<td></td>
									<td align="right"><?= CStr::formatNumber($rowda['jmlangsuran'],0,$dot)?></td>
									<td align="center"><?= $rowda['isdibayar'] == 'Y' ? '<img src="'.$conf['img_dir'].'check.png" />' : ''?></td>
									<td align="right"><?= $rowda['isdibayar']=='Y' ? CStr::formatNumber($saldo,0,$dot) : ''?></td>
									<td><?= $rowda['keterangan']?></td>
								</tr>
						<?	}
						?>
							<tr>
								<td colspan="7" align="center"><b>SALDO</b></td>
								<td align="right"><?= CStr::formatNumber(empty($rowda['saldo'])?$saldo:$rowda['saldo'],0,$dot)?></td>
								<td></td>
							</tr>
						<?
						}
					}
				}?>
			
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
<?}?>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
