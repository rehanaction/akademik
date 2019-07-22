<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$conn->debug = true;
	// include
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$pkey = CStr::removeSpecial($_REQUEST['pkey']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Jabatan Struktural';
	$p_tbwidth = 750;
	$p_aktivitas = 'UNIT';
	$p_key = 'idjstruktural';
	$p_dbtable = 'ms_struktural';
	$p_listpage = Route::getListPage();
	
	$p_model = mMastKepegawaian;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//cek bila add child, lalu ambil field dari parent
	if(!empty($pkey)){
		$pcat = $p_model::pStruktural($conn,$pkey);
	}
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idjstruktural', 'label' => 'Kode Jabatan', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'idjabatan', 'label' => 'Jenis Jabatan', 'type' => 'S', 'option' => $p_model::aJabatan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'jabatanstruktural', 'label' => 'Nama Jabatan', 'maxlength' => 100, 'size' => 70, 'notnull' => true);
	
	//default dari parent
	if(!empty($pkey)){
		$a_input[] = array('kolom' => 'parentjstruktural', 'label' => 'Parent', 'type' => 'S', 'empty' => true, 'option' => mCombo::strukturalSave($conn,false),'default' => $pkey);
		$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit Kerja', 'type' => 'S', 'option' => mCombo::unitSave($conn,false),'default' => $pcat['idunit']);
	}else{
		$a_input[] = array('kolom' => 'parentjstruktural', 'label' => 'Parent', 'type' => 'S', 'empty' => true, 'option' => mCombo::strukturalSave($conn,false));
		$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit Kerja', 'type' => 'S', 'option' => mCombo::unitSave($conn,false));
	}
	
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'pangkatmax', 'label' => 'Pangkat Max', 'type' => 'S', 'option' => $p_model::aPangkat($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodeeselon', 'label' => 'Eselon', 'type' => 'S', 'option' => $p_model::aEselon($conn));
	//$a_input[] = array('kolom' => 'pangkatmin', 'label' => 'Pangkat Min', 'type' => 'S', 'option' => $p_model::cobaPangkat($conn), 'empty' => true);
	//$a_input[] = array('kolom' => 'pangkatmax', 'label' => 'Pangkat Max', 'type' => 'S', 'option' => $p_model::aPangkat($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'bebansks', 'label' => 'Beban SKS', 'maxlength' => 4, 'size' => 5, 'type' => 'N');		
	$a_input[] = array('kolom' => 'ispimpinan', 'label' => 'is Pimpinan?', 'type' => 'S', 'option' => $p_model::isPimpinan());
	
	//default dari parent
	if(!empty($pkey))
		$a_input[] = array('kolom' => 'isaktif', 'label' => 'is Aktif?', 'type' => 'S', 'option' => mCombo::isAktif(),'default' => $pcat['isaktif']);
	else
		$a_input[] = array('kolom' => 'isaktif', 'label' => 'is Aktif?', 'type' => 'S', 'option' => mCombo::isAktif());
		
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
					
		$conn->BeginTrans();
		
		//untuk infoleft dan inforight	
		list($recadd,$p_posterr) = $p_model::saveInfo($conn,$p_dbtable,'parentjstruktural',$p_key,$record['parentjstruktural'],$r_key);
		if(!$p_posterr){
			if(count($recadd) > 0)
				$record = array_merge($record,$recadd);
			
			if(empty($r_key))
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
			else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		}
		
		if(!$p_posterr){			
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
			
			unset($post);
			$pkey = '';
		}else
			$conn->RollbackTrans();
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
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
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="pkey" id="pkey" value="<?= $pkey ?>">
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

</script>
</body>
</html>
