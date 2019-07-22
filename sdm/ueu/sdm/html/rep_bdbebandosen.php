<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_unit = CStr::removeSpecial($_POST['unit']);
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$r_pegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('bebandosen'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 8;
	$p_file = 'rekap_beban_dosen_'.$r_unit;
	$p_model = mBebanDosen;
	$p_window = 'Rekapitulasi Beban Kerja Dosen';
	
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
	
    $a_laporan = $p_model::getLapBebanDosen($conn,$r_periode,$r_unit,$r_pegawai);
	
	$rs = $a_laporan['list'];
	$a_data = $a_laporan['listdet'];
	$namaunit = $a_laporan['unit'];
	$namaperiode = $a_laporan['periode'];
		
	//bidang beban dosen
	$a_bidang = $p_model::getBidangBKD($conn);
	
	$p_title = 'Rekapitulasi Beban Dosen';
	if(!empty($namaunit))
		$p_title .= 'Unit '.$namaunit;
	$p_title .= '<br />Periode '.$namaperiode;
?>
<html>
<head>
	<title><?= $p_window; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
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
		
		<? while($row = $rs->FetchRow()){ ?>
		
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br>
		<br>
		<br>
		
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse 1px">
			<tr>
				<td colspan="3" style="border-top:none;border-left:none;border-right:none;"><b>IDENTITAS</b></td>
			</tr>
			<tr>
				<td width="150px">Nama</td>
				<td width="10px">:</td>
				<td><?= $row['namalengkap']?></td>
			</tr>
			<tr>
				<td>No. Sertifikat</td>
				<td>:</td>
				<td><?= $row['nosertifikat']?></td>
			</tr>
			<tr>
				<td>Perguruan Tinggi</td>
				<td>:</td>
				<td><?= $conf['univ_name']?></td>
			</tr>
			<tr>
				<td>Status</td>
				<td>:</td>
				<td><?= $row['statusdosen']?></td>
			</tr>
			<tr>
				<td>Alamat Perguruan Tinggi</td>
				<td>:</td>
				<td><?= $conf['univ_address']?></td>
			</tr>
			<tr>
				<td>Fakultas / Departemen</td>
				<td>:</td>
				<td><?= $row['fakultas']?></td>
			</tr>
			<tr>
				<td>Jurusan / Prodi</td>
				<td>:</td>
				<td><?= $row['jurusan']?></td>
			</tr>
			<tr>
				<td>Pangkat / Golongan</td>
				<td>:</td>
				<td><?= $row['golongan']?></td>
			</tr>
			<tr>
				<td>Tempat, Tgl. Lahir</td>
				<td>:</td>
				<td><?= $row['tmplahir'].', '.CStr::formatDate($row['tgllahir'])?></td>
			</tr>
			<tr>
				<td>S1</td>
				<td>:</td>
				<td><?= $row['pendsarjana']?></td>
			</tr>
			<tr>
				<td>S2</td>
				<td>:</td>
				<td><?= $row['pendmagister']?></td>
			</tr>
			<tr>
				<td>S3</td>
				<td>:</td>
				<td><?= $row['penddoktoral']?></td>
			</tr>
			<tr>
				<td>Ilmu yang ditekuni</td>
				<td>:</td>
				<td><?= $row['bidangilmu']?></td>
			</tr>
			<tr>
				<td>No. HP</td>
				<td>:</td>
				<td><?= $row['nohp']?></td>
			</tr>
		</table>
		<br>
		<table width="<?= $p_tbwidth ?>" align="center" cellpadding="0" cellspacing="0">
			<?
				foreach($a_bidang as $bid => $bidval){
					$d=0;
					if(!empty($a_data[$row['idpegawai']][$bid])){
						if($bid != $tempbid){
			?>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<?			}?>
			<tr>
				<td>
					<table width="100%" cellpadding="4" cellspacing="2" align="center" border="1" style="border-collapse:collapse 1px">
						<tr>
							<td colspan="12" style="border-top:none;border-left:none;border-right:none;"><b>Bidang <?= $bidval?></b></td>
						</tr>
						<tr bgcolor = "gray">
							<th rowspan="3"><b  style = "color:#FFFFFF">No</b></th>
							<th rowspan="3"><b  style = "color:#FFFFFF">Kegiatan</b></th>
							<th colspan="2"><b  style = "color:#FFFFFF">Beban Kerja</b></th>
							<th rowspan="3"><b  style = "color:#FFFFFF">Masa Pelaksanaan</b></th>
							<th colspan="3"><b  style = "color:#FFFFFF">Kinerja</b></th>
							<th colspan="3"><b  style = "color:#FFFFFF">Penilaian</b></th>
						</tr>
						<tr bgcolor = "gray">
							<th rowspan="2"><b  style = "color:#FFFFFF">Bukti Penugasan</b></th>
							<th rowspan="2"><b  style = "color:#FFFFFF">SKS</b></th>
							<th rowspan="2"><b  style = "color:#FFFFFF">Bukti Dokumen</b></th>
							<th colspan="2"><b  style = "color:#FFFFFF">Capaian</b></th>
							<th rowspan="2"><b  style = "color:#FFFFFF">Bukti Dokumen</b></th>
							<th colspan="2"><b  style = "color:#FFFFFF">Capaian</b></th>
						</tr>
						<tr bgcolor = "gray">
							<th>%</th>
							<th>SKS</th>
							<th>%</th>
							<th>SKS</th>
						</tr>
						<? 
									if($bid != $tempbid)
										$no=0;
									foreach($a_data[$row['idpegawai']][$bid] as $val => $rowd){
										$d++;
										$no++;
										$sks[$bid] += $rowd['sks'];
										$skscapaian[$bid] += $rowd['skscapaian'];
										$skscapaianmonev[$bid] += $rowd['skscapaianmonev'];
						?>
						<tr>
							<td align="center"><?= $no ?></td>
							<td><?= $rowd['kegiatan'] ?></td>
							<td><?= $rowd['buktipenugasan'] ?></td>
							<td align="center"><?= $rowd['sks'] ?></td>
							<td><?= $rowd['waktu'] ?></td>
							<td><?= $rowd['buktidokumen'] ?></td>
							<td align="center"><?= $rowd['capaian'] ?></td>
							<td align="center"><?= $rowd['skscapaian'] ?></td>
							<td><?= $rowd['penilaianmonev'] ?></td>
							<td align="center"><?= $rowd['capaianmonev'] ?></td>
							<td align="center"><?= $rowd['skscapaianmonev'] ?></td>
						</tr>
						<? 
									}
						  ?>
						<tr style="font-weight:bold">
							<td align="center" colspan="3">Total SKS Beban Kerja</td>
							<td align="center"><?= $sks[$bid]?></td>
							<td align="center" colspan="3">Total SKS Kinerja</td>
							<td align="center"><?= $skscapaian[$bid]?></td>
							<td align="center" colspan="2">Total SKS Penilaian</td>
							<td align="center"><?= $skscapaianmonev[$bid]?></td>
						</tr>
					</table>
				</td>
			</tr>
			<?
				
								
								$tempbid = $bid;}
			}
			?>
		</table>
		<? include($conf['view_dir'].'inc_footerrep.php'); ?>
		
		<? } ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
