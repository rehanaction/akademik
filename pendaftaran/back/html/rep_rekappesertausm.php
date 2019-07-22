<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    
	// hak akses
	Modul::getFileAuth();
	
   require_once(Route::getModelPath('laporan'));
   require_once(Route::getModelPath('combo'));
  
	//parameter
	$r_tglawal    = $_REQUEST['tglawal'];
	$r_tglakhir    = $_REQUEST['tglakhir'];
	$r_periode    = CStr::removeSpecial($_REQUEST['periode']);
	$r_sistemkuliah   = CStr::removeSpecial($_REQUEST['sistemkuliah']);
	$format    = CStr::removeSpecial($_REQUEST['format']);
	
	//model
	$p_model=mLaporan;
	$p_tbwidth = 950;
	$p_namafile = 'Laporan Rekapitulasi Peserta USM';
	
	$r_data = mLaporan::getRekappesertausm($conn,$r_periode,$r_sistemkuliah, $r_tglawal,$r_tglakhir);
	$arrUnit = mCombo::unitProdi($conn);
	$arrSistemkuliah = mCombo::sistemKuliah($conn);
	
	foreach ($r_data as $key => $val){
		$data[$val['pilihan1']] = $val['jumlah'];
		}
 
	Page::setHeaderFormat($r_format,$p_namafile);
?>

<html>
<head>
	<title><?= $p_namafile?></title>
	<link rel="icon" type="image/x-icon" href="image/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
</head>

<body style="font-size:11pt" onLoad="window.print()">
	<center>
	<div style="width:<?= $p_tbwidth?>px; min-height:29.1cm; background:#FFF;" >
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
		</table>
	</div>
	<br>
	<table style="width:<?= $p_tbwidth?>px; border-collapse:collapse; text-align:left" border="1" cellpadding="3">
		<thead>
		<tr>
			<th width="10">No</th>
			<th width="50">Kode Prodi</th>
			<th>Program Studi</th>
			<th width="40">Jumlah</th>
		</tr>
		</thead>
		<tbody>
		<?$no=0; foreach ($arrUnit as $key => $val){ $no++;?>
		<tr>
			<td width="10"><?= $no?></td>
			<td align="center"><?= $val['kodenim']?></td>
			<td><?= $val['namaunit']?></td>
			<td align="right"><?= cStr::formatNumber($data[$key]);?></td>
		</tr>
			
			
		<? $t_pendaftar+=$data[$key];	}?>
		</tbody>
		<tr>
		<th colspan="3" align="center">TOTAL</th>
		<th align="right"><?= cStr::formatNumber($t_pendaftar)?></th>
		
		</tr>
	</table>
	
	</div>
	</center>
</body>
</html>
