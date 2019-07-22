<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
 
	// hak akses
	Modul::getFileAuth();
	
   require_once(Route::getModelPath('laporan'));
   require_once(Route::getModelPath('combo'));
   require_once(Route::getModelPath('unit'));
  
	//parameter
	$r_kodeunit    = CStr::removeSpecial($_REQUEST['kodeunit']);
	$r_periode    = CStr::removeSpecial($_REQUEST['periode']);
	$r_sistemkuliah   = CStr::removeSpecial($_REQUEST['sistemkuliah']);
	$format    = CStr::removeSpecial($_REQUEST['format']);
	
	$arrSistemkuliah = mCombo::sistemKuliah($conn);
	
	//model
	$p_model=mLaporan;
	$p_tbwidth = 950;
	$p_namafile = 'Daftar Mahasiswa Berdasarkan Asal Sekolah';
	
	list($data_smu,$data) = mLaporan::getAsalsekolahPendaftar($conn,$r_kodeunit,$r_periode,$r_sistemkuliah);
 
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
	<div style="width:29.7cm; min-height:21cm; background:#FFF;" >
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
				<td>Prodi</td>
				<td>:</td>
				<td><?= mUnit::getNamaUnit($conn,$r_kodeunit)?></td>
			</tr>
		</table>
	</div>
	<br>
	<table style="width:100%; text-align:left" border="0" cellpadding="3">
		<? $no=0; foreach ($data_smu as $key => $val){
			$asalsmu = trim($val['asalsmu']);
			$kodekotasmu = trim($val['kodekotasmu']);
			
			?>
		<tr>
			<th width="500"><?=  $val['namasmu'] ? $val['namasmu'].' '.$val['namakota'] : 'Asal sekolah tidak di isi'?></th>
			<th>JUMLAH MAHASISWA: <?= $val['jumlah']?></th>
		</tr>
		<tr>
			<td colspan="6">
				<table border="1" style="border-collapse:collapse" width="100%">
					<tr>
						<td width="10">No</td>
						<td width="150">NIM</td>
						<td width="250">Nama Mahasiswa</td>
						<td width="400">Alamat</td>
						<td width="100">Telp</td>
						<td width="100">Hp</td>
						<td width="300">Email</td>
					</tr>
					<? 
					$jumlahdata = count ($data[$asalsmu][$kodekotasmu]); 
			 
					for ($a=0; $a < $jumlahdata; $a++){ $no++ ?>
					<tr>
						<td><?= $no ?></td>
						<td><?= $data[$asalsmu][$kodekotasmu][$a]['nimpendaftar']?></td>
						<td><?= $data[$asalsmu][$kodekotasmu][$a]['nama']?></td>
						<td><?= strtolower($data[$asalsmu][$kodekotasmu][$a]['alamat'])?></td>
						<td><?= $data[$asalsmu][$kodekotasmu][$a]['telp']?></td>
						<td><?= $data[$asalsmu][$kodekotasmu][$a]['hp']?></td>
						<td><?= $data[$asalsmu][$kodekotasmu][$a]['email']?></td>
					</tr>
						
						
					<? 	} ?>
					
				</table>
			</td>
		</tr>
		<? } ?>
	</table>
	
	</div>
	</center>
</body>
</html>
