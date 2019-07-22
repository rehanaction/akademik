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
		
	$p_tbwidth = "500";
	$p_title = "Data Periode Tarif Penggajian";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ms_periodetarif";
	$p_key = "periodetarif";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'periodetarif', 'label' => 'Kode', 'maxlength' => 6, 'size' => 6, 'notnull' => true);
	$a_input[] = array('kolom' => 'namaperiode', 'label' => 'Nama Periode', 'maxlength' => 100, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tgl. Mulai Berlaku', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'fromperiodetarif', 'label' => 'Salin dari Periode', 'type' => 'S', 'option' => $p_model::periodeTarifSalin($conn,$r_key));	
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			$r_key = $record['periodetarif'];
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'salin' and $c_edit) {
		$conn->BeginTrans();
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			$f_key = $record['fromperiodetarif'];
			list($p_posterr,$p_postmsg) = $p_model::saveSalinTarif($conn,$r_key,$p_dbtable,$f_key);
			
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
		}
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	//utk not null
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
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'periodetarif') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'periodetarif') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaperiode') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namaperiode') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglmulai') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglmulai') ?></td>
						</tr>
						<?if(!empty($r_key)){?>
						<tr height="30">
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr height="30">
							<td colspan="2" class="DataBG">Salin semua tarif dari periode</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'fromperiodetarif') ?></td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'fromperiodetarif') ?>
								&nbsp;&nbsp;&nbsp;
								<span id="edit" style="display:none">
									<input type="button" value="Salin Tarif" id="be_savedet" class="ControlStyle" onClick="goSaveDet()">
								</span>
							</td>
						</tr>
						<?}?>
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

function goSaveDet(){
	var combo2 = document.getElementById("fromperiodetarif");
    var val = combo2.options[combo2.selectedIndex].text

	var retval = confirm("Apakah anda yakin akan menyalin semua tarif gaji dari periode " + val + " ? ");
	if(retval) {
		document.getElementById("act").value = "salin";
		goSubmit();
	}
}
</script>
</body>
</html>