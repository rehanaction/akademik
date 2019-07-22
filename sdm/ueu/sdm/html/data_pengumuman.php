<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('public'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pengumuman';
	$p_tbwidth = 600;
	$p_aktivitas = 'NEWS';
	$p_dbtable = "pe_pengumuman";
	$p_key = "idpengumuman";
	
	$p_model = mPublic;
	
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// struktur view
	$a_input = array();
	
	if(!empty($r_key)) {
		$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Waktu Posting', 'type' => 'DT', 'readonly' => true);
	}
	
	$a_input[] = array('kolom' => 'judulpengumuman', 'label' => 'Judul', 'maxlength' => 300, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'isipengumuman', 'label' => 'Isi Berita', 'type' => 'M');
	$a_input[] = array('kolom' => 'pengumuman', 'label' => 'Gambar', 'type' => 'UI', 'uptype' => 'pengumuman', 'size' => 40);
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Kadaluarsa', 'type' => 'D');
	$a_input[] = array('kolom' => 'link', 'label' => 'Halaman/Link', 'maxlength' => 300, 'size' => 50);
	$a_input[] = array('kolom' => 'filepengumuman', 'label' => 'File Pengumuman', 'type' => 'U', 'uptype' => 'filepengumuman', 'size' => 40);	
	
	// ada aksi
	$r_act = $_POST['act'];	
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
			$record['tglmulai'] = date('Y-m-d H:i:s');
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,$p_dbtable,'pengumuman',$p_key);
	
	// ambil data halaman
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	$ismce = false;
	$isupload = false;
	foreach($a_input as $t_input) {
		if($t_input['type'] == 'M')
			$ismce = true;
		else if($t_input['type'][0] == 'U')
			$isupload = true;
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
	<? if($ismce) { ?>
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
	<? } ?>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post"<?= $isupload ? ' enctype="multipart/form-data"' : '' ?>>
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
		
	$("img").not("[src]").each(function() {
		this.src = "index.php?page=img_datathumb&type="+this.id+"&id="+document.getElementById("key").value;
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
