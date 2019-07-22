<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('dinas'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mDinas;
		
	$p_tbwidth = "600";
	$p_title = "Data Anggaran Dinas Unit";
	$p_listpage = Route::getListPage();
	$p_dbtable = "ms_anggarandinas";
	$p_key = "thnanggaran,idunit";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'thnanggaran', 'label' => 'Tahun', 'type' => 'S', 'option' => mCombo::tahun(true,(date('Y')-5)), 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => mCombo::unitSave($conn,false), 'notnull' => true);
	$a_input[] = array('kolom' => 'anggaran', 'label' => 'Besar Anggaran', 'maxlength' => 14, 'size' => 14, 'type' => 'N', 'notnull' => true);
	$a_input[] = array('kolom' => 'anggaranterpakai', 'label' => 'Sisa Anggaran', 'type' => 'H');
	
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			unset($post);
			$r_key = $record['thnanggaran'].'|'.$record['idunit'];
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
		
	$a_required = array('thnanggaran','idunit','anggaran');
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<style>
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<table width="100%">
				<tr>
					<td>
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
							<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
							<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
								<tbody>
									<tr>
										<td width="200"><?= Page::getDataLabel($row,'thnanggaran') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'thnanggaran') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'idunit') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'idunit') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'anggaran') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'anggaran') ?></td>
									</tr>
									<? if (!empty($r_key)) {?>
									<tr>
										<td>Sisa Anggaran</td>
										<td>:</td>
										<td><?= CStr::formatNumber($row[3]['value'],0); ?></td>
									</tr>
									<? } ?>
								</tbody>
							</table>
							</div>
							</center>
							<? } ?>
							<input type="hidden" name="act" id="act">
							<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
						</form>
					</td>
				</tr>
			</table>
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