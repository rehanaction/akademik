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
	require_once(Route::getModelPath('tugas'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_kelas = CStr::removeSpecial($_REQUEST['kelas']);
	
	// properti halaman
	$p_title = 'Tugas';
	$p_tbwidth = 700;
	$p_aktivitas = 'KULIAH';
	$p_listpage = 'list_tugas';
	$p_detailpage = 'data_tugas';
	$p_subpage = 'set_upnilaitugas';
	
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
	
	$p_model = mTugas;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		//die('ok');
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	
	// mendapatkan data
	$a_infokelas = mKelas::getDataSingkat($conn,$r_kelas);
	$a_data = $p_model::getListForumKelas($conn,$r_kelas);
	
	if(Akademik::isMhs())
		$a_kumpul = $p_model::getListSubmitMhs($conn,Modul::getUserName(),$r_kelas);
	
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
						Forum : <span class="ULink" onclick="goUp()">Tugas</span>
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
						<th>Tugas</th>
						<th width="90">Mulai</th>
						<th width="90">Batas Akhir</th>
						<?	if($c_edit) { ?>
						<th width="30">Edit</th>
						<?	}
							if($c_delete) { ?>
						<th width="30">Hapus</th>
						<?	} ?>
					</tr>
					<?	$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							
							$t_key = $p_model::getKeyRow($row);
							
							// cek pengumpulan
							if(isset($a_kumpul) and empty($a_kumpul[$t_key]))
								$rowstyle = 'YellowBG';
							
							// cek batas akhir
							$t_styleakhir = '';
							if(!empty($row['batasakhir']) and substr($row['batasakhir'],0,10) <= date('Y-m-d'))
								$t_styleakhir = 'RedFont';
							
							if(!empty($t_styleakhir))
								$t_styleakhir = ' class="'.$t_styleakhir.'"';
					?>
					<tr class="<?= $rowstyle ?>">
						<td>
							<span id="<?= $t_key ?>" class="ForumLink" onclick="goSub(this)"><?= $row['judultugas'] ?></span>
							<div class="Break"></div>
							<?= $row['isi'] ?>
						</td>
						<td align="center"><?= CStr::formatDateInd($row['batasawal'],false) ?></td>
						<td align="center"<?= $t_styleakhir ?>>
							<?= CStr::formatDateInd($row['batasakhir'],false) ?>
						</td>
						<?	if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Edit Materi" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="5" align="center">Data kosong</td>
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

function goUp() {
	goView("<?= $p_listpage ?>");
}

function goNew() {
	location.href = detailpage + "&kelas=<?= $r_kelas ?>";
}

function goSub(elem) {
	goView("<?= $p_subpage ?>&key="+elem.id);
}

</script>
</body>
</html>
