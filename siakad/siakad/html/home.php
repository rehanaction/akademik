<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// cek apakah sudah login
	if(!Modul::isAuthenticated()){//echo "b".die();
		Route::redirect($conf['menu_path']);}
	// ada aksi ganti role
	$r_aksi = $_REQUEST['act'];
	if($r_aksi == 'chgrole') {
		list($r_role,$r_unit) = explode(':',CStr::removeSpecial($_REQUEST['key']));
		
		Modul::changeRole($r_role,$r_unit);
	}
	$conn->debug= false;
	// include
	require_once(Route::getModelPath('berita'));
	require_once(Route::getModelPath('diskusi'));
	require_once(Route::getModelPath('pesan'));
	require_once(Route::getModelPath('setting'));

	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('pegawai'));
		
	// properti halaman
	$p_title = 'Selamat Datang di SIM Akademik';
	//$p_model = mMahasiswa;
	// mendapatkan data
	$rekap = mMahasiswa::getStatusAllMahasiswa($conn);
	$a_pengumuman = mBerita::getListPengumuman($conn);
	$a_berita = mBerita::getListBerita($conn);
	$a_diskusi = mDiskusi::getListTerbaru($conn);
	$n_pesan = mPesan::getNumUnread($conn);
	$rekap = mMahasiswa::getStatusAllMahasiswa($conn);

	//$a_tagihan = $p_model::getTagihanMhs($conn,$r_nim);
	$r_periode = Akademik::getPeriode();
	if(Akademik::isMhs()) {
		$p_role = 'M';
		
		require_once(Route::getModelPath('krs'));
		require_once(Route::getModelPath('mahasiswa'));
		require_once(Route::getModelPath('perwalian'));
		require_once(Route::getModelPath('tugas'));
		require_once(Route::getModelPath('tagihanmhs'));
		require_once(Route::getModelPath('akademik'));
 		require_once(Route::getModelPath('tagihan'));
  		require_once(Route::getModelPath('pembayaran'));
 		require_once(Route::getModelPath('pembayarandetail'));
		require_once(Route::getUIPath('form'));
		
		$r_key = Modul::getUserName();
		$p_foto = uForm::getPathImageMahasiswa($conn,$r_key);
		$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
		$n_tugas = mTugas::getNumUnsubmit($conn,$r_key);
		$a_jadwal = mKRS::getDataJadwal($conn,date('N'),$r_key);
		$a_tagihan2 = mMahasiswa::getTagihanMhsBB($conn,$r_key);
		$kartuUts = mTagihanMhs::getUts($conn, $r_key,$r_periode);


		$a_ips = array();
		$a_ipk = array();
		$a_sks = array();
		$a_semester = array();
		
		$a_datasmt = mPerwalian::getList($conn);
		
		$i = 0;
		foreach($a_datasmt as $row) {
			if(strval($row['semmhs']) == '')
				continue;
			
			$a_ips[] = $row['ips'];
			$a_ipk[] = $row['ipk'];
			$a_sks[] = $row['skssem'];
			$a_semester[] = ++$i; // $row['semmhs'];
		}
		
		$showtagihan = mTagihanMhs::isShowTagihan($conn,$r_key);
		$a_tagihan = mTagihanMhs::getStatusPeriode($conn,$r_key);
		//$tmpbayar = mTagihan::getTmpPembayaran($conn,$r_key);
		/*if(!empty($tmpbayar)){
			//cek status tagihan
			//print_r($tmpbayar);
			$data = array();
			$data['invoice']=$tmpbayar['noinvoice'];
			$data['merchant_id']=$conf['merchant_id'];
			$data['sof_id']=$tmpbayar['sof_id'];
			$data['sof_type']='check';
			$password=$conf['merchant_password'];
			ksort($data);
			$componetSignature = '';
			foreach ($data as $key => $val) {
				//echo $key." : ".$val."<br/>";
				$componetSignature = $componetSignature."".strtoupper($val)."%";
			}
			$data['mer_signature']=strtoupper(hash("sha256",$componetSignature."".$password));
			$result=mMahasiswa::BayarTagihan($data);
			$paydet = json_decode($result['payment_detail']);
			if($result['status_code']==201){
						$t_tgl = $tmpbayar['expireddate'];
						$t_tgl = date('Y-m-d H:i:s',strtotime($paydet->pay_date));
						if(empty($t_tgl)){
							$t_tgl = $row['expireddate'];
						} 
						$paydate = new DateTime($t_tgl);
						$datecreate = new DateTime($tmpbayar['datecreate']);
						$conn->BeginTrans();
						$record = array();
						// $record['idpembayaran'] = (mPembayaran::idmaks($conn))+1;
						$record['tglbayar'] = $t_tgl;
						$record['jumlahbayar'] = $tmpbayar['total'];
						$record['jumlahuang'] = str_replace('.','',$tmpbayar['total']);
						$record['ish2h'] = 1;
						$record['companycode'] ='FINNET';
						//$record['nip'] = $_SESSION[SITE_ID]['MODUL']['USERNAME'];
						do{
						$record['refno'] = mAkademik::random(10);
						$cek = mPembayaran::cekRefno($conn,$record['refno']);
						}
						while(!$cek);
						$record['periodebayar'] = $r_periode;
						$record['nokuitansi'] = mPembayaran::getNoBSM($conn,substr($t_tgl,0,4).substr($t_tgl,5,2));
						$record['nim'] = $a_infomhs['nim'];
						$recdetail = mTagihan::getAllTmpPembayaran($conn,$tmpbayar['kodepembayaran']);
						if($paydate >= $datecreate) {
						$err = mPembayaran::insertRecord($conn,$record);
						$idpembayaran = mPembayaran::idmaks($conn);
						$record['idpembayaran'] = $idpembayaran;
						$rec = array();
						$rec['idpembayaran'] = $idpembayaran;
						foreach($recdetail as $row){
							$rec['idtagihan']=$row['idtagihan'];
							$rec['nominalbayar'] = $row['nominaltagihan'];
							$err = mPembayarandetail::insertRecord($conn,$rec);
						}
					   
						$conn->CommitTrans();
						if($err <> '0')
						{
							$p_postmsg = " Gagal melakukan Pembayaran";
							$p_posterr = true;
							$c_inquiry = false;
						}
						else
						{
							$subject="Pembayaran Kuliah";
							$body = 'Pembayaran Kuliah STIE INABA'
									.'<br>  Kode Pembayaran             : '.$tmpbayar['kodepembayaran']
									.'<br>  NIM                         : '.$a_infomhs['nim']
									.'<br>  Nama Mahasiswa              : '.$a_infomhs['nama']
									.'<br> Perbayaran Telah Berhasil Dilakukan Cek ulang tagihan anda di siakad.inaba.ac.id jika belum terupdate segera hubungi bagian IT dengan membawa bukti pembayaran <br>';
							mTagihan::sendMail($a_infomhs['email'],$subject,$body);
							$ok = mTagihan::updateTmpPembayaran($conn,$row['kodepembayaran']);
							$_SESSION['message_done'] = "Pembayaran Berhasil";
						}
					}
			}elseif($result['status_code']==211 OR $result['status_code']==203){
				$ok = mTagihan::deleteTmpPembayaran($conn,$tmpbayar['kodepembayaran']);
				$_SESSION['message_cancel'] = "Pembayaran Di Batalkan Oleh sistem";
			}
		}*/
	}
	elseif (Akademik::isDosen()) {
		$p_role = 'D';

		require_once(Route::getModelPath('pegawai'));
		require_once(Route::getUIPath('form'));


		$r_key = Modul::getUserIDPegawai();
		// /echo $r_key;
		$a_dosen = mPegawai::getDosen($conn,$r_key);

		//var_dump($a_dosen);

	}
	else {
		$p_role = 'A';
		$n_berita = mBerita::getNumInvalid($conn,$r_key);
		require_once(Route::getModelPath('pegawai'));
		require_once(Route::getUIPath('form'));


		$r_key = Modul::getUserIDPegawai();
		// /echo $r_key;
		$a_pegawai = mPegawai::getDosen($conn,$r_key);
	}

	$u_role=Modul::getRole();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/modal2.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style type="text/css">
		#imgfoto{
		  
		  width: 40%;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		
		<div class="SideItem" style="float:left;width:55%;">
			<?php if($p_role == 'D') { ?>
				
				</br>
				<b>Batas Akhir Input Nilai UAS</b>
				</br>	
				</br>
				<BR>
				Kepada Yth Bapak/Dosen,
				<br>
				Batas akhir input nilai UAS periode Genap 2018/2019 berakhir pada tanggal <b>20 Juli 2019</b>
				</br>
				<br>
				Demikian agar dapat menjadi perhatian, Terima Kasih.</br>
				</br>
				</br>
				</br>
				
			
			<?php }elseif($p_role == 'M'){ ?>
				
			<?php } ?>

			<?php if($p_role == 'A'){ ?>
				
			<?php } ?>
			
			<br>
			<br>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/BERITA.png" onerror="loadDefaultActImg(this)"> Pengumuman
			</div>

			<?	foreach($a_pengumuman as $row) { ?>
			<div id="div_pengumuman" style="height:240px;display:none">
<!--
				<img src="<?= Route::navAddress('img_datathumb&type='.mBerita::uptype.'&id='.$row['idberita']) ?>">
-->					
				<div class="Break"></div>
				<div class=""><h2><b><?= $row['judulberita'] ?></b></h2></div>
				<div class="Break"></div>
				<div class="NewsContent" style="font-size: 14px;"><?= $row['isi'] ?></div>
				<div class="Break"></div>
				<u class="ULink" onclick="javascript:goDetail('<?= $row['idberita'] ?>')">Selengkapnya...</u>
			</div>
			<?	} ?>
			
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideSubTitle" colspan="2">Daftar Pengumuman</td>
				</tr>
			<?	foreach($a_pengumuman as $row) { ?>
				<tr>
					<td><u class="ULink" style="font-size: 14px;" id="u_pengumuman" onclick="javascript:goDetail('<?= $row['idberita'] ?>')"><?= $row['judulberita'] ?></u></td>
					<td align="right"><?= CStr::formatDateDiff($row['waktuvalid']) ?></td>
				</tr>
			<?	} ?>
			</table>
			<br>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/BERITA.png" onerror="loadDefaultActImg(this)"> Berita
			</div>
			
			<?	foreach($a_berita as $row) { ?>
			<div id="div_berita" style="height:240px;display:none">
<!--
				<img src="<?= Route::navAddress('img_datathumb&type='.mBerita::uptype.'&id='.$row['idberita']) ?>">
-->
				<div class="Break"></div>
				<div class="SideTitle"><?= $row['judulberita'] ?></div>
				<div class="Break"></div>
				<div class="NewsContent"><?= CStr::cBrief($row['isi']) ?></div>
				<div class="Break"></div>
				<u class="ULink" onclick="javascript:goDetail('<?= $row['idberita'] ?>')">Selengkapnya...</u>
			</div>
			<?	} ?>
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideSubTitle" colspan="2">Daftar Berita</td>
				</tr>
			<?	foreach($a_berita as $row) { ?>
				<tr>
					<td><u class="ULink" id="u_berita" onclick="javascript:goDetail('<?= $row['idberita'] ?>')"><?= $row['judulberita'] ?></u></td>
					<td align="right"><?= CStr::formatDateDiff($row['waktuvalid']) ?></td>
				</tr>
			<?	} ?>
			</table>
			<br>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/FORUM.png" onerror="loadDefaultActImg(this)"> Diskusi
			</div>
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideSubTitle" colspan="2">Diksusi Terbaru</td>
				</tr>
			<?	foreach($a_diskusi as $row) {
					list($t_waktuposting,$t_user) = explode('|',$row['max']);
			?>
				<tr>
					<td><u id="<?= $row['idforum'] ?>" class="ULink" onclick="javascript:goDiskusi(this)"><?= $row['judulforum'] ?></u></td>
					<td align="right"><?= $t_user ?>, <?= CStr::formatDateDiff($t_waktuposting) ?></td>
				</tr>
			<?	} ?>
			</table>

		</div>

		
		
		<?	if($p_role == 'M') { ?>
		<div class="SideItem" style="width:35%;float:right;">
			<div class="LeftRibbon" style="width: 250px;">
				Buku Panduan E-Learning
			</div>
			<center><a href="images/panduan_e_learning_mahasiswa.pdf" target="_BLANK"><img src="images/download-button.png" width="70%"></center></a>
			<br>
			<br>
			<div class="LeftRibbon">
				<img width="auto" src="images/aktivitas/JADWAL.png" onerror="loadDefaultActImg(this)"> <?= CStr::formatDateTimeInd(date('Y-m-d'),false,true) ?>
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td colspan="2">
						<center>
							<?= uForm::getImageMahasiswa($conn,$r_key,$c_upload) ?>
						</center>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="border-bottom:1px solid #647287" align="center">
						<b><?= Modul::getUserDesc() ?></b><br><?= Modul::getUserName() ?>
					</td>
				</tr>

				<tr class="Break">
					<td>Program Studi</td>
					<td align="right"><b><?= $a_infomhs['jenjang'] ?> - <?= $a_infomhs['jurusan'] ?></b></td>
					
				</tr>
				<tr>
					<td>Basis Kelas</td>
					<td align="right"><b><?= $a_infomhs['namasistemkuliah'] ?></b></td>
				</tr>
				<tr>
					<td>Dosen Wali</td>
					<td align="right"><b><?= $a_infomhs['dosenwali'] ?></b></td>
				</tr>
				<tr>
					<td>Biodata Terisi</td>
					<?php
						if ($a_infomhs['biodataterisi'] == '-1') {
							$bio="Terisi";

						}elseif ($a_infomhs['biodataterisi'] == '0') {
							$bio="Belum Terisi";
						}
					?>
					<td align="right"><b><?= $bio ?></b></td>
				</tr>
				<tr>
					<td>Status</td>
					<td align="right"><b><?= $a_infomhs['status'] ?></b></td>
				</tr>
				<tr>
					<td>Login Terakhir</td>
					<td align="right"><b><?= CStr::formatDateTimeInd(Modul::getLastLogin(),false,true) ?></b></td>
				</tr>
				<tr class="break">
					<td>&nbsp;</td>
				</tr>
			</table>

			<div class="LeftRibbon" style="width: auto;">
				<img width="24px" src="images/aktivitas/JADWAL.png" onerror="loadDefaultActImg(this)"> Cetak Kartu Ujian
			</div>

			<table width="100%">
				<tr>
					<td>
						<center>
						<?php
							if ($a_infomhs['kodeunit'] =='4302161101') {
								print_r($statuas);
									$statuts = mSetting::getKartuUtsMM($conn);
									$statuas= mSetting::getKartuUasMM($conn);
									if($statuts==1 and $kartuUts['isuts'] == -1){
								?>
									<input type="button" onclick="javascript:goCetakkartu('<?= $r_key ?>','uts')" value="Cetak Kartu">
									<br>
								<?php } 
									elseif($statuas==1 and $kartuUts['isuas'] == -1){
										?>
									<input type="button" onclick="javascript:goCetakkartu('<?= $r_key ?>','uas')" value="Cetak Kartu">
									<br>

								<?php		
									}else{
									?>
										<b>Untuk Cetak Kartu Ujian,<br>Anda wajib melakukan pelunasan dari semua tagihan </b>
										<br><br>
								<?php

									}

								?>
							<?php }else{ 
							
									$statuts = mSetting::getKartuUtsS1($conn);
									$statuas= mSetting::getKartuUasS1($conn);
									if($statuts==1 and $kartuUts['isuts'] == -1){
								?>
									<input type="button" onclick="javascript:goCetakkartu('<?= $r_key ?>','uts')" value="Cetak Kartu">
									<br>
								<?php } 
									elseif($statuas==1 and $kartuUts['isuas'] == -1){
										?>
									<input type="button" onclick="javascript:goCetakkartu('<?= $r_key ?>','uas')" value="Cetak Kartu">
									<br>

								<?php		
									}else{
									?>
										<b>Untuk Cetak Kartu Ujian,<br>Anda wajib melakukan pelunasan dari semua tagihan </b>
										<br><br>
								<?php

									}

								?>
							
							<?php } ?>

						</center>
					</td>
				</tr>

			</table>
			<br>
			<div class="break"></div>
			<div class="LeftRibbon" style="width: auto;">
				<img width="16px" src="images/aktivitas/JADWAL.png" onerror="loadDefaultActImg(this)"> Jadwal Kuliah Hari Ini
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">

				<tr>
					<td style="border-bottom:1px solid #647287"><b>Jam</b></td>
					<td style="border-bottom:1px solid #647287"><b>Matakuliah</b></td>
					<td style="border-bottom:1px solid #647287"><b>Ruangan</b></td>
				</tr>
				<?	if(empty($a_jadwal)) { ?>
				<tr>
					<td colspan="3" align="center">(Tidak ada jadwal)</td>
				</tr>
				<?	}
					foreach($a_jadwal as $t_jadwal) {
						foreach($t_jadwal as $row) { ?>
				<tr>
					<td width="80"><?= $row['jam'] ?></td>
					<td><?= $row['mk'] ?></td>
					<td><?= $row['koderuang'] ?></td>
				</tr>
				<?		}
					} ?>
			</table>
			
			<br>
			<div class="LeftRibbon" style="width: auto;">
				<img width="16px" src="images/aktivitas/JADWAL.png" onerror="loadDefaultActImg(this)"> Tagihan 
			</div>
			<table width="100%">
				<tr>
					<td colspan="3" style="border-bottom:1px solid #647287"><b>Tagihan Anda di Periode <?php echo Akademik::getNamaPeriodeShort() ?></b></td>
				</tr>
				
				<?php if (empty($a_tagihan2)) { ?>
					<tr>
						<td colspan="3"><center>Tidak Ada Tagihan</center></td>
					</tr>
				<?php }else{ $t=0; ?>
					<?php foreach($a_tagihan2 as $rowh){ 
							$sisa = $rowh['nominaltagihan']-($rowh['nominalbayar']+$rowh['potongan']);
							$total += $sisa;
						?>
						<tr>
							<td><?= $rowh['namajenistagihan'] ?> </td>
							<td align="center" width="10">:</td>
							<td align="right"><b>Rp. <?=cStr::formatNumber($sisa)?></b></td>
						</tr>
					<?php } ?>
						<tr>
							<td align="right" colspan="2" style="border-top:1px solid #647287"><b>Total Tagihan</b></td>
							<td align="right" width="100" style="border-top:1px solid #647287"><b>Rp. <?=cStr::formatNumber($total); ?></b></td>
						</tr>
						
						<tr>
							
							<td align="center" colspan=3><!--<input type="button" onclick="javascript:goBayar('<?//Akademik::base64url_encode($r_key) ?>')" value="Pembayaran">-->
								<br></td>
						</tr>

				<?php } ?>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				
				<tr>
					<td colspan="3"><center><b>Detail/Riwayat Keuangan Anda bisa dilihat di halaman <i>Biodata Mahasiswa</i> di TAB <i>Riwayat Keuangan</i></b></center></td>
				</tr>
				
			</table>
			<!--div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/GRAFIK.png" onerror="loadDefaultActImg(this)"> Kemajuan Belajar
			</div>
			<div id="container_ipk" style="height:200px"></div>
			<br>
			<div id="container_ips" style="height:200px"></div>
			<br>
			<div id="container_sks" style="height:200px"></div-->
		</div>
		<?	} else if($p_role == 'D') { ?>
				<div class="SideItem" style="width:35%;float:right;">
					<div class="LeftRibbon" style="width: 250px;">
					Buku Panduan E-Learning
				</div>
				<center><a href="images/panduan_e_learning_dosen.pdf" target="_BLANK"><img src="images/download-button.png" width="70%"></center></a>
				<br>
				<br>
			
				<div class="LeftRibbon">
					<img width="auto" src="images/aktivitas/JADWAL.png" onerror="loadDefaultActImg(this)"> <?= CStr::formatDateTimeInd(date('Y-m-d'),false,true) ?>
				</div>
				<table width="100%" cellpadding="4" cellspacing="0">
					<tr>
						<td colspan="2">
							<center>
								<?= uForm::getImagePegawai($conn,$r_key,$c_upload) ?>
							</center>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<b><?= Modul::getUserDesc() ?></b><br>&nbsp;<br><?= $a_dosen['jabatan'] ?><br>Dosen
						</td>
					</tr>
					<tr>
						<td colspan="2" style="border-top:1px solid black">&nbsp;</td>
					</tr>
					<tr>
						<td>NIDN/NUP</td>
						<td align="right"><b><?= $a_dosen['nidn'] ?></b></td>
					</tr>
					<tr>
						<td>Jabatan Fungsional</td>
						<td align="right"><b><?= $a_dosen['jafung'] ?></b></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" style="border-top:1px solid black">&nbsp;</td>
					</tr>
					<tr>
						<td>NIK</td>
						<td align="right"><b><?= $a_dosen['nik'] ?></b></td>
					</tr>
					<tr>
						<td>Jabatan</td>
						<td align="right"><b><?= $a_dosen['jabatan'] ?></b></td>
					</tr>

					<tr>
						<td>Tempat, Tgl Lahir</td>
						<td align="right"><b><?= $a_dosen['tmplahir'] ?>, <?= cstr::formatDateInd($a_dosen['tgllahir'])?></b></td>
					</tr>
					<tr>
						<td colspan="2" style="border-bottom: 1px solid black;">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td>Username</td>
						<td align="right"><b><?= $a_dosen['username'] ?></b></td>
					</tr>
					<tr>
						<td>Login Terakhir</td>
						<td align="right"><b><?= CStr::formatDateTimeInd(Modul::getLastLogin(),false,true) ?></b></td>
					</tr>
					
				</table>
			</div>
			
		<?	} else if($p_role == 'A') { ?>
			<div class="SideItem" style="width:35%;float:right;">
					<div class="LeftRibbon">
						<img width="auto" src="images/aktivitas/JADWAL.png" onerror="loadDefaultActImg(this)"> <?= CStr::formatDateTimeInd(date('Y-m-d'),false,true) ?>
					</div>
					<table width="100%" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="2">
								<center>
									<?= uForm::getImagePegawai($conn,$r_key,$c_upload) ?>
								</center>
							</td>
						</tr>
						<?php if ($a_pegawai['isdosen'] == '-1') {
							$dosen = 'Dosen';
						} ?>
						<tr>
							<td colspan="2" align="center">
								<b><?= Modul::getUserDesc() ?></b><br>&nbsp;<br><?= $a_pegawai['jabatan'] ?><br><?= $dosen; ?>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="border-top:1px solid black">&nbsp;</td>
						</tr>
						<tr>
							<td>NIK</td>
							<td align="right"><b><?= $a_pegawai['nik'] ?></b></td>
						</tr>
						<tr>
							<td>Jabatan</td>
							<td align="right"><b><?= $a_pegawai['jabatan'] ?></b></td>
						</tr>
						<tr>
							<td>Tempat, Tgl Lahir</td>
							<td align="right"><b><?= $a_pegawai['tmplahir'] ?>, <?= CStr::formatDateInd($a_pegawai['tgllahir'])?></b></td>
						</tr>
						<tr>
							<td colspan="2" style="border-bottom: 1px solid black;">&nbsp;</td>
						</tr>
						<tr>
							<td>Username</td>
							<td align="right"><b><?= $a_pegawai['username'] ?></b></td>
						</tr>
						<tr>
							<td>Login Terakhir</td>
							<td align="right"><b><?= CStr::formatDateTimeInd(Modul::getLastLogin(),false,true) ?></b></td>
						</tr>
						
					</table>
				</div>

		<?	} ?>
		
		<div style="clear:both"></div>
	</div>
