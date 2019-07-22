<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('user'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_user = CStr::removeSpecial($_POST['user']);
	$r_labeluser = CStr::removeSpecial($_POST['labeluser']);
	
	if(empty($r_user) and !empty($r_labeluser))
		$r_user = $r_labeluser;
	// properti halaman
	$p_title = 'Login Sebagai...';
	$p_tbwidth = 500;
	$p_aktivitas = 'USER';
	
	$p_model = mUser;
	
	if(!empty($r_user)) {

	// logout
		Modul::logOut();
		//Modul::logOut(GATE_SITE_ID);
		
		// login
		$ok = mUser::logIn($conn,$r_user,false);
		$p_posterr = Query::isErr($ok);
		//echo $conf['menu_path'];die();
		//echo $_SESSION[SITE_ID]['MODUL']['USERID'];die();
//echo Modul::getUserId();die();
if($ok)
			Route::redirect($conf['menu_path']);
		else
			$p_postmsg = "Proses login sebagai <strong>'.$r_labeluser.'</strong> gagal";
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;<?= $p_title ?>
						</span>
					</div>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<strong>Login Sebagai : </strong>
						<?= UI::createTextBox('labeluser',$r_labeluser,'ControlStyle',50,50) ?>
						<input type="hidden" id="user" name="user" value="<?= $r_user ?>">
						<input type="button" value="Login" onclick="goSubmit()">
					</div>
				</center>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	// autocomplete
	$("#labeluser").xautox({strpost: "f=acuser", targetid: "user"});
	$( "#labeluser" ).focus();
});

</script>
</body>
</html>
