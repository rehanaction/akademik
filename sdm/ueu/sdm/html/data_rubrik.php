<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('bebandosen'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
		
	// properti halaman
	$p_title = 'Data Rubrik BKD';
	$p_tbwidth = 600;
	$p_aktivitas = 'NILAI';
	$p_dbtable = 'ms_kegiatandosen';
	$p_key = 'idjeniskegiatan';
	$p_listpage = Route::getListPage();
	
	$p_model = mBebanDosen;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan', 'type'=> 'A', 'notnull' => true, 'rows' => 3, 'cols' => 60, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'kodekegiatan', 'label' => 'Kode Kegiatan');
	$a_input[] = array('kolom' => 'idkategori', 'label' => 'Kategori', 'type' => 'S', 'option' => $p_model::getCKategoriRubrik($conn));
	$a_input[] = array('kolom' => 'sksmax', 'label' => 'SKS Max.', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'masaberlaku', 'label' => 'Masa Berlaku', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'ketmasaberlaku', 'size' => 30, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'bukti', 'label' => 'Bukti', 'type'=> 'A','rows' => 2, 'cols' => 60, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'is Aktif', 'type' => 'S', 'option' => SDM::getValid());
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
		
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
						require_once('inc_databutton.php');
					
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
				
					if(empty($p_fatalerr)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title; ?></h1>
							</div>
						</div>
					</header>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'namakegiatan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namakegiatan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodekegiatan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodekegiatan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idkategori') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idkategori') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'sksmax') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'sksmax') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'masaberlaku') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'masaberlaku') ?> - <?= Page::getDataInput($row,'ketmasaberlaku') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'bukti') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'bukti') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isaktif') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'isaktif') ?></td>
						</tr>
					</table>
					</div>
				</center>
				<? } ?>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
});
</script>
</body>
</html>
</html>
