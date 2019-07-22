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
	$p_title = 'Formulir Bebas Tanggungan';
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
	<span id="namaform">Form C2</span><br><br>
	
	<b>FORMULIR</b><br>
	<b>BEBAS TANGGUNGAN</b><br>
	<b>UNIVERSITAS ESA UNGGUL</b><br>
	
	<hr>
	<br>
	
	<table width="100%" cellpadding="3">
		<tr>
			<td width="50">Nama</td>
			<td width="10" align="center">:</td>
			<td width="130"><?=$a_data['nama']?></td>
		</tr>
		<tr>
			<td>NIM</td>
			<td align="center">:</td>
			<td><?=$a_data['nim']?></td>
		</tr>
		<tr>
			<td>Prorgram Studi</td>
			<td align="center">:</td>
			<td><?=$a_data['jurusan']?></td>
		</tr>
	</table>
	<br>
	<table width="100%" cellpadding="3" border="1">
		<tr align="center">
			<td>No</td>
			<td>Uraian / Perincian</td>
			<td>Nama Pejabat yang Berwenang</td>
			<td>Tanda Tangan dan Stempel</td>
			<td>Tanggal Penyelesaian</td>
		</tr>
		<tr>
			<td align="center">1</td>
			<td>Administdasi Akademik <br> <u><i>Lulus semua mata kuliah</i></u><br><br></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="center">2</td>
			<td>Lunas Administrasi Kaeuangan<br><br></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="center">3</td>
			<td>Bebas Taggungan Peralatan Laboratorium Program Studi<br><br></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="center">4</td>
			<td>Bebas peminjaman buku - buku Perpustakaan<br><br></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="center">5</td>
			<td>Telah menyerahkan Skripsi / KTI, TA, dan (CD dan Hard Copy)<br><br></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="center">6</td>
			<td>Telah menyerahkan Skripsi / KTI, TA, Tempat Penelitian (lembaga terkait)<br><br></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="center">7</td>
			<td>Iuran Alumni<br><br></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<br>
	<table width="100%">
		<tr>
			<td width="60%">
			Mengetahui,<br> Dekan / Ketua Program Studi <br><br><br><br><br>
			<?=!empty($a_data['kaprodi'])?$a_data['kaprodi']:"---------------------------------<br>Nama dan Stempel."?>
			</td>
			<td width="40%">
				Jakarta, <?=Date::indoDate(date('Y-m-d'))?><br>
				Mahasiswa yang Bersangkutan <br><br><br><br><br>
				<?=$a_data['nama']?>
			</td>
		</tr>
	</table>
</div>
</center>
</body>
</html>
