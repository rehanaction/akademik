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
	require_once(Route::getModelPath('pesan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_mail = CStr::removeSpecial($_REQUEST['mail']);
	
	// properti halaman
	$p_title = 'Pesan Terkirim';
	$p_tbwidth = 800;
	$p_aktivitas = 'MAIL';
	$p_partnav = true;
	
	$p_model = mPesan;
	
	// cek data
	if(!empty($r_mail)) {
		$a_cek = $p_model::getData($conn,$r_mail);
		
		if($a_cek['idpengirim'] != Modul::getUserName()) {
			$r_mail = '';
			unset($a_cek);
		}
	}
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'outbox');
	}
	else if(substr($r_act,0,6) == 'check_') {
		$a_centang = $_POST['check'];
		if(!empty($a_centang)) {
			foreach($a_centang as $i => $t_centang)
				$a_centang[$i] = CStr::removeSpecial($t_centang);
			
			$conn->BeginTrans();
			
			$r_subact = substr($r_act,6);
			if($r_subact == 'delete' and $c_delete)
				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$a_centang,'outbox');
		
			$ok = ($p_posterr ? false : true);
			$conn->CommitTrans($ok);
		}
	}
	
	// ambil data halaman
	if(empty($r_mail)) {
		$r_page = Page::setPage($_POST['page']);
		
		$a_data = $p_model::getListOutbox($conn,$r_page);
		$p_lastpage = Page::getLastPage();
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	if(!empty($p_postmsg)) { ?>
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
								<h1><?= $p_title ?> <span class="ViewSubTitle"><?= $p_subtitle ?></span></h1>
							</div>
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<? } ?>
						</div>
					</header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="10" cellspacing="0" class="GridNormal" align="center">
					<tr valign="top">
						<td align="center" class="PMProfil">
							<img src="images/user/DEFAULT.png"><br>
							<?= Modul::getUserName() ?>
							<div style="height:20px"></div>
				<table width="100%" cellpadding="4" cellspacing="0" class="GridNormal" align="center">
					<tr>
						<td onclick="goInbox()">
							<img src="images/inbox.png" align="left"> &nbsp;
							Kotak Masuk
						</td>
					</tr>
					<tr>
						<td class="Selected" onclick="goOutbox()">
							<img src="images/outbox.png" align="left"> &nbsp;
							Pesan Terkirim
						</td>
					</tr>
					<tr>
						<td onclick="goNew()">
							<img src="images/edit.png" align="left"> &nbsp;
							Tulis Pesan
						</td>
					</tr>
				</table>
						</td>
						<td>
				<?	if(empty($r_mail)) {
						if($c_delete) {
				?>
				<strong>Pesan tercentang:</strong> &nbsp;&nbsp;
				<u class="ULink" onclick="goHapusCentang()">Hapus</u> &nbsp;&nbsp;
				<div class="Break"></div>
				<?		} ?>
				<table width="100%" cellpadding="4" cellspacing="0" class="GridNormal" align="center">
					<tr>
						<th align="center" width="20"><input type="checkbox" id="checkall"></th>
						<th width="150">Penerima</th>
						<th>Pesan</th>
						<th width="130">Waktu Kirim</th>
						<th width="50">Aksi</th>
					</tr>
					<?	$i = 0;
						foreach($a_data as $row) {
							// if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG';
							$rowstyle = 'NormalBG'; $i++;
							
							$t_key = $p_model::getKeyRow($row);
					?>
					<tr class="<?= $rowstyle ?>">
						<td align="center"><input type="checkbox" name="check[]" value="<?= $t_key ?>"></td>
						<td><?= $row['userdesc'] ?></td>
						<td><?= $row['subyek'] ?></td>
						<td><?= CStr::formatDateDiff($row['waktukirim']) ?></td>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Pesan" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
							<?	if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
							<?	} ?>
						</td>
					</tr>
				<?		}
						if($i == 0) { ?>
					<tr>
						<td colspan="5" align="center">Data kosong</td>
					</tr>
				<?		} ?>
				</table>
				<?		include('inc_listnav.php');
					}
					else { ?>
				<table width="100%" cellpadding="4" cellspacing="0" class="NoGrid">
					<tr valign="top">
						<td width="80"><strong>Penerima</strong></td>
						<td width="10" align="center"><strong>:</strong></td>
						<td><?= $a_cek['namapenerima'] ?></td>
					</tr>
					<tr valign="top">
						<td><strong>Judul</strong></td>
						<td align="center"><strong>:</strong></td>
						<td><?= $a_cek['subyek'] ?></td>
					</tr>
					<tr valign="top">
						<td><strong>Waktu Kirim</strong></td>
						<td align="center"><strong>:</strong></td>
						<td><?= CStr::formatDateTimeInd($a_cek['waktukirim'],true,true) ?></td>
					</tr>
					<tr>
						<td colspan="3">
				<table border="0" cellspacing="4">
					<tr>
						<td class="TDButton" onclick="goOutbox()">
							<img src="images/list.png"> Daftar
						</td>
						<td id="<?= $r_mail ?>" class="TDButton" onclick="goDelete(this)">
							<img src="images/delete.png"> Hapus
						</td>
					</tr>
				</table>
				<div class="PMMessage"><?= $a_cek['pesan'] ?></div>
						</td>
					</tr>
				</table>
				<?	} ?>
						</td>
					</tr>
				</table>
				
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var lastpage = -1;
	
$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$("#checkall").click(function() {
		$("[name='check[]']").attr("checked",this.checked);
	});
});

function goHapusCentang() {
	document.getElementById("act").value = "check_delete";
	goSubmit();
}

function goDetail(elem) {
	goView('list_outbox&mail='+elem.id);
}

function goReply(elem) {
	goView('set_compose&key='+elem.id+'&mode=reply');
}

function goForward(elem) {
	goView('set_compose&key='+elem.id+'&mode=forward');
}

function goInbox() {
	goView('list_inbox');
}

function goOutbox() {
	goView('list_outbox');
}

function goNew() {
	goView('set_compose');
}

</script>
</body>
</html>