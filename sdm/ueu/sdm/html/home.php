<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	//$a_auth = Modul::getFileAuth();
	//$conn->debug= true;
	$c_other = $a_auth['canother'];
	$c_dashboard = $c_other['D'];
	
	// cek apakah sudah login
	if(!Modul::isAuthenticated())
		Route::redirect($conf['menu_path']);
	
	// include
	require_once(Route::getModelPath('public'));
	require_once(Route::getModelPath('dashboard'));

		
	//koneksi dengan mutu;

	
	// ada aksi ganti role
	$r_aksi = $_REQUEST['act'];
	if($r_aksi == 'chgrole') {
		list($r_role,$r_unit) = explode(':',CStr::removeSpecial($_REQUEST['key']));
		
		Modul::changeRole($r_role,$r_unit);
	}
	else if($r_aksi == 'isikuesioner'){
		//merandomkan karakter
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 30; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        //verification
        if(!empty($randomString)){
            $record = array();
            $record['kodequisioner'] = $_POST['key'];
            $record['noverification'] = $randomString;
            $record['username'] = Modul::getUserID();
            $record['status'] = 'Y';

            $p_posterr = Query::recInsert($connmutu,$record,'mutu.qu_verifikasi');
            if($p_posterr)
            	$p_postmsg = "Mohon ma'af, ada masalah untuk menuju halaman pengisian kuesioner";
            else
            	Route::redirect();
        }
	}

	$p_model = mPublic;
	
	$a_pengumuman = array();
	$a_pengumuman = $p_model::getPengumuman($conn);
	
	$a_notice = array();
	$a_notice = $p_model::getNotice($conn);

	$idlogpegawai = Modul::getIDPegawai();
	
	//cek notifikasi pensiun dan hub kerja
	if (Modul::getRole() != 'A' and Modul::getRole() != 'admhrm' and Modul::getRole() != 'admpeg'){
		$c_pensiun = $p_model::getNotifikasiPensiun($conn,$idlogpegawai);
	}
	else{
		$c_pensiun = $p_model::getNotifikasiPensiun($conn,'');
	}
	
	//cek notifikasi untuk pengajuan cuti
	if (Modul::getRole() == 'A' or Modul::getRole() == 'admhrm' or Modul::getRole() == 'admpeg' or Modul::getRole() == 'Jab'){
		$c_cuti = $p_model::getNotifikasiCuti($conn);
		$c_dinas = $p_model::getNotifikasiDinas($conn,Modul::getRole());
	}
	
	//cek notifikasi untuk status 
	if (Modul::getRole() == 'A' or Modul::getRole() == 'admga')
		$c_honor = $p_model::getNotifikasiHonor($conn);
	
	
	// properti halaman
	$p_title = 'Selamat Datang di SIM Human Resources Management';	
	$p_tbwidth = 985;
	
	$a_data = mDashboard::graphJumlahKaryawan($conn);
	
	$g_graph = $a_data['graph'];

	//tipe
	$g_graphtipebaru = $g_graph['tipebaru'];
	$g_tipebaru = $a_data['tipepegawaibaru'];
	$g_keterangantipebaru = $a_data['keterangantipebaru'];

	//jenis dosen
	$g_graphjenisdosen = $g_graph['jenisdosen'];
	$g_jenisdosen = $a_data['jenispegawaidosen'];
	$g_keteranganjenisdosen = $a_data['keteranganjenisdosen'];

	//jenis kependidikan
	$g_graphjeniskependidikan = $g_graph['jeniskependidikan'];
	$g_jeniskependidikan = $a_data['jenispegawaikependidikan'];
	$g_keteranganjeniskependidikan = $a_data['keteranganjeniskependidikan'];

	//kelompok admin
	$g_graphkelompokpegadm = $g_graph['kelompokpegadm'];
	$g_kelompokpegadm = $a_data['kelompokpegawaiadm'];
	$g_keterangankelompokpegadm = $a_data['keterangankelompokpegadm'];

	//kelompok non admin
	$g_graphkelompokpegnonadm = $g_graph['kelompokpegnonadm'];
	$g_kelompokpegnonadm = $a_data['kelompokpegawainonadm'];
	$g_keterangankelompokpegnonadm = $a_data['keterangankelompokpegnonadm'];

	/*
	//tipe
	$g_graphtipe = $g_graph['tipe'];
	$g_tipe = $a_data['tipepegawai'];
	$g_keterangantipe = $a_data['keterangantipe'];
	
	//jenis
	$g_graphjenis = $g_graph['jenis'];
	$g_jenis = $a_data['jenispegawai'];
	$g_keteranganjenis = $a_data['keteranganjenis'];
	
	//status aktif
	$g_graphstatusaktif = $g_graph['statusaktif'];
	$g_statusaktif = $a_data['statusaktif'];
	$g_keteranganstatusaktif = $a_data['keteranganstatusaktif'];
	
	//hubungan
	$g_graphubungan = $g_graph['hubungan'];
	$g_hubungan = $a_data['hubungan'];
	$g_keteranganhubungan = $a_data['keteranganhubungan'];
	
	//pangkat
	$g_grappangkat = $g_graph['pangkat'];
	$g_pangkat = $a_data['pangkat'];
	$g_keteranganpangkat = $a_data['keteranganpangkat'];
	*/

	//untuk dosen fungsional
	$r_jenispegawaifung = Modul::setRequest($_POST['idjenispegawai'],'JENISPEGAWAIFUNGSIONAL');
	$a_jenispegawaifung = mDashboard::jenispegawaibaru($conn);
	$l_jenispegawaifung = UI::createSelect('idjenispegawai',$a_jenispegawaifung,$r_jenispegawaifung,'ControlStyle',true,'onchange="goSubmit()"',true,'-- Semua Jenis Pegawai --');

	$a_datadosenfung = mDashboard::graphJumlahDosenFung($conn,$r_jenispegawaifung);	
	$g_graphdosenfung = $a_datadosenfung['graph'];
	
	//fungsional
	$g_grapfungsional = $g_graphdosenfung['fungsional'];
	$g_fungsional = $a_datadosenfung['fungsional'];
	$g_keteranganfungsional = $a_datadosenfung['keteranganfungsional'];

	/*
	//untuk dosen hoem
	$r_jenispegawaihb = Modul::setRequest($_POST['idjenispegawaihb'],'JENISPEGAWAIHOMEBASE');
	$a_jenispegawaihb = mDashboard::jenispegawai($conn);
	$l_jenispegawaihb = UI::createSelect('idjenispegawaihb',$a_jenispegawaihb,$r_jenispegawaihb,'ControlStyle',true,'onchange="goSubmit()"',true,'-- Semua Jenis Pegawai --');

	$a_datadosenhb = mDashboard::graphJumlahDosenHB($conn,$r_jenispegawaihb);	
	$g_graphdosenhb = $a_datadosenhb['graph'];
	
	//homebase
	$g_graphomebase = $g_graphdosenhb['homebase'];
	$g_homebase = $a_datadosenhb['homebase'];
	$g_keteranganhomebase = $a_datadosenhb['keteranganhomebase'];*/
	
	//kuesioner
	//$a_kuesioner = $p_model::getKuesioner($connmutu);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
	.NoticeAlert {
		color: #b94a48;
		background-color: #f8c2c2;
		border:1px solid #ea8080;
		padding: 8px 10px 8px 15px;
		text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
		-webkit-border-radius: 4px;
		border-radius: 4px;
	}
	.CloseAlert{
		float:right;
		color: #8f8f8f;
		text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
		cursor:pointer;
	}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<?if ($c_pensiun){?>
			<br>
			<?if (Modul::getRole() == 'A' or Modul::getRole() == 'admhrm'){?>
		<div id="noticePensiunAdm" class="NoticeAlert" style="width:<?= $p_tbwidth ?>px;">
			<b>Peringatan! </b> Ada pegawai yang akan pensiun, lihat daftar <a class="ULink" target="_blank" href="<?= Route::navAddress('list_notepensiun') ?>"><strong>klik disini</strong></a>
			<div class="CloseAlert" onclick="goCloseNotice('noticePensiunAdm')"><b>X</b></div>
		</div>
			<?}else if(Modul::getRole() == 'Peg'){?>
		<div id="noticePensiunPeg" class="NoticeAlert" style="width:<?= $p_tbwidth ?>px;">
			<b>Peringatan! </b> Anda akan pensiun kurang dari 1 Tahun. 
			<div class="CloseAlert" onclick="goCloseNotice('noticePensiunPeg')"><b>X</b></div>
		</div>
			<?}?>
		<?}?>
		<?if ($c_cuti){?>
			<br>
			<?if (Modul::getRole() == 'A' or Modul::getRole() == 'admhrm' or Modul::getRole() == 'admpeg' or Modul::getRole() == 'Jab'){?>
		<div id="noticeCuti" class="NoticeAlert" style="width:<?= $p_tbwidth ?>px;">
			<b>Peringatan! </b> Ada pengajuan cuti yang belum disetujui, lihat daftar <a class="ULink" target="_blank" href="<?= Route::navAddress('list_persetujuancuti') ?>"><strong>klik disini</strong></a>
			<div class="CloseAlert" onclick="goCloseNotice('noticeCuti')"><b>X</b></div>
		</div>
			<?}?>
		<?}?>
		<?if ($c_dinas){?>
			<br>
			<?if (Modul::getRole() == 'A' or Modul::getRole() == 'admhrm' or Modul::getRole() == 'admpeg' or Modul::getRole() == 'Jab'){?>
		<div id="noticeDinas" class="NoticeAlert" style="width:<?= $p_tbwidth ?>px;">
			<b>Peringatan! </b> Ada pengajuan dinas yang belum disetujui, lihat daftar <a class="ULink" target="_blank" href="<?= Route::navAddress('list_persetujuandinas') ?>"><strong>klik disini</strong></a>
			<div class="CloseAlert" onclick="goCloseNotice('noticeDinas')"><b>X</b></div>
		</div>
			<?}?>
		<?}?>
		<?if ($c_honor){?>
			<br>
			<?if (Modul::getRole() == 'A' or Modul::getRole() == 'admga'){?>
		<div id="noticeHonor" class="NoticeAlert" style="width:<?= $p_tbwidth ?>px;">
			<b>Peringatan! </b> Ada rate honor yang belum di validasi, lihat daftar <a class="ULink" target="_blank" href="<?= Route::navAddress('list_hitajardosen') ?>"><strong>klik disini</strong></a>
			<div class="CloseAlert" onclick="goCloseNotice('noticeHonor')"><b>X</b></div>
		</div>
			<?}?>
		<?}?>


		<?	if(!empty($p_postmsg)) { ?>
		<center>
		<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
			<?= $p_postmsg ?>
		</div>
		</center>
		<div class="Break"></div>
		<?	} ?>

		<? if ($c_dashboard) {?>		
		<form name="pageform" id="pageform" method="post">
		<div class="SideItem" style="width:97%">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/DASHBOARD.png" onerror="loadDefaultActImg(this)"> DASHBOARD
			</div>
			<div class="SideTitle">Jumlah Pegawai <em>Per Tanggal <?= CStr::formatDateInd(date("Y-m-d"))?></em></div><div class="Break"></div><div class="Break"></div>
			<div class="tabs" style="width:<?= $p_tbwidth ?>px">
				<ul>
					<li><a href="javascript:void(0)">Tipe Pegawai</a></li>
					<li><a href="javascript:void(0)">Jenis Pegawai Dosen</a></li>
					<li><a href="javascript:void(0)">Jenis Pegawai Kependidikan</a></li>
					<li><a href="javascript:void(0)">Kelompok Pegawai Administrasi</a></li>
					<li><a href="javascript:void(0)">Kelompok Pegawai Non Administrasi</a></li>
					<?/*
					<li><a href="javascript:void(0)">Jenis Pegawai</a></li>
					<li><a href="javascript:void(0)">Status Aktif</a></li>
					<li><a href="javascript:void(0)">Hubungan Kerja</a></li>
					<li><a href="javascript:void(0)">Golongan</a></li>
					*/?>					
					<li><a href="javascript:void(0)">Jabatan Akademik</a></li>
					<?//<li><a href="javascript:void(0)">Jumlah Pegawai Homebase</a></li>?>
				</ul>
						
				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_tipebaru) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_tipebaru as $tipebaru){ 
									$total += $g_keterangantipebaru[$tipebaru['idtipepeg']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $tipebaru['idtipepeg']; ?></td>
									<td><?= $tipebaru['tipepeg']; ?></td>
									<td align="right"><?= $g_keterangantipebaru[$tipebaru['idtipepeg']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_tipebaru" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>

				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_jenisdosen) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_jenisdosen as $jenisdosen){ 
									$total += $g_keteranganjenisdosen[$jenisdosen['idjenispegawai']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $jenisdosen['idjenispegawai']; ?></td>
									<td><?= $jenisdosen['nama']; ?></td>
									<td align="right"><?= $g_keteranganjenisdosen[$jenisdosen['idjenispegawai']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_jenisdosen" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>

				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_jeniskependidikan) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_jeniskependidikan as $jeniskependidikan){ 
									$total += $g_keteranganjeniskependidikan[$jeniskependidikan['idjenispegawai']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $jeniskependidikan['idjenispegawai']; ?></td>
									<td><?= $jeniskependidikan['nama']; ?></td>
									<td align="right"><?= $g_keteranganjeniskependidikan[$jeniskependidikan['idjenispegawai']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_jeniskependidikan" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>

				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_kelompokpegadm) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_kelompokpegadm as $kelompokpegadm){ 
									$total += $g_keterangankelompokpegadm[$kelompokpegadm['idkelompok']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $kelompokpegadm['idkelompok']; ?></td>
									<td><?= $kelompokpegadm['namakelompok']; ?></td>
									<td align="right"><?= $g_keterangankelompokpegadm[$kelompokpegadm['idkelompok']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_kelompokpegadm" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>

				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_kelompokpegnonadm) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_kelompokpegnonadm as $kelompokpegnonadm){ 
									$total += $g_keterangankelompokpegnonadm[$kelompokpegnonadm['idkelompok']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $kelompokpegnonadm['idkelompok']; ?></td>
									<td><?= $kelompokpegnonadm['namakelompok']; ?></td>
									<td align="right"><?= $g_keterangankelompokpegnonadm[$kelompokpegnonadm['idkelompok']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_kelompokpegnonadm" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>
				
				<?/*						
				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_tipe) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_tipe as $tipe){ 
									$total += $g_keterangantipe[$tipe['idtipepeg']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $tipe['idtipepeg']; ?></td>
									<td><?= $tipe['tipepeg']; ?></td>
									<td align="right"><?= $g_keterangantipe[$tipe['idtipepeg']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_tipe" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>

				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_jenis) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_jenis as $jenis){ 
									$total += $g_keteranganjenis[$jenis['idjenispegawai']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $jenis['idjenispegawai']; ?></td>
									<td><?= $jenis['nama']; ?></td>
									<td align="right"><?= $g_keteranganjenis[$jenis['idjenispegawai']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_jenis" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>
						
				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_statusaktif) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_statusaktif as $statusaktif){ 
									$total += $g_keteranganstatusaktif[$statusaktif['idstatusaktif']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $statusaktif['idstatusaktif']; ?></td>
									<td><?= $statusaktif['namastatusaktif']; ?></td>
									<td align="right"><?= $g_keteranganstatusaktif[$statusaktif['idstatusaktif']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_statusaktif" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>
						
				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_hubungan) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_hubungan as $hubungan){ 
									$total += $g_keteranganhubungan[$hubungan['idhubkerja']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $hubungan['idhubkerja']; ?></td>
									<td><?= $hubungan['hubkerja']; ?></td>
									<td align="right"><?= $g_keteranganhubungan[$hubungan['idhubkerja']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_hubungan" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>
						
				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_pangkat) > 0) {	?>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_pangkat as $pangkat){ 
									$total += $g_keteranganpangkat[$pangkat['idpangkat']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $pangkat['idpangkat']; ?></td>
									<td><?= $pangkat['golongan']; ?></td>
									<td align="right"><?= $g_keteranganpangkat[$pangkat['idpangkat']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_pangkat" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>
				*/?>

				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_fungsional) > 0) {	?>
							<table>
								<tr>
									<td><strong>Filter : </strong></td>
									<td><?= $l_jenispegawaifung ?></td>
								</tr>
							</table>
							<br>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_fungsional as $fungsional){ 
									$total += $g_keteranganfungsional[$fungsional['idjfungsional']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $fungsional['idjfungsional']; ?></td>
									<td><?= $fungsional['jabatanfungsional']; ?></td>
									<td align="right"><?= $g_keteranganfungsional[$fungsional['idjfungsional']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_fungsional" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>
				
				<?/*
				<div id="items">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="50%" valign="top">
							<? if (count($g_homebase) > 0) {	?>
							<table>
								<tr>
									<td><strong>Filter : </strong></td>
									<td><?= $l_jenispegawaihb ?></td>
								</tr>
							</table>
							<br>
							<table cellpadding="4" cellspacing="0" class="GridStyle">
								<tr class="DataBG">
									<td>Kode</td>
									<td>Keterangan</td>
									<td>Jumlah</td>
								</tr>
								<? 
								$i=0;$total=0;
								foreach($g_homebase as $homebase){ 
									$total += $g_keteranganhomebase[$homebase['kodeunit']];
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								?>
								<tr class="<?= $rowstyle ?>">
									<td><?= $homebase['kodeunit']; ?></td>
									<td style="padding-left:<?= $homebase['level']*10?>px;"><?= $homebase['namaunit']; ?></td>
									<td align="right"><?= $g_keteranganhomebase[$homebase['kodeunit']]; ?></td>
								</tr>
								<? } ?>
								<tr>
									<td colspan="2" align="center" class="FootBG"><b>Total</b></td>
									<td align="right" class="FootBG"><b><?= $total?></b></td>
								</tr>
							</table>
							<? } ?>
							</td>
							<td width="50%" valign="top">
								<div id="container_homebase" style="width:500px;height:300px"></div>
							</td>
						</tr>
					</table>
				</div>
				*/?>
			</div>
		</div>
		
		<input type="hidden" name="idx" id="idx">
		<input type="hidden" name="act" id="act">
		<input type="hidden" name="key" id="key">
		</form>
		<br>
		<? } ?>
		<div class="SideItem" style="width:60%">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/NEWS.png" onerror="loadDefaultActImg(this)"> Pengumuman
			</div>
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideTitle" colspan="2">Daftar Pengumuman<div class="Break"></div><div class="Break"></div></td>
				</tr>
				<?	
				if (count($a_pengumuman) > 0) {
					foreach($a_pengumuman as $row) { 
						if(!empty($row['tglselesai'])){
							if(date('Y-m-d') >= $row['tglmulai'] and date('Y-m-d') <= $row['tglselesai']){
				?>
				<tr>
					<td align="right" colspan="2">Posting : <?= CStr::formatDateInd($row['tglmulai']) ?></td>
				</tr>
				<tr style="border-bottom:1px solid black">
					<td colspan="2">
					<img src="<?= Route::navAddress('img_datathumb&type=pengumuman&id='.$row['idpengumuman']) ?>">
					<div class="Break"></div>
					<div class="SideSubTitle"><?= $row['judulpengumuman'] ?></div>
					<div class="Break"></div>
					<div class="NewsContent"><?= CStr::cBrief($row['isipengumuman']) ?></div>
					<div class="Break"></div>
					<u class="ULink" onclick="javascript:openDetail('<?= $row['idpengumuman'] ?>')">Selengkapnya...</u>
					<div class="Break"></div><div class="Break"></div>
					</td>
				</tr>
				<? }}else{?>
				<tr>
					<td align="right" colspan="2">Posting : <?= CStr::formatDateInd($row['tglmulai']) ?></td>
				</tr>
				<tr style="border-bottom:1px solid black">
					<td colspan="2">
					<img src="<?= Route::navAddress('img_datathumb&type=pengumuman&id='.$row['idpengumuman']) ?>">
					<div class="Break"></div>
					<div class="SideSubTitle"><?= $row['judulpengumuman'] ?></div>
					<div class="Break"></div>
					<div class="NewsContent"><?= CStr::cBrief($row['isipengumuman']) ?></div>
					<div class="Break"></div>
					<u class="ULink" onclick="javascript:openDetail('<?= $row['idpengumuman'] ?>')">Selengkapnya...</u>
					<div class="Break"></div><div class="Break"></div>
					</td>
				</tr>
				<?	}}} ?>
			</table>
			<br>
		</div>
		<div class="SideItem" style="float:right;width:30%">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/INFO.png" onerror="loadDefaultActImg(this)"> INFORMASI
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td colspan="3">
						<img width="16px" src="images/aktivitas/BIODATA.png" onerror="loadDefaultActImg(this)">
						&nbsp; <?= Modul::getUserName() ?> - <?= Modul::getUserDesc() ?>,
						<br><span class="SideSubTitle">Login : </span> <?= CStr::formatDateTimeInd(Modul::getLastLogin(),false,true) ?>
					</td>
				</tr>
			</table>
			<br>
			
			<? if (count($a_notice) > 0) {?>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/NOTIFICATION.png" onerror="loadDefaultActImg(this)"> NOTIFIKASI
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<? foreach($a_notice as $col){?>
				<tr>
					<td colspan="3">
						<img width="16px" src="images/aktivitas/NOTIFICATION.png" onerror="loadDefaultActImg(this)">
						<span class="NewsContent"><?= CStr::cBrief($col['pesan']) ?></span>
						<u class="ULink" onclick="javascript:openDetailNotice('<?= $col['idpesan'] ?>','<?= $col['ispopup']?>','<?= Route::navAddress($col['namafile'])?>')">Selengkapnya...</u>
						<div class="Break"></div><div class="Break"></div>
					</td>
				</tr>
				<? } ?>
			</table>
			<br>
			<? } ?>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/DOCUMENT.png" onerror="loadDefaultActImg(this)"> USER GUIDE
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td>
						<?if(Modul::getRole() == 'admhrm' or Modul::getRole() == 'A'){?>
						<u class="ULink" onclick="javascript:goDownload('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('ug'); ?>','admin')" target="_blank"><img width="16px" src="images/aktivitas/DOWNLOAD.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download UG Administrator </span></u> <br />

						<!-- portal rekrutmen-->
						<u class="ULink" onclick="javascript:goDownload('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('ug'); ?>','portal')" target="_blank"><img width="16px" src="images/aktivitas/DOWNLOAD.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download UG Portal Rekrutmen </span></u> <br />
						<?}?>
						<?if(Modul::getRole() == 'admga' or Modul::getRole() == 'gajihrm' or Modul::getRole() == 'A'){?>
						<u class="ULink" onclick="javascript:goDownload('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('ug'); ?>','admingaji')" target="_blank"><img width="16px" src="images/aktivitas/DOWNLOAD.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download UG Penggajian </span></u> <br />
						<?}?>
						<u class="ULink" onclick="javascript:goDownload('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('ug'); ?>','peg')"><img width="16px" src="images/aktivitas/DOWNLOAD.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download UG Pegawai </span></u>
					</td>
				</tr>
			</table>

			<!-- untuk link quisioner-->
			<?php
			if(count($a_kuesioner)>0){
			?>
			<br>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/DATA.png" onerror="loadDefaultActImg(this)"> LINK KUESIONER
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<?php
				foreach ($a_kuesioner as $key => $value) {
				?>
				<tr>
					<td>
						<u class="ULink" onclick="javascript:isiKuesioner('<?php echo $key?>')"nk">
							<img width="16px" src="images/aktivitas/LAPORAN.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> <?php echo $value;?> </span>
						</u>
					</td>
				</tr>
			<?php }?>
			</table>
			<?php }?>
		</div>
	</div>
</div>
</body>
<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	var detform = "<?= Route::navAddress('pop_pengumuman') ?>";
	var detformnote = "<?= Route::navAddress('pop_notice') ?>";

	<? if ($c_dashboard) { ?>
	$(function () {
    	 var chart_tipebaru,chart_jenisdosen,chart_jeniskependidikan,chart_kelompokpegadm,chart_kelompokpegnonadm,chart_fungsional;
    	 //var chart_tipe, chart_jenis, chart_hubungan, chart_pangkat, chart_fungsional, chart_homebase;
	
		$(document).ready(function() {
			 initTab('<?= $_POST['idx']?>');
			 
			chart_tipebaru = new Highcharts.Chart({
				chart: {
					renderTo: 'container_tipebaru',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Tipe Pegawai'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Tipe Pegawai',
					data: [<?= $g_graphtipebaru; ?>]
				}]
			});

			chart_jenisdosen = new Highcharts.Chart({
				chart: {
					renderTo: 'container_jenisdosen',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Jenis Pegawai Dosen'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Jenis Pegawai Dosen',
					data: [<?= $g_graphjenisdosen; ?>]
				}]
			});

			chart_jeniskependidikan = new Highcharts.Chart({
				chart: {
					renderTo: 'container_jeniskependidikan',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Jenis Pegawai Kependidikan'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Jenis Pegawai Kependidikan',
					data: [<?= $g_graphjeniskependidikan; ?>]
				}]
			});

			chart_kelompokpegadm = new Highcharts.Chart({
				chart: {
					renderTo: 'container_kelompokpegadm',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Kelompok Pegawai Administrasi'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Kelompok Pegawai Administrasi',
					data: [<?= $g_graphkelompokpegadm; ?>]
				}]
			});

			chart_kelompokpegnonadm = new Highcharts.Chart({
				chart: {
					renderTo: 'container_kelompokpegnonadm',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Kelompok Pegawai Non Administrasi'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Kelompok Pegawai Non Administrasi',
					data: [<?= $g_graphkelompokpegnonadm; ?>]
				}]
			});
			 
			
			/*
			chart_tipe = new Highcharts.Chart({
				chart: {
					renderTo: 'container_tipe',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Tipe Pegawai'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Tipe Pegawai',
					data: [<?= $g_graphtipe; ?>]
				}]
			});

			chart_jenis = new Highcharts.Chart({
				chart: {
					renderTo: 'container_jenis',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Jenis Pegawai'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Jenis Pegawai',
					data: [<?= $g_graphjenis; ?>]
				}]
			});
			 
			chart_statusaktif = new Highcharts.Chart({
				chart: {
					renderTo: 'container_statusaktif',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Status Aktif'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Status Aktif',
					data: [<?= $g_graphstatusaktif; ?>]
				}]
			});
			
			chart_hubungan = new Highcharts.Chart({
				chart: {
					renderTo: 'container_hubungan',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Hubungan Kerja'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Hubungan Kerja',
					data: [<?= $g_graphubungan; ?>]
				}]
			});
			
			chart_pangkat = new Highcharts.Chart({
				chart: {
					renderTo: 'container_pangkat',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Golongan'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Golongan',
					data: [<?= $g_grappangkat; ?>]
				}]
			});
			*/

			chart_fungsional = new Highcharts.Chart({
				chart: {
					renderTo: 'container_fungsional',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Jabatan Akademik'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Jabatan Akademik',
					data: [<?= $g_grapfungsional; ?>]
				}]
			});
			
			/*
			chart_homebase = new Highcharts.Chart({
				chart: {
					renderTo: 'container_homebase',
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: {
					text: 'Berdasarkan Homebase'
				},
				tooltip: {
					pointFormat: '<strong>{point.percentage}%</strong>',
					percentageDecimals: 2
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							distance: 5,
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Jumlah Pegawai Berdasarkan Homebase',
					data: [<?= $g_graphomebase; ?>]
				}]
			});
			*/
        });
		
	});
	<? } ?>
	
	function openDetail(pkey){
		$.ajax({
			url: detform,
			type: "POST",
			data: {key : pkey},
			success: function(data){
				$.facybox(data);
			}
		});
	}
	
	function openDetailNotice(pkey,ispop,file){
		if(ispop == 'Y'){
			$.ajax({
				url: detformnote,
				type: "POST",
				data: {key : pkey},
				success: function(data){
					$.facybox(data);
				}
			});
		}else{
			$('#pageform').attr('action', file);
			$('#pageform').attr('target', '_blank');
			$('#pageform').submit();
			$('#pageform').attr('action', '');
			$('#pageform').attr('target', '');
		}
	}
	
	function initTab(tab) {
		$("div.tabs a").click(function() {
			var index = $("div.tabs a").index(this);
			
			$("#idx").val(index);
			$("div.tabs li").removeAttr("class");
			$(this).parent("li").attr("class","selected");
			
			$("div[id='items']").hide();
			$("div[id='items']").eq(index).show();
		});
		
		chooseTab(tab);
	}

	function chooseTab(idx) {
		$("div.tabs a").eq(idx).triggerHandler("click");
	}
	
	function goCloseNotice(notice){
		$("#"+notice).hide();
	}

	function isiKuesioner(kue){
		$("#act").val('isikuesioner');
		$("#key").val(kue);
		goSubmit();
	}
	
</script>
</html>
