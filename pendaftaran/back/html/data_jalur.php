<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
       // hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
        
	// include
	require_once(Route::getModelPath('gelombangdaftar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_self = (int)$_REQUEST['self'];
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getUserName();
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Setting Jalur Penerimaan';
	$p_tbwidth = 620;
	$p_aktivitas = 'SPMB';
	$p_listpage = Route::getListPage();
        //$p_foto = uForm::getPathImageMahasiswa($conn,$r_key);
	
	$p_model = mGelombangDaftar;
	
	
        // hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
        
	// struktur view
	if(empty($r_key))
		$p_edit = false;
	else
		$p_edit = true;
	
	$a_input = array();
	
	$a_input[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur Penerimaan', 'type' => 'S', 'notnull' => true, 'option' => mCombo::jalur($conn));
	$a_input[] = array('kolom' => 'periodedaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'notnull' => true, 'option' => mCombo::periode($conn));
	$a_input[] = array('kolom' => 'idgelombang', 'label' => 'Gelombang', 'type' => 'S', 'notnull' => true, 'option' => mCombo::gelombang($conn));
        
        $a_input[] = array('kolom' => 'tglawaldaftar', 'label' => 'Tanggal Pendaftaran', 'type' => 'D');
        $a_input[] = array('kolom' => 'tglakhirdaftar', 'label' => '', 'type' => 'D');
        $a_input[] = array('kolom' => 'tglujian', 'label' => 'Tanggal Ujian', 'type' => 'D');
        $a_input[] = array('kolom' => 'tglujian2', 'label' => '', 'type' => 'D');
        $a_input[] = array('kolom' => 'tglpengumuman', 'label' => 'Tanggal Pengumuman', 'type' => 'D');
        $a_input[] = array('kolom' => 'tglawalregistrasi', 'label' => 'Tanggal Registrasi', 'type' => 'D');
        $a_input[] = array('kolom' => 'tglakhirregistrasi', 'label' => '', 'type' => 'D');
        $a_input[] = array('kolom' => 'sistemkuliah', 'label' => 'Sistem Kuliah', 'type' => 'S','notnull' => true, 'option' => mCombo::sistemKuliah($conn));
        $a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif ?', 'type' => 'C', 'option' => array('t' => ''));
		$a_input[] = array('kolom' => 'isopen', 'label' => 'buka ?', 'type' => 'C', 'option' => array('t' => ''));
		$a_input[] = array('kolom' => 'isbayar', 'label' => 'Berbayar ?', 'type' => 'C', 'option' => array('t' => ''));
        $a_input[] = array('kolom' => 'pengumuman', 'label' => 'Informasi Jalur Pendaftaran', 'type' => 'M');
        $a_input[] = array('kolom' => 'pengumumandu', 'label' => 'Informasi Daftar Ulang', 'type' => 'M');
		$a_input[] = array('kolom' => 'filependaftaran', 'label' => 'File Info Pendaftaran', 'type' => 'U','uptype' => 'pengumumanpendaftaran','arrtype'=>array('pdf','docx','doc'));        
		$a_input[] = array('kolom' => 'filedaftarulang', 'label' => 'File Info Daftar Ulang', 'type' => 'U','uptype' => 'pengumumandaftarulang','arrtype'=>array('pdf','docx','doc'));        
		
       //aksi
        $r_act = $_POST['act'];
        $r_jenis = $_POST['jenis'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}else if($r_act == 'deletefile' and $c_delete) {
		
		list($p_posterr,$p_postmsg) = $p_model::deleteCFile($conn,$r_key,$r_jenis);
		
	}
        
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	if(empty($row[0]['value']) and !empty($r_key)) {
		$p_posterr = true;
		$p_fatalerr = true;
		$p_postmsg = 'Tidak ada jalur seperti yang dimaksud.';
	}
        
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript" src="scripts/foredit.js"></script>
        <script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<!--tynyMCE -->
	<script type="text/javascript" src="scripts/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
	tinyMCE.init({
		mode: "textareas",
		height: "300",
		theme: "advanced",
		theme_advanced_toolbar_location : "top",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,hr,removeformat,|,charmap,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor",
		theme_advanced_buttons3 : ""
	});
	</script>

	<style>
		#table_evaluasi { border-collapse:collapse }
		#table_evaluasi .td_ev { border:1px solid #666 }
	</style>
</head>
<body>
	
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
        <script type="text/javascript">
$(".subnav").hover(function() {
    $(this.parentNode).addClass("borderbottom");
}, function() {
    $(this.parentNode).removeClass("borderbottom");
});

</script>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
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
					<?	$a_required = array('periodedaftar','jalurpenerimaan', 'idgelombang'); ?>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'periodedaftar') ?>
						<?= Page::getDataTR($row,'jalurpenerimaan') ?>
						<?= Page::getDataTR($row,'idgelombang',', ') ?>
						<?//= Page::getDataTR($row,'sistemkuliah') ?>
						<?= Page::getDataTR($row,'isaktif') ?>
						<?= Page::getDataTR($row,'isopen') ?>
						<?= Page::getDataTR($row,'isbayar') ?>
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Tanggal Penting</a></li>
						<li><a id="tablink" href="javascript:void(0)">Deskripsi</a></li>
					</ul>
				
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglawaldaftar') ?></td>
							<td><?= Page::getDataInput($row,'tglawaldaftar') ?> sampai dengan <?= Page::getDataInput($row,'tglakhirdaftar') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglujian') ?></td>
							<td><?= Page::getDataInput($row,'tglujian') ?> sampai dengan <?= Page::getDataInput($row,'tglujian2') ?></td>
						</tr>
						<?//= Page::getDataTR($row,'tglujian',', ') ?>
						<?= Page::getDataTR($row,'tglpengumuman') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglawalregistrasi') ?></td>
							<td><?= Page::getDataInput($row,'tglawalregistrasi') ?> sampai dengan <?= Page::getDataInput($row,'tglakhirregistrasi') ?></td>
						</tr>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filependaftaran') ?></td>
							<td><?= Page::getDataInput($row,'filependaftaran') ?>
								<?= Page::getDataValue($row,'filependaftaran') ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pengumuman') ?></td>
							<td><?= Page::getDataInput($row,'pengumuman') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filedaftarulang') ?></td>
							<td><?= Page::getDataInput($row,'filedaftarulang') ?>
								<?= Page::getDataValue($row,'filedaftarulang') ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pengumumandu') ?></td>
							<td><?= Page::getDataInput($row,'pengumumandu') ?></td>
						</tr>
						
					</table>
					</div>
				</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="jenis" id="jenis">
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
	initTab();	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goDeleteFile(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus file ini?");
	if(hapus) {
		
		document.getElementById("act").value = "deletefile";
		document.getElementById("jenis").value = elem;
		goSubmit();
	}
}


</script>
</body>
</html>
