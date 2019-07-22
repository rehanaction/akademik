<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// cek apakah sudah login
	if(!Modul::isAuthenticated())
		Route::redirect($conf['menu_path']);
		
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('public'));
	
	// ada aksi ganti role
	$r_aksi = $_REQUEST['act'];
	if($r_aksi == 'chgrole') {
		list($r_role,$r_unit) = explode(':',CStr::removeSpecial($_REQUEST['key']));
		
		Modul::changeRole($r_role,$r_unit);
	}

	$p_model = mPublic;
	
	$a_pengumuman = array();
	$a_pengumuman = $p_model::getPengumuman($conn);
	
	$a_notice = array();
	$a_notice = $p_model::getNotice($conn);
		
	
	// properti halaman
	$p_title = 'Selamat Datang di SIM Human Resources Management';
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<script type="text/javascript" src="scripts/common.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" style="width:60%">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/NEWS.png" onerror="loadDefaultActImg(this)"> Pengumuman
			</div>
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideTitle" colspan="2">Daftar Pengumuman<div class="Break"></div><div class="Break"></div></td>
				</tr>
				<?	
				if (count($a_pengumuman) > 0) {
					foreach($a_pengumuman as $row) { ?>
				<tr>
					<td align="right" colspan="2">Posting : <?= CStr::formatDateInd($row['tglmulai']) ?></td>
				</tr>
				<tr style="border-bottom:1px solid black">
					<td colspan="2">
					<img src="<?= Route::navAddress('img_datathumb&type=pengumuman&id='.$row['idpengumuman']) ?>">
					<div class="Break"></div>
					<div class="SideSubTitle"><?= $row['judulpengumuman'] ?></div>
					<div class="Break"></div>
					<div class="NewsContent"><?= CStr::cBrief($row['isipengumuman']) ?></div>
					<div class="Break"></div>
					<u class="ULink" onclick="javascript:openDetail('<?= $row['idpengumuman'] ?>')">Selengkapnya...</u>
					<div class="Break"></div><div class="Break"></div>
					</td>
				</tr>
				<?	}} ?>
			</table>
			<br>
		</div>
		<div class="SideItem" style="float:right;width:30%">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/INFO.png" onerror="loadDefaultActImg(this)"> INFORMASI
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td colspan="3">
						<img width="16px" src="images/aktivitas/BIODATA.png" onerror="loadDefaultActImg(this)">
						&nbsp; <?= Modul::getUserName() ?> - <?= Modul::getUserDesc() ?>,
						<br><span class="SideSubTitle">Login : </span> <?= CStr::formatDateTimeInd(Modul::getLastLogin(),false,true) ?>
					</td>
				</tr>
			</table>
			<br>
			
			<? if (count($a_notice) > 0) {?>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/NOTIFICATION.png" onerror="loadDefaultActImg(this)"> NOTIFIKASI
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<? foreach($a_notice as $col){?>
				<tr>
					<td colspan="3">
						<img width="16px" src="images/aktivitas/NOTIFICATION.png" onerror="loadDefaultActImg(this)">
						<span class="NewsContent"><?= CStr::cBrief($col['pesan']) ?></span>
						<u class="ULink" onclick="javascript:openDetailNotice('<?= $col['idpesan'] ?>')">Selengkapnya...</u>
						<div class="Break"></div><div class="Break"></div>
					</td>
				</tr>
				<? } ?>
			</table>
			<br>
			<? } ?>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/DOCUMENT.png" onerror="loadDefaultActImg(this)"> USER GUIDE
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td>
						<u class="ULink" onclick="javascript:goDownload('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('ug'); ?>','admin')" target="_blank"><img width="16px" src="images/aktivitas/DOWNLOAD.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download Administrator </span></u> <br />
						<u class="ULink" onclick="javascript:goDownload('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('ug'); ?>','peg')"><img width="16px" src="images/aktivitas/DOWNLOAD.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download Pegawai </span></u>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
</body>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	var detform = "<?= Route::navAddress('pop_pengumuman') ?>";
	var detformnote = "<?= Route::navAddress('pop_notice') ?>";

	function openDetail(pkey){
		$.ajax({
			url: detform,
			type: "POST",
			data: {key : pkey},
			success: function(data){
				$.facybox(data);
			}
		});
	}
	
	function openDetailNotice(pkey){
		$.ajax({
			url: detformnote,
			type: "POST",
			data: {key : pkey},
			success: function(data){
				$.facybox(data);
			}
		});
	}
</script>
</html>
