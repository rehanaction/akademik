<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	 
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pesertaseminar'));
	require_once(Route::getModelPath('seminar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);

	if (isset ($_GET['key']))
	$r_key = CStr::removeSpecial($_GET['key']);

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = false;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Absensi Seminar';
	$p_tbwidth = 600;
	$p_listpage = Route::getListPage();
	
	$p_model = mPesertaSeminar;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	$c_readlist = empty($a_authlist) ? false : true;
	
	$a_seminar = mSeminar::getArray($conn);

	// cek boleh absen nggak ?
	$isbuka = mPesertaSeminar::getIsBukaAbsen($conn,$r_key);
	print_r($isbuka);

	if ($isbuka['1'] == '1') {
		$c_edit = true;
	}


	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idseminar', 'label' => 'Seminar', 'type' => 'S', 'option' => $a_seminar, 'readonly' => true);	
	$a_input[] = array('kolom' => 'nopeserta', 'label' => 'Nomor Peserta', 'size' => 50, 'maxlength' => 50, 'readonly' => true);
	$a_input[] = array('kolom' => 'namapeserta', 'label' => 'Nama Peserta', 'size' => 50, 'maxlength' => 50, 'readonly' => true);
	$a_input[] = array('kolom' => 'waktucheckin','label' => 'Jam Mulai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');	
	$a_input[] = array('kolom' => 'waktucheckout','label' => 'Jam Selesai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');
	
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
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
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
							<?= Page::getDataTR($row,'idseminar') ?>
							<?= Page::getDataTR($row,'nopeserta') ?>
							<?= Page::getDataTR($row,'namapeserta') ?>
							<?= Page::getDataTR($row,'waktucheckin') ?>
							<?= Page::getDataTR($row,'waktucheckout') ?>					
						</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script src="scripts/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>	
<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
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
});

$(function() {
    $.mask.definitions['~'] = "[+-]";
	$("#i_jammulai").mask("99:99");
	$("#i_jamselesai").mask("99:99");
	$(".jam").mask("99:99");
});

</script>
</body>
</html>
