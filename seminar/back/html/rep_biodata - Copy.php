<?
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once($conf['ui_dir'].'u_form.php');
	require_once($conf['model_dir'].'m_biodata.php');
	require_once($conf['model_dir'].'m_smu.php');
	require_once($conf['model_dir'].'m_combo.php');
	require_once(Route::getModelPath('pendaftar'));
	$nopendaftar=$_POST['filval'];
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	$data=mPendaftar::getBiodata($conn,$nopendaftar);
	
	$pendidikan=mCombo::pendidikan($conn);
	
	$kotaprop = explode('#',Modul::getKotaProp($conn,$data['kodekota'],$data['kodepropinsi']));
	$kotapropdom = explode('#',Modul::getKotaProp($conn,$data['kodekotadomisili'],$data['kodepropinsidomisili']));
	$kotaproportu = explode('#',Modul::getKotaProp($conn,$data['kodekotaortu'],$data['kodepropinsiortu']));
	$tglujian = Modul::tglUjian($conn,$data['jalurpenerimaan']);
    
	$conn->debug = false;
	// ambil data sma
	// $rs_sma = $conn->Execute("select * from lv_smu");
	$rs_sma = mSmu::getSmu();
	$list_smu = array();
	$list_alamatsmu = array();
	$list_tlpsmu = array();
	while($row = $rs_sma->FetchRow()){
		$list_smu[$row['idsmu']] = $row['namasmu'];
		$list_alamatsmu[$row['idsmu']] = $row['alamatsmu'];
		$list_tlpsmu[$row['idsmu']] = $row['telpsmu'];
	}
	
	$mprop = mCombo::propinsi($conn);
	$mkota = mCombo::getKota();
	$propinsi = $mprop[$data['kodepropinsilahir']];
	$kota = $mkota[$data['kodekotalahir']];
	$propinsi_ayah = $mprop[$data['kodepropinsilahirayah']];
	$kota_ayah = $mkota[$data['kodekotalahirayah']];
	$propinsi_ibu = $mprop[$data['kodepropinsilahiribu']];
	$kota_ibu = $mkota[$data['kodekotalahiribu']];
	
	$saudara=mPendaftar::saudara($conn,$nopendaftar);
	$pend_formal=mPendaftar::pendFormal($conn,$nopendaftar);
	$pend_nonformal=mPendaftar::pendNonFormal($conn,$nopendaftar);
	$organisasi=mPendaftar::organisasi($conn,$nopendaftar);
	$prestasi_akad=mPendaftar::prestasiAkad($conn,$nopendaftar);
	$prestasi_nonakad=mPendaftar::prestasiNonAkad($conn,$nopendaftar);
	$status_keluarga=mCombo::statusKeluarga();
	$p_namafile = 'formulir_'.$nopendaftar;
	Page::setHeaderFormat($r_format,$p_namafile);
?>

