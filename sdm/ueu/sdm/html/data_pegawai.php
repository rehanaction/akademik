<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('form'));
	
	// variabel esensial
	if(SDM::isPegawai())
		$r_self = 1;
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	if(empty($r_key) and Modul::getRole() != 'A' and Modul::getRole() != 'admhrm')
		Route::navigate('home');
		
	$r_idx = CStr::removeSpecial($_REQUEST['idx']);
	$r_link = CStr::removeSpecial($_REQUEST['link']);
	
	// definisi variabel halaman
	$p_title = 'Data Pegawai';
	$p_window = '['.$conf['page_title'].'] '.$p_title;
	$p_tbwidth = '860';
	$p_col = 4;
	$p_listpage = 'list_pegawai';
	$dirfoto = 'fotopeg';
	$p_foto = uForm::getPathImageFoto($conn,$r_key,$dirfoto,true);
	
	$p_model = mPegawai;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);	
	$c_readlist = empty($a_authlist) ? false : true;
	
	if (!empty($r_key)){
		$info = $p_model::getSimplePegawai($conn,$r_key);
		$r_namalengkap = $info['namalengkap'];
		$p_label = $r_namalengkap.(!empty($info['nip']) ? ' | '.$info['nip'] : '');
		
		//tipe pegawai
		$tipepeg = $p_model::getTipePegawai($conn, $r_key);
	}else{
		$p_label = "Tambah Pegawai Baru";
	}
	//print_r("a");
	
	
	
?>
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?= $p_window ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<style type="text/css" media="screen">@import "style/tabs.css";</style>
	<link href="style/style.css" type="text/css" rel="stylesheet">
	<link href="style/pager.css" type="text/css" rel="stylesheet">
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		.EditInfo {
			color:#000;
			font-weight:bolder;
			background: url('images/kate.png');
			background-position:right;
			background-repeat:no-repeat;
		}
		
		.DTitle {
			color: #647287;
			font-size: 18px;
			font-weight: bold;
		}
	</style>
</head>

