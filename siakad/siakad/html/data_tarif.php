<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('tarif'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Tarif';
	$p_tbwidth = 500;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();
	
	$p_model = mTarif;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;	
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'jenistarif', 'label' => 'Jenis Tarif', 'type' => 'S', 'option' => $p_model::jenisTarif($conn));
	$a_input[] = array('kolom' => 'semester', 'label' => 'Periode Daftar', 'type' => 'S', 'option' => mCombo::semester(), 'request' => 'SEMESTER');
	$a_input[] = array('kolom' => 'tahun', 'type' => 'S', 'option' => mCombo::tahun(), 'request' => 'TAHUN');
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Unit', 'type' => 'S', 'option' => mCombo::unit($conn,false));
	$a_input[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur', 'type' => 'S', 'option' => mCombo::jalurPenerimaan($conn));
	$a_input[] = array('kolom' => 'isasing', 'label' => 'Mhs Asing', 'type' => 'C', 'option' => array('-1' => ''));
	$a_input[] = array('kolom' => 'jumlahtotal', 'label' => 'Total Tarif', 'type' => 'N', 'size' => 14, 'maxlength' => 14);
	
	// mengambil data pelengkap
	$a_detail = array();
	
	$t_detail = array();
	$t_detail[] = array('kolom' => 'nourut', 'label' => 'No.', 'type' => 'N', 'size' => 2, 'maxlength' => 2, 'width' => 35);
	$t_detail[] = array('kolom' => 'namatarif', 'label' => 'Nama', 'size' => 30, 'maxlength' => 50);
	$t_detail[] = array('kolom' => 'jumlahtarif', 'label' => 'Tarif', 'type' => 'N', 'size' => 14, 'maxlength' => 14);
	
	$a_detail['detail'] = array('key' => $p_model::getDetailInfo('detail','key'), 'data' => $t_detail);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		// hapus manual :D
		unset($post['jumlahtotal']);
		
		$record['periodemasuk'] = $record['tahun'].$record['semester'];
		
		// pengecekan tarif existing
		$t_isdup = $p_model::getTarifDuplikat($conn,$record,$r_key);
		
		if($t_isdup === false) {
			if(empty($r_key))
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}
		else {
			$p_posterr = true;
			$p_postmsg = 'Tarif tersebut telah ada sebelumnya, untuk melihatnya klik <u class="ULink" onclick="goView(\''.$i_page.'&key='.$t_isdup.'\')">di sini</u>';
		}
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit and !$p_limited) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_name = CStr::cEmChg($t_detail['nameid'],$t_detail['kolom']);
			$a_value[$t_name] = $_POST[$r_detail.'_'.$t_name];
		}
		
		list(,$record) = uForm::getPostRecord($a_detail[$r_detail]['data'],$a_value);
		$record['idtarif'] = $r_key;
		
		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit and !$p_limited) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	if(!empty($r_key)) {
		$rowd = array();
		$rowd += $p_model::getDetailTarif($conn,$r_key,'detail');
	}
	
	// ganti manual :D
	if(!empty($rowd['detail']))
		$row[4]['input'] = $row[4]['value'];
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
			<form name="pageform" id="pageform" method="post"<?= $isupload ? ' enctype="multipart/form-data"' : '' ?>>
				<?	/*****************/
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
						<?= Page::getDataTR($row,'jenistarif') ?>
						<?= Page::getDataTR($row,'semester,tahun') ?>
						<?= Page::getDataTR($row,'kodeunit') ?>
						<?= Page::getDataTR($row,'jalurpenerimaan') ?>
						<?= Page::getDataTR($row,'isasing') ?>
						<?= Page::getDataTR($row,'jumlahtotal') ?>
					</table>
					<? if(!empty($r_key)) { ?>
					<br>
					<?= Page::getDetailTable($rowd,$a_detail,'detail','Detail Tarif',false) ?>
					<? } ?>
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

function goSave() {
	// cek unit
	var pass = true;
	
	var opslc = $("#kodeunit option:selected");
	var opnext = opslc.next();
	
	if(opnext) {
		var nameslc = opslc.text();
		var dotslc = 0;
		for(i=0;i<nameslc.length;i++) {
			if(nameslc.charCodeAt(i) == 160)
				dotslc++;
			else
				break;
		}
		
		var namenext = opnext.text();
		var dotnext = 0;
		for(i=0;i<namenext.length;i++) {
			if(namenext.charCodeAt(i) == 160)
				dotnext++;
			else
				break;
		}
		
		if(dotnext > dotslc) {
			pass = false;
			doHighlight(document.getElementById("kodeunit"));
			
			alert("Pilih Unit dengan level terendah (jurusan atau program studi)");
		}
	}
	
	if(pass) {
		if(typeof(required) != "undefined") {
			if(!cfHighlight(required))
				pass = false;
		}
	}
	
	if(pass) {
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

</script>
</body>
</html>