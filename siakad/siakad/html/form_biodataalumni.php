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
		font-size:12pt;
		font-family:Times New Roman;
	}
	table{
		font-size:12pt;
		font-family:Times New Roman;
	}
	#header{
		border:2px solid;
		padding:20px;
	}
	#big_header{
		font-weight:bold;
		font-size:20pt;
	}
	#small_header{
		font-weight:bold;
		font-size:17pt;
	}
	#foto{
		width:3cm;
		height:4cm;
		border:1px solid;
	}
	#title{
		position:absolute;
		left:0;
	}
</style>
</head>
<body>
	<center>
	<div id="page">
		<span id="title">Form C3</span>
		<div id="small_header">FORMULIR</div>
		<div id="big_header">BIODATA ALUMNI</div>
		<b>UNIVERSITAS ESA UNGGUL</b>
		<div id="header">
			<table width="100%" style="font-weight:bold">
				<tr>
					<td>NAMA PROGRAM STUDI</td>
					<td> : </td>
					<td><?=$a_data['jurusan']?></td>
				</tr>
				<tr>
					<td>WISUDA KE </td>
					<td> : </td>
					<td><?=$a_data['idyudisium']?></td>
				</tr>
				<tr>
					<td>TANGGAL WISUDA</td>
					<td> : </td>
					<td><?=$a_data['tglyudisium']?></td>
				</tr>
				<tr>
					<td>TEMPAT AACARA WISUDA</td>
					<td> : </td>
					<td>&nbsp;</td>
				</tr>
				
			</table>
		</div>
		<table width="100%" cellpadding="3">
			<tr>
				<td>1.</td>
				<td><i>NAMA</i></td>
				<td> : </td>
				<td><?=$a_data['nama']?></td>
			</tr>
			<tr>
				<td>2.</td>
				<td><i>NPM</i></td>
				<td> : </td>
				<td><?=$a_data['nim']?></td>
			</tr>
			<tr>
				<td>3.</td>
				<td><i>JENIS KELAMIN</i></td>
				<td> : </td>
				<td><?=$a_data['sex']=='L'?'Laki Laki':'Perempuan'?></td>
			</tr>
			<tr>
				<td>4.</td>
				<td><i>TEMPAT DAN TANGGAL LAHIR</i></td>
				<td> : </td>
				<td><?=$a_data['tmplahir']?>, <?=Date::indoDate($a_data['tgllahir'])?></td>
			</tr>
			<tr>
				<td>5.</td>
				<td><i>NAMA ORANG TUA</i></td>
				<td> : </td>
				<td><?=$a_data['namaortu']?></td>
			</tr>
			<tr>
				<td>6.</td>
				<td><i>ALAMAT</i></td>
				<td> : </td>
				<td><?="RT ".$a_data['rt'].", RW ".$a_data['rt'].", Kelurahan ".$a_data['kelurahan']?>
						<br>
						<?=" Kecamatan ".$a_data['kecamatan']?></td>
			</tr>
			<tr>
				<td>7.</td>
				<td><i>KOTA</i></td>
				<td> : </td>
				<td><?=$a_data['namakota']?></td>
			</tr>
			<tr>
				<td>8.</td>
				<td><i>TELP / HP</i></td>
				<td> : </td>
				<td><?=$a_data['telephon']?>/<?=$a_data['hp']?></td>
			</tr>
			<tr>
				<td>9.</td>
				<td><i>INSTANSI TEMPAT KERJA</i></td>
				<td> : </td>
				<td><?=$a_data['namaperusahaan']?></td>
			</tr>
			<tr>
				<td>10.</td>
				<td><i>ALAMAT INSTANSI</i></td>
				<td> : </td>
				<td><?=$a_data['alamatperusahaan']?></td>
			</tr>
			<tr>
				<td>11.</td>
				<td><i>LAMA STUDI</i></td>
				<td> : </td>
				<td><?=$a_data['semestermhs']?> Semester</td>
			</tr>
			<tr>
				<td>12.</td>
				<td><i>TANGGAL LULUS</i></td>
				<td> : </td>
				<td><?=$a_data['tglskyudisium']?></td>
			</tr>
			<tr>
				<td>13.</td>
				<td><i>IPK</i></td>
				<td> : </td>
				<td><?=$a_data['ipk']?></td>
			</tr>
			<tr>
				<td>14.</td>
				<td><i>JUDUL SKRIPSI/KTI</i></td>
				<td> : </td>
				<td><?=$a_data['judulta']?></td>
			</tr>
			
		</table>
		<br>
		<table width="100%" cellpadding="3">
			<tr align="center">
				<td><div id="foto"></div></td>
				<td><div id="foto"></div></td>
				<td>
				Jakarta, .............<br> 
				Yang membuat
				<br><br><br><br>
				<?=$a_data['nama']?>
				</td>
			</tr>
			<tr align="left">
				<td colspan="2">Biodata diatas diapkai sebagai dasar penyusunan buku wisuda</td>
				<td></td>
			</tr>
		</table>
	</div>
</center>
</body>
</html>
