<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getUIPath('combo'));
	

		
	if(Akademik::isMhs())
	{
		$r_key = Modul::getUserName();
		$display="none";
	}
	else if(Akademik::isDosen())
	{
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
		$display="none";
			// variabel request
		$r_act = $_POST['act'];
		if($r_act == 'first')
			$r_key = mMahasiswa::getFirstNIM($conn,$r_nim,$r_nip);
		else if($r_act == 'prev')
			$r_key = mMahasiswa::getPrevNIM($conn,$r_nim,$r_nip);
		else if($r_act == 'next')
			$r_key = mMahasiswa::getNextNIM($conn,$r_nim,$r_nip);
		else if($r_act == 'last')
			$r_key = mMahasiswa::getLastNIM($conn,$r_nim,$r_nip);
	}	
	else
	{
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
		$display="block";
	}
	// properti halaman
	$p_title = 'Mengulang Matakuliah';
	$p_tbwidth = "100%";
	$p_aktivitas = 'NILAI';
	
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_data = mKRS::getDataMengulang($conn,$r_key);
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
			<div style="float:left; width:100%;  ">
			<?php require_once('inc_headermahasiswa.php'); ?>
			</div>
			<div style="float:left; width:100%;">
			<form name="pageform" id="pageform" method="post">		
			<center>
			<?php require_once('inc_headermhs_krs.php') ?>
		 
			<br>
			 
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
						</div>
					</div>
				</header>
			
			<?	/*************/
				/* LIST DATA */
				/*************/
			?>
			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
				<?	/**********/
					/* HEADER */
					/**********/
				?>
				<tr>
					<th>No.</th>
					<th>Kode</th>
					<th>Matakuliah</th>
					<th>SKS</th>
					<th>Sem.</th>
					<th>Nilai</th>
					<th>Ulang</th>
				</tr>
				<?	/********/
					/* ITEM */
					/********/
					
					$i = 0;
					foreach($a_data as $row) {
						if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				?>
				<tr valign="top" class="<?= $rowstyle ?><?= empty($row['prasyaratspp']) ? '' : ' GreenBG' ?>">
					<td align="right"><?= $i ?>.</td>
					<td><?= $row['kodemk'] ?></td>
					<td><?= $row['namamk'] ?></td>
					<td align="center"><?= $row['sks'] ?></td>
					<td align="center"><?= $row['semmk'] ?></td>
					<td><?= implode('<br>',$row['ulang']) ?></td>
					<td align="center"><?= count($row['ulang']) ?></td>
				</tr>
				<?	}
					if($i == 0) {
				?>
				<tr>
					<td colspan="7" align="center">Data kosong</td>
				</tr>
				<?	} ?>
			</table>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
				<? if(Akademik::isDosen()) { ?>
				<input type="hidden" name="nip" id="nip" value="<?= Modul::getUserName() ?>">
				<? } ?>
			</center>	
			</form>
			</div>
		</div>
	</div>
</div>
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	<? if(Akademik::isDosen()) { ?>
	$("#mahasiswa").xautox({strpost: "f=acmhswali", targetid: "npmtemp", postid: "nip"});
	<? } else { ?>
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "npmtemp"});
	<? } ?>
	});
</script>	
</body>
</html>