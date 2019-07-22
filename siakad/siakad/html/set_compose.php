<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	
	// include
	require_once(Route::getModelPath('user'));
	require_once(Route::getModelPath('pesan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_mode = CStr::removeSpecial($_REQUEST['mode']);
	
	if(empty($r_mode))
		$r_mode == 'reply';
	
	// properti halaman
	$p_title = 'Tulis Pesan';
	$p_tbwidth = 800;
	$p_aktivitas = 'MAIL';
	$p_listpage = 'list_inbox';
	
	$p_model = mPesan;
	
	// cek hak akses
	if(!$c_insert)
		Route::navigate($p_listpage);
	
	// cek data
	if(!empty($r_key)) {
		$a_cek = $p_model::getData($conn,$r_key);
		
		if($a_cek['idpenerima'] == Modul::getUserName()) {
			$r_judul = $a_cek['subyek'];
			
			$r_pesan = $a_cek['pesan'];
			if(substr($r_pesan,0,3) == '<p>')
				$r_pesan = substr($r_pesan,3,strlen($r_pesan)-7);
			
			if($r_mode == 'reply') {
				$r_user = $a_cek['idpengirim'].' - '.$a_cek['namapengirim'];
				$r_userid = $a_cek['idpengirim'];
				$r_judul = 'Re: '.$r_judul;
				
				$r_pesan = '<em>Pesan yang dibalas:</em><br>'.$r_pesan;
			}
			else if($r_mode == 'forward') {
				$r_judul = 'Fwd: '.$r_judul;
				
				$r_pesan = '<em>Pesan yang diteruskan:</em><br>'.$r_pesan;
			}
		}
		else
			$r_key = '';
	}
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_insert) {
		$ok = true;
		$conn->BeginTrans();
		
		$record = array();
		$record['idmailprev'] = CStr::cStrNull($r_key);
		$record['idpengirim'] = Modul::getUserName();
		$record['jenispengirim'] = substr(Modul::getRole(),0,1);
		$record['waktukirim'] = date('Y-m-d H:i:s');
		$record['subyek'] = CStr::cStrNull($_POST['title']);
		$record['pesan'] = $_POST['message'];
		
		$a_tujuan = $_POST['recipient'];
		if(!empty($a_tujuan)) {
			foreach($a_tujuan as $t_tujuan) {
				$t_tujuan = CStr::removeSpecial($t_tujuan);
				
				$record['idpenerima'] = CStr::cStrNull($t_tujuan);
				$record['jenispenerima'] = mUser::getRole($conn,$t_tujuan);
				
				list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
				if($p_posterr) {
					$ok = false;
					break;
				}
			}
		}
		
		$conn->CommitTrans($ok);
		
		if(!$p_posterr)
			Route::navigate('list_outbox');
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
	tinyMCE.init({
		mode: "textareas",
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
						<td onclick="goOutbox()">
							<img src="images/outbox.png" align="left"> &nbsp;
							Pesan Terkirim
						</td>
					</tr>
					<tr>
						<td class="Selected" onclick="goNew()">
							<img src="images/edit.png" align="left"> &nbsp;
							Tulis Pesan
						</td>
					</tr>
				</table>
						</td>
						<td>
				<table border="0" cellspacing="4" align="center">
					<tr>
						<td class="TDButton" onclick="saveData()">
							<img src="images/disk.png"> Simpan
						</td>
						<td class="TDButton" onclick="goReset()">
							<img src="images/undo.png"> Batal
						</td>
					</tr>
				</table>
				<table width="100%" cellpadding="4" cellspacing="2" align="center" class="NoGrid">
					<tr valign="top">
						<td class="LeftColumnBG" width="150">Tujuan *</td>
						<td class="RightColumnBG" id="td_user">
							<?= UI::createTextBox('user',$r_user,'ControlStyle',70,70) ?>
							<input type="hidden" id="userid" value="<?= $r_userid ?>">
						</td>
					</tr>
					<tr valign="top">
						<td class="LeftColumnBG">Judul *</td>
						<td class="RightColumnBG"><?= UI::createTextBox('title',$r_judul,'ControlStyle',70,70) ?></td>
					</tr>
					<tr valign="top">
						<td class="LeftColumnBG">Pesan</td>
						<td class="RightColumnBG"><?= UI::createTextArea('message',$r_pesan,'ControlStyle') ?></td>
					</tr>
				</table>
						</td>
					</tr>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var required = "title";
	
$(document).ready(function() {
	$("#user").xautox({strpost: "f=acuser", targetid: "userid"});
	$("#user").focus();
	
	$("#userid").change(function() {
		addRecipient();
	});
	
	if($("#userid").val() != "") {
		addRecipient();
		$("#title").focus();
	}
	
	$("#td_user").delegate("div","click",function() {
		$(this).remove();
	});
});

function addRecipient() {
	// cek dulu
	var userid = $("#userid").val();
	var userlabel = $("#user").val();
	
	if($("[name='recipient[]'][value='"+userid+"']").length == 0) {
		var div = $("<div>");
		
		div.attr("title","Batalkan tujuan");
		div.addClass("PMRecipient");
		div.append(userlabel);
		div.append('<input type="hidden" name="recipient[]" value="'+userid+'">');
		
		$("#td_user").append(div);
	}
}

function saveData() {
	if($("[name='recipient[]']").length == 0) {
		alert("Tambahkan data tujuan terlebih dahulu");
		$("#user").focus();
	}
	else
		goSave();
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