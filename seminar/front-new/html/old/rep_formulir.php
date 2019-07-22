
<?php
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once($conf['ui_dir'].'u_form.php');
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('kuisioner'));
	require_once(Route::getModelPath('smu'));
	require_once(Route::getModelPath('biodata'));
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$data= mPendaftar::getData($conn,Modul::getUserIDFront());
	$data_kuisioner = mKuisioner::getData($conn,Modul::getUserIDFront());
	$data_agama = mbiodata::agama($conn);
	$data_stnikah = mbiodata::statusNikah($conn);
	
	$arrPekerjaan = mCombo::pekerjaan($conn);
	$arrPendidikan = mCombo::pendidikan($conn);
	$arrAgama = mCombo::agama($conn);
	
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
						<td colspan="2"><?= cStr::upperCase($data['nama'])?></td>
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
						<td colspan="2"><div class="sm_box">Nomor Rumah : <?= cStr::upperCase($data['nomorrumah']) ?></div> <div class="sm_box">RT. <?= cStr::upperCase($data['rt']) ?></div> <div class="sm_box">RW. <?= cStr::upperCase($data['rw']) ?></div> </td>
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
						<td colspan="2"><div class="md_box">Kota : <?= cStr::upperCase($data['kodekota_text']) ?></div> <div class="md_box">Kode Pos : <?= cStr::upperCase($data['kodepos']) ?></div></td>
					</tr>
					<tr>
						<td width="20">7.</td>
						<td colspan="2"><div class="md_box">No Telp. (Kode Area - No.Telp) : </div> <div class="md_box">No.Handphone : </div></td>
					</tr>					
					<tr>
						<td>&nbsp;</td>
						<td colspan="2"><div class="md_box"><?= cStr::upperCase(substr_replace($data['telp'], '-', 3, 0)) ?></div> 
						<div class="md_box"><?= cStr::upperCase($data['hp']) ?></div></td>
					</tr>			
					<tr>
						<td width="20">8.</td>
						<td colspan="2">Alamat Email : <?= cStr::upperCase($data['email']) ?> </td>
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
							<div class="v_sm_box">10.Status Kerja:</div>
							<? $datakerja = array('0'=>'Belum Bekerja','-1'=>'Sudah bekerja');
								foreach($datakerja as $key => $val){ 
									$arrdatakerja = array($key=>'');
									?>
									<div class="sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('isbekerja',$arrdatakerja,$data['isbekerja'],false) ?></div><?= $val?>	
									</div>								
							<?}?>	

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
						<td colspan="2">Kota/Kabupaten Tempat Lahir : <?= cStr::upperCase($data['kodekotalahir_text'])?></td>
					</tr>
					<tr>
						<td width="20">16.</td>
						<td colspan="2">Propinsi Tempat Lahir : <?= cStr::upperCase($data['kodepropinsilahir_text'])?></td>
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
								1. <?= cStr::upperCase($data['namaunit'])?><br>
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
						<td colspan="2">Kode Sekolah : <?= cStr::upperCase($data['asalsmu'])?></td>
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
						<td colspan="2">Kota/Kabupaten : <?= cStr::upperCase($data['kodekotasmu_text'])?></td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2">Propinsi : <?= cStr::upperCase($data['propinsismu_text'])?></td>
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
								<?
								$jenissekolah = array(1=>'Swasta',2=>'Negeri');
								foreach($jenissekolah as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="v_sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('jenissekolah',$arrdatawn,$data['jenissekolah'],false) ?></div><?= $val?>
									</div>								
								<?}?>									
							<div class="sm_box" >7. Tahun Lulus: <?= cStr::upperCase($data['thnlulussmaasal'])?></div>
						</td>
					</tr>	
					<tr>
						<td width="20">8.</td>
						<td colspan="2">
							<div class="v_sm_box">Basis Agama :</div> 
								<?
								foreach($arrAgama as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="v_sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('basisagama',$arrdatawn,$data['kodeagamasekolah'],false) ?></div><?= $val?>
									</div>								
								<?}?>									
						</td>
					</tr>	
					<tr>
						<td colspan="3"> <b>DATA PEKERJAAN (diisi hanya bagi yang sudah bekerja) </b></td>
					</tr>
					<tr>
						<td width="20">1.</td>
						<td colspan="2">Nama Perusahaan : <?= cStr::upperCase($data['namaperusahaan'])?></td>
					</tr>						
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Alamat Lengkap :  <?= cStr::upperCase($data['alamatperusahaan'])?></td>
					</tr>						
					<tr>
						<td width="20">3.</td>
						<td colspan="2"><div class="sm_box">Nomor Kantor :</div> <div class="sm_box">RT.  <?= cStr::upperCase($data['rtkantor'])?></div> <div class="sm_box">RW. <?= cStr::upperCase($data['rwkantor'])?></div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2">Kelurahan :  <?= cStr::upperCase($data['kelkantor'])?></td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kota :  <?= cStr::upperCase($data['kodekotakantor_text'])?></td>
					</tr>						
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Bagian :  <?= cStr::upperCase($data['bagian'])?></td>
					</tr>						
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Jabatan :  <?= cStr::upperCase($data['jabatankerja'])?></td>
					</tr>						
					<tr>
						<td width="20">9.</td>
						<td colspan="2"><div class="lg_box">No Telp (kode Area - No Telp) : <?= cStr::upperCase($data['telpkantor'])?></div>  
						<div class="sm_box">Bekerja Sejak Tahun:  <?= cStr::upperCase($data['thnmasuk'])?></div>
					</tr>					
					<tr>
						<td colspan="3"> <b>DATA ORANG TUA WALI </b></td>
					</tr>
					<tr>
						<td colspan="3">
								<?
								$arrJeniswali = array(1=>'ayah',2=>'Wali');
								foreach($arrJeniswali as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('jeniswali',$arrdatawn,$data['jeniswali'],false) ?></div><?= $val?>
									</div>								
								<?}?>									

						</td>
					</tr>						
					<tr>
						<td width="20">1.</td>
						<td colspan="2">
							<div class="sm_box">Status Ayah / Wali :</div> 
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
						<td width="20">2.</td>
						<td colspan="2">Nama Ayah/Wali : <?= cStr::upperCase($data['namaayah'])?></td>
					</tr>																										
					<tr>
						<td width="20">3.</td>
						<td colspan="2">Alamat Lengkap : <?= cStr::upperCase($data['alamatayah'])?> </td>
					</tr>								
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="sm_box">Nomor Rumah :</div> <div class="sm_box">RT. <?= cStr::upperCase($data['rtayah'])?></div> <div class="sm_box">RW. <?= cStr::upperCase($data['rwayah'])?></div> </td>
					</tr>	
					<tr>
						<td width="20"></td>
						<td colspan="2">Kelurahan : <?= cStr::upperCase($data['kelayah'])?></td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kecamatan : <?= cStr::upperCase($data['kecayah'])?></td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kota :<?= cStr::upperCase($data['kodekotaayah_text'])?></td>
					</tr>
					<tr>
						<td width="20"></td></td>
						<td colspan="2"><div class="md_box">No Telp. (Kode Area - No.Telp) :</div> <div class="md_box">No.Handphone Ayah/Wali :</div></td>						
					</tr>					
					<tr>
						<td width="20"></td></td>
						<td colspan="2"><div class="md_box"><?= cStr::upperCase($data['telpayah'])?> </div> <div class="md_box"></div><?= cStr::upperCase($data['hpayah'])?></td>						
					</tr>					
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Alamat E-mail Ayah/Wali : <?= cStr::upperCase($data['emailayah'])?></td>
					</tr>								
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Pekerjaan : <?= cStr::upperCase($arrPekerjaan[$data['kodepekerjaanayah']])?></td>
					</tr>					
					<tr>
						<td width="20">6.</td>
						<td colspan="2">Jabatan : <?= cStr::upperCase($data['jabatankerjaayah'])?></td>
					</tr>
					<tr>
						<td width="20">7.</td>
						<td colspan="2">Nama Perusahaan : <?= cStr::upperCase($data['namaperusahaanayah'])?></td>
					</tr>
					<tr>
						<td width="20">8.</td>
						<td colspan="2">Pendidikan : <?= cStr::upperCase($arrPendidikan[$data['kodependidikanayah']])?></td>
					</tr>
					<tr>
						<td colspan="3"> <b>DATA IBU </b></td>
					</tr>
					<tr>
						<td width="20">1.</td>
						<td colspan="2">
							<div class="sm_box">Status Ibu :</div> 
								<?
								$datastatus = array(1=>'Masih Hidup',2=>'telah Meninggal');
								foreach($datastatus as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('jeniswali',$arrdatawn,$data['statusibu'],false) ?></div><?= $val?>
									</div>								
								<?}?>									
						</td>
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Nama Ibu : <?= cStr::upperCase($data['namaibu'])?></td>
					</tr>																										
					<tr>
						<td width="20">3.</td>
						<td colspan="2">Alamat Lengkap : <?= cStr::upperCase($data['alamatibu'])?></td>
					</tr>								
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="sm_box">Nomor Rumah :</div> <div class="sm_box">RT. <?= cStr::upperCase($data['rtibu'])?></div> <div class="sm_box">RW. <?= cStr::upperCase($data['rwibu'])?></div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2">Kelurahan : <?= cStr::upperCase($data['kelibu'])?></td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kecamatan : <?= cStr::upperCase($data['kecibu'])?></td>
					</tr>						
					<tr>
						<td width="20"></td>
						<td colspan="2">Kota : <?= cStr::upperCase($data['kodekotaibu_text'])?></td>
					</tr>
					<tr>
						<td width="20"></td></td>
						<td colspan="2"><div class="md_box">No Telp. (Kode Area - No.Telp) :</div> <div class="md_box">No.Handphone Ibu :</div></td>						
					</tr>					
					<tr>
						<td width="20"></td></td>
						<td colspan="2"><div class="md_box"><?= cStr::upperCase($data['telpibu'])?> </div> <div class="md_box"></div><?= cStr::upperCase($data['hpibu'])?> </td>						
					</tr>					
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Alamat E-mail Ibu : <?= cStr::upperCase($data['emailibu'])?></td>
					</tr>
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Pekerjaan : <?= cStr::upperCase($arrPekerjaan[$data['kodepekerjaanibu']])?></td>
					</tr>					
					<tr>
						<td width="20">6.</td>
						<td colspan="2">Jabatan : <?= cStr::upperCase($data['jabatankerjaibu'])?></td>
					</tr>
					<tr>
						<td width="20">7.</td>
						<td colspan="2">Nama Perusahaan : <?= cStr::upperCase($data['namaperusahaanibu'])?></td>
					</tr>
					<tr>
						<td width="20">8.</td>
						<td colspan="2">Pendidikan : <?= cStr::upperCase($arrPendidikan[$data['kodependidikanibu']])?></td>
					</tr>
					<tr>
						<td colspan="3"> <b>DATA PERGURUAN TINGI (hanya diisi bagi yang pernah/sedang kuliah di perguruan tinggi lain) </b></td>
					</tr>						
					<tr>
						<td width="20">1.</td>
						<td colspan="2">Nama Perguruan Tinggi : <?= cStr::upperCase($data['ptasal'])?></td>
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Kota / Kabupaten : <?= cStr::upperCase($data['kodekotapt_text'])?></td>
					</tr>
					<tr>
						<td width="20">3.</td>
						<td colspan="2">Propinsi : <?= cStr::upperCase($data['propinsiptasal_text'])?></td>
					</tr>
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Negara : <?= cStr::upperCase($data['negaraptasal'])?></td>
					</tr>
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Fakultas/Jurusan : <?= cStr::upperCase($data['ptjurusan'])?></td>
					</tr>
					<tr>
						<td width="20">6.</td>
						<td colspan="2"><div class="md_box">Tahun Masuk Kuliah (YYY) : <?= cStr::upperCase($data['ptthnmasuk'])?></div> <div class="md_box">7.Tahun Keluar Kuliah (YYYY) : <?= cStr::upperCase($data['ptthnlulus'])?></div></td>
					</tr>
					<tr>
						<td width="20">8.</td>
						<td colspan="2">
							<div class="sm_box">Semester Keluar : <?= cStr::upperCase($data['semesterkeluar'])?></div> 
							<div class="sm_box">9. SKS yang dicapai: <?= cStr::upperCase($data['sksasal'])?></div>
							<div class="sm_box">10. IPK Terakhir: <?= cStr::upperCase($data['ptipk'])?></div> 
						</td>
					</tr>
					<tr>
						<td colspan="3"> <b>ALASAN MEMILIH UEU</b></td>
					</tr>
					<tr>
						<td width="20">1.</td>
						<td colspan="2">Darimana Anda Mengetahui UEU ? : <?= cStr::upperCase($data_kuisioner['ptjurusan'])?></td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_1']); ?></div>Kunjungan Sekolah</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('5',$data_kuisioner['jawab_1']); ?></div>Media Online</div>
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('9',$data_kuisioner['jawab_1']); ?></div>Seminar</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('2',$data_kuisioner['jawab_1']); ?></div>Pameran Sekolah</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('6',$data_kuisioner['jawab_1']); ?></div>Website</div>
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('10',$data_kuisioner['jawab_1']); ?></div>Teman</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_1']); ?></div>Iklan Cetak, <i>sebutkan</i></div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('7',$data_kuisioner['jawab_1']); ?></div>Spanduk</div>
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('11',$data_kuisioner['jawab_1']); ?></div>Poster</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('4',$data_kuisioner['jawab_1']); ?></div>Data Langsung</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('8',$data_kuisioner['jawab_1']); ?></div>Pameran JHCC</div>
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('12',$data_kuisioner['jawab_1']); ?></div>Lain nya, <i>sebutkan</i></div> 
						</td>						
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Berikan urutan alasan dibawah ini, sesuai dengan urutan alasan anda masuk UEU :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_2']); ?></div>Biaya Perkuliahan Terjangkau</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('5',$data_kuisioner['jawab_2']); ?></div>Kegiatan Mahasiswanya menarik</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('2',$data_kuisioner['jawab_2']); ?></div>Program akademik berkualitas</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('6',$data_kuisioner['jawab_2']); ?></div>Fasilitas IT yang lengkap (Wifi,Microsoft learning gateway, dll)</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_2']); ?></div>Permintaan dari orang tua/diajak Saudara</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('7',$data_kuisioner['jawab_2']); ?></div>Diajak teman</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('4',$data_kuisioner['jawab_2']); ?></div>Akses Mudah</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('8',$data_kuisioner['jawab_2']); ?></div>Lain nya, sebutkan,</div>
						</td>						
					</tr>
					<tr>
						<td width="20">3.</td>
						<td colspan="2">Sebutkan media cetak yang sering anda baca ? :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_3']); ?><div class="boxcheck"></div>Koran</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_3']); ?></div>Tidak ada</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('2',$data_kuisioner['jawab_3']); ?></div>Majalah</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('4',$data_kuisioner['jawab_3']); ?></div>Lain nya, sebutkan,</div>
						</td>						
					</tr>
					<tr>
						<td width="20">4.</td>
						<td colspan="2"><div class="lg_box">Apakah saudara mendaftar di perguruan tinggi</div> 

							<?
							$datastatus = array(1=>'Ya',2=>'Tidak');
							foreach($datastatus as $key => $val){ 
								$arrdatawn = array($key=>'');
								?>
								<div class="v_sm_box">
									<div class="boxcheck"><?= UI::createCheckBox('jeniswali',$arrdatawn,$data_kuisioner['jawab_4'],false) ?></div><?= $val?>
								</div>								
							<?}?>							
						</td>
					</tr>					
					<tr>
						<td width="20">5.</td>
						<td colspan="2"><div class="lg_box">Apakah saudara mendaftar di perguruan tinggi swasta lainnya</div> 
							<?
							$datastatus = array(1=>'Ya',2=>'Tidak');
							foreach($datastatus as $key => $val){ 
								$arrdatawn = array($key=>'');
								?>
								<div class="v_sm_box">
									<div class="boxcheck"><?= UI::createCheckBox('jeniswali',$arrdatawn,$data_kuisioner['jawab_5'],false) ?></div><?= $val?>
								</div>								
							<?}?>							
						</td>
					</tr>
					<tr>
						<td width="20">6.</td>
						<td colspan="2"><div class="lg_box">Urutkan 5 Universitas Swasta yang menjadi pilihan anda termasuk UEU ?</div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="md_box">1. <?= $data_kuisioner['jawab_6_1']?></div> <div class="md_box">4. <?= $data_kuisioner['jawab_6_4']?> </div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="md_box">2. <?= $data_kuisioner['jawab_6_2']?></div> <div class="md_box">5. <?= $data_kuisioner['jawab_6_5']?></div> </td>
					</tr>
					<tr>
						<td width="20"></td>
						<td colspan="2"><div class="md_box">3. <?= $data_kuisioner['jawab_6_3']?></div> </td>
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
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_7']); ?></div>Orang tua</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_7']); ?></div>Wali</div>
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('5',$data_kuisioner['jawab_7']); ?></div>Beasiswa</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('2',$data_kuisioner['jawab_7']); ?></div>Ikatan dinas</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('4',$data_kuisioner['jawab_7']); ?></div>Sendiri</div>
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('6',$data_kuisioner['jawab_7']); ?></div>Lainnya,</div> 
						</td>						
					</tr>
					<tr>
						<td width="20">2.</td>
						<td colspan="2">Status tempat tinggal :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_8']); ?></div>Orang tua</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_8']); ?></div>Sendiri</div>
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('5',$data_kuisioner['jawab_8']); ?></div>Lainnya,</div> 
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('2',$data_kuisioner['jawab_8']); ?></div>Ikut saudara</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('4',$data_kuisioner['jawab_8']); ?></div>kost/sewa/kontrak</div>
						</td>						
					</tr>
					<tr>
						<td width="20">3.</td>
						<td colspan="2">
							<div class="md_box">Memiliki Komputer</div> 
							<div class="v_sm_box"><div class="boxcheck"></div>Ya</div> <div class="v_sm_box"><div class="boxcheck"></div>Tidak</div> 
						</td>
					</tr>
					<tr>
						<td width="20">4.</td>
						<td colspan="2">Transportasi yang digunakan menuju kampus UEU ? :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_10']); ?></div>Kendaraan Pribadi</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_10']); ?></div>Kendaraan Umum</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('2',$data_kuisioner['jawab_10']); ?></div>Motor</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('4',$data_kuisioner['jawab_10']); ?></div>Lain nya,</div>
						</td>						
					</tr>
					<tr>
						<td width="20">5.</td>
						<td colspan="2">Jenis Beasiswa (hanya diisi bagi penerima beasiswa lain luar UEU) ? :</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_11']); ?></div>Bakat</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_11']); ?></div>Supersemar</div>
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('5',$data_kuisioner['jawab_11']); ?></div>Jurusan Langka</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('2',$data_kuisioner['jawab_11']); ?></div>Ikatan dinas</div> 
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('4',$data_kuisioner['jawab_11']); ?></div>BEasiswa Swasta</div>
							<div class="sm_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('6',$data_kuisioner['jawab_11']); ?></div>Lain nya,</div>
						</td>						
					</tr>
					<tr>
						<td width="20">6.</td>
						<td colspan="2">Jenis Beasiswa yang diperoleh dari UEU : <?= $data_kuisioner['jawabketerangan_12']?></td>
					</tr>						
					<tr>
						<td width="20">7.</td>
						<td colspan="2">Prestasi yang pernah dicapai</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="md_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('1',$data_kuisioner['jawab_13']); ?></div>Olahraga, sebutkan</div> 
							<div class="md_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('4',$data_kuisioner['jawab_13']); ?></div>Juara Sekolah, sebutkan</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="md_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('2',$data_kuisioner['jawab_13']); ?></div>Kesenian, sebutkan</div> 
							<div class="md_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('5',$data_kuisioner['jawab_13']); ?></div>Lain - lain</div>
						</td>						
					</tr>
					<tr>
						<td></td>
						<td colspan="2">
							<div class="md_box"><div class="boxcheck"><?= mKuisioner::cekJawaban('3',$data_kuisioner['jawab_13']); ?></div>Olimpiade, sebutkan</div> 
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
