<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	Modul::getFileAuth();
        
	require_once(Route::getModelPath('laporan'));


	//parameter
	$r_periode    = CStr::removeSpecial($_REQUEST['periode']);
	$r_kodeunit      = CStr::removeSpecial($_REQUEST['kodeunit']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	//model
	$p_model=mLaporan;
	$p_title='Laporan Pendaftar (Belum daftar ulang)';
	$p_tbwidth = 950;
	$a_data = $p_model::getPendaftarbelumdaftarulang($conn, $r_periode, $r_kodeunit);
	$p_namafile = 'laporan_'.$r_periode.'_'.$r_kodeunit;
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
<html>
	<head>
			<title><?= $p_title?></title>   
			<link rel="icon" type="image/x-icon" href="image/favicon.png">
			<link href="style/style.css" rel="stylesheet" type="text/css">
			
	</head>

	<body style="background:white" onLoad="window.print();">  

	<center>
		<div style=" width:<?=$p_tbwidth ?>px;" >	
		<table width="<?=$p_tbwidth ?>px">
			<tr>
				<td align="center" width="100"><img src="images/logo.jpg" height="70"></td>
				<td align="left">
				   
					<span style="font-size: 1;">KEMENTRIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
					<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL JAKARTA</span><br>
					<span style="font-size: 1;">Jalan Arjuna Utara No.9, Kebon jeruk-Jakarta Barat 11510</span><br>
					<span style="font-size: 1;">021-5674223 (hunting) 021-5682510 (direct) Fax: 021-5674248 Website:www.esaunggul.ac.id, email:info@esaunggul.ac.id</span>
				</td>
			</tr>
		</table>
	<hr>
		<h2>Laporan pendaftar yang belum melakukan daftar ulang <br>  <?= $r_periode ? 'Periode '.Pendaftaran::getNamaPeriode($r_periode) : ''?></h2>
	</center>
	<center>
		<table border="1" style="border-collapse:collapse; font-size:12pt; width:<?= $p_tbwidth.'px'?>" cellpadding="5" >
			<thead>
				<tr>
					<th>No</td>
					<th>No Pendaftar</th>
					<th>Nama</th>
					<th>No Hp</th>
					<th>Email</th>
					<th>Pilihan 1</th>
					<th>Jurusan</th>
					<th>Basis</th>
					<th>Follow Up</th>
					<th>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<? $no=0; foreach ($a_data as $row){ $no++;?>
				<tr>
					<td><?= $no?></td>
					<td><?= $row['nopendaftar']?></td>
					<td><?= $row['nama']?></td>
					<td><?= $row['hp']?></td>
					<td><?= $row['email']?></td>
					<td><?= $row['namaunitpilihan1']?></td>
					<td><?= $row['namaunit']?></td>
					<td><?= $row['namasistem']?></td>
					<td><?= $row['isfollowup'] =='-1' ? '<img src="images/check.png">' : ''?></td>
					<td><?= $row['keterangan']?></td>
				</tr>
				<? } ?>

			
			
			</tbody>
		</table>
	
	</center>

	</div>
	
	</body>
</html>
