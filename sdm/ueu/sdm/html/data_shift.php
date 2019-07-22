<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mPresensi;
		
	$p_tbwidth = "700";
	$p_title = "Data Shift";
	$p_aktivitas = 'TIME';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ms_kelkerja";
	$p_key = "kodekelkerja";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
	$a_hari = Date::arrayDay();
	$a_jamhadir = $p_model::getCJamHadir($conn);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Nama Shift', 'maxlength' => 255, 'size' => 60, 'notnull' => true);
	foreach ($a_hari as $inc => $hari){
		$a_input[] = array('kolom' => strtolower($hari), 'label' => $hari, 'type' => 'S', 'option' => $a_jamhadir, 'empty' => true);
	}
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif', 'type' => 'S', 'option' => SDM::getValid(), 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekelkerja', 'type' => 'H');
		
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
				
		if(empty($r_key)){
			$record['kodekelkerja'] =  $p_model::getKodeShift($conn);
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){	
			unset($post);
			$r_key = $record['kodekelkerja'];
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
		
	$a_required = array('keterangan');
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
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
										<td width="200"><?= Page::getDataLabel($row,'keterangan') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'keterangan') ?></td>
									</tr>
									<? foreach($a_hari as $inc => $hari){ ?>
									<tr>
										<td><?= Page::getDataLabel($row,strtolower($hari)) ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,strtolower($hari)) ?></td>
									</tr>
									<? } ?>
									<tr>
										<td><?= Page::getDataLabel($row,'isaktif') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'isaktif') ?><?= Page::getDataInput($row,'kodekelkerja') ?></td>
									</tr>
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