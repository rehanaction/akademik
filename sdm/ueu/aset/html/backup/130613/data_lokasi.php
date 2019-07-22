<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_lokasi');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('lokasi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Lokasi';
	$p_tbwidth = 500;
	$p_aktivitas = 'Lokasi';
	$p_listpage = Route::getListPage();
	
	$p_model = mLokasi;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idlokasi', 'label' => 'ID. Lokasi', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'namalokasi', 'label' => 'Nama Lokasi', 'maxlength' => 45, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'idjenislokasi', 'label' => 'Jenis Lokasi', 'type' => 'S', 'option' => mCombo::jenislokasi($conn), 'add' => 'style="width:150px;"', 'empty' => true);
	$a_input[] = array('kolom' => 'luas', 'label' => 'Luas (M2)', 'maxlength' => 6, 'type' => 'N', 'size' => 6 );
	$a_input[] = array('kolom' => 'kapasitas', 'label' => 'Kapasitas (Orang)', 'maxlength' => 6, 'size' => 6);
	$a_input[] = array('kolom' => 'petugas', 'label' => 'Petugas', 'size' => 35);
	$a_input[] = array('kolom' => 'idpetugas', 'type' => 'H');
	$a_input[] = array('kolom' => 'idgedung', 'label' => 'Gedung', 'type' => 'S', 'option' => mCombo::gedung($conn), 'add' => 'style="width:100px;"');
	$a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	//require_once(Route::getViewPath('inc_data'));

    /*	
	if(!empty($r_key)){
	    $r_idpetugas = Page::getDataValue($row,'idpetugas');
	    if(!empty($r_idpetugas))
        	$r_namapetugas = mPegawai::getNamaPegawai($conn,$r_idpetugas);

	}
	*/
	
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
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
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
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
                        <?= Page::getDataTR($row,'idlokasi') ?>
                        <?= Page::getDataTR($row,'namalokasi') ?>
                        <?= Page::getDataTR($row,'idjenislokasi') ?>
                        <?= Page::getDataTR($row,'luas') ?>
                        <?= Page::getDataTR($row,'kapasitas') ?>
	                    <tr>
		                    <td class="LeftColumnBG"><?= Page::getDataLabel($row,'petugas') ?></td>
		                    <td class="RightColumnBG">
		                        <?= Page::getDataInput($row,'petugas') ?>
		                        <?= Page::getDataInput($row,'idpetugas') ?>
		                    </td>
	                    </tr>                        
                        <?= Page::getDataTR($row,'idgedung') ?>
                        <?= Page::getDataTR($row,'catatan') ?>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>

	// autocomplete
	$("#namapetugas").xautox({strpost: "f=acxpegawai", targetid: "idpetugas"});
});

</script>
</body>
</html>
