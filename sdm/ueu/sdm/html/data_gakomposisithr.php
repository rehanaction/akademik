<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$r_periode = CStr::removeSpecial($_REQUEST['periodegaji']);
	
	$p_tbwidth = "500";
	$p_title = "Data Komposisi THR";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ga_komposisithr";
	$p_key = "periodegaji,idhubkerja";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	if(empty($r_key)){
		$a_input[] = array('kolom' => 'periodegaji', 'label' => 'Periode Gaji', 'type' => 'S', 'option' => $p_model::getCPeriodeGajiTHR($conn), 'default' => $r_periode);
		$a_input[] = array('kolom' => 'idhubkerja', 'label' => 'Hubungan Kerja', 'type' => 'S', 'option' => $p_model::getCHubkerja($conn));
	}else{
		$a_input[] = array('kolom' => 'periodegaji', 'label' => 'Periode Gaji', 'type' => 'S', 'option' => $p_model::getCPeriodeGajiTHR($conn), 'readonly' => true);
		$a_input[] = array('kolom' => 'idhubkerja', 'label' => 'Hubungan Kerja', 'type' => 'S', 'option' => $p_model::getCHubkerja($conn), 'readonly' => true);
	}
		
		
	$a_input[] = array('kolom' => 'mkmin', 'label' => 'Masa Kerja', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'mkmax', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'prosentasethr', 'label' => 'Prosentase THR (%)', 'maxlength' => 3, 'size' => 3, 'notnull' => true, 'type' => 'N');
	
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			if(empty($r_key))
				$r_key = $record['periodegaji'].'|'.$record['idhubkerja'];
				
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$sql = $p_model::getDataEditKomposisiTHR($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
		
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
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
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'periodegaji') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'periodegaji') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idhubkerja') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idhubkerja') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'mkmin') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'mkmin') ?> - <?= Page::getDataInput($row,'mkmax') ?> bulan</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'prosentasethr') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'prosentasethr') ?></td>
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
