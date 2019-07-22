<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	//$conn->debug=true;
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('absensikuliah'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(Akademik::isMhs())
		$r_key = Modul::getUserName();
	else
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
	
	$r_periode = Akademik::getPeriode();
	
	// properti halaman
	$p_title = 'Absensi Mahasiswa';
	$p_tbwidth = "100%";
	$p_aktivitas = 'ABSENSI';
	
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_data = mAbsensiKuliah::getListPerMhsPeriode($conn,$r_key,$r_periode);
	
	$a_jeniskul=mKuliah::jenisKuliah($conn);
	$min_absen=mSetting::minAbsen($conn);
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
			<?php require_once('inc_headermhs.php') ?>
			</center>
			<br>
			<center>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
						</div>
					</div>
				</header>
			</center>
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
					<th>Nama Matakuliah</th>
					<th>Kelas</th>
					<th>Jenis Kuliah</th>
					<th>Absen Mhs</th>
					<th>Absen Dosen</th>
					<th>% Absen Mhs</th>
				</tr>
				<?	/********/
					/* ITEM */
					/********/
					
					$i = 0;
					foreach($a_data as $row) {
						if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; 
						
						if($row['jeniskuliah']=='P' and ($row['kelompok']!=$row['kelompok_prak']))
							continue;
						
						$i++;
							
						$t_absenkelas = $row['totalabsenkelas'];
						if(!empty($t_absenkelas)) {
							$t_absenmhs = round(($row['totalabsenmhs']*100)/$t_absenkelas);
							if($t_absenmhs < $min_absen) 
								$t_absenmhs = '<span style="color:red">'.$t_absenmhs.' %</span>';
							else
								$t_absenmhs = $t_absenmhs.' %';
						}
						else
							$t_absenmhs = '&nbsp;';
				?>
				<tr valign="top" class="<?= $rowstyle ?><?= empty($row['prasyaratspp']) ? '' : ' GreenBG' ?>">
					<td align="right"><?= $i ?>.</td>
					<td align="center"><?= $row['kodemk'] ?></td>
					<td><?= $row['namamk'] ?></td>
					<td align="center"><?= $row['kelasmk'] ?></td>
					<td align="center"><?= $a_jeniskul[$row['jeniskuliah']] ?></td>
					<td align="right"><?= $row['totalabsenmhs'] ?></td>
					<td align="right"><?= $row['totalabsenkelas'] ?></td>
					<td align="right"><?= $t_absenmhs ?></td>
				</tr>
				<?	}
					if($i == 0) {
				?>
				<tr>
					<td colspan="5" align="center">Data kosong</td>
				</tr>
				<?	} ?>
			</table>
		</div>
	</div>
</div>
</body>
</html>
