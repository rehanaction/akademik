<?php
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once($conf['ui_dir'].'u_form.php');
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('kuisioner'));
	require_once(Route::getModelPath('smu'));
	require_once(Route::getModelPath('biodata'));
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$data= mPendaftar::getData($conn,$_GET['id']);
	$data_kuisioner = mKuisioner::getData($conn,$_GET['id']);
	$data_agama = mbiodata::agama($conn);
	$data_stnikah = mbiodata::statusNikah($conn);
	
	$arrPekerjaan = mCombo::pekerjaan($conn);
	$arrPendidikan = mCombo::pendidikan($conn);
	$arrAgama = mCombo::agama($conn);
	
	$p_namafile = 'formulir_pendaftaran_'.$_GET['id'];
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
			 
			table, th, td {
			    vertical-align: top;
			}
		 </style>
	</head>
	<body>
		<center>
			<div class="content">
				<?/* header atas*/?>
				<table width="100%" >
					<tr>
						<td width="40px"><img src="../front/images/logo.png"></td>
						<td>
						<span class="header">STIE INABA</span><br>
						<span class="subheader">JL. SOEKARNO HATTA 448 BANDUNG</span>
						<br>humas@inaba.ac.id | www.inaba.ac.id
						</td>
					</tr>
				</table>
				<? /* Kotak tengah*/ ?>
				<table align="CENTER">
					<tr>
						<td>
						<center><b>FORMULIR PENDAFTARAN <br>
						<span>MAHASISWA BARU / LANJUTAN / PINDAHAN</span></b></center>
						</td>
					</tr>
				</table>
				<?/* isi formulir pendaftaran*/?>
				<table cellpadding="4px">
					<tr>
						<td colspan="4">
						<b>DATA PRIBADI</b>
						</td>
					</tr>
					<tr>
						<td width="5px">1.</td>
						<td width="150">Nama Peserta</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['nama'])?></td>
					</tr>
					<tr>
						<td>2.</td>
						<td>Jenis Kelamin</td>
						<td>:</td>
						<td>
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
						<td>3.</td>
						<td>Tanggal Lahir</td>
						<td>:</td>
						<td><?= cStr::formatDate($data['tgllahir'])?></td>
					</tr>
					<tr>
						<td>4.</td>
						<td>Tempat Lahir</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['kodekotalahir_text'])?></td>
					</tr>
					<tr>
						<td>5.</td>
						<td>Agama</td>
						<td>:</td>
						<td>
								<?
								foreach($data_agama as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="v_sm_box" style="width: 120px !important;">
										<div class="boxcheck"><?= UI::createCheckBox('kodeagama',$arrdatawn,$data['kodeagama'],false) ?></div><?= $val?>
									</div>								
								<?}?>
								
						</td>
					</tr>
					<tr>
						<td>6.</td>
						<td>Alamat Lengkap</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['jalan']) ?> RT. <?= cStr::upperCase($data['rt']) ?> RW. <?= cStr::upperCase($data['rw']) ?> KELURAHAN <?= cStr::upperCase($data['kel']) ?> KECAMATAN <?= cStr::upperCase($data['kec']) ?> <?= cStr::upperCase($data['kodekota_text']) ?> <?= cStr::upperCase($data['kodepos']) ?></td>
					</tr>
					<tr>
						<td>7.</td>
						<td>Handphone</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['hp']) ?></td>
					</tr>
					<tr>
						<td>8.</td>
						<td>Telp. Rumah</td>
						<td>:</td>
						<td><?= cStr::upperCase(substr_replace($data['telp'], '-', 3, 0)) ?></td>
					</tr>					
						
					<tr>
						<td>9.</td>
						<td>Email</td>
						<td>:</td>
						<td><?= $data['email'] ?> </td>
					</tr>							
					<tr>
						<td colspan="4"></td>
					</tr>			
					<tr>
						<td colspan="4"> <b>DATA SEKOLAH </b></td>
					</tr>					
					<tr>
						<td>10.</td>
						<td>Asal SMA/Sederajat</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['namasmu'])?></td>
					</tr>	
					<tr>
						<td>11.</td>
						<td>Status SMA/SMK</td>
						<td>:</td>
						<td>
							<?
								$jenissekolah = array(1=>'Swasta',2=>'Negeri');
								foreach($jenissekolah as $key => $val){ 
									$arrdatawn = array($key=>'');
									?>
									<div class="v_sm_box">
										<div class="boxcheck"><?= UI::createCheckBox('jenissekolah',$arrdatawn,$data['jenissekolah'],false) ?></div><?= $val?>
									</div>								
								<?}?>
						</td>
					</tr>
					<tr>
						<td>12.</td>
						<td>Tahun Lulus</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['thnlulussmaasal'])?></td>
					</tr>							
					<tr>
						<td>13.</td>
						<td>Jurusan SMA/SMK</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['jurusansmaasal'])?></td>	
					</tr>
					<tr>
						<td>14.</td>
						<td>Jumlah Nilai UAN</td>
						<td>:</td>
						<td><?= $data['nemsmu'] ?></td>	
					</tr>
					<tr>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td colspan="4"> <b>DATA PERGURUAN TINGI (UNTUK ALIH PROGRAM / PINDAHAN / LANJUTAN) </b></td>
					</tr>						
					<tr>
						<td>15.</td>
						<td>Asal Perguruan Tinggi </td>
						<td>:</td>
						<td><?= cStr::upperCase($data['ptasal'])?></td>
					</tr>
					<tr>
						<td>16.</td>
						<td>Tanggal Lulus</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['ptthnlulus'])?></td>
					</tr>
					<tr>
						<td>17.</td>
						<td>Jurusan</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['ptjurusan'])?></td>
					</tr>
					<tr>
						<td>18.</td>
						<td>Pilihan Program Studi</td>
						<td>:</td>
						<td>
							<?= cStr::upperCase($data['namaunit'])?><br>
						</td>
					</tr>	
					<tr>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td colspan="4"> <b>DATA UMUM </b></td>
					</tr>

					<tr>
						<td>19.</td>
						<td>Nama Ibu</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['namaibu'])?></td>
					</tr>
																								
					<tr>
						<td>20.</td>
						<td>Alamat Lengkap</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['alamatibu'])?> RT. <?= cStr::upperCase($data['rtibu'])?> RW. <?= cStr::upperCase($data['rwibu'])?> KELURAHAN <?= cStr::upperCase($data['kelibu'])?> KECAMATAN <?= cStr::upperCase($data['kecibu'])?> <?= cStr::upperCase($data['kodekotaibu_text'])?></td>
					</tr>								
					<tr>
						<td>21.</td>
						<td>Telp. Rumah</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['telpibu'])?></td>						
					</tr>
					<tr>
						<td>22.</td>
						<td>Handphone</td>
						<td>:</td>
						<td><?= cStr::upperCase($data['hpibu'])?></td>						
					</tr>
					<tr>
						<td>23.</td>
						<td>Pekerjaan Ayah</td>
						<td>:</td>
						<td><?= cStr::upperCase($arrPekerjaan[$data['kodepekerjaanayah']])?></td>						
					</tr>
					<tr>
						<td>24.</td>
						<td>Pekerjaan Ibu</td>
						<td>:</td>
						<td><?= cStr::upperCase($arrPekerjaan[$data['kodepekerjaanibu']])?></td>						
					</tr>										

					
				</table>
				<br><br> <br>
				<table width="100%">
					<tr>
						<td><center>Petugas Pendaftaran<br><br><br><br> <br><br> ....................................</center></td>
						<td><center><div style="border:solid 1px; height:4cm; width:3cm"><?= uForm::getImageMahasiswa($conn,$data['nopendaftar'],true) ?></div></center></td>
						<td width="350"><center>..........................,20......<br>Calon Mahasiswa<br><br><br> <br><br>
							<b><?= cStr::upperCase($data['nama'])?></b></center></td>
					</tr>
				</table>
				<hr style="border: 2px solid black">
				<table width="100%">
					<tr>
						<td style="padding-left: 20px;">Bila Mahasiswa Pindahan/Lanjutan<br>Diisi Oleh Ketua Program Studi<br><br>Jumlah SKS Kuliah Asal  &nbsp;: <br>Jumlah SKS Yang Diakui :</td>
						<td><center>Tanda Tangan Yang Menyetujui,<br><br><br><br> <br> Ketua/ Sekretaris Prodi</center></td>
					</tr>
				</table>
				<table width="100%">
					<tr>
						<td><i>Catatan: Biaya pendaftaran tidak dapat dikembalikan</i></td>
					</tr>
				</table>
				
			</div>
		</center>

	</body>
</html>

