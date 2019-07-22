<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();

	// variabel request
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester =CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun =  CStr::removeSpecial($_REQUEST['tahun']);
	$r_propinsi = CStr::removeSpecial($_REQUEST['propinsi']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	$r_periode=$r_tahun.$r_semester;
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('mhsasuransi'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('settingmhs'));
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('kuisioner'));
	require_once(Route::getModelPath('pengajuanbeasiswa'));
	require_once(Route::getModelPath('pengajuanbeasiswapendaftar'));
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getModelPath('tahapbeasiswa'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('berkasbeasiswamaba'));
	require_once(Route::getModelPath('jenisprestasi'));
	require_once(Route::getModelPath('tingkatprestasi'));
	require_once(Route::getModelPath('kategoriprestasi'));
	require_once(Route::getModelPath('pendidikanbeasiswamaba'));


	//$i_mhs = mMahasiswa::getDataSingkat($conn,$r_unit,$r_semester,$r_propinsi);

	// properti halaman
	$p_title = 'Daftar Pengajuan Beasiswa';
	$p_tbwidth = 700;
	$p_namafile = 'daftar_pengajuanbeasiswamaba'.$r_nim;

	$a_data = mLaporan::getBeasiswaPendaftar($conn,$r_unit,$r_tahun.$r_semester,$r_propinsi);
	$a_pejabat = mSettingMhs::getData($conn,1);


	$arrPekerjaan = mCombo::pekerjaan($conn);
	$arrPendidikan = mCombo::pendidikan($conn);
	$arrAgama = mCombo::agama($conn);
	$arrUnit = mCombo::jurusan($conn);
	$arrPendapatan = mCombo::pendapatan($conn);
	$arrKota = mCombo::getkota($conn);
	$arrPropinsi = mCombo::propinsi($conn);
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>

