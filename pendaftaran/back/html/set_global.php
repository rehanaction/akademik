<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_read = $a_auth['canread'];
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('settingpendaftaran'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Setting Global';
	$p_tbwidth = 950;
	$p_aktivitas = 'NILAI';
	$p_model = mSettingpendaftaran;

	$a_input = array();	
	$a_input[] = array('kolom' => 'infospdureguler', 'label' => 'Informasuk SPDU (Reguler)', 'type'=>'M');
	$a_input[] = array('kolom' => 'infospduparalel', 'label' => 'Informasuk SPDU (Paralel)', 'type'=>'M');
	$a_input[] = array('kolom' => 'infoemailpendaftar', 'label' => 'Isi email pendaftar', 'type'=>'M');
	
	$r_act = $_POST['act'];
	
	if ($r_act =='save' and $c_edit){
		$r_key = 1;
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		foreach ($a_input as $key => $value) {
			if($value['type']=='M')
				$record[$value['kolom'].':skip'] = true;
		}

		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if (!$p_posterr)
		unset($post);
		
	}
	
	
	
	$row = $p_model::getDataEdit($conn,$a_input,1,$post);

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="scripts/foredit.js"></script>
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
			<form name="pageform" id="pageform" method="post">

				<?php require_once('inc_databutton.php'); ?>
				
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><img  style=" float: left; margin-top: -5px; margin-right: 10px;" id="img_workflow" width="26px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"><span><?= $p_title ?></span></div></center>
				<br>
				<?php
					
				if(isset($p_postmsg)){
				?>
					<center>
						<div class="DivSuccess" style="width:<?= $p_tbwidth ?>px"><?= $p_postmsg ?></div>
					</center>
				<?php
				} ?>
				
				<table width="<?= $p_tbwidth?>">
					<tr>
						<td><?= Page::getDataLabel($row,'infospdureguler')?> :<br> <?= Page::getDataInput($row,'infospdureguler')?></td>
						<td><?= Page::getDataLabel($row,'infospduparalel')?> :<br> <?= Page::getDataInput($row,'infospduparalel')?></td>
					</tr>
					<tr>
						<td><?= Page::getDataLabel($row,'infoemailpendaftar')?> :<br> <?=Page::getDataInput($row,'infoemailpendaftar')?></td>
					</tr>
					
				</table>
				
				<input type="hidden" name="act" id="act">
				
			</form>
		</div>
	</div>
</div>

</body>
</html>

<script>
 $(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
});
</script>
