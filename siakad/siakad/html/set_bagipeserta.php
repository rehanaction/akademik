<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('pesertakelas'));
	require_once(Route::getUIPath('combo'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Pembagian Kelas';
	$p_tbwidth = 950;
	$p_aktivitas = 'ABSENSI';
	
	$p_model = mPesertakelas;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'ratapeserta' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::ratakanPeserta($conn,$r_key);
	else if($r_act == 'ratakelas' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::ratakanKelas($conn,$r_key);
	
	// mendapatkan data
	$a_infomk = $p_model::getDataSingkat($conn,$r_key);
	$a_kelas = $p_model::getDataPerKelas($conn,$r_key);
	
	// var_dump(count($a_kelas));
	// data total
	$a_total = array();
	foreach($a_kelas as $t_kelas => $a_peserta)
		foreach($a_peserta as $row)
			$a_total[$row['nim']] = $row['nama'];
	
	ksort($a_total);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
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
								<td><strong> : </strong><?= $a_infomk['namamk'] ?></td>		
							</tr>
						</table>
								</td>
								<? if($c_edit) { ?>
								<td>
									<strong>Jumlah Kelas</strong> <strong> : </strong><?= count($a_kelas) ?>
									<div class="Break"></div>
									<input type="button" class="ControlStyle" value="Ratakan Peserta" onclick="goRataPeserta()">
									<input type="button" class="ControlStyle" value="Ratakan Kelas" onclick="goRataKelas()">
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
			<td colspan="3" align="center">Daftar Peserta Kelas (Total)</td>
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
	<?	foreach($a_kelas as $t_kelas => $a_peserta) { ?>
	<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
		<tr class="DataBG">
			<td colspan="3" align="center">Kelas <?= $t_kelas ?></td>
		</tr>
		<tr>
			<th>No.</th>
			<th>NIM</th>
			<th>Nama</th>
		</tr>
	<?		$i = 0;
			foreach($a_peserta as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
	?>
		<tr valign="top" class="<?= $rowstyle ?>">
			<td><?= $i ?>.</td>
			<td align="center"><?= $row['nim'] ?></td>
			<td><?= $row['nama'] ?></td>
		</tr>
	<?		} ?>
	</table>
	<br>
	<?	} ?>
			</td>
		</tr>
	</table>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
function goRataPeserta() {
	document.getElementById("act").value = "ratapeserta";
	goSubmit();
}

function goRataKelas() {
	document.getElementById("act").value = "ratakelas";
	goSubmit();
}

</script>
</body>
</html>