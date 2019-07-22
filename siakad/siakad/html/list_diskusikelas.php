<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('diskusi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_kelas = CStr::removeSpecial($_REQUEST['kelas']);
	$r_topik = CStr::removeSpecial($_REQUEST['topik']);
	
	// properti halaman
	$p_title = 'Diskusi';
	$p_tbwidth = 700;
	$p_aktivitas = 'FORUM';
	$p_listpage = 'list_diskusi';
	$p_topikpage = 'list_topikdiskusikelas';
	$p_detailpage = 'data_diskusi';
	$p_subpage = 'list_subdiskusikelas';
	$p_showpage = 5;
	
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
	
	// ada aksi
	/* $r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	} */
	
	// mendapatkan data
	$a_infokelas = mKelas::getDataSingkat($conn,$r_kelas);
	$a_data = $p_model::getListForumKelas($conn,$r_kelas);
	
	$p_subtitle = $a_infokelas['kodemk'].' - '.$a_infokelas['namamk'].' ('.$a_infokelas['kelasmk'].')';
	$p_topik = $p_model::getNamaTopik($conn,$r_topik);
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
					<div class="ForumCrumbsSmall" align="left" style="width:<?= $p_tbwidth ?>px">
						Forum : <span class="ULink" onclick="goTop()">Diskusi</span>
						&raquo; <span class="ULink" onclick="goUp()"><?= $p_subtitle ?></span>
						&raquo; <span class="ULink" onclick="goReload()"><?= $p_topik ?></span>
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
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<? } ?>
						</div>
					</header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridNormal" align="center">
					<tr>
						<th>Topik</th>
						<th width="60">Diskusi</th>
						<th width="100">Penulis</th>
						<th width="150">Posting Terakhir</th>
						<?	/* if($c_edit) { ?>
						<th width="30">Edit</th>
						<?	}
							if($c_delete) { ?>
						<th width="30">Hapus</th>
						<?	} */ ?>
					</tr>
					<?	$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							
							$t_key = $p_model::getKeyRow($row);
							$t_label = $row['label'];
							
							if(empty($t_label)) {
								$t_id = '';
								$t_waktu = '';
								$t_user = '';
							}
							else {
								$pos = strpos($t_label,'|');
								$pos2 = strpos($t_label,'|',$pos+1);
								
								$t_waktu = substr($t_label,0,$pos);
								$t_id = substr($t_label,$pos+1,$pos2-$pos-1);
								$t_user = substr($t_label,$pos2+1);
							}
					?>
					<tr class="<?= $rowstyle ?>">
						<td>
							<span id="<?= $t_key ?>" class="ForumLink" onclick="goSub(this,1)"><?= $row['judulforum'] ?></span>
							<?	if($row['jumlah'] > $conf['row_diskusi']) {
									$t_npage = ceil($row['jumlah']/$conf['row_diskusi']);
							?>
							<div class="Break"></div>
							Halaman:
							<?		$t_skiplast = $t_npage-$p_showpage+1;
									for($i=1;$i<=$t_npage;$i++) {
										if($i > $p_showpage and $i < $t_skiplast) {
											if($i == $p_showpage+1)
												echo '..., ';
											continue;
										}
										else {
											echo '<u id="'.$t_key.'" class="ULink" onclick="goSub(this,'.$i.')">'.$i.'</u>';
											if($i < $t_npage)
												echo ', ';
										}
									}
								} ?>
						</td>
						<td align="center"><?= $row['jumlah'] ?></td>
						<td align="center"><?= $row['creator'] ?></td>
						<td align="center">
						<?	if(!empty($t_id)) { ?>
							<?= CStr::formatDateDiff($t_waktu) ?> oleh <?= $t_user ?>
						<?	} ?>
						</td>
						<?	/* if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Edit Materi" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?	} */ ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="6" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

function goTop() {
	goView("<?= $p_listpage ?>");
}

function goUp() {
	goView("<?= $p_topikpage ?>&kelas=<?= $r_kelas ?>");
}

function goNew() {
	location.href = detailpage + "&kelas=<?= $r_kelas ?>&topik=<?= $r_topik ?>";
}

function goSub(elem,page) {
	goView("<?= $p_subpage ?>&key="+elem.id+"&go="+page);
}

</script>
</body>
</html>