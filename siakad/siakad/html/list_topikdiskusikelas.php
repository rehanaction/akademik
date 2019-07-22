<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('diskusi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_kelas = CStr::removeSpecial($_REQUEST['kelas']);
	
	// properti halaman
	$p_title = 'Topik Diskusi';
	$p_tbwidth = 700;
	$p_aktivitas = 'FORUM';
	$p_listpage = 'list_diskusi';
	$p_detailpage = 'list_diskusikelas';
	$p_subpage = 'list_subdiskusikelas';
	
	if(empty($r_kelas))
		Route::navigate($p_listpage);
	
	// cek kelas
	if(Akademik::isMhs()) {
		require_once(Route::getModelPath('krs'));
		
		if(!mKRS::isAmbil($conn,$r_kelas))
			Route::navigate($p_listpage);
	}
	else if(Akademik::isDosen()) {
		require_once(Route::getModelPath('mengajar'));
		
		if(!mMengajar::isAjar($conn,$r_kelas))
			Route::navigate($p_listpage);
	}
	
	$p_model = mDiskusi;
	
	// mendapatkan data
	$a_infokelas = mKelas::getDataSingkat($conn,$r_kelas);
	$a_data = $p_model::getListTopik($conn,true,$r_kelas);
	
	$p_subtitle = $a_infokelas['kodemk'].' - '.$a_infokelas['namamk'].' ('.$a_infokelas['kelasmk'].')';
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
					<div class="ForumCrumbs" align="left" style="width:<?= $p_tbwidth ?>px">
						Forum : <span class="ULink" onclick="goUp()">Diskusi</span>
						&raquo; <span class="ULink" onclick="goReload()"><?= $p_subtitle ?></span>
					</div>
				</center>
				<br>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
								<h1><?= $p_title ?> <span class="ViewSubTitle"><?= $p_subtitle ?></span></h1>
							</div>
						</div>
					</header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridNormal" align="center">
					<?	$t = 0;
						foreach($a_data as $t_topik => $t_parent) {
							$t++;
					?>
					<tr>
						<th><?= empty($t_parent['topik']) ? 'Topik Lain' : $t_parent['topik'] ?></th>
						<th width="60">Diskusi</th>
						<th width="150">Posting Terakhir</th>
					</tr>
					<?		$i = 0;
							foreach($t_parent['child'] as $t_child) {
								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								
								$t_label = $t_child['label'];
								
								if(empty($t_label)) {
									$t_id = '';
									$t_waktu = '';
									$t_topik = '';
								}
								else {
									$pos = strpos($t_label,'|');
									$pos2 = strpos($t_label,'|',$pos+1);
									
									$t_waktu = substr($t_label,0,$pos);
									$t_id = substr($t_label,$pos+1,$pos2-$pos-1);
									$t_topik = substr($t_label,$pos2+1);
									
									if(strlen($t_topik) > 30)
										$t_topik = substr($t_topik,0,27).'...';
								}
					?>
					<tr class="<?= $rowstyle ?>">
						<td><u class="ForumLink" id="<?= $t_child['idtopik'] ?>" onclick="goDetail(this)"><?= $t_child['topik'] ?></u></td>
						<td align="center"><?= $t_child['jumlah'] ?></td>
						<td align="center">
						<?	if(!empty($t_id)) { ?>
							<?= CStr::formatDateDiff($t_waktu) ?><br>
							<u class="ULink" onclick="goSubDetail(<?= $t_id ?>)"><?= $t_topik ?></u>
						<?	} ?>
						</td>
					</tr>
					<?		}	
						}
						if($t == 0) { ?>
					<tr>
						<td colspan="3" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

function goUp() {
	goView("<?= $p_listpage ?>");
}

function goDetail(elem) {
	location.href = detailpage + "&kelas=<?= $r_kelas ?>&topik=" + elem.id;
}

function goSubDetail(key) {
	goView("<?= $p_subpage ?>&key="+key);
}

</script>
</body>
</html>