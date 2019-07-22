<?
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once($conf['ui_dir'].'u_form.php');
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('smu'));
	require_once(Route::getModelPath('biodata'));
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$data= mPendaftar::getData($conn,Modul::getUserIDFront());
	$data_agama = mbiodata::agama($conn);
	$data_stnikah = mbiodata::statusNikah($conn);
	
	$p_namafile = 'formulir_pendaftaran_'.Modul::getUserIDFront();
	Page::setHeaderFormat($r_format,$p_namafile);
	
?>
 <html>
	 <title>Formulir Pendaftaran</title>
	 <head>
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
			 .keterangan {background:#B0B0B0; width:100%; height:50px; margin: 10px 0 10px 0;padding:10px; font-size:12px}
			 .footer {background:#B0B0B0; width:100%; height:5cm; margin: 10px 0 10px 0;padding:10px; font-size:12px}
		 </style>
	</head>
	<body>
		<center>
			<div class="content">
				<?/* header atas*/?>
				<table width="100%" >
					<tr>
						<td width="40px"><img src="../front/images/logo.png"></td>
						<td>U n i v e r s i t a s<br>
						<span class="header">Esa Unggul</span><br>
						<span class="subheader">FORMULIR PENDAFTARAN MAHASISWA BARU</span>
						<div align="right"><i>Registration form</i></div>
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
						<b>DATA CALON MAHASISWA</b>
						</td>
					</tr>
					<tr>
						<td width="20">1.</td>
						<td colspan="2">Nama Calon Mahasiswa (Sesuai dengan Akte Kelahitan atau Surat Ganti Nama) :</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2"><?= cStr::upperCase($data['gelardepan'].' '.$data['nama'].''.$data['gelarbelakang'])?></td>
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Alamat Lengkap :</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2"><?= cStr::upperCase($data['jalan']) ?></td>
					</tr>
					<tr>
						<td width="20">3.</td>
						<td colspan="2"><div class="sm_box">Nomor Rumah :</div> <div class="sm_box">RT. <?= cStr::upperCase($data['rt']) ?></div> <div class="sm_box">RW. <?= cStr::upperCase($data['rw']) ?></div> </td>
					</tr>
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Kelurahan : <?= cStr::upperCase($data['kel']) ?></td>
					</tr>
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Kecamatan : <?= cStr::upperCase($data['kec']) ?></td>
					</tr>
					<tr>
						<td width="20">6.</td>
						<td colspan="2"><div class="md_box">Kota : <?= cStr::upperCase($data['namakota']) ?></div> <div class="md_box">Kode Pos : <?= cStr::upperCase($data['kodepos']) ?></div></td>
					</tr>
					<tr>
						<td width="20">7.</td>
						<td colspan="2"><div class="md_box">No Telp. (Kode Area - No.Telp) : </div> <div class="md_box">No.Handphone : </div></td>
					</tr>					
					<tr>
						<td>&nbsp;</td>
						<td colspan="2"><div class="md_box"><?= cStr::upperCase(substr_replace($data['telp'], '-', 3, 0)) ?>/<?= cStr::upperCase(substr_replace($data['telp2'], '-', 3, 0)) ?></div> 
						<div class="md_box"><?= cStr::upperCase($data['hp']) ?>/<?= cStr::upperCase($data['hp2']) ?></div></td>
					</tr>			
					<tr>
						<td width="20">8.</td>
						<td colspan="2">Alamat Email : <?= cStr::upperCase($data['email']) ?>/<?= cStr::upperCase($data['email2']) ?></td>
					</tr>			
					<tr>
						<td width="20">9.</td>
						<td colspan="2">
							<div class="sm_box">Kewarganegaraan :</div> 
							<? $datawn = array('WNI'=>'','WNA'=>'');
								foreach($datawn as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="v_sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('kodewn',$arrdatawn,$data['kodewn'],false) ?></div><?= $key?>	
									</div>								
							<?}?>	
							<div class="v_sm_box">10. Status Kerja :</div>
							<div class="sm_box">
								<div class="boxcheck"></div>Belum Bekerja
							</div>
								<div class="sm_box"><div class="boxcheck"></div>Sudah Bekerja
							</div>
						</td>
					</tr>
					<tr>
						<td width="20">11.</td>
						<td colspan="2">
							<div class="v_sm_box">Jenis Kelamin :</div> 
							<? $datajk = array('L'=>'Laki-laki','P'=>'Perempuan');
								foreach($datajk as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="v_sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('sex',$arrdatawn,$data['sex'],false) ?></div><?= $val?>
									</div>								
							<?}?>
						</td>
					</tr>
					<tr>
						<td width="20">12.</td>
						<td colspan="2">
							<div class="v_sm_box">Agama :</div>
								<?
								foreach($data_agama as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="v_sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('kodeagama',$arrdatawn,$data['kodeagama'],false) ?></div><?= $val?>
									</div>								
								<?}?>
								
						</td>
					</tr>
					<tr>
						<td width="20">13.</td>
						<td colspan="2">
							<div class="v_sm_box">Status Perkawinan :</div> 
								<?
								foreach($data_stnikah as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="v_sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('statusnikah',$arrdatawn,$data['statusnikah'],false) ?></div><?= $val?>
									</div>								
								<?}?>
						</td>
					</tr>
					<tr>
						<td width="20">14.</td>
						<td colspan="2">Tanggal Lahir (DD-MM-YYYY) : <?= cStr::formatDate($data['tgllahir'])?></td>
					</tr>												
					<tr>
						<td width="20">15.</td>
						<td colspan="2">Kota/Kabupaten Tempat Lahir : <?= cStr::upperCase($data['namakotalahir'])?></td>
					</tr>
					<tr>
						<td width="20">16.</td>
						<td colspan="2">Propinsi Tempat Lahir : <?= cStr::upperCase($data['namapropinsi'])?></td>
					</tr>
					<tr>
						<td width="20">17.</td>
						<td colspan="2">Negara Tempat Lahir : <?= cStr::upperCase("INDONESIA")?></td> <? // hardcode negara :D?>
					</tr>					
					<tr>
						<td width="20">18.</td>
						<td colspan="2">
							<div>
								<div style="width:200px; height:50px; float:left;">
								Jurusan yang dipilih di UEU : 
								</div>
								<div style="width:500px;">
								1. <?= cStr::upperCase($data['pil1'])?><br>
								2. <?= cStr::upperCase($data['pil2'])?><br>
								3. <?= cStr::upperCase($data['pil3'])?><br>
								</div>
							</div>
						</td>
					</tr>					
					<tr>
						<td width="20">19.</td>
						<td colspan="2"><div class="lg_box">Apakah anda memiliki kelainan fisik yang memerlukan pelayanan khusus ?</div>  
						<div class="v_sm_box"><div class="boxcheck"></div>Ya</div>
						<div class="v_sm_box"><div class="boxcheck"></div>Tidak</div>
					</tr>					
					<tr>
						<td colspan="3"><div class="keterangan">Jika Ya Mohon Jelaskan : </div></td>
					</tr>					

					<tr>
						<td colspan="3"> <b>DATA SEKOLAH </b></td>
					</tr>					
					<tr>
						<td width="20">1.</td>
						<td colspan="2">Nama Sekolah : <?= cStr::upperCase($data['namasmu'])?></td>
					</tr>					
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Kode Sekolah : <?= cStr::upperCase($data['idsmu'])?></td>
					</tr>					
					<tr>
						<td width="20">3.</td>
						<td colspan="2">Jurusan di Sekolah : <?= cStr::upperCase($data['jurusansmaasal'])?>
						<div style="page-break-after:always"></div>	
					
						</td>
					</tr>
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Alamat Sekolah : <?= cStr::upperCase($data['alamatsmu'])?></td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kota/Kabupaten : <?= cStr::upperCase($data['namakotasmu'])?></td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2">Propinsi : <?= cStr::upperCase($data['namapropinsismu'])?></td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="md_box">Negara : INDONESIA</div> <div class="md_box">Kode Pos :</div></td>
					</tr>			
					<tr>
						<td width="20">5.</td>
						<td colspan="2">No.Telp. (Kode area - No.Telp) : <?= cStr::upperCase(substr_replace($data['telpsmu'], '-', 3, 0)) ?></td>
					</tr>
					<tr>
						<td width="20">6.</td>
						<td colspan="2">
							<div class="sm_box">Negeri/Swasta :</div> 
									<div class="v_sm_box">
										<div class="boxcheck"></div>Negeri
									</div>
									<div class="v_sm_box">
										<div class="boxcheck"></div>Swasta
									</div> 
							<div class="v_sm_box">7. Tahun Lulus: <?= cStr::upperCase($data['thnlulussmaasal'])?></div>
						</td>
					</tr>	
					<tr>
						<td width="20">8.</td>
						<td colspan="2">
							<div class="v_sm_box">Basis Agama :</div> 
								<div class="v_sm_box">
									<div class="boxcheck"></div>Islam
								</div>
								<div class="v_sm_box">
									<div class="boxcheck"></div>Katolik
								</div> 
								<div class="v_sm_box">
									<div class="boxcheck"></div>Kristen
								</div> 
								<div class="v_sm_box">
									<div class="boxcheck"></div>Budha
								</div> 
								<div class="v_sm_box">
									<div class="boxcheck"></div>Hindu
								</div> 
								<div class="v_sm_box">
									<div class="boxcheck"></div>Konghuchu
								</div> 
								<div class="v_sm_box">
									<div class="boxcheck"></div>Lain nya
								</div> 
						</td>
					</tr>	
					<tr>
						<td colspan="3"> <b>DATA PEKERJAAN (diisi hanya bagi yang sudah bekerja) </b></td>
					</tr>
					<tr>
						<td width="20">1.</td>
						<td colspan="2">Nama Perusahaan : <?= cStr::upperCase($data['tempatbekerja'])?></td>
					</tr>						
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Alamat Lengkap :</td>
					</tr>						
					<tr>
						<td width="20">3.</td>
						<td colspan="2"><div class="sm_box">Nomor Kantor :</div> <div class="sm_box">RT. </div> <div class="sm_box">RW.</div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2">Kelurahan :</td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kota :</td>
					</tr>						
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Bagian :</td>
					</tr>						
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Jabatan :</td>
					</tr>						
					<tr>
						<td width="20">9.</td>
						<td colspan="2"><div class="lg_box">No Telp (kode Area - No Telp)</div>  
						<div class="sm_box">Bekerja Sejak Tahun</div>
					</tr>					
					<tr>
						<td colspan="3"> <b>DATA ORANG TUA WALI </b></td>
					</tr>
					<tr>
						<td colspan="3"><div class="sm_box"><div class="boxcheck"></div>Ayah</div><div class="sm_box"><div class="boxcheck"></div>Wali</div></td>
					</tr>						
					<tr>
						<td width="20">1.</td>
						<td colspan="2">
							<div class="sm_box">Status Ayah / Wali :</div> 
									<div class="sm_box">
										<div class="boxcheck"></div>Masih Hidup
									</div>
									<div class="sm_box">
										<div class="boxcheck"></div>Telah Meninggal
									</div> 
						</td>
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Nama Ayah/Wali : <?= cStr::upperCase($data['namaayah'])?></td>
					</tr>																										
					<tr>
						<td width="20">3.</td>
						<td colspan="2">Alamat Lengkap :</td>
					</tr>								
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="sm_box">Nomor Rumah :</div> <div class="sm_box">RT. </div> <div class="sm_box">RW.</div> </td>
					</tr>	
					<tr>
						<td width="20"></td>
						<td colspan="2">Kelurahan :</td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kecamatan :</td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kota :</td>
					</tr>
					<tr>
						<td width="20"></td></td>
						<td colspan="2"><div class="md_box">No Telp. (Kode Area - No.Telp) :</div> <div class="md_box">No.Handphone Ayah/Wali :</div></td>						
					</tr>					
					<tr>
						<td width="20"></td></td>
						<td colspan="2"><div class="md_box">&nbsp; </div> <div class="md_box"></div>&nbsp; </td>						
					</tr>					
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Alamat E-mail Ayah/Wali :</td>
					</tr>								
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Pekerjaan : <?= cStr::upperCase($data['kodepekerjaanayah'])?></td>
					</tr>					
					<tr>
						<td width="20">6.</td>
						<td colspan="2">Jabatan :</td>
					</tr>
					<tr>
						<td width="20">7.</td>
						<td colspan="2">Nama Perusahaan :</td>
					</tr>
					<tr>
						<td width="20">8.</td>
						<td colspan="2">Pendidikan : <?= cStr::upperCase($data['kodependidikanayah'])?></td>
					</tr>
					<tr>
						<td colspan="3"> <b>DATA IBU </b></td>
					</tr>
					<tr>
						<td width="20">1.</td>
						<td colspan="2">
							<div class="sm_box">Status Ibu :</div> 
									<div class="sm_box">
										<div class="boxcheck"></div>Masih Hidup
									</div>
									<div class="sm_box">
										<div class="boxcheck"></div>Telah Meninggal
									</div> 
						</td>
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Nama Ibu : <?= cStr::upperCase($data['namaibu'])?></td>
					</tr>																										
					<tr>
						<td width="20">3.</td>
						<td colspan="2">Alamat Lengkap :</td>
					</tr>								
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="sm_box">Nomor Rumah :</div> <div class="sm_box">RT. </div> <div class="sm_box">RW.</div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2">Kelurahan :</td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kecamatan :</td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kota :</td>
					</tr>
					<tr>
						<td width="20"></td></td>
						<td colspan="2"><div class="md_box">No Telp. (Kode Area - No.Telp) :</div> <div class="md_box">No.Handphone Ayah/Wali :</div></td>						
					</tr>					
					<tr>
						<td width="20"></td></td>
						<td colspan="2"><div class="md_box">&nbsp; </div> <div class="md_box"></div>&nbsp; </td>						
					</tr>					
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Alamat E-mail Ibu :</td>
					</tr>
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Pekerjaan : <?= cStr::upperCase($data['kodepekerjaanibu'])?></td>
					</tr>					
					<tr>
						<td width="20">6.</td>
						<td colspan="2">Jabatan :</td>
					</tr>
					<tr>
						<td width="20">7.</td>
						<td colspan="2">Nama Perusahaan :</td>
					</tr>
					<tr>
						<td width="20">8.</td>
						<td colspan="2">Pendidikan : <?= cStr::upperCase($data['kodependidikanibu'])?></td>
					</tr>
					<tr>
						<td colspan="3"> <b>DATA PERGURUAN TINGI (hanya diisi bagy yang pernah/sedang kuliah di perguruan tinggi lain) </b></td>
					</tr>						
					<tr>
						<td width="20">1.</td>
						<td colspan="2">Nama Perguruan Tinggi : <?= cStr::upperCase($data['ptasal'])?></td>
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Kota / Kabupaten : <?= cStr::upperCase($data['kodekotapt'])?></td>
					</tr>
					<tr>
						<td width="20">3.</td>
						<td colspan="2">Provinsi : <?= cStr::upperCase($data['kodekotapt'])?></td>
					</tr>
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Negara :</td>
					</tr>
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Fakultas/Jurusan : <?= cStr::upperCase($data['ptjurusan'])?></td>
					</tr>
					<tr>
						<td width="20">6.</td>
						<td colspan="2"><div class="md_box">Tahun Masuk Kuliah (YYY) :</div> <div class="md_box">7.Tahun Keluar Kuliah (YYYY) : <?= cStr::upperCase($data['ptthnlulus'])?></div></td>
					</tr>
					<tr>
						<td width="20">8.</td>
						<td colspan="2">
							<div class="sm_box">Semester Keluar : </div> 
							<div class="sm_box">9. SKS yang dicapai: <?= cStr::upperCase($data['sksasal'])?></div>
							<div class="sm_box">10. IPK Terakhir: <?= cStr::upperCase($data['ptipk'])?></div> 
						</td>
					</tr>
					<tr>
						<td colspan="3"> <b>ALASAN MEMILIH UEU</b></td>
					</tr>
					<tr>
						<td width="20">1.</td>
						<td colspan="2">Darimana Anda Mengetahui UEU ? :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Kunjungan Sekolah</div> 
							<div class="sm_box"><div class="boxcheck"></div>Media Online</div>
							<div class="sm_box"><div class="boxcheck"></div>Seminar</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Pameran Sekolah</div> 
							<div class="sm_box"><div class="boxcheck"></div>Website</div>
							<div class="sm_box"><div class="boxcheck"></div>Teman</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Iklan Cetak, <i>sebutkan</i></div> 
							<div class="sm_box"><div class="boxcheck"></div>Spanduk</div>
							<div class="sm_box"><div class="boxcheck"></div>Poster</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Data Langsung</div> 
							<div class="sm_box"><div class="boxcheck"></div>Pameran JHCC</div>
							<div class="sm_box"><div class="boxcheck"></div>Lain nya, <i>sebutkan</i></div> 
						</td>						
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Berikan urutan alasan dibawah ini, sesuai dengan urutan alasan anda masuk UEU :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Biaya Perkuliahan Terjangkau</div> 
							<div class="sm_box"><div class="boxcheck"></div>Kegiatan Mahasiswanya menarik</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Program akademik berkualitas</div> 
							<div class="sm_box"><div class="boxcheck"></div>Fasilitas IT yang lengkap (Wifi,Microsoft learning gateway, dll)</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Permintaan dari orang tua/diajak Saudara</div> 
							<div class="sm_box"><div class="boxcheck"></div>Diajak teman</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Akses Mudah</div> 
							<div class="sm_box"><div class="boxcheck"></div>Lain nya, sebutkan,</div>
						</td>						
					</tr>
					<tr>
						<td width="20">3.</td>
						<td colspan="2">Sebutkan media cetak yang sering anda baca ? :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Koran</div> 
							<div class="sm_box"><div class="boxcheck"></div>Tidak ada</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Majalah</div> 
							<div class="sm_box"><div class="boxcheck"></div>Lain nya, sebutkan,</div>
						</td>						
					</tr>
					<tr>
						<td width="20">4.</td>
						<td colspan="2"><div class="lg_box">Apakah saudara mendaftar di perguruan tinggi</div> <div class="v_sm_box"><div class="boxcheck"></div>Ya</div> <div class="v_sm_box"><div class="boxcheck"></div>Tidak</div> </td>
					</tr>					
					<tr>
						<td width="20">5.</td>
						<td colspan="2"><div class="lg_box">Apakah saudara mendaftar di perguruan tinggi swasta lainnya</div> <div class="v_sm_box"><div class="boxcheck"></div>Ya</div> <div class="v_sm_box"><div class="boxcheck"></div>Tidak</div> </td>
					</tr>
					<tr>
						<td width="20">6.</td>
						<td colspan="2"><div class="lg_box">Urutkan 5 Universitas Swasta yang menjadi pilihan anda termasuk UEU</div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="md_box">1. </div> <div class="md_box">4. </div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="md_box">2. </div> <div class="md_box">5. </div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="md_box">3. </div> </td>
					</tr>
					<tr>
						<td colspan="3"> <b>DATA PENDUKUNG</b></td>
					</tr>
					<tr>
						<td width="20">1.</td>
						<td colspan="2">Kuliah Di biayai oleh :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Orang tua</div> 
							<div class="sm_box"><div class="boxcheck"></div>Wali</div>
							<div class="sm_box"><div class="boxcheck"></div>Beasiswa</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Ikatan dinas</div> 
							<div class="sm_box"><div class="boxcheck"></div>Sendiri</div>
							<div class="sm_box"><div class="boxcheck"></div>Lainnya,</div> 
						</td>						
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Status tempat tinggal :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Orang tua</div> 
							<div class="sm_box"><div class="boxcheck"></div>Sendiri</div>
							<div class="sm_box"><div class="boxcheck"></div>Lainnya,</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Ikut saudara</div> 
							<div class="sm_box"><div class="boxcheck"></div>kost/sewa/kontrak</div>
						</td>						
					</tr>
					<tr>
						<td width="20">3.</td>
						<td colspan="2"><div class="md_box">Memiliki Komputer</div> <div class="v_sm_box"><div class="boxcheck"></div>Ya</div> <div class="v_sm_box"><div class="boxcheck"></div>Tidak</div> </td>
					</tr>
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Transportasi yang digunakan menuju kampus UEU ? :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Kendaraan Pribadi</div> 
							<div class="sm_box"><div class="boxcheck"></div>Kendaraan Umum</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Motor</div> 
							<div class="sm_box"><div class="boxcheck"></div>Lain nya,</div>
						</td>						
					</tr>
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Jenis Beasiswa (hanya diisi bagi penerima beasiswa lain luar UEU) ? :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Bakat</div> 
							<div class="sm_box"><div class="boxcheck"></div>Supersemar</div>
							<div class="sm_box"><div class="boxcheck"></div>Jurusan Langka</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"></div>Ikatan dinas</div> 
							<div class="sm_box"><div class="boxcheck"></div>BEasiswa Swasta</div>
							<div class="sm_box"><div class="boxcheck"></div>Lain nya,</div>
						</td>						
					</tr>
					<tr>
						<td width="20">6.</td>
						<td colspan="2">Jenis Beasiswa yang diperoleh dari UEU :</td>
					</tr>						
					<tr>
						<td width="20">7.</td>
						<td colspan="2">Prestasi yang pernah dicapai</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="md_box"><div class="boxcheck"></div>Olahraga, sebutkan</div> 
							<div class="md_box"><div class="boxcheck"></div>Juara Sekolah, sebutkan</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="md_box"><div class="boxcheck"></div>Kesenian, sebutkan</div> 
							<div class="md_box"><div class="boxcheck"></div>Lain - lain</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="md_box"><div class="boxcheck"></div>Olimpiade, sebutkan</div> 
						</td>						
					</tr>
					<tr>
						<td colspan="3"><div class="keterangan"><b>DEKLARASI </b> <br>
						Dengain ini saya menyatakan bahwa data-data yang saya isi adalah benar dan bersedia menerima sanksi yang diberikan apabila data-data tersebut tidak benar
						</div></td>
					</tr>
					<tr>
						<td colspan="3"><div class="footer">
							<div class="sm2_box">Jakarta, <br>pendaftar,<br><br><br><br> <br><br><br><br><br> ___________________</div>
							<div class="sm2_box" valign="center" style="border:solid 1px; height:4cm; width:3cm">templekan foto 3x4<br> (harus berkerah dan tanpa logo OSIS)</div>
							<div class="sm2_box" align="center" style="margin-left:150px" > 
								
								<table width="100%" style="text-align:center">
									<tr>
										<td colspan="2"><b>VALIDASI (diisi oleh petugas)</b></td>
									</tr>
									
									<tr>
										<td>Tanggal diterima <br><br><br></td>
										<td>Tanggal Diadakan <br><br><br></td>
									</tr>
									<tr>
										<td>Diterima oleh <br><br><br><br><br></td>
										<td>Diadakan oleh <br><br><br><br><br></td>
									</tr>
									<tr>
										<td>Staff registrasi</td>
										<td>Staff database administrasi</td>
									</tr>
									
								</table>
							</div>
						</div></td>
					</tr>					
					

				</table>
			</div>
		</center>
	</body>
</html>
