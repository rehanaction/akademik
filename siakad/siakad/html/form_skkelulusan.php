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
			font-size:22pt;
		}
		#subheader{
			font-size:14pt;
		}
		table{
		font-size:12pt;
		font-family:Times New Roman;
		border-collapse:collapse;
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
	<span id="namaform">Form C1</span><br><br>
	<img src="images/esaunggul.png" style="float:left" width="65">
	<b>Universita Nahdlatul Ulama Jakarta</b>
	<div id="header"><b>( UEU )</b></div>
	<i>Kampus A Jl. Semea 57 Jakarta Telp. 8291920, 8284508 Fax, 8298582 </i> <br>
	<i>Kampus B RS. Islam Jemursari Jl. Jemursari 51 - 57 Jakarta Telp. 8479070</i> <br>
	<i>Website : <u>www.esaunggul.ac.id</u> Email : <u>info@esaunggul.ac.id</u></i>
	<hr>
	<br>
	
	<i>Bismillahirrahmanirrohim</i>
	<br><br>
	<div id="subheader"><b><u>SURAT KETERANGAN KELULUSAN</u></b></div>
	Keabsahan Peserta Wisuda
	<br><br><br>
	<div align="justify">
	Yang bertanda tangan dibawah ini Ketua Program Studi / Dekan, menerangakan bahwa setelah diadakan pemerikasaan 
	dan pengecekan terhadap mahasiswa dibawah ini <br> <br>
	</div>
	<table width="100%" cellpadding="3">
		<tr>
			<td width="50">Nama Mahasiswa</td>
			<td width="10" align="center">:</td>
			<td width="130"><?=$a_data['nama']?></td>
		</tr>
		<tr>
			<td>NPM</td>
			<td align="center">:</td>
			<td><?=$a_data['nim']?></td>
		</tr>
		<tr>
			<td>Tempat, Tanggal Lahir</td>
			<td align="center">:</td>
			<td><?=$a_data['tmplahir']?>,<?=Date::indoDate($a_data['tgllahir'])?></td>
		</tr>
		<tr>
			<td>Prorgram Studi</td>
			<td align="center">:</td>
			<td><?=$a_data['jurusan']?></td>
		</tr>
		<tr>
			<td>Jenjang / Strata</td>
			<td align="center">:</td>
			<td><?=$a_data['jenjang']?></td>
		</tr>
	</table>
	<br>
	<div align="justify">
	Maka yang bersangkutan kami nyatakan ............. dan <u>memenuhi semua persyaratan</u> yang ditentukan
	sehingga kepadanya berhak mengikuti wisuda. <br><br>
	</div>
	<table width="100%" cellpadding="3">
		<tr>
			<td width="50">Tanggal Lulus / Yudisium</td>
			<td width="10" align="center">:</td>
			<td width="130"><?=Date::indoDate($a_data['tglskyudisium'])?></td>
		</tr>
		<tr>
			<td>IPK</td>
			<td align="center">:</td>
			<td><?=$a_data['ipk']?></td>
		</tr>
		<tr>
			<td>Judul Skripsi / Tugas Akhir</td>
			<td align="center">:</td>
			<td><?=$a_data['judulta']?>,</td>
		</tr>
	</tr>
	</table>
	<br>
	<div align="justify">
	Demikian surat keterangan dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya. 
	</div>
	<br><br>
	<table width="100%">
		<tr>
			<td width="60%"></td>
			<td width="40%">
				Jakarta, <?=Date::indoDate(date('Y-m-d'))?><br>
				Ka. Prodi <br><br><br><br>
				<?=!empty($a_data['kaprodi'])?$a_data['kaprodi']:"---------------------------------<br>Nama dan Stempel."?>
			</td>
		</tr>
	</table>
</div>
</center>
</body>
</html>