<!DOCTYPE html>
    <html>
	<head>
		<title>Cetak Formulir Pendaftaran</title>   
		<link rel="icon" type="image/x-icon" href="images/favicon.png">
		<link href="style/daftar.css" rel="stylesheet" type="text/css">
		<link href="style/style.css" rel="stylesheet" type="text/css">
		<style>.label{ padding-left:15px}</style>
	</head>
	<body style="background: white" onLoad="window.print();">
		<center>
			<div style="border:none; width: 650px; height: 500px;">
				<table width="100%" cellspacing=0 style="padding:20px;border:groove">
					<tr><td>
						<table width="100%" style="border-bottom:1px solid #000000">
							<tr style="background: #ffffff;">
								  <td style="padding:0 20px; width:70px"><img width=80 height=80 src="images/logo.jpg" width="100"></td>
								  <td nowrap >
									<p>
										<span style="font-size: 1;">KEMENTRIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
								<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL SURABAYA</span><br>
								<span style="font-size: 1;">Jalan Arjuna Utara No.9, Kebon jeruk-Jakarta Barat 11510</span><br>
								<span style="font-size: 1;">021-5674223 (hunting) 021-5682510 (direct) Fax: 021-5674248 Website:www.esaunggul.ac.id, email:info@esaunggul.ac.id</span>

									</p>
								  </td>  
								  <td>
									<div style="position:relative;left:20px;top:125px"><img width="90" height="110" id="imgfoto" border="1" src="<?= uForm::getPathImageMahasiswa($conn,$data['nopendaftar'],true)?>" style="cursor:pointer"></div>
									<?//= uForm::getImageMahasiswa($conn,$data['nopendaftar'],true) ?>
								  </td>  
							</tr>
						</table>
					</td></tr>
					<tr>
						<td colspan=2 align="center">
							<table border=0 width="100%">
								<tr>
									<td colspan=2 valign="top" nowrap align="center" valign="middle">
										<font size=3><b>BIODATA MAHASISWA BARU UNUSA<BR>TAHUN AKADEMIK <?= $data['periodedaftar']."-".($data['periodedaftar']+1)?></b></font>
									</td>
								</tr>
								<tr><td colspan=2>&nbsp;</td></tr>
								<tr>
									<td colspan=2><b>A. DATA PENERIMAAN</b></td>
								</tr>
								<tr>
									<td  width="170" class="label">No. Pendaftaran/No. Tes</td>
									<td>: <?= $data['nopendaftar']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Program Studi</td>
									<td>: <?= !empty($data['prodipindahan'])?$data['prodipindahan']:$data['jurusan']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Fakultas</td>
									<td>: <?= !empty($data['fakpindahan'])?$data['fakpindahan']:$data['fakultas']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Program / Jalur Seleksi</td>
									<td>: <?= $data['jalurpenerimaan']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Gelombang</td>
									<td>: <?= $data['namagelombang']?></td>
								</tr>
								<tr>
									<td colspan=2><b>B. DATA MAHASISWA</b></td>
								</tr>
								<tr>
									<td  width="170" class="label">Nama Lengkap</td>
									<td>: <?= $data['namalengkap']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Nama Panggilan</td>
									<td>: <?= $data['nama']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Tempat/Tgl. Lahir</td>
									<td>: <?= $kota?> / <?= !empty($data['tgllahir'])?date('d-m-Y',strtotime($data['tgllahir'])):''?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Usia Per 1 Sept. <?=$data['periodedaftar']?></td>
									<td>: <?= $data['usia_1sept']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Jenis Kelamin</td>
									<td>: <?if($data['sex']=='L') echo 'Laki-Laki'; else if($data['sex']=='P') echo 'Perempuan';?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Anak Ke-</td>
									<td>: <?= $data['anakke']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Agama</td>
									<td>: <?= mBiodata::getAgama($conn,$data['kodeagama'])?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Status Kawin</td>
									<td>: <?=$data['statusnikah']=='0'?'Belum Kawin':'Kawin'?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Warga Negara</td>
									<td>: <?= $data['kodewn']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Golongan Darah</td>
									<td>: <?= $data['goldarah']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Alamat Asal</td>
									<td>: Jln. <?= $data['jalan'] ?> RT. <?= $data['rt'] ?> RW. <?= $data['rw'] ?> Kelurahan <?= $data['kel'] ?>
										<br>Kecamatan <?= $data['kec'] ?>, <?= $kotaprop[0] ?>, <?= $kotaprop[1] ?>
										
									</td>
								</tr>
								<tr>
									<td  width="170" class="label">Alamat Domisili</td>
									<td>: Jln. <?= $data['jalandomisili'] ?> RT. <?= $data['rtdomisili'] ?> RW. <?= $data['rwdomisili'] ?>  Kelurahan <?= $data['keldomisili'] ?>
										<br>Kecamatan <?= $data['kecdomisili'] ?>, <?= $kotapropdom[0] ?>, <?= $kotapropdom[1] ?>
										
									</td>
								</tr>
								<tr>
									<td  width="170" class="label">Jarak Rumah Ke Kampus</td>
									<td>: <?= $data['jarakrumah']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Transportasi yang digunakan</td>
									<td>: <?= $data['transportasi']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">No. Telp. / HP</td>
									<td>: <?= $data['telp']?> / <?= $data['hp']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Identitas (KTP)</td>
									<td>: <?= $data['nomorktp']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Hobi</td>
									<td>: <?= $data['hoby']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Cita Cita</td>
									<td>: <?= $data['cita2']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Email</td>
									<td>: <?= $data['email']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Facebook</td>
									<td>: <?= $data['facebook']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Twitter</td>
									<td>: -</td>
								</tr>
								<tr>
									<td  width="170" class="label">Ukuran Almamater</td>
									<td>: <?= $data['ukuranalmamater']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">No HP teman</td>
									<td>: <?= $data['nohpteman']?></td>
								</tr>
								<tr>
									<td colspan=2 ><b>C. DATA KELUARGA</b></td>
								</tr>
								<tr>
									<td colspan=2 class="label"><b>Ayah</b></td>
								</tr><tr>
									<td  width="170" class="label">Nama Lengkap</td>
									<td>: <?= $data['namaayah']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Tempat/Tanggal Lahir</td>
									<td>: <?= $propinsi_ayah.','.$kota_ayah?> / <?= !empty($data['tgllahirayah'])?date('d-m-Y',strtotime($data['tgllahirayah'])):''?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Pekerjaan</td>
									<td>: <?= mBiodata::getPekerjaan($conn, $data['kodepekerjaanayah'])?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Pendidikan Terakhir</td>
									<td>: <?= $pendidikan[$data['kodependidikanayah']]?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Status</td>
									<td>: <?= $data['statusayahkandung']=='0'?'Tiri':'Kandung'?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Alamat</td>
									<td>: Jln. <?= $data['jalanortu'] ?> RT. <?= $data['rtortu'] ?> RW. <?= $data['rwortu'] ?> <?= $kotaproportu[0] ?> <?= $kotaproportu[1] ?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Penghasilan Ayah/Bulan</td>
									<td>: <?= Modul::formatNumber($data['pendapatanayah'],'',true);?></td>
								</tr>
								<tr>
									<td  width="170" class="label">No Telp/HP</td>
									<td>: <?= $data['telportu']?></td>
								</tr>
								<tr>
									<td colspan=2 class="label"><b>Ibu</b></td>
								</tr>
								<tr>
									<td  width="170" class="label">Nama Lengkap</td>
									<td>: <?= $data['namaibu']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Tempat/Tanggal Lahir</td>
									<td>: <?= $propinsi_ibu.','.$kota_ibu?> / <?= !empty($data['tgllahiribu'])?date('d-m-Y',strtotime($data['tgllahiribu'])):''?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Pekerjaan</td>
									<td>: <?= mBiodata::getPekerjaan($conn, $data['kodepekerjaanibu'])?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Pendidikan Terakhir</td>
									<td>: <?= $pendidikan[$data['kodependidikanayah']]?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Status</td>
									<td>: <?= $data['statusibukandung']=='0'?'Tiri':'Kandung'?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Alamat</td>
									<td>: Jln. <?= $data['jalanortu'] ?> RT. <?= $data['rtortu'] ?> RW. <?= $data['rwortu'] ?> <?= $kotaproportu[0] ?> <?= $kotaproportu[1] ?></td>
								</tr><tr>
									
									
									<td  width="170" class="label">Penghasilan Ibu/Bulan</td>
									<td>: <?= Modul::formatNumber($data['pendapatanibu'],'',true);?></td>
								</tr>
								<tr>
									<td  width="170" class="label">No. Telp</td>
									<td>: <?= $data['telportu']?></td>
								</tr>
								<tr>
									<td colspan=2 class="label"><b>Wali (JIka Ada)</b></td>
								</tr>
								<tr>
									<td  width="170" class="label">Nama Wali</td>
									<td></td>
								</tr>
								<tr>
									<td  width="170" class="label">Alamat</td>
									<td></td>
								</tr>
								<tr>
									<td  width="170" class="label">No Telephon</td>
									<td></td>
								</tr>
								<tr>
									<td  width="170" class="label">Status Hubungan</td>
									<td></td>
								</tr>
								<tr>
									<td colspan=2 class="label"><b>Data Saudara</b></td>
								</tr>
								<tr>
									<td colspan=2 class="label">
										<table border="1" style="border-collapse:collapse" bordercolor="black" cellpadding="2">
											<tr>
												<td><b>No</b></td>
												<td><b>Nama Saudara</b></td>
												<td><b>tempat / Tanggal Lahir</b></td>
												<td><b>Pendidikan</b></td>
												<td><b>Status</b></td>
											</tr>
											<?php 
											$no=0;
											foreach($saudara as $data_saudara) {
											$no++;	
											?>
											<tr>
												<td><?=$no?></td>
												<td><?=$data_saudara['namasaudara']?></td>
												<td><?=$mkota[$data_saudara['kodekotasaudara']]?>/<?=Date::indoDate($data_saudara['tgllahirsaudara'],false)?></td>
												<td><?=$pendidikan[$data_saudara['kodependidikan']]?></td>
												<td><?=$status_keluarga[$data_saudara['status']]?></td>
											</tr>
											<?php }?>
											<?php if($no==0) { ?>
											<tr align="center">
												<td colspan="5">Data Kosong</td>
											</tr>
											<?php } ?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan=2 ><b>D. DATA ASAL SEKOLAH</b></td>
								</tr>
								
								<tr>
									<td  width="170" class="label">Nama SMA/SMK/MA</td>
									<td>: <?= $list_smu[$data['asalsmu']]?></td>
								</tr><tr>
									<td  width="170" class="label">Jurusan</td>
									<td>: <?= $data['jurusansmaasal']?></td>
								</tr><tr>
									<td  width="170" class="label">Alamat Sekolah</td>
									<td>: <?= $list_alamatsmu[$data['asalsmu']]?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Tahun Lulus</td>
									<td>: <?= $data['thnlulussmaasal']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Nomor Ijasah</td>
									<td>: <?= $data['noijasahsmu']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Nilai Ijasah / Transkrip</td>
									<td>: <?= $data['nemsmu']?></td>
								</tr>
								<tr>
									<td colspan=2><b>E. DATA PENDIDIKAN / PRESTASI</b></td>
								</tr>
								<tr>
									<td colspan=2 class="label"><b>Riwayat Pendidikan Formal</b></td>
								</tr>
								<tr>
									<td colspan=2 class="label">
										<table border="1" style="border-collapse:collapse" bordercolor="black" cellpadding="2">
											<tr>
												<td><b>No</b></td>
												<td><b>Nama Pendidikan</b></td>
												<td><b>tempat Pendidikan</b></td>
												<td><b>Tahun Masuk</b></td>
												<td><b>Tahun Lulus</b></td>
											</tr>
											<?php 
											$no=0;
											foreach($pend_formal as $data_pendformal) {
											$no++;
											?>
											<tr>
												<td><?=$no?></td>
												<td><?=$data_pendformal['namapend']?></td>
												<td><?=$data_pendformal['tempatpend']?></td>
												<td><?=$data_pendformal['tahunmasuk']?></td>
												<td><?=$data_pendformal['tahunlulus']?></td>
											</tr>
											<?php }?>
											<?php if($no==0) { ?>
											<tr align="center">
												<td colspan="5">Data Kosong</td>
											</tr>
											<?php } ?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan=2 class="label"><b>Riwayat Pendidikan Nonformal / Pelatuhan, Kursus Yang pernah Diidkuti</b></td>
								</tr>
								<tr>
									<td colspan=2 class="label">
										<table border="1" style="border-collapse:collapse" bordercolor="black" cellpadding="2">
											<tr>
												<td><b>No</b></td>
												<td><b>Nama Jenis Pendidikan</b></td>
												<td><b>Tingkat</b></td>
												<td><b>Tahun</b></td>
												
											</tr>
											<?php 
											$no=0;
											foreach($pend_nonformal as $data_pendnonformal) {
											$no++;
											?>
											<tr>
												<td><?=$no?></td>
												<td><?=$data_pendnonformal['namapelatihan']?></td>
												<td><?=$data_pendnonformal['tingkatpelatihan']?></td>
												<td><?=$data_pendnonformal['tahun']?></td>
												
											</tr>
											<?php }?>
											<?php if($no==0) { ?>
											<tr align="center">
												<td colspan="5">Data Kosong</td>
											</tr>
											<?php } ?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan=2 class="label"><b>Pengalaman Organisasi</b></td>
								</tr>
								<tr>
									<td colspan=2 class="label">
										<table border="1" style="border-collapse:collapse" bordercolor="black" cellpadding="2">
											<tr>
												<td><b>No</b></td>
												<td><b>Nama Organisasi</b></td>
												<td><b>Jabatan</b></td>
												<td><b>Tahun</b></td>
												
											</tr>
											<?php 
											$no=0;
											foreach($organisasi as $data_organisasi) {
											$no++;	
											?>
											<tr>
												<td><?=$no?></td>
												<td><?=$data_organisasi['namaorganisasi']?></td>
												<td><?=$data_organisasi['jabatan']?></td>
												<td><?=$data_organisasi['tahun']?></td>
												
											</tr>
											<?php }?>
											<?php if($no==0) { ?>
											<tr align="center">
												<td colspan="5">Data Kosong</td>
											</tr>
											<?php } ?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan=2 class="label"><b>Prestasi Akademik</b></td>
								</tr>
								<tr>
									<td colspan=2 class="label">
										<table border="1" style="border-collapse:collapse" bordercolor="black" cellpadding="2">
											<tr>
												<td><b>No</b></td>
												<td><b>Nama Prestasi Yang Diraih</b></td>
												<td><b>Juara</b></td>
												<td><b>Tingkat</b></td>
												<td><b>Tahun</b></td>
												
											</tr>
											<?php 
											$no=0;
											foreach($prestasi_akad as $data_prestasiakad) {
											$no++;	
											?>
											<tr>
												<td><?=$no?></td>
												<td><?=$data_prestasiakad['namaprestasi']?></td>
												<td><?=$data_prestasiakad['juara']?></td>
												<td><?=$data_prestasiakad['tingkat']?></td>
												<td><?=$data_prestasiakad['tahun']?></td>
												
											</tr>
											<?php }?>
											<?php if($no==0) { ?>
											<tr align="center">
												<td colspan="5">Data Kosong</td>
											</tr>
											<?php } ?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan=2 class="label"><b>Prestasi Non Akademik</b></td>
								</tr>
								<tr>
									<td colspan=2 class="label">
										<table border="1" style="border-collapse:collapse" bordercolor="black" cellpadding="2">
											<tr>
												<td><b>No</b></td>
												<td><b>Nama Prestasi Yang Diraih</b></td>
												<td><b>Juara</b></td>
												<td><b>Tingkat</b></td>
												<td><b>Tahun</b></td>
												
											</tr>
											<?php 
											$no=0;
											foreach($prestasi_nonakad as $data_prestasinonakad) {
											$no++;
											?>
											<tr>
												<td><?=$no?></td>
												<td><?=$data_prestasinonakad['namaprestasi']?></td>
												<td><?=$data_prestasinonakad['juara']?></td>
												<td><?=$data_prestasinonakad['tingkat']?></td>
												<td><?=$data_prestasinonakad['tahun']?></td>
												
											</tr>
											<?php }?>
											<?php if($no==0) { ?>
											<tr align="center">
												<td colspan="5">Data Kosong</td>
											</tr>
											<?php } ?>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan=2 ><b>F. INFORMASI LAIN</b></td>
								</tr>
								<tr>
									<td  width="170" class="label">Tempat Bekerja</td>
									<td>: <?= $data['tempatbekerja']?></td>
								</tr>
								<tr>
									<td  width="170" class="label">Biaya Dari Instansi / Beasiswa</td>
									<td>: <?= $data['biayakuliah']?></td>
								</tr>
								
								
								<tr><td colspan=2>&nbsp;</td></tr>
								<tr align="center">
									<td colspan=2><b>Dengan ini, saya yang bertanda tangan dibawah ini menyatakan bahwa data diatas adalah benar.</b></td>
								</tr>
								
								<tr><td colspan=2>&nbsp;</td></tr>
								<tr>
									<td colspan=2>
										<table width="100%">
										<tr align="center">
											<td width="350">&nbsp;</td>
											<td>Surabaya, <?= date('d-m-Y')?> <br>Tanda Tangan Mahasiswa</td>
										</tr>
										</table>
									</td>
								</tr>
								<tr><td colspan=2>&nbsp;</td></tr>
								<tr><td colspan=2>&nbsp;</td></tr>
								<tr>
									<td colspan=2>
										<table width="100%">
										<tr align="center">
											<td width="350">&nbsp;</td>
											<td><u>(&nbsp; <?= $data['nama']?> &nbsp;)</u></td>
										</tr>
										</table>
									</td>
								</tr>
								
								
							</table>
						</td>
					</tr>
				</table>
			</div>
		</center>
	</body>
</html>
