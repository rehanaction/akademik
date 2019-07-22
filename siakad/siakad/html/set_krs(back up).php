<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_buka = $a_auth['canother']['B'];
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getModelPath('transkrip'));
	require_once(Route::getModelPath('setting'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_act = $_POST['act'];
	if(!Akademik::isMhs()) {
		$display="block";
		if(empty($r_key)) {
			// cek aksi
			$r_nim = CStr::removeSpecial($_REQUEST['npm']);
			if(Akademik::isDosen()){
				$r_nip = Modul::getUserName();
				$display="none";
				}
			else
				$r_nip = '';
			
			if($r_act == 'first')
				$r_key = mMahasiswa::getFirstNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'prev')
				$r_key = mMahasiswa::getPrevNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'next')
				$r_key = mMahasiswa::getNextNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'last')
				$r_key = mMahasiswa::getLastNIM($conn,$r_nim,$r_nip);
			else
				$r_key = $r_nim;
		}
	}
	else{
		$r_key = Modul::getUserName();
		$display="none";
	}
	
	$r_periode = Akademik::getPeriode();
	
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	//die($r_key);
	$r_keywali = $r_key.'|'.$r_periode;
	
	// properti halaman
	$p_title = 'Kartu Rencana Studi (KRS)';
	$p_tbwidth = 700;
	$p_lwidth = 550;
	$p_aktivitas = 'ABSENSI';
	$p_model = mKRS;
	
	// cek periode dan isi biodata
	$a_postmsg = array();
	
	if(Akademik::getTahap() != 'KRS') {
		$p_sposterr = true;
		$a_postmsg[] = 'Tahap KRS belum dibuka';
	}
	
	else if(Akademik::isMhs() and empty($a_infomhs['biodataterisi'])) {
		$p_sposterr = true;
		$a_postmsg[] = 'Biodata belum diisi lengkap, untuk mengisi biodata klik <u class="ULink" onclick="goOpen(\'data_mahasiswa&self=1\')">di sini</u>';
	}/*else if(!mKrs::cekQuisioner($conn,$r_key,$r_periode-1)){
		$p_sposterr = true;
		$a_postmsg[] = 'Quisioner '.Akademik::getNamaPeriode($r_periode-1).' belum diisi lengkap, untuk mengisi klik <u class="ULink" onclick="goOpen(\'list_mkquiz\')">di sini</u>';
	}*/
	
	if($p_sposterr) {
		$c_insert = false;
		$c_update = false;
		$c_delete = false;
	}
	
	if($c_buka) {
		$c_insert = true;
		$c_delete = true;
	}
	
	// cek perwalian (atas)
	$sql = mPerwalian::dataQuery($r_keywali);
	$a_wali = $conn->GetRow($sql);
	
	if(empty($a_wali['prasyaratspp'])) {
		$c_insert = false;
		$c_update = false;
		$c_delete = false;
	}
	if(!empty($a_wali['frsdisetujui'])) {
		$c_insert = false;
		$c_delete = false;
	}
	//echo $c_update."ok";die();
	// ada aksi
	if($r_act == 'insert' and $c_insert) {
		$a_mkambil = array();
		if(!empty($_POST['mkambil'])) {
			foreach($_POST['mkambil'] as $t_key)
				$a_mkambil[CStr::removeSpecial($t_key)] = true;
		}
		
		$ok = true;
		// $conn->BeginTrans();
		
		$t_posterr = false;
		$t_postmsg = '';
		
		if(Akademik::isMhs()) {
			foreach($a_mkambil as $t_key => $t_true) {
				list($t_kurikulum,$t_kodemk,$t_kodeunit,$t_kelasmk) = explode('|',$t_key);
				
				list($p_posterr,$p_postmsg) = $p_model::insertByMhs($conn,$t_kurikulum,$r_periode,$t_kodeunit,$t_kodemk,$t_kelasmk,$r_key);
				if($p_posterr) {
					$ok = false;
					// break;
					
					$t_posterr = true;
					if($p_postmsg != $t_postmsg) {
						if(!empty($t_postmsg)) $t_postmsg .= '<br>';
						$t_postmsg .= $p_postmsg;
					}
				}
			}
		}
		else {
			$record = array();
			$record['nim'] = $r_key;
			$record['periode'] = $r_periode;
			// $record['thnkurikulum'] = $a_infomhs['thnkurikulum'];
			// $record['kodeunit'] = $a_infomhs['kodeunit'];
			$record['semestermhs'] = $a_infomhs['semmhs'];
			
			foreach($a_mkambil as $t_key => $t_true) {
				list($record['thnkurikulum'],$record['kodemk'],$record['kodeunit'],$record['kelasmk']) = explode('|',$t_key);
				
				list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
				if($p_posterr) {
					$ok = false;
					// break;
					
					$t_posterr = true;
					if($p_postmsg != $t_postmsg) {
						if(!empty($t_postmsg)) $t_postmsg .= '<br>';
						$t_postmsg .= $p_postmsg;
					}
				}
			}
		}
		
		// $conn->CommitTrans($ok);
		
		$p_posterr = $t_posterr;
		if(!empty($t_postmsg))
			$p_postmsg = $t_postmsg;
	}
	else if($r_act == 'delete' and $c_delete) {
		$t_key = CStr::removeSpecial($_POST['key']);
		list($t_kurikulum,$t_kodemk,$t_kodeunit,$t_kelasmk) = explode('|',$t_key);
		
		if(Akademik::isMhs()) {
			list($p_posterr,$p_postmsg) = $p_model::deleteByMhs($conn,$t_kurikulum,$r_periode,$t_kodeunit,$t_kodemk,$t_kelasmk,$r_key);
		}
		else {
			$t_key = $t_kurikulum.'|'.$t_kodemk.'|'.$t_kodeunit.'|'.$r_periode.'|'.$t_kelasmk.'|'.$r_key;
			
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$t_key);
		}
	}
	else if($r_act == 'kunci' and $c_update) {
		$record = array();
		$record['frsdisetujui'] = -2;
		
		list($p_posterr,$p_postmsg) = mPerwalian::updateRecord($conn,$record,$r_keywali,true);
		
		if(!$p_posterr) {
			$a_flash = array();
			$a_flash['r_key'] = $r_key;
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash);
		}
	}
	else if($r_act == 'buka' and $c_update and $c_buka) {
		$record = array();
		$record['frsdisetujui'] = 0;
		
		list($p_posterr,$p_postmsg) = mPerwalian::updateRecord($conn,$record,$r_keywali,true);
		
		if(!$p_posterr) {
			$a_flash = array();
			$a_flash['r_key'] = $r_key;
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash);
		}
	}
	
	//cek masa studi
	// cek perwalian (bawah)
	if(empty($a_wali['prasyaratspp'])) {
		/*$p_sposterr = true;
		$a_postmsg[] = 'Anda Belum Bayar SPP';
		
		$c_insert = false;
		$c_update = false;
		$c_delete = false;*/
	}
	if(!empty($a_wali['frsdisetujui'])) {
		$p_sposterr = true;
		if($a_wali['frsdisetujui'] == -1) {
			$a_postmsg[] = 'KRS Sudah Diprint, tunggu waktu Revisi untuk mengubah';
			$c_update = false;
		}
		else if($a_wali['frsdisetujui'] == -2) {
			$t_postmsg = 'KRS sudah dikunci';
			if(!empty($a_wali['t_updatetime']))
				$t_postmsg .= ' pada tanggal '.CStr::formatDateTimeInd($a_wali['t_updatetime']);
			if(!$c_buka)
				$t_postmsg .= ', hubungi petugas akademik untuk mengubah';
			$a_postmsg[] = $t_postmsg;
		}
		
		$c_insert = false;
		$c_delete = false;
	}
	
	
	
	// mendapatkan krs
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_tidaklulus = mTranskrip::getDataTidakLulus($conn,$r_key);
	
	$t_lintas = mSetting::getLintasKurikulum($conn);
	$a_kelas = mKelas::getDataPeriode($conn,$r_periode,$a_infomhs['kurikulum'],$a_infomhs['kodeunit'],$t_lintas);
	//$a_kelas = mKelas::getDataPeriodeTh($conn,$r_periode,$a_infomhs['kurikulum'],$a_infomhs['kodeunit'],$t_lintas);
	$a_jadwal = mKelas::getFormatJadwal($a_kelas);
	
	if(Akademik::isMhs())
		$a_kelas = mKelas::getFormatPerJadwal($a_kelas);
	else
		$a_kelas = mKelas::getFormatPerSemester($a_kelas);
	//$r_periode-=1;
	$a_data = mKRS::getDataPeriode($conn,$r_key,$r_periode);
	
	//cek jumlah semester mahasiswa
	$infoProgpend=mPerwalian::infoProgpend($conn,$r_key);
	$infoMhs=mPerwalian::infoMhs($conn,$r_key);
	if($infoMhs['jum_smt']>$infoProgpend['lamastudi']){
		$p_sposterr = true;
		$a_postmsg[] = 'Anda Sudah Melampaui Batas Studi Maksimal';
	}
	if($infoMhs['jum_smt']==$infoProgpend['lamastudi']){
		$p_sposterr = true;
		$a_postmsg[] = 'Ini Merupakan Semester Terakhir Anda';
	}
	//cek status KRS per prodi
	if(mKRS::getStatusKrs($conn,$a_infomhs['kodeunit'])!='') {
		
		$p_sposterr = true;
		$a_postmsg[] = 'Periode KRS Prodi '.mKRS::getStatusKrs($conn,$a_infomhs['kodeunit']).' belum dibuka';
		
		$c_insert = false;
		$c_update = false;
		$c_delete = false;
	}
	if(!empty($a_postmsg)) {
		$p_posterr = $p_sposterr;
		$p_postmsg = implode('<br>',$a_postmsg);
	}
	
	// data untuk grafik
	$a_semester = array();
	$a_skssemester = array();
	$a_ipssemester = array();
	$a_nhuruf = array();
	
	$a_datasmt = mKRS::getDataPerSemester($conn,$r_key,$a_infomhs['periodedaftar'],false,true);
	
	foreach($a_datasmt as $t_semester => $t_data) {
		$t_tsks = 0;
		$t_tbobot = 0;
		
		foreach($t_data as $row) {
			$t_sks = (int)$row['sks'];
			$t_tsks += $t_sks;
			
			if(!empty($row['nilaimasuk'])) {
				$t_bobot = $t_sks * (float)$row['nangka'];
				$t_tbobot += $t_bobot;
				
				$t_nh = trim($row['nhuruf']);
				$a_nhuruf[$t_nh]++;
			}
		}
		
		$t_ttsks += $t_tsks;
		$t_ttbobot += $t_tbobot;
		
		if($t_tsks == 0)
			$t_ips = 0;
		else
			$t_ips = number_format(round($t_tbobot/$t_tsks,2),2);
		
		// untuk grafik
		if(!empty($t_semester)) {
			$a_semester[] = $t_semester;
			$a_skssemester[] = $t_tsks;
			$a_ipssemester[] = $t_ips;
		}
	}
	
	// menghitungan persentase nilai huruf
	ksort($a_nhuruf);
	
	$t_jumlahnilai = 0;
	foreach($a_nhuruf as $t_nhuruf => $t_jumlah)
		$t_jumlahnilai += $t_jumlah;
	
	$a_nhurufpie = array();
	foreach($a_nhuruf as $t_nhuruf => $t_jumlah)
		$a_nhurufpie[] = "'$t_nhuruf', ".round(($t_jumlah*100)/$t_jumlahnilai,2);
		
		
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
	<script type="text/javascript" src="scripts/perwalian.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<?php require_once('inc_headermahasiswa.php'); ?>
		<form name="pageform" id="pageform" method="post">
			 
			<?php require_once('inc_headermhs_krs.php') ?>
		 
			<br>
	<div style="width:860px;margin-left:120px">
		<div style="float:left;width:<?= $p_lwidth ?>px">
			<?	if(!empty($p_postmsg)) { ?>
			<center>
			<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_lwidth ?>px">
				<?= $p_postmsg ?>
			</div>
			</center>
			<div class="Break"></div>
			<?	} 
				if($c_update and empty($a_wali['frsdisetujui'])) { ?>
			<center>
			<div style="width:<?= $p_lwidth ?>px">
				<strong>Setelah KRS disetujui dosen wali harap klik tombol kunci di bawah ini*</strong>
			</div>
			</center>
			<div class="Break"></div>
			<?	} ?>
			<center>
				<header style="width:<?= $p_lwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							<h1><?= $p_title ?> - <?= Akademik::getNamaPeriode($r_periode) ?></h1>
						</div>
						<div class="right">
							<img title="Cetak KRS" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
							<?	if($c_update) {
									if(empty($a_wali['frsdisetujui'])) { ?>
							<img title="Kunci KRS" width="24px" src="images/tablelock.png" style="cursor:pointer" onclick="goLock()">
							<?		}
									else if($c_buka) { ?>
							<img title="Buka Kunci KRS" width="24px" src="images/tableunlock.png" style="cursor:pointer" onclick="goUnlock()">
							<?		}
								} ?>
						</div>
					</div>
				</header>
			</center>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<th width="25">No.</th>
		<th width="90">Kode</th>
		<th>Nama Matakuliah</th>
		<th width="40">Kelas</th>
		<th width="30">SKS</th>
		<th width="100">Waktu</th>
		<? if($c_delete) { ?>
		<th>Aksi</th>
		<? } ?>
	</tr>
