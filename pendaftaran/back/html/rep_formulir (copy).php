<?
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once($conf['ui_dir'].'u_form.php');
	require_once($conf['model_dir'].'m_biodata.php');
	require_once($conf['model_dir'].'m_smu.php');
	require_once($conf['model_dir'].'m_combo.php');
	require_once(Route::getModelPath('pendaftar'));
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	//cek session
	// if($_SESSION['PENDAFTARAN']['FRONT']['USERID'] == null or $_SESSION['PENDAFTARAN']['FRONT']['USERID'] == ''){
		// header("Location: index.php");
	// }
	
	$data=Modul::SessionDataBack($conn,$_GET['id']);
	$data=$data->FetchRow();
	$kotaprop = explode('#',Modul::getKotaProp($conn,$data['kodekota'],$data['kodepropinsi']));
	$kotaproportu = explode('#',Modul::getKotaProp($conn,$data['kodekotaortu'],$data['kodepropinsiortu']));
	// $tglujian = Modul::tglUjian($conn,$data['jalurpenerimaan']);
	$tglujian = Modul::tglUjian($conn,$data['nopendaftar']);
	//$conn->debug=true;
   //print_r($data);
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
	$israport = $conn->GetOne("select israport from akademik.lv_jalurpenerimaan where jalurpenerimaan='".$data['jalur']."'"); 
	
	$p_namafile = 'formulir_'.$_GET['id'];
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
    <html>
	<head>
		<title>Cetak Formulir Pendaftaran</title>   
		<link rel="icon" type="image/x-icon" href="images/favicon.png">
		<link href="style/daftar.css" rel="stylesheet" type="text/css">
		<link href="style/style.css" rel="stylesheet" type="text/css">
		<style>
			.header{font-size:35px; font-weight:bold}
			.subheader{font-size:18px; font-weight:bold}
		</style>

	</head>
	<!--body style="background: white" onLoad="window.print();"-->
		<body style="background: white">
		<center>
			<div style="border:none; width: 650px; height: 500px;">
				<table width="100%" cellspacing=0 style="padding:5px;border:1px solid #000000">
					<tr><td>
						<table width="100%" style="border-bottom:1px solid #000000">
							<tr style="background: #ffffff;">
								  <td style="padding:0 20px; width:70px"><img width=80 height=80 src="../front/images/logo.png" width="70"></td>
								  <td nowrap >
									<p>
									UNIVERSITAS<br><span class="header">Esa Unggul</span><br>
									<span class="subheader">FORMULIR PENDAFTARAN MAHASISWA BARU</span>
									</p>
								  </td>  
							</tr>
						</table>
					</td></tr>
					<tr>
						<td colspan=2 align="center">
							<table border=0 width="100%">
						  
								<tr>
									<td nowrap width="150" align="left">
										<b>JALUR SELEKSI</b>
									</td>
									<td align="left" valign="middle"><?= str_repeat('&nbsp;',10)?>: 
										<b><?= $data['jalur']?></b>
									</td>
								</tr>
								<tr>
									<td colspan=2 width="50"><b>A. DATA DIRI</b></td>
								</tr>
								<tr>
									<td  width="50">Nama Pendaftar</td>
									<td>: <?= $data['nama']?></td>
								</tr>
								<tr>
									<td  width="50">Tempat/Tgl. Lahir</td>
									<td>: <?= $kota?> / <?= date('d-m-Y',strtotime($data['tgllahir']))?></td>
								</tr>
								<tr>
									<td  width="50" valign="top">Alamat</td>
									<td>: Jln. <?= $data['jalan'] ?> RT. <?= $data['rt'] ?> RW. <?= $data['rw'] ?>, Kelurahan <?= $data['kel'] ?>
										<br>&nbsp;&nbsp;Kecamatan <?= $data['kec'] ?>, <?= $kotaprop[0] ?> <?= $kotaprop[1] ?></td>
								</tr>
								<tr>
									<td  width="50">No. Telp. / HP</td>
									<td>: <?= $data['telp']?> / <?= $data['hp']?></td>
								</tr>
								<tr>
									<td  width="50">Agama</td>
									<td>: <?= mBiodata::getAgama($conn,$data['kodeagama'])?></td>
								</tr>
								<tr>
									<td  width="50">Jenis Kelamin</td>
									<td>: <?if($data['sex']=='L') echo 'Laki-Laki'; else if($data['sex']=='P') echo 'Perempuan';?></td>
								</tr><tr>
									<td  width="50">Identitas (KTP)</td>
									<td>: <?= $data['nomorktp']?></td>
								</tr><tr>
									<td  width="50">Email</td>
									<td>: <?= $data['email']?></td>
								</tr><tr>
									<td colspan=2 width="50"><b>B. DATA ORANG TUA</b></td>
								</tr><tr>
									<td  width="50">Nama Ayah</td>
									<td>: <?= $data['namaayah']?></td>
								</tr><tr>
									<td  width="50">Pekerjaan Ayah</td>
									<td>: <?= mBiodata::getPekerjaan($conn, $data['kodepekerjaanayah'])?></td>
								</tr><tr>
									<td  width="50">Nama Ibu</td>
									<td>: <?= $data['namaibu']?></td>
								</tr><tr>
									<td  width="50">Pekerjaan Ibu</td>
									<td>: <?= mBiodata::getPekerjaan($conn, $data['kodepekerjaanibu'])?></td>
								</tr><tr>
									<td  width="50">Alamat Orang Tua</td>
									<td>: Jln. <?= $data['jalanortu'] ?> RT. <?= $data['rtortu'] ?> RW. <?= $data['rwortu'] ?> , Kelurahan <?= $data['kelortu'] ?>
										<br>&nbsp;&nbsp;Kecamatan <?= $data['kecortu'] ?>, <?= $kotaproportu[0] ?> <?= $kotaproportu[1] ?></td>
								</tr><tr>
									<td  width="50">Penghasilan Ayah/Bulan</td>
									<td>: <?= Modul::formatNumber($data['pendapatanayah'],'',true);?>
									</td>
								</tr><tr>
									<td  width="50">Penghasilan Ibu/Bulan</td>
									<td>: <?= Modul::formatNumber($data['pendapatanibu'],'',true);?>
									</td>
								</tr><tr>
									<td  width="50">No. Telp</td>
									<td>: <?= $data['telportu']?></td>
								</tr><tr>
									<td colspan=2 width="50"><b>C. DATA ASAL SEKOLAH</b></td>
								</tr><tr>
									<td  width="50">Nama SMA/SMK/MA</td>
									<td>: <?= $list_smu[$data['asalsmu']]?></td>
								</tr><tr>
									<td  width="50">Jurusan</td>
									<td>: <?= $data['jurusansmaasal']?></td>
								</tr><tr>
									<td  width="50">Alamat Sekolah</td>
									<td>: <?= $list_alamatsmu[$data['asalsmu']]?></td>
								</tr><tr>
									<td  width="50">Tahun Lulus</td>
									<td>: <?= $data['thnlulussmaasal']?></td>
								</tr>
								<?if($israport == '-1'){?>
								<tr>
									<td valign=top width="50">Nilai Rata-Rata Raport</td>
									<td >: 
										<table width="70%" border=1 style=" border-collapse:collapse" >
											<tr>
												<td align=center colspan=2><b>Kelas X</b></td>
												<td align=center colspan=2><b>Kelas XI</b></td>
												<td align=center colspan=2><b>Kelas XII</b></td>
											</tr>
											<tr>
												<td align=center ><?= $data['raport_10_1']?></td>
												<td align=center ><?= $data['raport_10_2']?></td>
												<td align=center ><?= $data['raport_11_1']?></td>
												<td align=center ><?= $data['raport_11_2']?></td>
												<td align=center ><?= $data['raport_12_1']?></td>
												<td align=center ><?= $data['raport_12_2']?></td>
											</tr>
										</table>
									</td>
								</tr>
								<?}?>
								<tr>
									<td colspan=2 width="50"><b>D. DATA PROGRAM STUDI PILIHAN</b></td>
								</tr><tr>
									<td colspan=2 width="50">
										<table width="80%" border=1 style=" border-collapse:collapse" >
											<tr>
												<td align=center ><b>Pilihan I</b></td>
												<td align=center ><b>Pilihan II</b></td>
												<td align=center ><b>Pilihan III</b></td>
											</tr>
											<tr>
												<td align=center > <?=mPendaftar::getPilihan($data['pilihan1'])?></td>
												<td align=center ><? if(!empty($data['pilihan2'])) echo mPendaftar::getPilihan($data['pilihan2'])?></td>
												<td align=center ><? if(!empty($data['pilihan3'])) echo mPendaftar::getPilihan($data['pilihan3'])?></td>
											</tr>
										</table>
									</td>
								</tr><tr>
									<td colspan=2 width="50"><b>E. Pelaksaan Seleksi (<i>diisi khusus jalur seleksi Prestasi/Umum</i>)</b></td>
								</tr>
								<tr>
									<td width="50">Tanggal Pelaksanaan</td>
									<td>: <?= date('d-m-Y',strtotime($tglujian))?></td>
								</tr>
								<tr><td colspan=2 width="50">&nbsp;</td></tr>
								<tr>
									<td colspan=2 width="50">Data yang saya isikan diatas, dapat saya pertanggung jawabkan kebenarannya.</td>
								</tr>
								<tr>
									<td colspan=2 width="50">Surabaya, <?=!empty($data['tglregistrasi'])?date('d-m-Y',strtotime($data['tglregistrasi'])):date('d-m-Y')?></td>
								</tr>
								<tr><td colspan=2 width="50">&nbsp;</td></tr>
								<tr><td colspan=2 width="50">&nbsp;</td></tr>
								<tr>
									<td colspan=2 width="50">
										<table width="70%">
										<tr>
											<td>Tanda Tangan Petugas</td>
											<td>Tanda Tangan Pendaftar</td>
										</tr>
										</table>
									</td>
								</tr>
								<tr><td colspan=2 width="50">&nbsp;</td></tr>
								<tr><td colspan=2 width="50">&nbsp;</td></tr>
								<tr>
									<td colspan=2 width="50">
										<table width="70%">
										<tr>
											<td width="230">(<?= str_repeat('&nbsp;',30)?>)</td>
											<td>(<?= $data['nama']?>)</td>
										</tr>
										</table>
									</td>
								</tr>
								<tr>
								<td colspan=2>
									<div align=right style="z-index:10;position:relative;left:px;top:-120px;">
										<?//= uForm::getImageMahasiswa($conn,$data['nopendaftar'],true) ?>
										<img style="width:3cm;height:4cm" id="imgfoto" border="1" src="<?= uForm::getPathImageMahasiswa($conn,$data['nopendaftar'],true)?>" style="cursor:pointer">
									</div>
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