<head>
<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		.content{width:950px; }
		.header{font-size:30px; font-weight:bold}
		.subheader{font-weight:bold}
		body{ font-family:"Times New Roman", Times, serif}
		.englisttext{color:#858585}
		.box{background:#B0B0B0; width:100%; margin: 10px 0 10px 0;padding:10px; font-size:12px}
		.maincontent{font-size:15px}
		.v_sm_box{float:left; width:100px}
		.sm_box{float:left; width:200px}
		.sm2_box{float:left; width:300px}
		.md_box{float:left; width:400px}
		.lg_box{float:left; width:600px}

		.boxcheck{border:solid 1px; width:15px; height:15px;float:left; margin-right:3px}
		.boxrapor{border:solid 1px; width:55px; height:25px;float:left; margin-right:3px}
		.keterangan {background:#B0B0B0; width:100%; height:50px; margin: 10px 0 10px 0;padding:10px; font-size:12px}
		.footer {background:#B0B0B0; width:100%; height:5cm; margin: 10px 0 10px 0;padding:10px; font-size:12px}
	</style>
</head>
<body>

	<?php
	//var_dump($a_data);
	foreach($a_data as $row)
	{

		  $r_nopendaftar = $row['nopendaftar'];
		  $r_idpengajuan = $row['idpengajuanbeasiswa'];
		  $idbeasiswa = $row['idbeasiswa'];
		  $i_beasiswa = mBeasiswa::getData($conn,$idbeasiswa);
		  $idpengajuan = $row['idpengajuanbeasiswa'];
		  $datapendaftar = mPengajuanBeasiswaPd::getData($conn,$idpengajuan);
			$datapendidikan = mPendidikanBeasiswa::getData($conn,$idpengajuan);
		  $dataorganisasi = mPengajuanBeasiswaPd::getOrganisasi($conn,$idpengajuan,'organisasi');
		  $dataprestasi = mPengajuanBeasiswaPd::getPrestasi($conn,$idpengajuan,'prestasi');
		  $datapelatihan = mPengajuanBeasiswaPd::getPelatihan($conn,$idpengajuan,'pelatihan');
		  $datakerja = mPengajuanBeasiswaPd::getKerja($conn,$idpengajuan,'kerja');
			$data = mPendaftar::getData($conn,$row['nopendaftar']);
	?>
		<center>
			<div class="content">
				<?/* header atas*/?>
				<table width="100%" >
					<tr>
						<td width="40px"><img src="../front/images/logo.png"></td>
						<td>U n i v e r s i t a s<br>
						<span class="header">Esa Unggul</span><br>
						<span class="subheader">FORMULIR PENDAFTARAN BEASISWA UNIVERSITAS ESA UNGGUL</span>
						</td>
					</tr>
				</table>
				<? /* Kotak tengah*/ ?>
				<table align="left" class="box">
					<tr>
						<td>
						PEDOMAM MENGISI FORMULIR <br>
						<span class="englisttext">How to fill this form</span>
						<ul>
							<li>Isilah data yang diminta pada kotak-kota yang disediakan dengan menggunakan huruf CETAK KAPITAL (A,B,C dst bukan a,b,c dst)
								<br><span class="englisttext">Fill in the data BLOCK letter (A,B,C, etc not a,b,c,etc)</span>
							</li>
							<li>Untuk memilih jawaban yang disediakan, berikan tanda silang(x) pada kota jawaban yang anda pilih<br>
								<span class="englisttext">To choose the answer, please cross (x) at the appropriate answer</span>
							</li>
						</ul>
						</td>
					</tr>
				</table>
				<?/* isi formulir pendaftaran*/?>
				<table align="left" class="maincontent" cellpadding="3px">
					<tr>
						<td colspan="3">
							<table>
								<tr>
									<td colspan="3"><strong>Status Mahasiswa</strong> </td>
									<td colspan="2">: <strong>Calon Mahasiswa Baru</<strong></td>
								</tr>
								<tr>
									<td colspan="3"><strong>1. Pilihan Beasiswa</strong></td>
									<td colspan="2">: <?= cStr::upperCase($i_beasiswa['namabeasiswa'])?></td>
								</tr>
								<tr>
									<td colspan="3"><strong>2. Pilihan Program Studi</strong></td>
									<td colspan="2"></td>
								</tr>
								<tr>
									<td colspan="3"><strong>Pilihan 1</strong></td>
									<td colspan="2">: <?= cStr::upperCase($arrUnit[$datapendaftar['pilihan1']])?></td>
								</tr>
								<tr>
									<td colspan="3"><strong>Pilihan 2</strong></td>
									<td colspan="2">: <?= cStr::upperCase($arrUnit[$datapendaftar['pilihan2']])?></td>
								</tr>
								<tr>
									<td colspan="5">Alasan memilih program studi</td>
								</tr>
								<tr>
									<td colspan="3"><strong>Pilihan 1</strong></td>
									<td colspan="2">: <?= $datapendaftar['alasan1']?></td>
								</tr>
								<tr>
									<td colspan="3"><strong>Pilihan 2</strong></td>
									<td colspan="2">: <?= $datapendaftar['alasan2']?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr class="box">
						<td colspan="3">
						<b >A. DATA PRIBADI</b>
						</td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Nama Lengkap</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendaftar['nama'])?></td>
								</tr>
								<tr>
									<td><strong>Tempat Lahir<strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['kodekotalahir_text'])?></td>
								</tr>
								<tr>
									<td><strong>Tanggal Lahir</strong></td>
									<td>:</td>
									<td><?= cStr::formatDateInd($data['tgllahir'])?></td>
								</tr>
								<tr>
									<td><strong>Agama</strong></td>
									<td>:</td>
									<td><?= $arrAgama[$datapendaftar['kodeagama']]?></td>
								</tr>
								<tr>
									<td><strong>Jenis Kelamin</strong></td>
									<td>:</td>
									<td><?= ($data['sex']=='L')?'Laki-laki':'Perempuan' ?></td>
								</tr>
								<tr>
									<td><strong>Kewarganegaraan</strong></td>
									<td>:</td>
									<td><?= ($data['kodewn']=='WNI')?'Warga Negara Indonesia':'Warga Negara Asing' ?></td>
								</tr>
								<tr>
									<td><strong>Alamat</strong></td>
									<td>:</td>
									<td><?= $data['jalan'] ?></td>
								</tr>
								<tr>
									<td><strong>No. Rumah</strong></td>
									<td>:</td>
									<td>
										<div class="sm_box"><?= cStr::upperCase($data['nomorrumah']) ?></div>
										<div class="sm_box">RT. <?= cStr::upperCase($data['rt']) ?></div>
										<div class="sm_box">RW.<?= cStr::upperCase($data['rw']) ?></div>
									</td>
								</tr>
								<tr>
									<td><strong>Kelurahan</strong></td>
									<td>:</td>
									<td> <?= cStr::upperCase($data['kel']) ?></td>
								</tr>
								<tr>
									<td><strong>Kecamatan</strong></td>
									<td>:</td>
									<td> <?= cStr::upperCase($data['kec']) ?></td>
								</tr>
								<tr>
									<td><strong>Kota / Kabupaten</strong></td>
									<td>:</td>
									<td> <?= cStr::upperCase($data['kodekota_text']) ?></td>
								</tr>
								<tr>
									<td><strong>Provinsi</strong></td>
									<td>:</td>
									<td> <?= cStr::upperCase($data['kodepropinsi_text']) ?></td>
								</tr>
								<tr>
									<td><strong>Kode Pos</strong></td>
									<td>:</td>
									<td> <?= cStr::upperCase($data['kodepos']) ?></td>
								</tr>
								<tr>
									<td><strong>Telepon (rumah)</strong></td>
									<td>:</td>
									<td> <?= cStr::upperCase($data['kodepos']) ?></td>
								</tr>
								<tr>
									<td><strong>Handphone</strong></td>
									<td>:</td>
									<td> <?= cStr::upperCase($data['hp']) ?></td>
								</tr>
								<tr>
									<td><strong>Fax</strong></td>
									<td>:</td>
									<td> <?= cStr::upperCase($data['fax']) ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr class="box">
						<td colspan="3">
						<b >B. DATA ORANG TUA</b>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>1. Ayah</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Pendidikan Terakhir</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrPendidikan[$data['kodependidikanayah']])?></td>
								</tr>
								<tr>
									<td><strong>Pekerjaan</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrPekerjaan[$data['kodepekerjaanayah']])?></td>
								</tr>
								<tr>
									<td><strong>Jabatan</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['jabatankerjaayah'])?></td>
								</tr>
								<tr>
									<td><strong>Perusahaan</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['namaperusahaanayah'])?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>2. Ibu</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Pendidikan Terakhir</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrPendidikan[$data['kodependidikanibu']])?></td>
								</tr>
								<tr>
									<td><strong>Pekerjaan</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrPekerjaan[$data['kodepekerjaanibu']])?></td>
								</tr>
								<tr>
									<td><strong>Jabatan</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['jabatankerjaibu'])?></td>
								</tr>
								<tr>
									<td><strong>Perusahaan</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['namaperusahaanibu'])?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>3. Alamat Orangtua</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Alamat</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['alamatayah'])?></td>
								</tr>
								<tr>
									<td><strong>Nomor Rumah</strong></td>
									<td>:</td>
									<td> <div class="sm_box">RT. <?= cStr::upperCase($data['rtayah'])?></div> <div class="sm_box">RW. <?= cStr::upperCase($data['rwayah'])?></div></td>
								</tr>
								<tr>
									<td><strong>Kelurahan</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['kelayah'])?></td>
								</tr>
								<tr>
									<td><strong>Kecamatan</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['kecayah'])?></td>
								</tr>
								<tr>
									<td><strong>Kota</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['kodekotaayah_text'])?></td>
								</tr>
								<tr>
									<td><strong>Provinsi</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['kodepropinsiayah_text'])?></td>
								</tr>
								<tr>
									<td><strong>Telp. (rumah)</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['telpayah'])?></td>
								</tr>
								<tr>
									<td><strong>(handphone)</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['hpayah'])?></td>
								</tr>
								<tr>
									<td><strong>Fax</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($data['faxayah'])?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>4. Penghasilan Orangtua</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Ayah-Ibu Perbulan</strong></td>
									<td>:</td>
									<td>
										<?php
										foreach($arrPendapatan as $key => $val){
											?>
											<div class="sm_box">
												<div class="boxcheck"><?= UI::createCheckBox('penghasilanortu',$arrPendapatan,$data['penghasilanortu'],false) ?></div><?= $val?>
											</div><br>
									<?}?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>5. Kondisi Orangtua </strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Ayah</strong></td>
									<td>:</td>
									<td>
										<?
										$datastatus = array(1=>'Masih Hidup',2=>'telah Meninggal');
										foreach($datastatus as $key => $val){
											$arrdatawn = array($key=>'');
											?>
											<div class="sm_box">
												<div class="boxcheck"><?= UI::createCheckBox('jeniswali',$arrdatawn,$data['statuswali'],false) ?></div><?= $val?>
											</div>
										<?}?>
									</td>
								</tr>
								<tr>
									<td><strong>Ibu</strong></td>
									<td>:</td>
									<td>
										<?
										$datastatus = array(1=>'Masih Hidup',2=>'telah Meninggal');
										foreach($datastatus as $key => $val){
											$arrdatawn = array($key=>'');
											?>
											<div class="sm_box">
												<div class="boxcheck"><?= UI::createCheckBox('jeniswali',$arrdatawn,$data['statusibui'],false) ?></div><?= $val?>
											</div>
										<?}?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Jumlah anak dalam keluarga</strong></td>
									<td>:</td>
									<td>
										<?php
										echo $datapendaftar['jumlahanakkeluarga'];
										?>
									</td>
								</tr>
								<tr>
									<td><strong>Anda anak ke </strong></td>
									<td>:</td>
									<td>
										<?php
										echo $datapendaftar['anakke'];
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5">
							<table>
								<tr>
									<td width="240"><strong>6. Biaya hidup ditanggung oleh</strong></td>
									<td>:</td>
									<td colspan="2">
										<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_7']); ?></div>Orang tua</div>
										<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_7']); ?></div>Wali</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5">
							<table>
								<tr>
									<td width="240"><strong>7. Biaya Pendidikan ditanggung oleh</strong></td>
									<td>:</td>
									<td colspan="2">
										<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_8']); ?></div>Orang tua</div>
										<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_8']); ?></div>Wali</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr class="box">
						<td colspan="3">
						<b >C. Riwayat Pendidikan dan Pengalaman</b>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>1. Sekolah Dasar (SD)</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Nama Sekolah</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendidikan['namasd'])?></td>
								</tr>
								<tr>
									<td><strong>Kota/Kab.</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrKota[$datapendidikan['kodekotasd']])?></td>
								</tr>
								<tr>
									<td><strong>Propinsi</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrPropinsi[$datapendidikan['kodepropinsisd']])?></td>
								</tr>
								<tr>
									<td><strong>Tahun Masuk</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendidikan['tahunmasuksd'])?></td>
								</tr>
								<tr>
									<td><strong>Tahun Lulus</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendidikan['tahunlulussd'])?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>2. Sekolah Lanjutan Tingkat Pertama (SLTP)</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Nama Sekolah</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendidikan['namasmp'])?></td>
								</tr>
								<tr>
									<td><strong>Kota/Kab.</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrKota[$datapendidikan['kodekotasmp']])?></td>
								</tr>
								<tr>
									<td><strong>Propinsi</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrPropinsi[$datapendidikan['kodepropinsismp']])?></td>
								</tr>
								<tr>
									<td><strong>Tahun Masuk</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendidikan['tahunmasuksmp'])?></td>
								</tr>
								<tr>
									<td><strong>Tahun Lulus</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendidikan['tahunlulussmp'])?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>3. Sekolah Lanjutan Tingkat Atas (SLTA)</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td><strong>Nama Sekolah</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendidikan['namasma'])?></td>
								</tr>
								<tr>
									<td><strong>Kota/Kab.</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrKota[$datapendidikan['kodekotasma']])?></td>
								</tr>
								<tr>
									<td><strong>Propinsi</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($arrPropinsi[$datapendidikan['kodepropinsisma']])?></td>
								</tr>
								<tr>
									<td><strong>Tahun Masuk</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendidikan['tahunmasuksma'])?></td>
								</tr>
								<tr>
									<td><strong>Tahun Lulus</strong></td>
									<td>:</td>
									<td><?= cStr::upperCase($datapendidikan['tahunlulussma'])?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>4. Nilai Rata-rata Rapor SLTA</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<tr>
									<td>KELAS</td>
									<td></td>
									<td>Smt 1</td>
									<td>Smt 2</td>
								</tr>
								<tr>
									<td><strong>Kelas X</strong></td>
									<td>:</td>
									<td><div class="boxrapor"></div></td>
									<td><div class="boxrapor"></div></td>
								</tr>
								<tr>
									<td><strong>Kelas XI</strong></td>
									<td>:</td>
									<td><div class="boxrapor"></div></td>
									<td><div class="boxrapor"></div></td>
								</tr>
								<tr>
									<td><strong>Kelas XII</strong></td>
									<td>:</td>
									<td><div class="boxrapor"></div></td>
									<td></td>
								</tr>
								<tr>
									<td><strong>Rata-rata</strong></td>
									<td>:</td>
									<td colspan="2"><div class="boxrapor"></div></td>
								</tr>

							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>5. Organisasi yang pernah diikuti di sekolah atau di luar sekolah</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table class="table" border="1" width="100%">
								<thead>
									<tr>
										<th width="20">No.</th>
										<th>Nama Organisasi</th>
										<th>Jabatan</th>
										<th>Jenis Kegiatan</th>
									</tr>
								</thead>
								<?php
								foreach($dataorganisasi as $key => $row){
								?>
									<tr>
										<td><?=($key+1)?></td>
										<td><?=$row['namaorganisasi']?></td>
										<td><?=$row['jabatanorganisasi']?></td>
										<td><?=$row['jeniskegiatan']?></td>
									</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>6. Prestasi dalam Perlombaan/Kejuaraan sejak SMP sampai SMA</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table>
								<?php
								foreach($dataprestasi as $key => $row){
								?>
									<tr>
										<td><strong><?=($key+1)?>. </strong></td>
										<td><strong>Tingkat</strong></td>
										<td>:</td>
										<td><?=$row['namatingkatprestasi']?></td>
									</tr>
									<tr>
										<td></td>
										<td><strong>Nama Lomba</strong></td>
										<td>:</td>
										<td><?=$row['namaprestasi']?></td>
									</tr>
									<tr>
										<td></td>
										<td><strong>Peringkat/Juara</strong></td>
										<td>:</td>
										<td><?=$row['namakategoriprestasi']?></td>
									</tr>
									<tr>
										<td></td>
										<td><strong>Tempat</strong></td>
										<td>:</td>
										<td><?=$row['tempat']?></td>
									</tr>
									<tr>
										<td></td>
										<td><strong>Tahun</strong></td>
										<td>:</td>
										<td><?=$row['tahun']?></td>
									</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>7. Pelatihan/Seminar/Kursus</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table class="table" border="1" width="100%">
								<thead>
									<tr>
										<th width="20">No.</th>
										<th>Nama Pelatihan</th>
										<th>Lembaga</th>
										<th>Tahun</th>
									</tr>
								</thead>
								<?php
								foreach($datapelatihan as $key => $row){
								?>
									<tr>
										<td><?=($key+1)?></td>
										<td><?=$row['namapelatihan']?></td>
										<td><?=$row['lembaga']?></td>
										<td><?=$row['tahun']?></td>
									</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="5"><strong>7. Pengalaman Kerja</strong></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="5">
							<table class="table" border="1" width="100%">
								<thead>
									<tr>
										<th width="20">No.</th>
										<th>Nama Perusahaan</th>
										<th>Bidang</th>
										<th>Jabatan</th>
									</tr>
								</thead>
								<?php
								foreach($datakerja as $key => $row){
								?>
									<tr>
										<td><?=($key+1)?></td>
										<td><?=$row['namaperusahaan']?></td>
										<td><?=$row['bidang']?></td>
										<td><?=$row['jabatan']?></td>
									</tr>
								<?php } ?>
							</table>
						</td>
					</tr>

					<tr class="box">
						<td colspan="3">
							<b >D. Esay Pengembangan Potensi Diri.</b>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<?php
							echo $datapendaftar['potensidiri'];
							?>
						</td>
					</tr>

				</table>
			</div>
			<br>
			<div id="foot">
				<table class="tb_foot tb_foot_ttd" width="<?= $p_tbwidth ?>">
					<tr>
						<td width="<?= $p_tbwidth/3 ?>"></td>
						<td width="<?= $p_tbwidth/3 ?>"></td>
						<td width="<?= $p_tbwidth/3 ?>">Jakarta, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
					</tr>
					<tr>
						<td width="<?= $p_tbwidth/3 ?>"></td>
						<td width="<?= $p_tbwidth/3 ?>"></td>
						<td width="<?= $p_tbwidth/3 ?>">Kabiro Pengembangan Kerjasama Institusi</td>
					</tr>
					<tr>
						<td colspan="3" height="50">&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td><?=$a_pejabat['namabeasiswa']?><hr>Nip. <?=$a_pejabat['nipbeasiswa']?></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</table>
				<br>
			</div>
		</center>
		
		<div style="page-break-after: always;"></div>
	<?php
	}
	?>
 </body>
 </html>
