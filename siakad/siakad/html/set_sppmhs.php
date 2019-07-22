<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses

	$a_auth = Modul::getFileAuth();
	
	//$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('perwalian'));
	
	// variabel request
	if(Akademik::isMhs())
		$r_key = Modul::getUserName();
	else
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
	
	// properti halaman
	$p_title = 'Status SPP Mahasiswa';
	$p_tbwidth = 700;
	$p_aktivitas = 'SPP';
	
	$p_model = mPerwalian;
	//$p_key = $p_model::key;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'set' and $c_edit) {
		$t_key = CStr::removeSpecial($_POST['key']);
		
		$record = array();
		$record['prasyaratspp'] = -1;
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$t_key,true);
	}
	else if($r_act == 'unset' and $c_edit) {
		$t_key = CStr::removeSpecial($_POST['key']);
		
		$record = array();
		$record['prasyaratspp'] = 0;
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$t_key,true);
	}
	else if($r_act == 'delete' and $c_delete) {
		$t_key = CStr::removeSpecial($_POST['key']);


		
		list($p_posterr,$p_postmsg) = $p_model::deleteRecord($conn,$t_key, true);
	}
	
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	
	$a_data = $p_model::getStatusSPP($conn,$r_key);
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
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<div style="float:left; width:15%;">
			<?php require_once('inc_headermahasiswa.php'); ?>
			</div>
			<div style="float:left; width:60%">
			
			<form name="pageform" id="pageform" method="post">
				<center>
				<?php require_once('inc_headermhs.php') ?>
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
				<center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th>Periode</th>
						<th>Status Mahasiswa</th>
						<th>Dosen Wali</th>
						<th>KRS Terisi</th>
						<th>SPP</th>
						<? if($c_edit) { ?>
						<th>Prasyarat</th>
						<? } ?>


						
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							
							$t_key = $p_model::getKeyRow($row);
					?>
					<tr valign="top" class="<?= $rowstyle ?><?= empty($row['prasyaratspp']) ? '' : ' GreenBG' ?>">
						<td><?= Akademik::getNamaPeriode($row['periode']) ?></td>
						<td><?= $row['namastatus'] ?></td>
						<td><?= $row['nama'] ?><?= empty($row['nipdosenwali']) ? '' : ' ('.$row['nipdosenwali'].')' ?></td>
						<td align="center"><?= empty($row['frsterisi']) ? '' : '<img src="images/check.png">' ?></td>
						<td align="center"><?= empty($row['prasyaratspp']) ? '' : '<img src="images/check.png">' ?></td>
						<? if($c_edit) { ?>
							<td align="center"><input type="checkbox" value="<?= $t_key ?>"<?= empty($row['prasyaratspp']) ? '' : ' checked' ?> onclick="goSetSPP(this)"></td>
						<? } ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="6" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				</center>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
			</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

function goSetSPP(elem) {
	document.getElementById("act").value = (elem.checked ? 'set' : 'unset');
	document.getElementById("key").value = elem.value;
	
	goSubmit();
}

</script>
</body>
</html>
