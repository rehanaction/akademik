<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('settingskpi'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = 1;
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Informasi SKPI';
	$p_tbwidth = 640;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mSettingSkpi;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	
	$a_input[] = array('kolom' => 'pendahuluan', 'label' => 'Pendahuluan','type'=>'M');
	$a_input[] = array('kolom' => 'pendahuluanen', 'label' => 'Pendahuluan (en)','type'=>'M');
	$a_input[] = array('kolom' => 'infopt', 'label' => 'Sistem Perguruan Tinggi','type'=>'M');
	$a_input[] = array('kolom' => 'infopten', 'label' => 'Sistem Perguruan Tinggi (en)','type'=>'M');
	$a_input[] = array('kolom' => 'jenjang', 'label' => 'Jenjang Pendidikan <br> dan Syarat Belajar','type'=>'M');
	$a_input[] = array('kolom' => 'jenjangen', 'label' => 'Jenjang Pendidikan <br> dan Syarat Belajar (en)','type'=>'M');
	$a_input[] = array('kolom' => 'sks', 'label' => 'SKS dan Lama Studi','type'=>'M');
	$a_input[] = array('kolom' => 'sksen', 'label' => 'SKS dan Lama Studi (en)','type'=>'M');
	$a_input[] = array('kolom' => 'infokkni', 'label' => 'Informasi KKNI ','type'=>'M');
	$a_input[] = array('kolom' => 'infokknien', 'label' => 'Informasi KKNI (en) ','type'=>'M');
	$a_input[] = array('kolom' => 'filetable', 'label' => 'File Skema KKNI', 'type' => 'U', 'uptype' => 'skpi');

	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		foreach ($a_input as $key => $value) {
			if($value['type']=='M')
				$record[$value['kolom'].':skip'] = true;
		}
		$record['kodeunit']= $r_key;
		$cek = $p_model::getData($conn,$r_key);	
		if(empty($cek))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteProdi($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}else if($r_act == 'savefoto') {
		if($_FILES['foto']['size']>512000){
			$msg = 'Upload gagal, Maksimal File 500 KB';
		}else if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],uForm::getPathImageSKPI($conn,$r_key),300,300);
			//var_dump($err);die;
			switch($err) {
				case -1:
				case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
				case -3: $msg = 'foto tidak bisa disimpan'; break;
				default: $msg = false;
			}
			if($msg !== false)
				$msg = 'Upload gagal, '.$msg;
			
		}
		else
			$msg = Route::uploadErrorMsg($_FILES['foto']['error']);
		
		uForm::reloadImageSKPI($conn,$r_key,$msg);
	}
	else if($r_act == 'deletefoto' and $c_upload) {
		@unlink($p_foto);
		
		uForm::reloadImageSKPI($conn,$r_key);
	}
	
	$r_namaunit = mUnit::getNamaUnit($conn,$r_key);
	
	//$sql = $p_model::dataQueryProdi($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$sql);
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
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
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
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">PENDAHULUAN</a></li>
						<li><a id="tablink" href="javascript:void(0)">INFORMASI SISTEM PENDIDIKAN TINGGI DI INDONESIA</a></li>
						<li><a id="tablink" href="javascript:void(0)">INFORMASI KKNI</a></li>
					</ul>
										
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'pendahuluan') ?>
						<?= Page::getDataTR($row,'pendahuluanen') ?> 
						
					</table>
					</div>
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'infopt') ?>
						<?= Page::getDataTR($row,'infopten') ?> 
						<?= Page::getDataTR($row,'jenjang') ?> 
						<?= Page::getDataTR($row,'jenjangen') ?> 
						<?= Page::getDataTR($row,'sks') ?> 
						<?= Page::getDataTR($row,'sksen') ?> 
						
					</table>
					</div>
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'infokkni') ?>
						<?= Page::getDataTR($row,'infokknien') ?> 
						<tr>
							<td class="LeftColumnBG">File Skema KKNI</td>
							<td class="RightColumnBG"><?=uForm::getImageSKPI($conn,$r_key)?><br>klik gambar untuk upload</td>
						</tr>
					</table>
					</div>
				</div>
				
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
	initTab();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
