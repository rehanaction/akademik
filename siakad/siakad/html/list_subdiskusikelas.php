<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('diskusi'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mDiskusi;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	$r_page = (int)$_POST['page'];
	if(empty($r_page))
		$r_page = (int)$_GET['go'];
	
	// properti halaman
	$p_title = 'Diskusi';
	$p_tbwidth = 700;
	$p_aktivitas = 'FORUM';
	$p_listpage = 'list_diskusikelas';
	$p_detailpage = 'data_diskusi';
	$p_topikpage = 'list_topikdiskusikelas';
	$p_toppage = 'list_diskusi';
	$p_navpage = true;
	
	// cek request
	if(empty($r_key))
		Route::navigate($p_listpage);
	if(empty($r_page))
		$r_page = 1;
	
	// cek data
	$a_cek = $p_model::getData($conn,$r_key);
	
	$r_kelas = mKelas::getKeyRow($a_cek);
	$r_topik = $a_cek['idtopik'];
	
	$p_listpage .= ('&kelas='.$r_kelas.'&topik='.$r_topik);
	
	// cek kelas
	if(Akademik::isMhs()) {
		require_once(Route::getModelPath('krs'));
		
		if(!mKRS::isAmbil($conn,$r_kelas))
			Route::navigate($p_toppage);
	}
	else if(Akademik::isDosen()) {
		require_once(Route::getModelPath('mengajar'));
		
		if(!mMengajar::isAjar($conn,$r_kelas))
			Route::navigate($p_toppage);
	}
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// cek captcha
		$r_captcha = trim(strtolower($_POST['captcha']));
		
		if (empty($_SESSION['captcha']) or $r_captcha != $_SESSION['captcha']) {
			$p_posterr = true;
			$p_postmsg = 'Kata yang dimasukkan tidak sesuai';
		}
		
		if(!$p_posterr) {
			$record = array();
			$record['idforum'] = $r_key;
			$record['creator'] = Modul::getUserName();
			$record['isiforumdetail'] = $_POST['reply'];
			$record['waktuposting'] = date('Y-m-d H:i:s');
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecordDetail($conn,$record,true);
		}
	}
	if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr)
			Route::navigate($p_listpage);
	}
	
	// mendapatkan data
	$a_infokelas = mKelas::getDataSingkat($conn,$r_kelas);
	$a_forum = $p_model::getData($conn,$r_key);
	$a_data = $p_model::getListDetail($conn,$r_key,$r_page,$r_lastpage);
	$p_lastpage = Page::getLastPage();
	
	$p_subtitle = $a_infokelas['kodemk'].' - '.$a_infokelas['namamk'].' ('.$a_infokelas['kelasmk'].')';
	$p_topik = $p_model::getNamaTopik($conn,$r_topik);
	$p_forum = $a_cek['judulforum'];
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
	tinyMCE.init({
		mode: "textareas",
		width: "520",
		height: "250",
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
				<center>
					<div class="ForumCrumbsSmall" align="left" style="width:<?= $p_tbwidth ?>px">
						Forum : <span class="ULink" onclick="goTop()">Diskusi</span>
						&raquo; <span class="ULink" onclick="goTopik()"><?= $p_subtitle ?></span>
						&raquo; <span class="ULink" onclick="goUp()"><?= $p_topik ?></span>
					</div>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<br>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
								<h1><?= $p_title ?> <span class="ViewSubTitle"><?= $p_forum ?></span></h1>
							</div>
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<?	}
								if($c_delete) { ?>
							<div class="right">
								<div class="addButton" style="padding:0 0 3px 7px" title="Hapus Data" onClick="goDelete()">
									<img src="images/delete.png" style="float:none;padding:0px 0px 2px;margin-right:6px">
								</div>
							</div>
							<?	}
								if($c_edit) { ?>
							<div class="right">
								<div class="addButton" style="padding:0 0 3px 7px" title="Edit Data" onClick="goDetail()">
									<img src="images/edit.png" style="float:none;padding:0px 0px 2px;margin-right:6px">
								</div>
							</div>
							<?	} ?>
						</div>
					</header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridNormal" align="center">
					<tr>
						<th width="130">Penulis</th>
						<th>Pesan</th>
					</tr>
					<tr valign="top">
						<td align="center" style="padding:15px 0">
							<img src="images/user/DEFAULT.png"><br>
							<?= $a_forum['creator'] ?>
						</td>
						<td class="ForumItem">
							<div class="ForumItemHead"><?= CStr::formatDateTimeInd($a_forum['waktuposting'],true,true) ?></div>
							<div class="ForumItemContent"><?= $a_forum['isi'] ?></div>
							<div class="ForumItemFoot"><?= $a_forum['userdesc'] ?></div>
						</td>
					</tr>
					<tr class="NoGrid">
						<td colspan="2"></td>
					</tr>
					<?	$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center" style="padding:15px 0">
							<img src="images/user/DEFAULT.png"><br>
							<?= $row['t_updateuser'] ?>
						</td>
						<td class="ForumItem">
							<div class="ForumItemHead"><?= CStr::formatDateTimeInd($row['waktuposting'],true,true) ?></div>
							<div class="ForumItemContent"><?= $row['isiforumdetail'] ?></div>
							<div class="ForumItemFoot"><?= $row['userdesc'] ?></div>
						</td>
					</tr>
					<?	}
						if($i == 0) { ?>
					<tr>
						<td colspan="2" align="center">Belum ada pesan balasan</td>
					</tr>
					<?	}
						if($c_insert) { ?>
					<tr class="NoGrid">
						<td colspan="2" style="padding:0">
							<? include('inc_listnav.php') ?>
						</td>
					</tr>
					<tr class="NoGrid">
						<td colspan="2"></td>
					</tr>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center" class="LeftColumnBG" style="padding:15px 0">
							<img src="images/user/DEFAULT.png"><br>
							<?= Modul::getUserName() ?>
						</td>
						<td align="center" class="LeftColumnBG" style="padding:15px">
							<?= UI::createTextArea('reply','','ControlStyle') ?><br>
							<div style="float:left;padding-left:5px">
								<img title="Ganti kata" src="../includes/cool-php-captcha/captcha.php?<?= mt_rand() ?>" onclick="goRefreshCaptcha(this)" style="border:1px solid #CCC;cursor:pointer">
							</div>
							<div style="float:left;padding-left:25px">
								Sebelum klik Tulis Balasan, masukkan kata di samping:
								<div class="Break"></div>
								<?= UI::createTextBox('captcha','','ControlStyle',40,40) ?>
								<div class="Break"></div>
								<input type="button" value="Tulis Balasan" onclick="goReply()">
							</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="go" id="page" value="<?= $r_page ?>"> <!-- menipu post -->
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

var lastpage = <?= $r_lastpage ?>; // -1;
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goTop() {
	goView("<?= $p_toppage ?>");
}

function goTopik() {
	goView("<?= $p_topikpage ?>&kelas=<?= $r_kelas ?>");
}

function goUp() {
	goView("<?= $p_listpage ?>&kelas=<?= $r_kelas ?>&topik=<?= $r_topik ?>");
}

function goReply() {
	document.getElementById("act").value = "insert";
	goSubmit();
}

function goRefreshCaptcha(elem) {
	elem.src = "../includes/cool-php-captcha/captcha.php?"+Math.random();
}

function goDetail() {
	location.href = detailpage + "&key=<?= $r_key ?>";
}

function goDelete() {
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		goSubmit();
	}
}

</script>
</body>
</html>