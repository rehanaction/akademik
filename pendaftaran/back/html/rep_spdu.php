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
	$r_nopendaftar = $_POST['nopendaftar'];
	
	$p_tbwidth ='950';
	$arrSistem = mCombo::sistemKuliah($conn);
	
	if (!empty($r_nopendaftar)){
		$data = mPendaftar::getData($conn, $r_nopendaftar);
		$arrTagihan = mTagihanKUA::getTagihanPendaftar($conn, $r_nopendaftar);
		
		$setting = mSettingpendaftaran::getData($conn, 1);
		
		if ($data['sistemkuliah'] =='R'){
			$infospdu = $setting['infospdureguler'];	
		}else{
			$infospdu = $setting['infospduparalel'];
		}		
	}
	
	
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
					<? foreach ($arrTagihan as $key => $val){ ?>
					<tr>
						<td><?= $val['angsuranke']?></td>
						<td><?= cStr::formatNumber($val['nominaltagihan'])?></td>
						<td><?= date::indoDate($val['tgltagihan'])?></td>
						<td><?= $val['flaglunas']?></td>
						<td><?= $val['keterangan']?></td>
					</tr>	
					<?	}?>
				</table>
				<br>
				
				<?= UI::createTextArea('info',$infospdu,'ControlRead','','',false,'')?>
				
				&nbsp;
			</div>
			
			
		</div>
		
		
	</center>
	</body>
</html>
