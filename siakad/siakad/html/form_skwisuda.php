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
	$p_title = 'Surat Keterangan Telah Daftar Wisuda';
	$p_tbwidth = 700;
	$p_maxrow = 46;
	$p_maxday = 16;
	$p_namafile = 'sk_wisuda'.$r_nim;
	
	
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
		border-collapse:collapse;
		}
		#small{
		font-size:10pt;
		text-align:left;
		}
		#namaform{
			position:absolute;
			left:0;
		}
</style>
</head>
<body>
	<center>
<div id="page">
	<span id="namaform">Form C0</span>
	<img src="images/esaunggul.png" width="65">
	<div id="header"><b>Surat Keterangan</b></div>
	<div id="header"><i>Telah Daftar Wisuda</i></div>
	<i>(Surat Keterangan ini digunakan untuk pengambilan Toga dan Undangan Wisuda) </i>
	
	<table width="100%" cellpadding="3" border="1">
		<tr>
			<td width="30" align="center">1</td>
			<td width="200">Tanggal Daftar</td>
			
			<td width="130" colspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td align="center">2</td>
			<td>Nama Lengkap</td>
			
			<td colspan="3"><?=$a_data['nama']?></td>
		</tr>
		<tr>
			<td align="center">3</td>
			<td>No. Induk Mahasiswa</td>
			
			<td colspan="3"><?=$a_data['nim']?></td>
		</tr>
		<tr>
			<td align="center">4</td>
			<td>Program Studi</td>
			
			<td colspan="3"><?=$a_data['jurusan']?></td>
		</tr>
		<tr>
			<td align="center">5</td>
			<td>IPK dan Tanggal Lulus</td>
			
			<td colspan="3"><?=$a_data['ipk']?>, <?=$a_data['tgl_skyudisium']?></td>
		</tr>
		<tr>
			<td align="center">6</td>
			<td>Alamat dan No. Telp Mahasiswa</td>
			
			<td colspan="3">
				<?="RT ".$a_data['rt'].", RW ".$a_data['rt'].", Kelurahan ".$a_data['kelurahan']?>
				<br>
				<?=" Kecamatan ".$a_data['kecamatan'].", ".$a_data['namakota']?><br>
				<?=$a_data['telephon'].", ".$a_data['hp']?>
			</td>
		</tr>
		<tr valign="top">
			<td align="center" rowspan="3">7</td>
			<td rowspan="3">Pengambilan Toga dan Tanda Tangan **) di Rektorat</td>
			<td width="100">Tanggal</td>
			<td width="200">&nbsp;</td>
			<td rowspan="3">Ttd</td>
		</tr>
		<tr>
			<td>Ukuran</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Petugas</td>
			<td>&nbsp;</td>
		</tr>
		<tr valign="top">
			<td align="center" rowspan="2">8</td>
			<td rowspan="2">Pengambilan Undangan **) Pada Saat DAPS</td>
			<td>Tanggal</td>
			<td>&nbsp;</td>
			<td rowspan="2">Ttd</td>
		</tr>
		<tr>
			<td>Nama Petugas</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div id="small">
	**) Coret yang tidak perlu<br>
	**) Diisi oleh petugas / panitia<br>
	Setelah diisi nomor 1 s/d 7 maka mahasiswa wajib mengcopy 2 lembar untuk proses pengambilan toga dan undangan 
	( asli disimpan mahasiswa, 1 untuk petugas toga, 1 petugas distribusi lapangan)
	</div><br>
	<div align="left"><i> Catatan Petugas (bila ada) </i></div><br>
	<hr> <br> <hr> </br> <hr> <br> <hr>
	
	<i> 
		Jakarta, <?=Date::indoDate(date('Y-m-d'))?><br> Petugas Pendaftaran <br><br><br><br>
		----------------------------------- <br>
		Nama terang dan stempel Prodi / Fakultas
	</i>
</div>
</center>
</body>
</html>
