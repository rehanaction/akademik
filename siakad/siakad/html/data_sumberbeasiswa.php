<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Sumber Beasiswa';
	$p_tbwidth = 600;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();
	
	$p_model = mSumberBeasiswa;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;	
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodesumber', 'label' => 'Kode Sumber', 'size' => 10, 'maxlength' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'namasumber', 'label' => 'Nama Sumber', 'size' => 50, 'maxlength' => 100, 'notnull' => true);
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'maxlength' => 150, 'size' => 50);
	$a_input[] = array('kolom' => 'kodepropinsi', 'label' => 'Propinsi', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'add' => 'onchange="loadKota()"', 'empty' => '-- Pilih Propinsi --');
	$a_input[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'type' => 'S', 'option' => mCombo::kota($conn), 'empty' => true, 'empty' => '-- Pilih Kota --');
	$a_input[] = array('kolom' => 'telp', 'label' => 'Telp', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'fax', 'label' => 'Fax', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'homepage', 'label' => 'Website', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'contactperson', 'label' => 'Contact Person', 'size' => 50, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 5, 'cols' => 40);
	
	// ada aksi
	$r_act = $_POST['act'];
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
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$r_kodekota = Page::getDataValue($row,'kodekota');
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
	
	loadKota();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

// ajax ganti kota
function loadKota() {
	var param = new Array();
	param[0] = $("#kodepropinsi").val();
	param[1] = "<?= $r_kodekota ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekota").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

</script>
</body>
</html>