<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
				<div class="DTitle" style="width:<?= $p_tbwidth ?>px;">
					<table border="0" cellspacing="10" align="left">
						<tr>
							<td valign="bottom" align="left" width="150px">
								<table border="0" cellspacing="10" align="left">
									<tr>
										<?	if($c_readlist) { ?>
										<td id="be_list" class="TDButton" onClick="location.href='<?= Route::navAddress($p_listpage); ?>'">
											<img src="images/list.png"> Daftar
										</td>
										<?}?>
									</tr>
								</table>
							</td>
							<td valign="bottom" width="600px" align="right"><div id="labelpeg"><?= $p_label ?></div></td>
							<td align="right" width="75px">
								<img id="imgfotobio" style="padding:3px;border:1pt solid #ccc;" src="<?= $p_foto?>" width="55" height="75">
							</td>
						</tr>
					</table>
				</div>
				<div style="border-bottom: 1px solid #EE4037;width:<?= $p_tbwidth ?>px;padding-top:100px;"></div>
			</center>
			<br />
				
			<table align="center" width="<?= $p_tbwidth; ?>"><tr><td>
			<div id="header">
			
			<ul id="primary_t" style="display:none;">
				<li><span>Biodata</span>
					<ul id="secondary">
						<li id="chosen"><a href="<?= Route::navAddress('data_addpegawai'); ?>" >Tambah Pegawai</a></li>
					</ul>
				</li>
			</ul>
			<ul id="primary">
				<li><span>Biodata</span>
					<ul id="secondary">
						<li id="inshide"><a href="<?= Route::navAddress('data_biodataq'); ?>" >Ringkasan Biodata</a></li>
						<li><a href="<?= Route::navAddress('data_biodata'); ?>" >Edit Biodata</a></li>
						<li><a href="<?= Route::navAddress('data_kepegawaian'); ?>" >Edit Kepegawaian</a></li>
						<li><a href="<?= Route::navAddress('list_istrisuami'); ?>" >Istri Suami</a></li>
						<li><a href="<?= Route::navAddress('list_anak'); ?>" >Anak</a></li>
					</ul>
				</li>
				<li><span>Riwayat</span>
					<ul id="secondary">
						<li><a href="<?= Route::navAddress('list_rpendidikan'); ?>" >Pendidikan</a></li>
						<li><a href="<?= Route::navAddress('list_rpangkat'); ?>" >Pangkat/Golongan</a></li>
						<?if($tipepeg == 'D' or $tipepeg == 'AD'){?>
						<li><a href="<?= Route::navAddress('list_rjabatanakd'); ?>" >Jabatan Akademik</a></li>
						<li><a href="<?= Route::navAddress('list_rjabatankop'); ?>" >Jabatan Kopertis</a></li>
						<?}?>
						<li><a href="<?= Route::navAddress('list_rjabatanstruk'); ?>" >Jabatan Struktural</a></li>
						<li><a href="<?= Route::navAddress('list_rmutasi'); ?>" >Mutasi</a></li>
						<li><a href="<?= Route::navAddress('list_rhubungankerja'); ?>" >Hubungan Kerja</a></li>
						<li><a href="<?= Route::navAddress('list_rriwayataktif'); ?>" >Riwayat Aktif</a></li>
					</ul>
				</li>
				<li><span>Pekerjaan</span>
					<ul id="secondary">
						<li><a href="<?= Route::navAddress('data_ppemberhentian'); ?>" >Pemberhentian</a></li>
						<?/*<li><a href="<?= Route::navAddress('list_ppenilaiankinerja'); ?>" >Penilaian Kinerja</a></li>*/?>
						<?if($tipepeg == 'D' or $tipepeg == 'AD'){?>
						<li><a href="<?= Route::navAddress('list_phomebase'); ?>" >Homebase</a></li>
						<?}?>
						<li><a href="<?= Route::navAddress('list_ppengalamankerja'); ?>" >Pengalaman Kerja</a></li>
					</ul>
				</li>
				<li><span>Presensi</span>
					<ul id="secondary">
						<li><a href="<?= Route::navAddress('list_pkehadiran'); ?>" >Kehadiran</a></li>
						<li><a href="<?= Route::navAddress('list_ppresensi'); ?>" >Input Presensi</a></li>
						<li><a href="<?= Route::navAddress('list_perubahankerja'); ?>" >Perubahan Jam Kerja</a></li>
						<li><a href="<?= Route::navAddress('list_plembur'); ?>" >Lembur</a></li>
					</ul>
				</li>
				<li><span>Pengembangan</span>
					<ul id="secondary">
						<li><a href="<?= Route::navAddress('list_gorganisasi'); ?>" >Organisasi</a></li>
						<li><a href="<?= Route::navAddress('list_gstudilanjut'); ?>" >Studi Lanjut</a></li>
						<li><a href="<?= Route::navAddress('list_gkedinasan'); ?>" >Tugas Kedinasan</a></li>
						<li><a href="<?= Route::navAddress('list_gsertifikasi'); ?>" >Sertifikat</a></li>
						<li><a href="<?= Route::navAddress('list_gpenelitian'); ?>" >Penelitian</a></li>
						<li><a href="<?= Route::navAddress('list_gpkm'); ?>" >Abdimas</a></li>
						<li><a href="<?= Route::navAddress('list_gkemampuanbhs'); ?>" >Kemampuan Bahasa</a></li>
					</ul>
				</li>
				<li><span>Penghargaan</span>
					<ul id="secondary">
						<li><a href="<?= Route::navAddress('list_repenghargaan'); ?>" >Penghargaan</a></li>
						<li><a href="<?= Route::navAddress('list_resanksi'); ?>" >Sanksi</a></li>
						<li><a href="<?= Route::navAddress('list_repiagam'); ?>" >Piagam</a></li>
					</ul>
				</li>
				<li><span>Permohonan</span>
					<ul id="secondary">
						<li><a href="<?= Route::navAddress('list_wcuti'); ?>" >Cuti</a></li>
						<?/*<li><a href="<?= Route::navAddress('list_wpinjaman'); ?>" >Pinjaman</a></li>*/?>
					</ul>
				</li>
				<?if($tipepeg == 'D' or $tipepeg == 'AD'){?>
				<li><span>KUM</span>
					<ul id="secondary">
						<li><a href="<?= Route::navAddress('list_akbidangsatua'); ?>" >Bidang IA</a></li>
						<li><a href="<?= Route::navAddress('list_akbidangsatub'); ?>" >Bidang IB</a></li>
						<li><a href="<?= Route::navAddress('list_akbidangdua'); ?>" >Bidang II</a></li>
						<li><a href="<?= Route::navAddress('list_akbidangtiga'); ?>" >Bidang III</a></li>
						<li><a href="<?= Route::navAddress('list_akbidangempat'); ?>" >Bidang IV</a></li>
						<li><a href="<?= Route::navAddress('list_aksimulasismtr'); ?>" >Simulasi Semester</a></li>
						<li><a href="<?= Route::navAddress('list_aksimulasi'); ?>" >Simulasi</a></li>
					</ul>
				</li>
				<?}?>
			</ul>
			</div>
			<div id="main">
				<div id="contents"></div>
			</div>
			
			<div id="roller" style="position:absolute;visibility:hidden;left:0px;top:0px;">
				<img src="images/roller.gif">
			</div>
			
			<div id="progressbar" style="position:absolute;visibility:hidden;left:0px;top:0px;">
				<table bgcolor="#FFFFFF" border="1" style="border-collapse:collapse;"><tr><td align="center">
				Mohon tunggu...<br><br><img src="images/progressbar.gif">
				</td></tr></table>
			</div>
		</div>
	</div>
</div>
</body>

<script type="text/javascript" src="scripts/jquery.common.js"></script>
<script type="text/javascript" src="scripts/commonx.js"></script>
<script type="text/javascript" src="scripts/jquery.xtabs.js"></script>
<script type="text/javascript">
	var listpage = "<?= Route::navAddress($p_listpage) ?>";
	var key = "<?= $r_key; ?>";
	var defsent = "key=" + key;
	
	$(function() {
		if(key == "") { // insert mode
			switchTab();
		}
		$("#header").xtabs({sent:defsent,deftab:"<?= empty($r_idx) ? '0' : $r_idx ?>",deflink:"<?= empty($r_link) ? '0' : $r_link ?>"});
		if(key == "") { // insert mode
			$("#header").showOnlyTab(0);
		}
	});
	
	function showRealTab() {
		$("#header").showrealtab(defsent);
	}
	
	function switchTab() {
		$("#primary").hide();
		$("#primary").attr("id","primary_t");
		$("#primary_t").attr("id","primary");
		$("#primary").show();
	}
</script>
</html>
