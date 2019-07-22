<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_insert = false;
	$c_update = $a_auth['canupdate'];
	$c_delete = false;
	
	//akses edit
	$c_edits = $a_auth['canother']['E'];
	
	// include
	require_once(Route::getModelPath('settingskpiprodi'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert and $c_edits) or (!empty($r_key) and $c_update and $c_edits))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Informasi Prodi';
	$p_tbwidth = 640;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mSettingSkpiProdi;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'namauniten', 'label' => 'Nama Prodi (en)');
	$a_input[] = array('kolom' => 'gelar', 'label' => 'Gelar');
	$a_input[] = array('kolom' => 'gelarsingkat', 'label' => 'Gelar Singkat');
	$a_input[] = array('kolom' => 'gelaren', 'label' => 'Gelar (eng)');
	$a_input[] = array('kolom' => 'syaratpenerimaan', 'label' => 'Syarat Penerimaan');
	$a_input[] = array('kolom' => 'syaratpenerimaanen', 'label' => 'Syarat Penerimaan (eng)');
	$a_input[] = array('kolom' => 'lamastudi', 'label' => 'Lama Studi');
	$a_input[] = array('kolom' => 'bahasapengantar', 'label' => 'Bahasa Pengantar');
	$a_input[] = array('kolom' => 'bahasapengantaren', 'label' => 'Bahasa Pengantar (en)');
	$a_input[] = array('kolom' => 'jenispendidikan', 'label' => 'Jenis Pendidikan');
	$a_input[] = array('kolom' => 'jenispendidikanen', 'label' => 'Jenis Pendidikan (en)');
	$a_input[] = array('kolom' => 'jenjangpendidikan', 'label' => 'Jenjang Pendidikan');
	$a_input[] = array('kolom' => 'jenjangpendidikanen', 'label' => 'Jenjang Pendidikan (en)');
	$a_input[] = array('kolom' => 'jenispendidikanlanjut', 'label' => 'Jenis Pendidikan Lanjut');
	$a_input[] = array('kolom' => 'jenispendidikanlanjuten', 'label' => 'Jenis Pendidikan Lanjut (en)');
	$a_input[] = array('kolom' => 'jenjangpendidikanlanjut', 'label' => 'Jenjang Pendidikan Lanjut');
	$a_input[] = array('kolom' => 'jenjangpendidikanlanjuten', 'label' => 'Jenjang Pendidikan Lanjut (en)');
	$a_input[] = array('kolom' => 'jenjangkkni', 'label' => 'Jenjang KKNI');
	$a_input[] = array('kolom' => 'formatnomor', 'label' => 'Format Nomor SKPI');
	
	$a_input[] = array('kolom' => 'kemampuankerja', 'label' => 'Kemampuan Kerja','type'=>'M');
	$a_input[] = array('kolom' => 'kemampuankerjaen', 'label' => 'Kemampuan (eng)','type'=>'M');
	$a_input[] = array('kolom' => 'penguasaanpengetahuan', 'label' => 'Penguasaan Pengetahuan','type'=>'M');
	$a_input[] = array('kolom' => 'penguasaanpengetahuanen', 'label' => 'Penguasaan Pengetahuan (eng)','type'=>'M');
	$a_input[] = array('kolom' => 'sikapkhusus', 'label' => 'Sikap Khusus','type'=>'M');
	$a_input[] = array('kolom' => 'sikapkhususen', 'label' => 'Sikap Khusus (eng)','type'=>'M');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
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
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<?	$a_required = array('nama_program_studi'); ?>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Program Studi</td>
							<td class="RightColumnBG"><?= $r_namaunit ?></td>
						</tr>
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Info Umum</a></li>
						<li><a id="tablink" href="javascript:void(0)">Capaian (Kemampuan Kerja)</a></li>
						<li><a id="tablink" href="javascript:void(0)">Capaian (Penguasaan Pengetahuan)</a></li>
						<li><a id="tablink" href="javascript:void(0)">Capaian (Sikap Khusus)</a></li>
					</ul>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'namauniten') ?>
						<?= Page::getDataTR($row,'gelar') ?>
						<?= Page::getDataTR($row,'gelarsingkat') ?>
						<?= Page::getDataTR($row,'gelaren') ?>
						<?= Page::getDataTR($row,'syaratpenerimaan') ?>
						<?= Page::getDataTR($row,'syaratpenerimaanen') ?>
						<?= Page::getDataTR($row,'lamastudi') ?>
						<?= Page::getDataTR($row,'bahasapengantar') ?>
						<?= Page::getDataTR($row,'bahasapengantaren') ?>
						<?= Page::getDataTR($row,'jenispendidikan') ?>
						<?= Page::getDataTR($row,'jenispendidikanen') ?>
						<?= Page::getDataTR($row,'jenjangpendidikan') ?>
						<?= Page::getDataTR($row,'jenjangpendidikanen') ?>
						<?= Page::getDataTR($row,'jenispendidikanlanjut') ?>
						<?= Page::getDataTR($row,'jenispendidikanlanjuten') ?>
						<?= Page::getDataTR($row,'jenjangpendidikanlanjut') ?>
						<?= Page::getDataTR($row,'jenjangpendidikanlanjuten') ?>
						<?= Page::getDataTR($row,'jenjangkkni') ?>
						<?= Page::getDataTR($row,'formatnomor') ?>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'kemampuankerja') ?>
						<?= Page::getDataTR($row,'kemampuankerjaen') ?> 
						
					</table>
					</div>
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'penguasaanpengetahuan') ?>
						<?= Page::getDataTR($row,'penguasaanpengetahuanen') ?> 
						
					</table>
					</div>
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'sikapkhusus') ?>
						<?= Page::getDataTR($row,'sikapkhususen') ?> 
						
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
