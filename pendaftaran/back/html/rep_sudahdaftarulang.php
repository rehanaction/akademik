<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	Modul::getFileAuth();
        
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('unit'));


	//parameter
	$r_periode  = CStr::removeSpecial($_REQUEST['periode']);
	$r_kodeunit = CStr::removeSpecial($_REQUEST['kodeunit']);	
	$r_tglawal  = $_POST['tglawal'];
	$r_tglakhir = $_POST['tglakhir'];
	
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	//model
	$p_model=mLaporan;
	$p_title='Laporan Pendaftar (Sudah daftar ulang)';
	$p_tbwidth = 950;
	$a_data = $p_model::getPendaftarsudahdaftarulang($conn, $r_periode, $r_kodeunit,$r_tglawal,$r_tglakhir);
	$p_namafile = 'laporan_'.$r_periode.'_'.$r_kodeunit;
	
	$r_tglawal = date::indoDateYmd($r_tglawal);
	$r_tglakhir = date::indoDateYmd($r_tglakhir);
	
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
		<h2>Daftar Mahasiswa Baru Yang Memiliki Outstanding Cicilan(BB)</h2>
		<h3> <?= $r_periode ? 'Periode '.Pendaftaran::getNamaPeriode($r_periode) : ' - '?>  
			<br>
			 <?= $r_tglawal <> '--' ?  'Tanggal '.date::indoDate($r_tglawal) : ''?> 
			 <?= $r_tglakhir <> '--' ?  's/d Tanggal '.date::indoDate($r_tglakhir) : ''?> 
			</h3>
	</center>
	<center>
		<table border="1" style="border-collapse:collapse; font-size:12pt" cellpadding="5" width="<?= $p_tbwidth?>" >
			<thead>
				<tr>
					<th>No</td>
					<th>NIM</th>
					<th>Nama</th>
					<th>No Hp</th>
					<th>Pilihan 1</th>
					<th>Jurusan</th>
					<th>Basis</th>
					<th>Tgl Tagihan</th>
					<th>Nominal</th>
					<th>Follow Up</th>
					<th>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<? $no=0; $t_total=0; foreach ($a_data as $row){ $no++;?>
				<tr>
					<td><?= $no?></td>
					<td><?= $row['nimpendaftar']?> </td>
					<td><?= $row['nama']?></td>
					<td><?= $row['hp']?></td>
					<td><?= $row['namaunitpilihan1']?></td>
					<td><?= $row['namaunit']?></td>
					<td><?= $row['namasistem']?></td>
					<td><?= date::indoDate($row['tgltagihan'])?></td>
					<td align="right"><?= cStr::formatNumber($row['nominaltagihan'])?></td>
					<td><?= $row['isfollowup'] =='-1' ? '<img src="images/check.png">' : ''?></td>
					<td><?= $row['keterangan']?></td>
				</tr>
				<? $t_total+= $row['nominaltagihan'];
					} 
					if (Modul::getRole() =='PP') {?>
				<tr>
					<td colspan="7" align="center"> Total</td>
					<td> <?= cStr::formatNumber($t_total)?></td>
				</tr>
				<? } ?>
			
			</tbody>
		</table>
	
	</center>

	</div>
	
	</body>
</html>
