<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_readlist = true;		
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai())
		$r_self = 1;
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_edit = $a_auth['canupdate'];
		$c_delete = $a_auth['candelete'];
	}
		
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
			
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	// properti halaman
	$p_title = 'Data Perubahan Hari Kerja';
	$p_tbwidth = 800;
	$p_aktivitas = 'TIME';
	$p_listpage = Route::getListPage();
	
	$p_model = mPresensi;
	$p_dbtable = "pe_ubahharikerja";
	$where = 'idperubahan';
	
	$a_jamhadir = $p_model::getCJamHadir($conn);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tgldiubah', 'label' => 'Tgl. Sebelumnya', 'type' => 'D', 'notnull' => true,'add' => 'onchange="getShift(this.value)"');
	$a_input[] = array('kolom' => 'sjamdatangubah', 'label' => 'Jam Sebelumnya', 'maxlength' => 4, 'size' => 4, 'notnull' => true, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'sjampulangubah', 'label' => 'Jam Pulang', 'maxlength' => 4, 'size' => 4, 'infoedit' => 'Contoh : 1000 s/d 1800', 'notnull' => true, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'tglperubahan', 'label' => 'Tgl. Perubahan', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'sjamdatang', 'label' => 'Jam Perubahan', 'maxlength' => 4, 'size' => 4, 'notnull' => true);
	$a_input[] = array('kolom' => 'sjampulang', 'label' => 'Jam Pulang', 'maxlength' => 4, 'size' => 4, 'infoedit' => 'Contoh : 0900 s/d 1700', 'notnull' => true);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$isExist = $p_model::isDataExist($conn,$record['tglperubahan'],'pe_ubahharikerja','tglperubahan');
		
		$record['idpegawai'] = $r_key;
		$conn->BeginTrans();
		
		if (!$isExist){
			if(empty($r_subkey))
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
			else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
			
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
					
			if(!$p_posterr) unset($post);
		}
		else{
			$p_posterr = true;
			$p_postmsg = "Tanggal Perubahan Hari Kerja sudah ada!!!";
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where);
		
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
		
		//setting jam		
		if($t_row['id'] == 'jamdatang')
			$jamdatang = $t_row['value'];	
		if($t_row['id'] == 'jampulang')
			$jampulang = $t_row['value'];
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
	<script type="text/javascript" src="scripts/foreditx.js"></script>
</head>
<body>
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
						require_once('inc_databuttonajax.php');
					
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
							<td width="200px" class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tgldiubah') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tgldiubah') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'sjamdatangubah') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'sjamdatangubah') ?> s/d <?= Page::getDataInput($row,'sjampulangubah') ?></td>
						</tr>
						<tr>
							<td width="200px" class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglperubahan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tglperubahan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'sjamdatang') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'sjamdatang') ?> s/d <?= Page::getDataInput($row,'sjampulang') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>						
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<?	} ?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {

	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function getShift(tgl){
	var posted = "f=gshift&q[]="+$("#key").val()+"&q[]="+tgl;
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var text = text.split('|');
		
		$("#sjamdatangubah").val(text[0]);
		$("#sjampulangubah").val(text[1]);
	});
}
</script>
</body>
</html>

