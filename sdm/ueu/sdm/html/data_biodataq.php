<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	if(SDM::isPegawai())
		$r_self = 1;
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	$p_model = mPegawai;
	$p_foto = uForm::getPathImageFoto($conn,$r_key,'fotopeg');
	$a_data = $p_model::getInfoPegawai($conn, $r_key);
	
	$a_statusnikah = mPegawai::statusNikah();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<style>
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
				<div class="LeftRibbonX">
					Identitas
					<?if($c_kepeg){?>
					<div class="right">
						<img title="Cetak ringkasan biodata pegawai" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
					</div>
					<?}?>
				</div>
					<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
						<tbody>
							<tr>
								<td width="180">ID System</td>
								<td>: <?= $a_data['idpegawai']?></td>
								<td rowspan="8" width="200" align="center">
									<?= uForm::getImageFoto($conn,$r_key,'fotopeg',false) ?>
								</td>
							</tr>
							<tr>
								<td>NIP</td>
								<td>: <?= $a_data['nip']?></td>
							</tr>
							<tr>
								<td>Nama</td>
								<td>: <?= $a_data['namalengkap']?></td>
							</tr>
							<tr>
								<td>Tipe Pegawai</td>
								<td>: <?= $a_data['tipepeg']?></td>
							</tr>
							<tr>
								<td>Jenis Pegawai</td>
								<td>: <?= $a_data['jenispegawai']?></td>
							</tr>
							<tr>
								<td>Kelompok Pegawai</td>
								<td>: <?= $a_data['namakelompok']?></td>
							</tr>
							<?/*
							<tr>
								<td>Hubungan Kerja</td>
								<td>: <?= $a_data['hubkerja']?></td>
							</tr>
							*/?>
							<tr>
								<td>Status Keaktifan</td>
								<td>: <?= $a_data['namastatusaktif']?></td>
							</tr>
							<?if($a_data['idtipepeg'] == 'D' or $a_data['idtipepeg'] == 'AD'){?>
							<tr>
								<td>No Dosen</td>
								<td>: <?= $a_data['nodosen']?></td>
							</tr>
							<tr>
								<td>NIDN</td>
								<td colspan="2">: <?= $a_data['nidn']?></td>
							</tr>
							<tr>
								<td>Rumpun Bidang Dosen</td>
								<td colspan="2">: <?= $a_data['namabidang']?></td>
							</tr>
							<tr>
								<td>No Sertifikasi Dosen</td>
								<td colspan="2">: <?= $a_data['nosertifikasi']?></td>
							</tr>
							<?}?>
							<tr>
								<td>Pendidikan Terakhir</td>
								<td <?= ($a_data['idtipepeg'] == 'D' or $a_data['idtipepeg'] == 'AD') ? 'colspan="2"' : ''?>>: <?= $a_data['namapendidikan']?></td>
							</tr>
							<tr>
								<td>Pangkat/Golongan</td>
								<td colspan="2">: <?= $a_data['namagolongan']; ?></td>
							</tr>
							<?/*
							<tr>
								<td>Masa Kerja Golongan</td>
								<td colspan="2">: <?= $a_data['masakerjathngol'].' tahun '.$a_data['masakerjablngol'].' bulan'?></td>
							</tr>
							*/?>
							<tr>
								<td>Masa Kerja Pengabdian</td>
								<td colspan="2">: <?= $a_data['masakerjathn'].' tahun '.$a_data['masakerjabln'].' bulan'?></td>
							</tr>
						</tbody>
					</table>
					<br />
				</div>	
					
				<div class="LeftRibbonX">Biodata</div>
					<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
						<tbody>
							<tr>
								<td width="180">Jenis Kelamin</td>
								<td>: <?= $a_data['jeniskelamin'] == 'P' ? 'Perempuan' : 'Laki-laki'; ?></td>
							</tr>
							<tr>
								<td>Tempat / Tanggal Lahir</td>
								<td>: <?= strtoupper($a_data['tmplahir']) ?> / <?= CStr::formatDateInd($a_data['tgllahir']); ?></td>
							</tr>
							<tr>
								<td>Umur</td>
								<td>: <?= $a_data['umurth']?> tahun <?= $a_data['umurmonth']?> bulan</td>
							</tr>
							<tr>
								<td>Agama</td>
								<td>: <?= $a_data['namaagama']?></td>
							</tr>
							<tr>
								<td>Status Pernikahan</td>
								<td>: <?= $a_statusnikah[$a_data['statusnikah']]?></td>
							</tr>
							<tr>
								<td>Nama Suami/Istri</td>
								<td>: <?= $a_data['namapasangan']?></td>
							</tr>
							<tr>
								<td>Jumlah Anak</td>
								<td>: <?= $a_data['jmlanak']?></td>
							</tr>
						</tbody>
					</table>
					<br />
				</div>
					
				<div class="LeftRibbonX">Kontak</div>
					<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
						<tbody>
							<tr>
								<td width="180">Alamat</td>
								<td>: <?= $a_data['alamat']; ?></td>
							</tr>
							<tr>
								<td>Propinsi</td>
								<td>: <?= $a_data['propinsi']; ?></td>
							</tr>
							<tr>
								<td>Kota/Kabupaten</td>
								<td>: <?= $a_data['kabupaten']; ?></td>
							</tr>
							<tr>
								<td>Kecamatan</td>
								<td>: <?= $a_data['kecamatan']; ?></td>
							</tr>
							<tr>
								<td>Kelurahan</td>
								<td>: <?= $a_data['kelurahan']; ?></td>
							</tr>
							<tr>
								<td>Kode Pos</td>
								<td>: <?= $a_data['kodepos']; ?></td>
							</tr>
							<tr>
								<td>Telp. Rumah</td>
								<td>: <?= $a_data['telepon']; ?></td>
							</tr>
							<tr>
								<td>Telp. Kantor</td>
								<td>: <?= $a_data['teleponkantor']; ?></td>
							</tr>
							<tr>
								<td>Handphone</td>
								<td>: <?= $a_data['nohp']; ?></td>
							</tr>
							<tr>
								<td>Email Esa Unggul</td>
								<td>: <?= $a_data['email']; ?></td>
							</tr>
							<tr>
								<td>Email Selain Esa Unggul</td>
								<td>: <?= $a_data['emailpribadi']; ?></td>
							</tr>
						</tbody>
					</table>
					<br />
				</div>
							
				<div class="LeftRibbonX">Pekerjaan</div>
					<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
						<tbody>
							<tr>
								<td width="180">Unit Kerja</td>
								<td>: <?= $a_data['namaparent'].' - '.$a_data['namaunit']; ?></td>
							</tr>
							<?if($a_data['idtipepeg'] == 'D' or $a_data['idtipepeg'] == 'AD'){?>
							<tr>
								<td width="180">Unit Homebase</td>
								<td>: <?= $a_data['parenthomebase'].' - '.$a_data['unithomebase']; ?></td>
							</tr>
							<?}?>
							<tr>
								<td>Kelompok</td>
								<td>: <?= $a_data['milikpeg']; ?></td>
							</tr>
							<?if($a_data['idtipepeg'] == 'D' or $a_data['idtipepeg'] == 'AD'){?>
							<tr>
								<td>Jabatan Akademik</td>
								<td>: <?= $a_data['jabatanfungsional']; ?></td>
							</tr>
							<?}?>
							<tr>
								<td>Jabatan Struktural</td>
								<td>: <?= $a_data['jabatanstruktural']; ?></td>
							</tr>
							<tr>
								<td>Jabatan Atasan</td>
								<td>: <?= $a_data['jabatanatasan']; ?></td>
							</tr>
							<?/*
							<tr>
								<td>No. SK Calon</td>
								<td>: <?= $a_data['noskcalon']; ?></td>
							</tr>
							<tr>
								<td>Tanggal Calon</td>
								<td>: <?= CStr::formatDateInd($a_data['tglcalon']); ?></td>
							</tr>
							<tr>
								<td>No. SK Pengangkatan</td>
								<td>: <?= $a_data['noskpengangkatan']; ?></td>
							</tr>
							<tr>
								<td>Tanggal Pengangkatan</td>
								<td>: <?= CStr::formatDateInd($a_data['tglpengangkatan']); ?></td>
							</tr>
							*/?>
						</tbody>
					</table>
					<br />
				</div>
			</td>
		</tr>
	</table>	
<script type="text/javascript">

function goPrint(){
	window.open("<?= Route::navAddress('rep_biodata') ?>&key=<?= $r_key?>&format=html","_blank");
}
</script>
</body>
</html>
