<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('absensikuliah'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));  
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_input = CStr::removeSpecial($_REQUEST['input']);
	$r_subact = CStr::removeSpecial($_REQUEST['subact']);
	$v_pertemuan = CStr::removeSpecial($_REQUEST['jenis_pertemuan']);  
  
	//echo $r_key;
	if(empty($r_key))
		Route::navigate($p_listpage);
	list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk,$jeniskul,$kelompok)=explode('|',$r_key);	
	$a_jeniskul=array('K'=>'Kuliah','P'=>'Praktikum','R'=>'Tutorial');
	
	// properti halaman
	// $p_title = 'Pengisian Absensi PRAKTIKUM Menggunakan RFID, tanggal : '. date('Y-m-d');
	$p_title = 'Pengisian Absensi '.$a_jeniskul[$jeniskul].' Kelompok '.$kelompok;
	$p_tbwidth = 700;
	$p_aktivitas = 'ABSENSI';
	$p_listpage = 'list_absensi';
	$p_printpage = 'rep_absensi';
	$p_model = mAbsensiKuliah;
	
	// variabel request
	//$r_key = CStr::removeSpecial($_REQUEST['key']);
	//$r_input = CStr::removeSpecial($_REQUEST['input']);
	//$r_subact = CStr::removeSpecial($_REQUEST['subact']);
	//$v_pertemuan = CStr::removeSpecial($_REQUEST['jenis_pertemuan']);
	
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	if($r_subact == 'barcode' and $c_edit) {
		// ambil mahasiswa berdasarkan rfid
		$nim = mMahasiswa::getNIMByRFID($conn,$r_input);
		if(empty($nim)) {
			$p_posterr = true;
			$p_postmsg = "Mahasiswa dengan RFID <b>".$r_input."</b> tidak ditemukan";
		}
		
		if(empty($p_posterr)) {
			$tanggal = mKuliah::getTglJurnal($conn,$r_key.'|'.$v_pertemuan,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,perkuliahanke');
			
			//thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,tglkuliah,perkuliahanke,nim
			$exist = $p_model::isDataExist($conn,$r_key.'|'.$tanggal.'|'.$v_pertemuan.'|'.$nim,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,tglkuliah,perkuliahanke,nim'); //ak_absensikuliah
			if($exist) {
				$p_posterr = true;
				$p_postmsg = "NIM <b>".$nim."</b> telah melakukan absensi";
			}
		}
		
		if(empty($p_posterr)) {
			//lakukan cek termasuk peserta kah?
			$exis = mKuliah::isPesertakelas($conn,$r_key,$nim);
			if(empty($exis)) {
				$p_posterr = true;
				$p_postmsg = "NIM <b>".$nim."</b> tidak terdaftar sebagai peserta kelas";
			}
		}
		
		if(empty($p_posterr)) {
			list($record['thnkurikulum'],$record['kodemk'],$record['kodeunit'],$record['periode'],$record['kelasmk'],$record['jeniskuliah'],$record['kelompok']) = explode('|',$r_key);
			
			$record['tglkuliah'] = $tanggal;
			$record['perkuliahanke'] = $v_pertemuan;
			$record['nim'] = $nim;
			$record['absen'] = 'H';
			$record['wakturfid'] = date('Y-m-d H:i:s');
			
			$p_model::updateJumlahPeserta($conn,$r_key."|$v_pertemuan|$tanggal");
			$err = $p_model::insertRecord($conn,$record);
			
			if($err) {
				$p_posterr = true;
				$p_postmsg = "NIM <b>".$nim."</b> gagal melakukan absensi";
			}
			else{
				$p_posterr = false;
				$p_postmsg = "NIM <b>".$nim."</b> berhasil melakukan absensi";
			}
		}
	}
	
	// mendapatkan data
	// $a_absen = $p_model::getListPerKelas($conn,$r_key);
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key); 
	if($jeniskul=='P')
    $kelprak = $kelompok;
	$a_data = mKelas::getDataPeserta($conn,$r_key,$kelprak);
	// $a_pertemuan = mKuliah::getdata_jurnal($conn,$r_key);
	$a_pertemuan = mKuliah::getListSelesai($conn,$r_key);
	if(empty($v_pertemuan))
		$v_pertemuan = key($a_pertemuan);
	if(!empty($v_pertemuan))
		$a_absen = $p_model::getListPerPertemuan($conn,$r_key.'|'.$v_pertemuan);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<style>
		#input {
		font-size:36px;
		font-weight:bold;
		color:#999999;
		padding:5px;
		text-transform:Uppercase;
		width:400px;
		text-align:center;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<center>
				<?php require_once('inc_headerkelas.php') ?>
				</center>
				<br>
				<?	if($c_edit) { ?>
				<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td align="center">
							Untuk mengisi absensi, diharuskan mengisi jurnal pada pertemuan yang diinginkan.
							Untuk mengisi jurnal perkuliahan klik <u class="ULink" onclick="goSubmitBlank('<?= Route::navAddress('list_jurnal') ?>')">di sini</u>.
						</td>
					</tr>
				</table>
				<br>
				<?	} ?>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<?	if(isset($p_posterr)) { ?>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				<?	} else { ?>
				<div style="width:<?= $p_tbwidth ?>px">
					<strong><?= $p_postmsg ?></strong>
				</div>
				<?	} ?>
				</center>
				<div class="Break"></div>
				<?	} ?>
			 
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
								<h1><?= $p_title ?></h1>
							</div>
							<div class="right">
								<img title="Cetak Absensi" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
							</div>
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
					//$a_pertemuan=array('1'=>'1', '2'=>'2');
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tr>
						<td align="center"><br>
						<?= UI::createSelect('jenis_pertemuan',$a_pertemuan,$v_pertemuan,'ControlStyle',true,'onchange="goSubmit()" style="width:400px"') ?></td>
					</tr>
					<?	if($c_edit) { ?>
					<tr>
						<td align="center"><input type="text" name="input" id="input" placeholder="RFID" autocomplete="off"></td>
					</tr>
					<?	} ?>
				</table>
				<br>
				<? /* Peserta Kelas*/?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Daftar Peserta Kelas</h1>
							</div>
						</div>
					</header>
				</center>				
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th width="30">No.</th>
						<th width="100">NIM</th>
						<th>Nama</th>
						<th>Status</th>
						<th>Waktu Log RFID</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $row['nim'];
							$t_absen = $a_absen[$row['nim']];
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						<td align="center"><?= $row['nim'] ?></td>
						<td><?= $row['nama'] ?></td>
						<td align="center">
							<?
								echo '<strong>';
								switch($t_absen['absen']) {
									case 'A': echo '<span style="color:red">Absen</span>'; break;
									case 'H': echo '<span style="color:green">Hadir</span>'; break;
									case 'I': echo '<span style="color:orange">Ijin</span>'; break;
									case 'S': echo '<span style="color:orange">Sakit</span>'; break;
									default: echo '<span style="color:grey">Belum Hadir</span>';
								}
								echo '</strong>';
							?>
						</td>
						<td><?= CStr::formatDateTimeInd($t_absen['wakturfid'],false,true) ?></td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="7" align="center">Kelas ini belum memiliki peserta</td>
					</tr>
					<?	} ?>
				</table>				
				<? /* Peserta Kelas*/?>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="subact" id="subact">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="format" id="format">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	<? if($c_edit) { ?>
	$("#input").focus().keyup(submitRFID).paste(submitRFID);
	<? } ?>
});

function submitRFID() {
	var val = $("#input").val();
	var len = val.length;
	
	if(len == <?= mMahasiswa::rfidLength ?>) {
		$("#subact").val("barcode");
		goSubmit();
	}
}

function goPrint() {
	goOpen('<?= $p_printpage ?>&key=' + document.getElementById("key").value);
}

</script>
</body>
</html>