<?php
	$i = 0;
	$t_totalsks = 0;
	foreach($a_data as $row) {
		if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
		
		$t_sks = (int)$row['sks'];
		$t_totalsks += $t_sks;
		$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td><?= $i ?>.</td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $t_sks ?></td>
		<td><?= $a_jadwal[$t_key] ?></td>
		<? if($c_delete) { ?>
		<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
		<? } ?>
	</tr>
<?php
	}
?>
	<tr>
		<th colspan="4">Total SKS</th>
		<th><?= $t_totalsks ?></th>
		<th colspan="2">&nbsp;</th>
	</tr>
</table>
<?	if($c_update and empty($a_wali['frsdisetujui'])) { ?>
<div class="Break"></div>
<center>
<div style="width:<?= $p_lwidth ?>px">
	* Jika KRS sudah disetujui dosen wali namun KRS belum dikunci, maka segala perubahan yang terjadi setelahnya bukan tanggung jawab petugas TU
</div>
</center>
<?	}
	if($c_insert) { ?>
			<br>
			<? /* <center>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img width="24px" src="images/aktivitas/DEFAULT.png">
							<h1>Daftar Kelas Perkuliahan</h1>
						</div>
					</div>
				</header>
			</center> */ ?>
			<center>
				<div class="ViewTitle" style="width:<?= $p_lwidth ?>px;">
					<span>
						<img id="img_workflow" width="24px" src="images/aktivitas/DEFAULT.png" onerror="loadDefaultActImg(this)">
						&nbsp;Pilihan Kelas Perkuliahan
						<div style="float:right">
							<input type="button" value="Tampilkan Daftar" class="ControlStyle" onClick="showPilihan()">
						</div>
					</span>
				</div>
			</center>
			<br>
<div id="div_pilihan" style="display:none">
<?php
	if(Akademik::isMhs()) {
?>
<? /* <table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<th width="25">&nbsp;</th>
		<th width="60">Mulai</th>
		<th width="60">Selesai</th>
		<th width="90">Kode</th>
		<th>Nama MataKuliah</th>
		<th width="40">Kelas</th>
		<th width="30">SKS</th>
	</tr>
<?php
		$i = 0;
		foreach($a_kelas as $t_nohari => $t_kelas) {
?>
	<tr bgcolor="#B4F6B5">
		<td class="LiteHeaderBG" align="center" colspan="7"><?= Date::indoDay($t_nohari) ?></td>
	</tr>
<?php
			foreach($t_kelas as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
				
				// pewarnaan
				$t_class = '';
				if($row['semmk'] == $a_infomhs['semmhs'])
					$t_class = 'YellowBG';
				else if($row['semmk'] < $a_infomhs['semmhs'] and $a_tidaklulus[$row['kodemk']])
					$t_class = 'RedBG';
?>
	<tr class="<?= $rowstyle ?> <?= $t_class ?>">
		<td><input type="checkbox" name="mkambil[]" value="<?= $t_key ?>"></td>
		<td align="center"><?= $row['jammulai'] ?></td>
		<td align="center"><?= $row['jamselesai'] ?></td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $row['sks'] ?></td>
	</tr>
<?php
			}
		}
		if($i == 0) {
?>
	<tr>
		<td align="center" colspan="7">
			Data kelas prodi <?= $a_infomhs['jurusan'] ?> kurikulum <?= $a_infomhs['kurikulum'] ?> tidak ada
		</td>
	</tr>
<?php
		}
?>
	<tr class="LeftColumnBG">
		<td align="center" colspan="7">
			<input type="button" value="Ambil Mata Kuliah" class="ControlStyle" onClick="goAmbil()">
		</td>
	</tr>
</table> */ ?>
<?php
		foreach($a_kelas as $t_nohari => $t_kelas) {
			$i = 0;
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<th colspan="7" class="SubHeaderBG"><?= Date::indoDay($t_nohari) ?></th>
	</tr>
	<tr>
		<th width="25">&nbsp;</th>
		<th width="60">Mulai</th>
		<th width="60">Selesai</th>
		<th width="90">Kode</th>
		<th>Nama MataKuliah</th>
		<th width="40">Kelas</th>
		<th width="30">SKS</th>
	</tr>
<?php
			foreach($t_kelas as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
				
				// pewarnaan
				$t_class = '';
				if($row['semmk'] == $a_infomhs['semmhs'])
					$t_class = 'YellowBG';
				else if($row['semmk'] < $a_infomhs['semmhs'] and $a_tidaklulus[$row['kodemk']])
					$t_class = 'RedBG';
?>
	<tr class="<?= $rowstyle ?> <?= $t_class ?>">
		<td><input type="checkbox" name="mkambil[]" value="<?= $t_key ?>"></td>
		<td align="center"><?= $row['jammulai'] ?></td>
		<td align="center"><?= $row['jamselesai'] ?></td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $row['sks'] ?></td>
	</tr>
<?php
			}
?>
</table>
<br>
<?php
		}
		if(empty($a_kelas)) {
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<td align="center" colspan="7">
			Data kelas prodi <?= $a_infomhs['jurusan'] ?> kurikulum <?= $a_infomhs['kurikulum'] ?> tidak ada
		</td>
	</tr>
</table>
<?php
		}
		else {
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr class="LeftColumnBG">
		<td align="center" colspan="7">
			<input type="button" value="Ambil Mata Kuliah" class="ControlStyle" onClick="goAmbil()">
		</td>
	</tr>
</table>
<?php
		}
	}
	else {
?>
<? /* <table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<th width="25">&nbsp;</th>
		<th width="90">Kode</th>
		<th>Nama Matakuliah</th>
		<th width="40">Kelas</th>
		<th width="30">SKS</th>
		<th width="200">Waktu</th>
	</tr>
<?php
		// menyusun semester
		$t_semmhs = $a_infomhs['semmhs'];
		
		$a_tempkelas = array();
		foreach($a_kelas as $t_semester => $t_kelas) {
			if($t_semester >= $t_semmhs)
				break;
			
			foreach($t_kelas as $row) {
				if($a_tidaklulus[$row['kodemk']])
					$a_tempkelas[$t_semester.'U'][] = $row;
			}
		}
		
		if(!empty($a_kelas[$t_semmhs]))
			$a_tempkelas[$t_semmhs] = $a_kelas[$t_semmhs];
		
		foreach($a_kelas as $t_semester => $t_kelas) {
			if($t_semester == $t_semmhs)
				continue;
			
			$a_tempkelas[$t_semester] = $t_kelas;
		}
		
		$a_kelas = $a_tempkelas;
		
		$i = 0;
		foreach($a_kelas as $t_semester => $t_kelas) {
			// pewarnaan
			$t_class = '';
			if(substr($t_semester,-1) == 'U') {
				$t_semester = substr($t_semester,0,strlen($t_semester)-1);
				$t_class = 'RedBG';
			}
			else if($t_semester == $a_infomhs['semmhs'])
				$t_class = 'YellowBG';
?>
	<tr class="LiteHeaderBG <?= $t_class ?>">
		<td align="center" colspan="6">SEMESTER <?= $t_semester ?></td>
	</tr>
<?php
			foreach($t_kelas as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
?>
	<tr class="<?= $rowstyle ?>">
		<td><input type="checkbox" name="mkambil[]" value="<?= $t_key ?>"></td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $row['sks'] ?></td>
		<td><?= $a_jadwal[$t_key] ?></td>
	</tr>
<?php
			}
		}
		if($i == 0) {
?>
	<tr>
		<td align="center" colspan="6">
			Data kelas prodi <?= $a_infomhs['jurusan'] ?> kurikulum <?= $a_infomhs['kurikulum'] ?> tidak ada
		</td>
	</tr>
<?php
		}
?>
	<tr class="LeftColumnBG">
		<td align="center" colspan="6">
			<input type="button" value="Ambil Mata Kuliah" class="ControlStyle" onClick="goAmbil()">
		</td>
	</tr>
</table> */ ?>
<?php
		// menyusun semester
		$t_semmhs = $a_infomhs['semmhs'];
		
		$a_tempkelas = array();
		foreach($a_kelas as $t_semester => $t_kelas) {
			if($t_semester >= $t_semmhs)
				break;
			
			foreach($t_kelas as $row) {
				if($a_tidaklulus[$row['kodemk']])
					$a_tempkelas[$t_semester.'U'][] = $row;
			}
		}
		
		if(!empty($a_kelas[$t_semmhs]))
			$a_tempkelas[$t_semmhs] = $a_kelas[$t_semmhs];
		
		foreach($a_kelas as $t_semester => $t_kelas) {
			if($t_semester == $t_semmhs)
				continue;
			
			$a_tempkelas[$t_semester] = $t_kelas;
		}
		
		$a_kelas = $a_tempkelas;
		
		foreach($a_kelas as $t_semester => $t_kelas) {
			$i = 0;
			
			// pewarnaan
			$t_class = '';
			if(substr($t_semester,-1) == 'U') {
				$t_semester = substr($t_semester,0,strlen($t_semester)-1);
				$t_class = 'RedBG';
			}
			else if($t_semester == $a_infomhs['semmhs'])
				$t_class = 'YellowBG';
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<th colspan="7" class="SubHeaderBG <?= $t_class ?>"> <span style="float:left; padding-left:10px">  SEMESTER <?= $t_semester ?> </span><span style="float:right; padding-right:10px"><?= Akademik::getNamaPeriodeTh($r_periode) ?></span></th>
	</tr>
	<tr>
		<th width="25">&nbsp;</th>
		<th width="90">Kode</th>
		<th>Nama Matakuliah</th>
		<th width="40">Kelas</th>
		<th width="30">SKS</th>
		<th width="200">Waktu</th>
	</tr>
<?php
			foreach($t_kelas as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
?>
	<tr class="<?= $rowstyle ?>">
		<td><input type="checkbox" name="mkambil[]" value="<?= $t_key ?>"></td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $row['sks'] ?></td>
		<td><?= $a_jadwal[$t_key] ?></td>
	</tr>
<?php
			}
?>
</table>
<br>
<?php
		}
		if(empty($a_kelas)) {
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<td align="center" colspan="6">
			Data kelas prodi <?= $a_infomhs['jurusan'] ?> kurikulum <?= $a_infomhs['kurikulum'] ?> tidak ada
		</td>
	</tr>
</table>
<?php
		}
		else {
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr class="LeftColumnBG">
		<td align="center" colspan="6">
			<input type="button" value="Ambil Mata Kuliah" class="ControlStyle" onClick="goAmbil()">
		</td>
	</tr>
</table>
<?php
		}
	}
?>
</div>
<? } ?>
		</div>
		<div style="float:left;padding-left:15px">
			<div id="container_sks" style="width:290px;height:200px"></div>
			<br>
			<div id="container_ips" style="width:290px;height:200px"></div>
			<br>
			<div id="container_nh" style="width:290px;height:200px"></div>
		</div>
	</div>
			<input type="hidden" name="act" id="act">
			<input type="hidden" name="key" id="key">
			<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
			<? if(Akademik::isDosen()) { ?>
			<input type="hidden" name="nip" id="nip" value="<?= Modul::getUserName() ?>">
			<? } ?>
		</form>
		</div>		
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	<? if(Akademik::isDosen()) { ?>
	$("#mahasiswa").xautox({strpost: "f=acmhswali", targetid: "npmtemp", postid: "nip"});
	<? } else { ?>
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "npmtemp"});
	<? } ?>
	
	Highcharts.setOptions({
		title: {
			style: {
				fontSize: '14px'
			}
		},
		xAxis: {
			labels: {
				style: {
					fontSize: '10px'
				}
			}
		},
		yAxis: {
			labels: {
				style: {
					fontSize: '10px'
				}
			}
		}
	});
	
	chart_sks = new Highcharts.Chart({
		chart: {
			renderTo: 'container_sks',
			type: 'line'
		},
		title: {
			text: 'SKS Mahasiswa',
			x: -20 //center
		},
		xAxis: {
			title: {
				text: 'Semester'
			},
			categories: ['<?= implode("', '",$a_semester) ?>']
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
					enabled: true,
					style: {
						fontSize: '10px'
					}
				}
			}
		},
		legend: {
			enabled: false
		},
		series: [{
			name: 'SKS',
			data: [<?= implode(', ',$a_skssemester) ?>]
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
			categories: ['<?= implode("', '",$a_semester) ?>'],
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
					enabled: true,
					style: {
						fontSize: '10px'
					}
				}
			}
		},
		legend: {
			enabled: false
		},
		series: [{
			name: 'IPS',
			data: [<?= implode(', ',$a_ipssemester) ?>]
		}]
	});
	
	chart_nh = new Highcharts.Chart({
		chart: {
			renderTo: 'container_nh',
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		title: {
			text: 'Perbandingan Nilai'
		},
		tooltip: {
			pointFormat: '<strong>{point.percentage}%</strong>',
			percentageDecimals: 2
		},
		/* plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false
				},
				showInLegend: true
			}
		}, */
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					color: '#000000',
					connectorColor: '#000000',
					formatter: function() {
						return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
					},
					distance: 5,
					style: {
						fontSize: '10px'
					}
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'Perbandingan Nilai',
			data: [
				[<?= implode('],[',$a_nhurufpie) ?>]
			]
		}]
	});
});
	
function goAmbil() {
	document.getElementById("act").value = "insert";
	goSubmit();
}

function goLock() {
	document.getElementById("act").value = "kunci";
	goSubmit();
}

function goUnlock() {
	document.getElementById("act").value = "buka";
	goSubmit();
}

function goDelete(elem) {
	var drop = confirm("Apakah anda yakin akan menghapus mata kuliah ini dari KRS?");
	if(drop) {
		document.getElementById("act").value = "delete";
		document.getElementById("key").value = elem.id;
		goSubmit();
	}
}

function goPrint() {
	showPage('npm','<?= Route::navAddress('rep_frs') ?>');
}

function showPilihan() {
	$("#div_pilihan").show();
}

</script>
</body>
</html>
