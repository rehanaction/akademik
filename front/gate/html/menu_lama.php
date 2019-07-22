<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// cek apakah sudah login
	if(!Modul::isAuthenticated())
		Route::navigate('login');
	
	// include
	require_once(Route::getModelPath('user'));
	
	$r_act = $_POST['act'];
	if($r_act == 'changepass') {
		$r_userid = Modul::getUserID();
		$r_oldpass = $_POST['oldpass'];
		$r_newpass = md5($_POST['newpass']);
		
		// cek kebenaran password
		$ok = mUser::cekUserPass($conn,$r_userid,$r_oldpass);
		
		if($ok) {
			$record = array();
			$record['password'] = $r_newpass;
			$record['t_updateact'] = 'ganti_password';
			
			$p_posterr = mUser::updateRecord($conn,$record,$r_userid);
			if($p_posterr)
				$p_postmsg = 'Penggantian password gagal';
			else
				$p_postmsg = 'Penggantian password berhasil';
		}
		else {
			$p_posterr = -1;
			$p_postmsg = 'Password Lama tidak cocok';
		}
	}
	
	$a_data = mUser::getDataAuth($conn,Modul::getUserID());
	$t_datapilih = current($a_data);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie ie6 no-js" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 no-js" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 no-js" lang="en"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 no-js" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>Selamat Datang</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	<link rel="stylesheet" type="text/css" href="style/menu.css" />
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
</head>
<body>
<form id="menuform" method="post">
	<div id="main">
		<div class="clr" style="margin-bottom:20px;"></div>
		<div id="header">
			<h1 class="main_title">Daftar Modul</h1>
			<ul id="navigation">
				<?	foreach($a_data as $t_modul => $t_data) { ?>
				<li onClick="openContent('<?= $t_data['kode'] ?>')" class="<?= $t_data['kode'] ?>">
					<span class="larrow"></span>
					<span class="carrow"><a id="link-<?= $t_data['kode'] ?>" href="#<?= $t_data['kode'] ?>"><?= $t_data['nama'] ?></a></span>
					<span class="rarrow"></span>
				</li>
				<? } ?>
			</ul>
		</div>
		<?	foreach($a_data as $t_modul => $t_data) { ?>
		<div id="<?= $t_data['kode'] ?>" class="panel">
			<div class="content">
				<div style="float:left;" id="img_<?= $t_data['kode'] ?>"></div><h2>Daftar Role <?= ucwords($t_data['nama']) ?></h2>
				<div class="clr" style="margin-bottom:20px;"></div>
				<div class="role_box">
					<? foreach($t_data['data'] as $t_kolom) { ?>
					<span class="role_container" onClick="goAccess('<?= $t_modul ?>','<?= $t_kolom['koderole'] ?>','<?= $t_kolom['kodeunit'] ?>')" style="float:left;width:100%;">
						<span class="rolename"><?= $t_kolom['namarole'] ?> -&nbsp;</span><span class="unitname"><?= $t_kolom['namaunit'] ?></span>
					</span>
					<? } ?>
				</div>
			</div>
		</div>
		<? } ?>
		<div id="password" class="panel">
			<div class="content">
				<div style="float:left;" id="img_password"></div><h2>Ganti Password</h2>
				<div class="clr" style="margin-bottom:20px;"></div>
				<div class="role_box">
				<table width="100%" class="role_container">
					<? if(!empty($p_postmsg)) { ?>
					<tr>
						<td colspan="2" class="<?= $p_posterr ? 'error' : 'success' ?>"><?= $p_postmsg ?></td>
					</tr>
					<?	} ?>
					<tr>
						<td width="170"><span class="rolename">Password Lama</span></td>
						<td><input type="password" name="oldpass" size="20"></td>
					</tr>
					<tr>
						<td><span class="rolename">Password Baru</span></td>
						<td><input type="password" name="newpass" id="newpass" size="20"></td>
					</tr>
					<tr>
						<td><span class="rolename">Password Baru (Ulangi)</span></td>
						<td><input type="password" name="renewpass" id="renewpass" size="20"></td>
					</tr>
					<tr>
						<td align="center" colspan="2" style="padding-top:10px">
							<input type="button" value="Ganti Password" onClick="changePass()" style="cursor:pointer">
						</td>
					</tr>
				</table>
				</div>
			</div>
		</div>
		<div class="clr"></div>
		<div id="bottom">
			<span onClick="openContent('password')" class="password">Ganti Password</span>
			<? /* <span class="profil">Edit Profil</span> */ ?>
			<span onClick="goLogout()" class="out">Logout</span>
		</div>
	</div>
	<input type="hidden" name="act" id="act">
	<input type="hidden" name="sessdata" id="sessdata">
</form>
</body>
<script type="text/javascript">
	var last;
	var now;
	
	function goAccess(modul,role,unit) {
		$.ajax({
			type: "POST",
			url: "<?= Route::navAddress('ajax') ?>",
			data: "c=access&p="+modul+"_"+role+"_"+unit,
			timeout: 20000,
			
			success: function(data) {
				var arr = data.split(":",1);
				var url = arr[0];
				var sessdata = data.substr(url.length+1);
				
				$("#menuform").attr("action",url);
				$("#sessdata").val(sessdata);
				$("#menuform").submit();
			},
			
			error: function(obj,err) {
				if(err == "timeout")
					alert("Proses pindah sistem timeout, coba ulangi lagi.");
			}
		});
	}
	
	function changePass() {
		if($("#newpass").val() == $("#renewpass").val()) {
			$("#act").val("changepass");
			$("#menuform").submit();
		}
		else
			alert("Password Baru dan Password Baru (Ulangi) tidak sama");
	}

	function goLogout() {
		location.href = 'index.php?page=logout';
	}

	function openContent(id) {
		if(id != "") {
			last = now;
			now = id;
			window.location.hash = id;
			
			if(last != '')
				$("."+last).attr("class",last);
				
			$("."+now).attr("class",now+" active");
		}
	}

	$(document).ready(function() {
		$(".HomeTable td").hover(
			function() {
				$(this).children("div:hidden").slideDown("fast");
			},
			function() {
				$(this).children("div:visible").slideUp("fast");
			}
		);
		
		$("[type='password']").keydown(function(e) {
			if(e.keyCode == 13) {
				changePass();
				return false;
			}
			
			return true;
		});
		
		openContent('<?= $r_act == 'changepass' ? 'password' : $t_datapilih['kode'] ?>');
	});
</script>
</html>
