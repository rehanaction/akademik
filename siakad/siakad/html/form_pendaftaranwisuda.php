<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporanmhs'));
	
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['nim']);
	if(empty($r_key))
		$r_key='130012070';
	
	$r_format = $_REQUEST['format'];
	
	// properti halaman
	$p_title = 'Formulir Pendaftarn Wisuda';
	$p_tbwidth = 700;
	$p_maxrow = 46;
	$p_maxday = 16;
	$p_namafile = 'pend_wisuda'.$r_nim;
	
	
	$a_data =  mLaporanMhs::getDataMhs($conn,$r_key);
	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		#page{
			width:18cm;
			height:27cm;
			font-size:12pt;
			font-family:Times New Roman;
		}
		#header{
			font-size:16pt;
		}

		table{
		font-size:12pt;
		font-family:Times New Roman;
		}
		table tr{
			vertical-align:top;
		}
		#foto{
		width:3cm;
		height:4cm;
		border:1px solid;
		}
		
</style>
</head>
<body>
	<center>
		<div id="page">
			
			<img src="images/esaunggul.png" width="65">
			<div id="header">FORMULIR PENDAFTARAN</div>
			<b>WISUDA</b><br>
			<b>UNIVERSITAS ESA UNGGUL </b>
			<hr>
			<table width="100%" cellpadding="3">
				<tr>
					<td width="10" align="center">1</td>
					<td width="50">Nama</td>
					<td width="10" align="center">:</td>
					<td width="130"><?=$a_data['nama']?></td>
				</tr>
				<tr>
					<td align="center">2</td>
					<td>NPM</td>
					<td align="center">:</td>
					<td><?=$a_data['nim']?></td>
				</tr>
				<tr>
					<td align="center">3</td>
					<td>Prorgram Studi</td>
					<td align="center">:</td>
					<td><?=$a_data['jurusan']?></td>
				</tr>
				<tr>
					<td align="center">4</td>
					<td>Tempat, Tanggal Lahir</td>
					<td align="center">:</td>
					<td><?=$a_data['tmplahir']?>,<?=Date::indoDate($a_data['tgllahir'])?></td>
				</tr>
				<tr>
					<td align="center">5</td>
					<td>Alamat</td>
					<td align="center">:</td>
					<td><?="RT ".$a_data['rt'].", RW ".$a_data['rt'].", Kelurahan ".$a_data['kelurahan']?>
						<br>
						<?=" Kecamatan ".$a_data['kecamatan'].", ".$a_data['namakota']?>
					</td>
				</tr>
				<tr>
					<td align="center">6</td>
					<td>Agama</td>
					<td align="center">:</td>
					<td><?=$a_data['namaagama']?></td>
				</tr>
				<tr>
					<td align="center">7</td>
					<td>Keterangan</td>
					<td align="center">:</td>
					<td><?=$a_data['keterangan']?></td>
				</tr>
			</table>
			<br>
			<table width="100%">
				<tr>
					<td align="center" width="50%"><div id="foto"></div></td>
					<td align="left" width="50%">
					Jakarta, <?=Date::indoDate(date('Y-m-d'))?><br>
					Petugas Pendaftaran <br><br><br><br><br>
					-----------------------------------
					</td>
				</tr>
			</table>
			<div align="left">
			&nbsp;&nbsp;Lampiran : <br>
			<ol>
				<li>Surat Keterangan Kelulusan / Keabsahan peserta wisuda</li>
				<li>Formulir bebas tanggungan (C2)</li>
				<li>Biodata Alumni (C3) ditempel foto sesuai ketentuan</li>
				<li>Form isian Ijazah ditempel foto sesuai ketentuan</li>
				<li>Salinan Ijazah sebelumnya</li>
				<li>Form isian Alumni ditempel foto sesuai ketentuan</li>
				<li>Foto copy bukti pembayaran wisuda 1 lembar</li>
				<li>Transkrip / Daftar nilai sementara	</li>
			</ol>
			&nbsp;&nbsp;Catatan :
			<ol>
				<li>Formulir ini bisa diperbanyak sendiri sesuai kebutuhan</li>
				<li>Diseerahkan ke panitia / BAAK Universitas dalam sotfmap warna kuning</li>
				<li>bagi yang telah daftar wisuda, namun tidak mengikuti wisuda wajib membuat surat ijin tertulis kepada rektor</li>
				<li>Pastikan semua isian telah diisi</li>
			</ol>
			</div>
		</div>
	</center>
</body>
</html>
