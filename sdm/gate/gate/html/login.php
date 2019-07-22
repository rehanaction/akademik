<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// cek apakah sudah login
	if(Modul::isAuthenticated())
		Route::navigate('menu');

	// include
	require_once($conf['model_dir'].'m_user.php');

	// proses login
	if(!empty($_POST)) {
		require_once($conf['includes_dir'].'phpmailer/class.pop3.php');
		
		$r_user = $_POST['txtUserID'];
		$r_password = $_POST['txtPassword'];
		if (strpos($r_user,'@') !== false){
			list(,$host) = explode('@',$r_user);
			$pop = new POP3();
			$okpop = $pop->Authorise($host, 110, 30, $r_user, $r_password);
			if($okpop){
				$ok = mUser::logIn($conn,$r_user,$r_password,true);
				if($ok)
					Route::navigate('menu');
			}else
				$alert = "Login gagal. Username atau Password anda salah.";
		}else{
			// mengambil data dari textbox
			$r_user = $_POST['txtUserID'];
			$r_password = $_POST['txtPassword'];
			$ok = mUser::logIn($conn,$r_user,$r_password);
			
			if($ok)
				Route::navigate('menu');
			else
				$alert = "Login gagal. Username atau Password anda salah.";
		}
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Selamat Datang di SIM UNIVERSITAS ESA UNGGUL</title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="stylesheet" type="text/css" href="style/login.css">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>
<body onload="document.getElementById('txtUserID').focus()">
	<div id="main_cont">
		<form name="formlogin" method="post" action="">
		<div id="login">
			<div id="logo"></div>
			<div class="clr"></div>
			<? if(!empty($alert)) { ?>
			<div class="DivError"><?= $alert ?></div>
			<? } ?>
			<div class="single_content">
				<div class="single_left"></div>
				<div class="single_center">
					<div id="login_content">
						<input type="text" name="txtUserID" id="txtUserID" class="usrname" value="" />
						<input type="password" name="txtPassword" class="pass" value="" />
					</div>
				</div>
				<div class="single_right"></div>
			</div>
			<? /* <a id="lupa" href="#">Lupa password</a> */ ?>
			<input type="submit" name="submit" id="submit" value="Log In" />
		</div>
		</form>
	</div>

<script type="text/javascript">
	
main = document.getElementById('main_cont');
var e = window, a = 'inner';
if ( !( 'innerHeight' in window ) )
{
	a = 'client';
	e = document.documentElement || document.body;
}
viewport = e[ a+'Height' ];
content = main.offsetHeight;
main.setAttribute("style","margin-top:"+((viewport-content)/2)+"px");
	
</script>	

</body>
</html>
