<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('pesertaruang'));
	require_once(Route::getUIPath('combo'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Pembagian Ruang';
	$p_tbwidth = 950;
	$p_aktivitas = 'ABSENSI';
	
	$p_model = mPesertaruang;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'ratapesertaprak' and $c_edit){
		if($_POST['acak'] == '1')
			list($p_posterr,$p_postmsg) = $p_model::ratakanPesertaPrakacak($conn,$r_key); //acak
		else
			list($p_posterr,$p_postmsg) = $p_model::ratakanPesertaPrak($conn,$r_key); //urut
	}else if($r_act == 'ratapesertatutor' and $c_edit){
		if($_POST['acak'] == '1')
			list($p_posterr,$p_postmsg) = $p_model::ratakanPesertaTutoracak($conn,$r_key);
		else	
			list($p_posterr,$p_postmsg) = $p_model::ratakanPesertaTutor($conn,$r_key);
	}else if($r_act == 'pindahruang' and $c_edit){
		$nim_pindah = $_POST['nim_mhs'];
		$kelompok_tujuan = $_POST['kel_tujuan'];
		$jenis_kel = $_POST['jenisk'];
		list($p_posterr,$p_postmsg) = $p_model::pindahRuang($conn,$r_key,$nim_pindah,$kelompok_tujuan,$jenis_kel);		
	}
	 
	// mendapatkan data
	$a_infomk = $p_model::getDataSingkat($conn,$r_key);
	$a_kelas = $p_model::getDataPerKelas($conn,$r_key);
	$a_kelompok = $p_model::getDataKelompok($conn,$r_key);
	
	$jml_prak = $a_kelompok['kelpraktikum'];
	$jml_tutor = $a_kelompok['keltutorial'];
//print_r($a_kelompok);
	
	// data total
	$a_total = array();
	foreach($a_kelas as $t_kelas => $a_peserta)
		foreach($a_peserta as $row)
			$a_total[$row['nim']] = $row['nama'];
	
	ksort($a_total);
	$datakey = explode('|',$r_key);
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<style>
		.badge {
		  display: inline-block;
		  min-width: 10px;
		  padding: 3px 7px;
		  font-size: 12px;
		  font-weight: bold;
		  line-height: 1;
		  color: #ffffff;
		  text-align: center;
		  white-space: nowrap;
		  vertical-align: baseline;
		  background-color: #999999;
		  border-radius: 10px; <!--#05c039-->
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
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;<?= $p_title ?> <?= Akademik::getNamaPeriode($a_infomk['periode']) ?>
						</span>
					</div>
				</center>
				<br>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr valign="top">
								<td valign="top" width="50%">
						<table width="100%" cellspacing="0" cellpadding="4">
							<tr>		
								<td width="50" style="white-space:nowrap"><strong>Kode MK</strong></td>
								<td><strong> : </strong><?= $a_infomk['kodemk'] ?></td>		
							</tr>
							<tr>		
								<td width="50" style="white-space:nowrap"><strong>Nama MK</strong></td>
								<td><strong> : </strong><?= $a_infomk['namamk'] ?> (<?= $datakey[4]?>)</td>		
							</tr>
						</table>
								</td>
								<? if($c_edit) { ?>
								<td valign="bottom">
									<!--<strong>Jumlah Ruang</strong> <strong> : </strong><?= count($a_kelas) ?>-->
									<div class="Break"></div>
									<select class="ControlStyle" name="acak" id="acak">
										<option value="1">Acak</option>
										<option value="0">Urut</option>
									</select>
									<input type="button" class="ControlStyle" value="Ratakan Kel. Praktikum" onclick="goRataPrak()">
									<input type="button" class="ControlStyle" value="Ratakan Kel. Tutorial" onclick="goRataTutor()">
								</td>
								<? } ?>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
	<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0">
		<tr valign="top">
			<td width="50%" style="padding-right:5px">
				<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td colspan="3" align="center">Daftar Peserta Kelas <?= $datakey[4]?> (Total)</td>
					</tr>
					<tr>
						<th>No.</th>
						<th>NIM</th>
						<th>Nama</th>
					</tr>
				<?	$i = 0;
					foreach($a_total as $t_npm => $t_nama) {
						if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						<td align="center"><?= $t_npm ?></td>
						<td><?= $t_nama ?></td>
					</tr>
				<?	}
					if($i == 0) {
				?>
					<tr>
						<td colspan="3" align="center">Data kosong</td>
					</tr>
				<?	} ?>
				</table>
			</td>
			<td>
				<?	for($j=1;$j<=$jml_prak;$j++) { ?>
					<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
						<tr class="DataBG">
							<td colspan="4" align="center">Kelompok Praktikum <?= $j ?></td>
						</tr>
						<tr>
							<th>No.</th>
							<th>NIM</th>
							<th>Nama</th>
							<th>Tujuan Pindah</th>
						</tr>
					<?		$i = 0;
							foreach($a_peserta as $row) {
								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; //$i++;
								if($row['kel_prak'] == $j){
									$i++;
					?>
									<tr valign="top" class="<?= $rowstyle ?>">
										<td width="20"><?= $i ?>.</td>
										<td width="70" nowrap align="center"><?= $row['nim'] ?></td>
										<td width="200" nowrap><?= $row['nama'] ?></td>
										<td align="center">
											<select name="kelprak_pindah_<?= $row['nim']?>" id="kelprak_pindah_<?= $row['nim']?>">
												<?for($k=1;$k<=$jml_prak;$k++){
													if($k != $j){
												?>
													<option value="<?= $k?>"><?= $k?></option>
												<?}}?>
											</select>
											<input type="button" class="ControlStyle" value="Pindah" onClick="goPindah('<?= $row['nim']?>','praktikum')">
											<?//for($k=1;$k<=$jml_prak;$k++){
												//if($k != $j){
											?>
												<!--<span class="badge badge-success"><span style="cursor:pointer" title="Pindahkan ke Kelompok <?= $k;?>" onClick="goPindah('<?= $row['nim']?>','<?= $k?>','praktikum')">K.<?= $k?></span></span>-->
											<?//}}?>
											
										</td>
									</tr>
					<?		}} ?>
					</table>
					<br>
				<?	} ?>
				<hr>
				<?	for($j=1;$j<=$jml_tutor;$j++) { ?>
					<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
						<tr class="DataBG">
							<td colspan="4" align="center">Kelompok Tutorial <?= $j ?></td>
						</tr>
						<tr>
							<th>No.</th>
							<th>NIM</th>
							<th>Nama</th>
							<th>Tujuan Pindah</th>
						</tr>
					<?		$i = 0;
							foreach($a_peserta as $row) {
								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; //$i++;
								if($row['kel_tutor'] == $j){
									$i++;
					?>
									<tr valign="top" class="<?= $rowstyle ?>">
										<td width="20"><?= $i ?>.</td>
										<td width="70"align="center"><?= $row['nim'] ?></td>
										<td width="200"><?= $row['nama'] ?></td>
										<td align="center">
											<select name="keltutor_pindah_<?= $row['nim']?>" id="keltutor_pindah_<?= $row['nim']?>">
												<?for($k=1;$k<=$jml_tutor;$k++){
													if($k != $j){
												?>
													<option value="<?= $k?>"><?= $k?></option>
												<?}}?>
											</select>
											<input type="button" class="ControlStyle" value="Pindah" onClick="goPindahtutor('<?= $row['nim']?>','tutorial')">
											<?/*for($k=1;$k<=$jml_tutor;$k++){
												if($k != $j){
											?>
												
												<!--<span class="badge badge-success"><span style="cursor:pointer" title="Pindahkan ke Kelompok <?= $k;?>" onClick="goPindah('<?= $row['nim']?>','<?= $k?>','tutorial')">K.<?= $k?></span></span>-->
											<?}}*/?>
											
										</td>
									</tr>
					<?		}} ?>
					</table>
					<br>
				<?	} ?>
			</td>
		</tr>
	</table>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="nim_mhs" id="nim_mhs">
				<input type="hidden" name="kel_tujuan" id="kel_tujuan">
				<input type="hidden" name="jenisk" id="jenisk">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="jml_prak" id="jml_prak" value="<?= $jml_prak ?>">
				<input type="hidden" name="jml_tutor" id="jml_tutor" value="<?= $jml_tutor ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
function goRataPrak() {
	if(document.getElementById("jml_prak").value != 0){
		document.getElementById("act").value = "ratapesertaprak";
		goSubmit();
	}else{
		alert('Mohon isi dahulu Jumlah kelompok Praktikum di Menu Perkuliahan >> Kuliah Semester >> Data Kelas(Sebaran).\nKemudian Klik detail sesuai kelas ini!');
	}
}

function goRataTutor() {
	if(document.getElementById("jml_tutor").value != 0){
		document.getElementById("act").value = "ratapesertatutor";
		goSubmit();
	}else{
		alert('Mohon isi dahulu Jumlah kelompok Tutorial di Menu Perkuliahan >> Kuliah Semester >> Data Kelas(Sebaran).\nKemudian Klik detail sesuai kelas ini!');
	}
}

// function goPindah(nim,kelompok,jenis){
	// document.getElementById("nim_mhs").value = nim;
	// document.getElementById("kel_tujuan").value = kelompok;
	// document.getElementById("jenisk").value = jenis;
	// document.getElementById("act").value = "pindahruang";
	// goSubmit();
// }

function goPindah(nim,jenis){
	document.getElementById("nim_mhs").value = nim;
	document.getElementById("kel_tujuan").value = document.getElementById("kelprak_pindah_"+nim).value;
	document.getElementById("jenisk").value = jenis;
	document.getElementById("act").value = "pindahruang";
	goSubmit();
}
function goPindahtutor(nim,jenis){
	document.getElementById("nim_mhs").value = nim;
	document.getElementById("kel_tujuan").value = document.getElementById("keltutor_pindah_"+nim).value;
	document.getElementById("jenisk").value = jenis;
	document.getElementById("act").value = "pindahruang";
	goSubmit();
}

</script>
</body>
</html>
