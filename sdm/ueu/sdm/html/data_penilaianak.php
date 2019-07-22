<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('angkakredit'));
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
	$p_title = 'Data Aturan Penilaian Angka Kredit';
	$p_tbwidth = 650;
	$p_aktivitas = 'UNIT';
	$p_key = 'idkegiatan';
	$p_dbtable = 'ms_penilaian';
	$p_listpage = Route::getListPage();
	
	$p_model = mAngkaKredit;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//cek bila add child, lalu ambil field dari parent
	if(!empty($pkey)){
		$pcat = $p_model::pPenilaian($conn,$pkey);
	}
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodekegiatan', 'label' => 'Kode Kegiatan', 'maxlength' => 20, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan', 'type' => 'A', 'rows' => 3, 'cols' => 70, 'maxlength' => 255, 'notnull' => true);
	$a_input[] = array('kolom' => 'stdkredit', 'label' => 'Nilai Kredit', 'maxlength' => 5, 'size' => 5, 'type' => 'N,2');
	
	//default dari parent
	if(!empty($pkey)){
		$a_input[] = array('kolom' => 'bidangkegiatan', 'label' => 'Berlaku Pada Bidang', 'type' => 'S', 'empty' => true, 'option' => $p_model::jenisBidang(),'default' => $pcat['bidangkegiatan']);
		$a_input[] = array('kolom' => 'parentkegiatan', 'label' => 'Parent', 'type' => 'A', 'rows' => 3, 'cols' => 70, 'maxlength' => 255, 'class' => 'ControlRead','default' => $pcat['parentkegiatan']);
		$a_input[] = array('kolom' => 'parentidkegiatan', 'type' => 'H','default' => $pkey);
	}else{
		$a_input[] = array('kolom' => 'bidangkegiatan', 'label' => 'Berlaku Pada Bidang', 'type' => 'S', 'empty' => true, 'option' => $p_model::jenisBidang());
		$a_input[] = array('kolom' => 'parentkegiatan', 'label' => 'Parent', 'type' => 'A', 'rows' => 3, 'cols' => 70, 'maxlength' => 255, 'class' => 'ControlRead');
		$a_input[] = array('kolom' => 'parentidkegiatan', 'type' => 'H');
	}
	
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	
	//default dari parent
	if(!empty($pkey))
		$a_input[] = array('kolom' => 'isaktif', 'label' => 'is Aktif?', 'type' => 'S', 'option' => mCombo::isAktif(),'default' => $pcat['isaktif']);
	else
		$a_input[] = array('kolom' => 'isaktif', 'label' => 'is Aktif?', 'type' => 'S', 'option' => mCombo::isAktif());		
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		//untuk level dan kode urutan
		$recadd = $p_model::levelPenilaian($conn,$record['parentidkegiatan'],$r_key);
		if(count($recadd) > 0)
			$record = array_merge($record,$recadd);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			unset($post);
			$pkey = '';
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$sql = $p_model::getDataEditPenilaian($r_key);
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
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodekegiatan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodekegiatan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namakegiatan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namakegiatan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'stdkredit') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'stdkredit') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'bidangkegiatan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'bidangkegiatan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'parentkegiatan') ?></td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'parentkegiatan') ?>
								<?= Page::getDataInput($row,'parentidkegiatan') ?>
								<span id="edit" style="display:none;"><img src="images/magnify.png" title="Pilih Parent Kegiatan" style="cursor:pointer" onclick="showParent()"></span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'keterangan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'keterangan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isaktif') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'isaktif') ?></td>
						</tr>
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

function showParent(){
	win = window.open("<?= Route::navAddress('pop_penilaian').'&m=1'?>","popup_penilaian","width=650,height=500,scrollbars=1");
	win.focus();
}
</script>
</body>
</html>
