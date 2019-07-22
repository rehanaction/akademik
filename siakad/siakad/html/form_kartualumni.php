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
</style>
</head>
<body>
	<center>
	<div id="page">
		
		<div id="big_header">FORM</div>
		<div id="big_header">KARTU ALUMNI</div><br>
		<div>DI ISI SESUAI DENGAN DATA IJAZAH SEBELUMNYA <BR> DAN DENGAN HURUF KAPITAL</div>
		<br>
		<table width="100%" cellpadding="3">
			<tr>
				<td>1.</td>
				<td>NAMA</td>
				<td> : </td>
				<td><?=$a_data['nama']?></td>
			</tr>
			<tr>
				<td>2.</td>
				<td>NIM</td>
				<td> : </td>
				<td><?=$a_data['nim']?></td>
			</tr>
			<tr>
				<td>3.</td>
				<td>TEMPAT LAHIR</td>
				<td> : </td>
				<td><?=$a_data['tmplahir']?></td>
			</tr>
			<tr>
				<td>4.</td>
				<td>TANGGAL LAHIR</td>
				<td> : </td>
				<td><?=Date::indoDate($a_data['tgllahir'])?></td>
			</tr>
			<tr>
				<td>5.</td>
				<td>PROGRAM STUDI</td>
				<td> : </td>
				<td><?=$a_data['jurusan']?></td>
			</tr>
			<tr>
				<td>6.</td>
				<td>ALAMAT</td>
				<td> : </td>
				<td>
					<?="RT ".$a_data['rt'].", RW ".$a_data['rt'].", Kelurahan ".$a_data['kelurahan']?>
						<br>
						<?=" Kecamatan ".$a_data['kecamatan'].", ".$a_data['namakota']?>
				</td>
			</tr>
			<tr>
				<td>7.</td>
				<td>NOMOR TELP</td>
				<td> : </td>
				<td><?=$a_data['telephon']?></td>
			</tr>
		</table>
		
		<br>
		<div align="left">
		Ketentuan <br>
		<ol>
			<li>Data nama tempat tanggal lahir diisi sesuai dan sama dengan ijazah sebelumnya.</li>
			<li>Foto ukuran 3x4 sebanyak 2 lembar
				<ol type="a">
					<li>Berwaran background / latar belakang mreah</li>
					<li>Mengenaka jas almamater</li>
					<li>Mengenakan hem warna putih / putri menyesuaikan</li>
					<li>Dicetak dengan kualitas baik / di studio foto esa unggul</li>
				</ol>
			</li>
			<li>Data dan foto yang tidak sesuai ketentuan tidak akan diproses</li>
			<li>Foto ditempel pada form ini juga (lem sedikit dan tidak penuh)</li>
			<li>Mahasiswa bertanggung jawab terhadap isi data</li>
			<li>Kartu alumni diambil saat pengambilan ijazah</li>
		<ol/>
		</div>
		<table width="100%" cellpadding="3">
			<tr align="center">
				<td><div id="foto"></div></td>
				<td><div id="foto"></div></td>
				<td>
			</tr>
		</table>
		<br><br>
		Jakarta, ..............<br>
		Tanda tangan Mahasiswa <br><br><br><br>
		<?=$a_data['nama']?>
		</div>
</center>
</body>
</html>
