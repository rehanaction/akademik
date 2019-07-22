<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// cek apakah sudah login
	if(!Modul::isAuthenticated())
		Route::redirect($conf['menu_path']);
	
	// include
	require_once(Route::getModelPath('berita'));
	
	$p_model = mBerita;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// cek data
	if(!empty($r_key)) {
		$a_cek = $p_model::getData($conn,$r_key);
		
		if(empty($a_cek['validator']))
			$r_key = '';
	}
	
	// data pelengkap
	$a_jenis = $p_model::jenisBerita();
	
	// properti halaman
	if(empty($r_key)) {
		$p_title = 'Daftar Berita';
		$p_jenis = 'Publikasi';
		
		$a_lain = mBerita::getList($conn);
	}
	else {
		$p_title = $a_cek['judulberita'];
		$p_jenis = $a_jenis[$a_cek['jenis']];
		
		if($a_cek['jenis'] == 'P')
			$a_lain = mBerita::getListPengumuman($conn,25);
		else
			$a_lain = mBerita::getListBerita($conn,25);
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
	<?	if(!empty($r_key)) { ?>
		<div id="div_left" class="SideItem" style="width:65%">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/BERITA.png" onerror="loadDefaultActImg(this)"> <?= $p_jenis ?>
			</div>
			<div>
<!--
				<img src="<?= Route::navAddress('img_datathumb&type='.mBerita::uptype.'&alt=2&id='.$a_cek['idberita']) ?>">
-->
				<div class="Break"></div>
				<div class="SideTitle"><?= $a_cek['judulberita'] ?></div>
				<div class="Break"></div>
				<div><em><?= CStr::formatDateTimeInd($a_cek['waktuvalid'],true,true) ?> oleh <?= $a_cek['namacreator'] ?></em></div>
				<div class="Break"></div>
				<div class="NewsContent"><?= $a_cek['isi'] ?></div>
				<div class="Break"></div>
				<div><u class="ULink" onclick="javascript:goView('home')">Kembali ke Home</u></div>
			</div>
		</div>
	<?	} ?>
		<div id="div_right" class="SideItem" style="float:right;width:<?= empty($r_key) ? '97' : '25' ?>%">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/BERITA.png" onerror="loadDefaultActImg(this)"> Daftar <?= $p_jenis ?>
			</div>
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
			<?	foreach($a_lain as $row) { ?>
				<tr>
					<? if(empty($r_key)) { ?>
					<td width="100"><?= $a_jenis[$row['jenis']]; ?></td>
					<? } ?>
					<td><u class="ULink" onclick="javascript:goDetail('<?= $row['idberita'] ?>')"><?= $row['judulberita'] ?></u></td>
					<td align="right"><?= CStr::formatDateDiff($row['waktuvalid']) ?></td>
				</tr>
			<?	} ?>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function() {
	if($("#div_left").height() > $("#div_right").height())
		$("#div_right").height($("#div_left").height());
	else
		$("#div_left").height($("#div_right").height());
});

function goDetail(id) {
	goView('view_berita&key='+id);
}

</script>

</body>
</html>
