<?php 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	Modul::getFileAuth();
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('settingpendaftaran'));
	require_once($conf['model_dir'].'m_tagihanKUA.php');
	
	$r_format= $_POST['format'];
	$r_nopendaftarmulai = $_POST['nopendaftarmulai'];
	$r_nopendaftarakhir = $_POST['nopendaftarakhir'];
	
	$p_tbwidth ='950';
	$arrSistem = mCombo::sistemKuliah($conn);	
	$setting = mSettingpendaftaran::getData($conn, 1);
	
	//data
	$arrPendaftar = mLaporan::getDataSPDU($conn, $r_nopendaftarmulai,$r_nopendaftarakhir);
	$arrKeuanganpendaftar = mLaporan::getTagihanPendaftar($conn, $r_nopendaftarmulai,$r_nopendaftarakhir); 
	 
	$p_namafile = 'spdupendaftar';
	Page::setHeaderFormat($r_format,$p_namafile);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Cetak SPDU</title>   
	<link rel="icon" type="image/x-icon" href="image/favicon.png">
	<style>
		body{font-size:12pt;  font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;}
		th {background: #015593; color:#FFF}
		.bold {font-weight:bold; font-size:14pt}
		.capitalize{text-transform:capitalize}
		.text-left{text-align:left; }
	</style>
</head>
	<body  onLoad="window.print();">		  
		
		
<? foreach ($arrPendaftar as $data){?>
	<center>
		<div style="width:<?= $p_tbwidth?>px; background:#FFF">
			<span class="bold">SURAT PEMBERITAHUAN PEMBAYARAN POLA ANGSURAN <BR>MAHASISWA BARU KELAS <?= strtoupper($data['namasistem']);?></span><br>
			<div class="text-left">
				<p>Sehubungan dengan Pelaksanaan Penerimaan Mahasiswa Baru <?= Pendaftaran::getNamaPeriode($data['periodedaftar'])?>, Bersama ini kami sampaikan bahwa :</p>
				<table>
					<tr>
						<td width="200">Nama</td>
						<td>:</td>
						<td class="capitalize"><?= $data['nama']?></td>
					</tr>
					<tr>
						<td>Nopendaftar</td>
						<td>:</td>
						<td><?= $data['nopendaftar']?></td>
					</tr>
				</table>
				<p>Diterima Pada</p>
				<table>
					<tr>
						<td width="200">Fakultas</td>
						<td>:</td>
						<td><?= $data['fakultas']?></td>
					</tr>
					<tr>
						<td>Program Studi</td>
						<td>:</td>
						<td><?= $data['namaunit']?></td>
					</tr>
					<tr>
						<td>Nomor Induk Mahasiswa</td>
						<td>:</td>
						<td><?= $data['nimpendaftar']?></td>
					</tr>
				</table>
				
				<p>dengan skema pembayaran sebagai berikut</p>
				<table border="1" style="border-collapse:collapse" cellpadding="5" width="500">
					<tr>
						<th>Angsuran</th>
						<th>Jumlah</th>
						<th>Tanggal</th>
						<th>Status</th>
						<th>Keterangan</th>
					</tr>
					<? 
					$jumlahtagihan = count($arrKeuanganpendaftar[$data['nopendaftar']]);
					for($a=0; $a<$jumlahtagihan; $a++){ ?>
					<tr>
						<td><?= $arrKeuanganpendaftar[$data['nopendaftar']][$a]['angsuranke']?></td>
						<td><?= cStr::formatNumber($arrKeuanganpendaftar[$data['nopendaftar']][$a]['nominaltagihan'])?></td>
						<td><?= date::indoDate($arrKeuanganpendaftar[$data['nopendaftar']][$a]['tgltagihan'])?></td>
						<td><?= $arrKeuanganpendaftar[$data['nopendaftar']][$a]['flaglunas']?></td>
						<td><?= $arrKeuanganpendaftar[$data['nopendaftar']][$a]['keterangan']?></td>
					</tr>	
					<?	} if ($a< 1){ ?>
					<tr>	
						<td colspan="5" align="center">Tidak ada tagihan</td>
					</tr>	
						
					<?	}?>
				</table>
				<br>
				
				<?= UI::createTextArea('info',($data['sistemkuliah'] == 'R' ? $setting['infospdureguler'] : $setting['infospduparalel']),'ControlRead','','',false,'')?>
				
				&nbsp;
			</div>
			
		</div>
		
	</center>
	<div style="page-break-after: always;"></div>
<? } ?>	
	</body>
</html>
