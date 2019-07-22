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
	require_once(Route::getModelPath('riwayat'));
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
			
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$r_periode = CStr::removeSpecial($_REQUEST['periodetarif']);
	$r_tunjangan = !empty($_REQUEST['kodetunjangan']) ? CStr::removeSpecial($_REQUEST['kodetunjangan']) : CStr::removeSpecial($_REQUEST['tunjangan']);
	if(empty($r_tunjangan) and !empty($r_key))
		$r_tunjangan = $p_model::getTunjangan($conn,$r_key);
		
	$p_tbwidth = "600";
	$p_title = "Data Tarif Tunjangan";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ms_tariftunjangan";
	$p_key = "idtarif";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'periodetarif', 'label' => 'Periode Tarif', 'type' => 'S', 'option' => $p_model::getCPeriodeTarif($conn), 'default' => $r_periode);
	
	if(empty($r_key))
		$a_input[] = array('kolom' => 'kodetunjangan', 'label' => 'Tunjangan', 'type' => 'S', 'option' => $p_model::getCTunjTarif($conn), 'default' => $r_tunjangan, 'add' => 'onchange="goSubmit()"');
	else
		$a_input[] = array('kolom' => 'kodetunjangan', 'label' => 'Tunjangan', 'type' => 'S', 'option' => $p_model::getCTunjTarif($conn), 'readonly' => true);
	
	if($r_tunjangan == 'T00001' or $r_tunjangan == 'T00017') //T. Struktural
		$a_input[] = array('kolom' => 'variabel1', 'label' => 'Jabatan', 'type' => 'S', 'option' => mMastKepegawaian::aJabatan($conn));
	
	else if($r_tunjangan == 'T00018') //T. Fasilitas
		$a_input[] = array('kolom' => 'variabel1', 'label' => 'Struktural', 'type' => 'S', 'option' => mCombo::strukturalSave($conn,false));
	
	else if($r_tunjangan == 'T00013') //T. Fungsional
		$a_input[] = array('kolom' => 'variabel1', 'label' => 'Fungsional', 'type' => 'S', 'option' => mRiwayat::jabatanFungsional($conn));
		
	else if($r_tunjangan == 'T00020'){ //T. Masa Kerja
		$a_input[] = array('kolom' => 'variabel1', 'label' => 'Masa Kerja Acuan', 'maxlength' => 3, 'size' => 3, 'notnull' => true, 'infoedit' => 'tahun');
	}
	
	else //Selain di atas
		$a_input[] = array('kolom' => 'variabel1', 'label' => 'Jenis Pegawai', 'type' => 'S', 'option' => $p_model::getCJenisPegawai($conn));
		
	$a_input[] = array('kolom' => 'nominal', 'label' => 'Jumlah Tarif', 'maxlength' => 12, 'size' => 15, 'type' => 'N', 'notnull' => true);
	
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			unset($post);
		}
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
				<input type="hidden" name="tunjangan" id="tunjangan" value="<?= $r_tunjangan ?>">
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

function goNew(){
	$("#key").val('');
	goSubmit();
}
</script>
</body>
</html>
