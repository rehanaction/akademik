<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    
	// hak akses
	Modul::getFileAuth();
	
   require_once(Route::getModelPath('laporan'));
   require_once(Route::getModelPath('combo'));
   require_once(Route::getModelPath('unit'));
  
	//parameter
	$r_tglawal    = $_REQUEST['tglawal'];
	$r_tglakhir    = $_REQUEST['tglakhir'];
	$r_periode    = CStr::removeSpecial($_REQUEST['periode']);
	$r_kodeunit   = CStr::removeSpecial($_REQUEST['kodeunit']);
	$r_sistemkuliah   = CStr::removeSpecial($_REQUEST['sistemkuliah']);
	$format    = CStr::removeSpecial($_REQUEST['format']);
	
	//model
	$p_model=mLaporan;
	$p_tbwidth = 950;
	$p_namafile = 'Generate NIM Harian';
	
	$a_data = mLaporan::getGeneratenim($conn, $r_tglawal,$r_tglakhir,$r_periode,$r_kodeunit,$r_sistemkuliah);
	$arrSistemkuliah = mCombo::sistemKuliah($conn);
 
	Page::setHeaderFormat($r_format,$p_namafile);
?>

<html>
<head>
	<title><?= $p_namafile?></title>
	<link rel="icon" type="image/x-icon" href="image/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
</head>

<body style="font-size:12pt" onLoad="window.print()">
	<center>
	<div style="width:<?= $p_tbwidth?>px; min-height:29.1cm; background:#FFF; ">
		<table width="<?=$p_tbwidth ?>px">
			<tr>
				<td align="center" width="100"><img src="images/logo.jpg" height="70"></td>
				<td align="left">					   
					<span>KEMENTRIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
					<span>UNIVERSITAS ESA UNGGUL JAKARTA</span><br>
					<span>Jalan Arjuna Utara No.9, Kebon jeruk-Jakarta Barat 11510</span><br>
					<span>021-5674223 (hunting) 021-5682510 (direct) Fax: 021-5674248 Website:www.esaunggul.ac.id, email:info@esaunggul.ac.id</span>
				</td>
			</tr>
		</table>
	<hr>
	<br>
	<span><?= strtoupper($p_namafile)?></span>
	<br>
	<div align="left">
		<table>
			<tr>
				<td>Basis</td>
				<td>:</td>
				<td><?= $arrSistemkuliah[$r_sistemkuliah]?></td>
			</tr>
			<tr>
				<td>Semester</td>
				<td>:</td>
				<td><?= Pendaftaran::getNamaPeriode($r_periode)?></td>
			</tr>
			<tr>
				<td>Tanggal</td>
				<td>:</td>
				<td><?= date::indoDate(date::indoDateYmd($r_tglawal))?> s/d <?= date::indoDate(date::indoDateYmd($r_tglakhir))?> </td>
			</tr>
			
		</table>
	</div>
	<br>
	<table style="width:<?= $p_tbwidth?>px; border-collapse:collapse; text-align:left" border="1" cellpadding="3">
		<thead>
			<tr>
				<th width="10">No</th>
				<th>No Pendaftar</th>
				<th>NIM</th>
				<th>Nama</th>
				<th>Jurusan</th>
				<th>Basis</th>
				<th>Nominal</th>
			</tr>
		</thead>
		<tbody>
			<?$no=0; foreach ($a_data as $key => $val){ $no++;?>
			<tr>
				<td width="10"><?= $no?></td>
				<td><?= $val['nopendaftar']?></td>
				<td><?= $val['nimpendaftar']?></td>
				<td><?= $val['nama']?></td>
				<td><?= $val['namaunit']?></td>
				<td align="center"><?= $val['sistemkuliah']?></td>
				<td align="right"><?= cStr::formatNumber($val['jumlahbayar'])?></td>
			</tr>
			<? }?>
		</tbody>
	</table>
	
	</div>
	</center>
</body>
</html>
