<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastaktifitas'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pelanggaran Dengan Sanksi';
	$p_tbwidth = 500;
	$p_aktivitas = 'BIODATA';
	$p_dbtable = 'lv_pelanggaran';
	$p_key = 'idjenispelanggaran';
	$p_listpage = Route::getListPage();
	
	$p_model = mMastAktifitas;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idjenispelanggaran', 'label' => 'Kode', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'jenispelanggaran', 'label' => 'Nama Pelanggaran', 'maxlength' => 100, 'size' => 30);
	
	//combo untuk sanksi
	$l_sanksi = uCombo::sanksi($conn,$r_sanksi);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'savesanksi' and $c_edit) {
		$a_inputsp = array();
		$a_inputsp[] = array('kolom' => 'idjenispelanggaran');
		$a_inputsp[] = array('kolom' => 'jenissanksi');
		
		list($post,$record) = uForm::getPostRecord($a_inputsp,$_POST);
		list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_inputsp,$record,$r_key,'lv_sanksipelanggaran');
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'deletesanksi' and $c_delete) {
		$r_sub = CStr::removeSpecial($_REQUEST['sub']);
		$a_key = $r_key.'|'.$r_sub;
		$where = 'idjenispelanggaran,jenissanksi';
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$a_key,'lv_sanksipelanggaran',$where);
		
		if(!$p_posterr) unset($post);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	if(!empty($r_key))
		$a_sanksi = $p_model::getSanksi($conn,$r_key);
	
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
					<?	$a_required = array();
						foreach($row as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
					?>
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
							</td>
							<td class="RightColumnBG">
								<span id="show"><?= $t_row['value'] ?></span>
								<span id="edit" style="display:none"><?= $t_row['input'] ?></span>
							</td>
						</tr>
					<?	} ?>
					</table>
					</div>
				</center>
				<br>
				<?
					//Tambahan untuk input sanksi dari pelanggaran
					if(!empty($r_key)){
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;display:none" id="edit">
						<table width="<?= $p_tbwidth ?>px" cellspacing="0" cellpadding="4">
							<tr>		
								<td width="50" style="white-space:nowrap"><strong>Sanksi</strong></td>
								<td><strong> : </strong><?= $l_sanksi; ?></td>
								<td width="100">
								<? if($c_insert and $r_key){?>
									<input type="button" value="Tambah Sanksi" class="ControlStyle" onClick="goSaveSanksi()">
								<?}?>
								</td>								
							</tr>
						</table>
					</div>
				</center>
				<br>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Daftar Sanksi</h1>
							</div>
						</div>
					</header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<th width="100">Kode Sanksi</th>
					<th>Nama Sanksi</th>
					<th width="50">Aksi</th>
					<?
						$i = 0;
						if(count($a_sanksi) > 0){
							foreach($a_sanksi as $rows){
								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $rows['jenissanksi'];?></td>
						<td><?= $rows['keterangan'];?></td>
						<td align="center">
						<? if($c_delete) { ?>
							<img id="<?= $rows['jenissanksi'] ?>" title="Hapus Sanksi" src="images/delete.png" onclick="goDeleteSanksi(this)" style="cursor:pointer">
						<? } ?>
						</td>
					</tr>
					<?
							}
						}
						if($i == 0) {
					?>
					<tr>
						<td colspan="3" align="center">Data kosong</td>
					</tr>
					<?	}
					?>
				</table>
				<?}?>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="sub" id="sub">
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

function goSaveSanksi(){
	if(cfHighlight('jenissanksi')){
		document.getElementById("act").value = "savesanksi";
		goSubmit();
	}
}

function goDeleteSanksi(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "deletesanksi";
		document.getElementById("sub").value = elem.id;
		goSubmit();
	}
}
</script>
</body>
</html>