</div>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content" style="margin: 20px auto; width: 30% !important;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  	<center><img src="images/quiz.PNG"></center>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>

  </div>
</div>
<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/modal.js"></script>
<script type="text/javascript" src="scripts/modalmanager.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>

<script type="text/javascript">


	
var idxberita = -1;
var idxpengumuman = -1;
$(document).ready(function() {
	chooseBerita(0);
	choosePengumuman(0);
	
	$("[id='u_berita'] ").mouseover(function() {
		var idx = $("[id='u_berita']").index(this);
		chooseBerita(idx);
	});
	$("[id='u_pengumuman'] ").mouseover(function() {
		var idx = $("[id='u_pengumuman']").index(this);
		choosePengumuman(idx);
	});
	
	<?	if($p_role == 'M') { ?>
	var chart_ipk, chart_ips, chart_sks;
	
    $(document).ready(function() {
		chart_ipk = new Highcharts.Chart({
            chart: {
                renderTo: 'container_ipk',
                type: 'line'
            },
            title: {
                text: 'IPK Mahasiswa',
                x: -20 //center
            },
            xAxis: {
				title: {
                    text: 'Semester'
                },
                categories: [<?= implode(',',$a_semester) ?>]
            },
            yAxis: {
                title: {
                    text: 'IPK'
                }
            },
			tooltip: {
                formatter: function() {
                    return '<strong>' + this.series.name + ': </strong>' + this.y;
                }
            },
			plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
			legend: {
                enabled: false
            },
            series: [{
                name: 'IPK',
                data: [<?= implode(',',$a_ipk) ?>]
            }]
        });
		
		chart_ips = new Highcharts.Chart({
            chart: {
                renderTo: 'container_ips',
                type: 'line'
            },
            title: {
                text: 'IPS Mahasiswa',
                x: -20 //center
            },
            xAxis: {
				title: {
                    text: 'Semester'
                },
                categories: [<?= implode(',',$a_semester) ?>]
            },
            yAxis: {
                title: {
                    text: 'IPS'
                }
            },
			tooltip: {
                formatter: function() {
                    return '<strong>' + this.series.name + ': </strong>' + this.y;
                }
            },
			plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
			legend: {
                enabled: false
            },
            series: [{
                name: 'IPS',
                data: [<?= implode(',',$a_ips) ?>]
            }]
        });
		
		chart_sks = new Highcharts.Chart({
            chart: {
                renderTo: 'container_sks',
                type: 'line'
            },
            title: {
                text: 'Pengambilan SKS',
                x: -20 //center
            },
            xAxis: {
				title: {
                    text: 'Semester'
                },
                categories: [<?= implode(',',$a_semester) ?>]
            },
            yAxis: {
                title: {
                    text: 'SKS'
                }
            },
			tooltip: {
                formatter: function() {
                    return '<strong>' + this.series.name + ': </strong>' + this.y;
                }
            },
			plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
			legend: {
                enabled: false
            },
            series: [{
                name: 'SKS',
                data: [<?= implode(',',$a_sks) ?>]
            }]
        });
	});
	
	<?	} ?>
});

function chooseBerita(idx) {
	if(idx != idxberita) {
		idxberita = idx;
		
		$("[id='div_berita']").hide();
		$("[id='div_berita']:eq("+idx+")").show(); // fadeIn(); // efeknya jelek
	}
}

function choosePengumuman(idx) {
	if(idx != idxpengumuman) {
		idxpengumuman = idx;
		
		$("[id='div_pengumuman']").hide();
		$("[id='div_pengumuman']:eq("+idx+")").show(); // fadeIn(); // efeknya jelek
	}
}

function goDetail(id) {
	goOpen('view_berita&key='+id);
}

function goCetakkartu(id,$jenis) {
	goOpen('kartuujian&key='+id+'&jenis='+$jenis);
}

function goBayar(id){
	//alert($id);
	goOpen('pembayaran&key='+id);
}

function goInbox() {
	goOpen('list_inbox');
}

function goTugas() {
	goOpen('list_tugas');
}

function goBerita() {
	goOpen('list_berita');
}

function goDiskusi(elem) {
	goOpen('list_subdiskusikelas&key='+elem.id);
}

</script>

</body>
</html>
