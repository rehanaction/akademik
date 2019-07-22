<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// cek apakah sudah login
	if(Modul::isAuthenticated())
		Route::navigate('menu');
	
	// include
	require_once(Route::getModelPath('user'));
	
	// proses login
	if(!empty($_POST['txtUserID'])) {
		// mengambil data dari textbox
		$r_user = $_POST['txtUserID'];
		$r_password = $_POST['txtPassword'];
		
		$ok = mUser::logIn($conn,$r_user,$r_password);
		
		if($ok)
			Route::navigate('menu');
		else
			$alert = "Login gagal. Username atau Password anda salah.";
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>Selamat Datang di SIAKAD</title>
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
      <div class="DivError">
        <?= $alert ?>
      </div>
      <? } ?>
      <div class="single_content">
        <div class="single_center">
          <div id="login_content"  style="display:inline-block;">
            <div class="title">
              <h2>Sistem Informasi Manajemen<br/>
                Universitas Wijaya Putra Surabaya</h2>
            </div>
            <div style="width:535px;">
              <div>
                <label for="txtUserID">Username</label>
                <br/>
                <input type="text" name="txtUserID" id="txtUserID" class="usrname" />
              </div>
              <div  style="margin-left:5px;">
                <label for="txtPassword">Password</label>
                <br/>
                <input type="password" name="txtPassword" class="pass" />
              </div>
              <div style="margin-top:18px; margin-left:5px;">
                <input type="submit" name="submit" id="submit" value="Log In" />
              </div>
              <div>Daftar user administrator prodi dapat dilihat <u style="cursor:pointer;color:blue" onclick="goAdmin()">di sini</u></div>
            </div>
          </div>
        </div>
      </div>
      <? /* <a id="lupa" href="#">Lupa password</a> */ ?>
    </div>
  </form>
</div>
<div class="login-bottom">
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

function goAdmin() {
	window.open("index.php?page=admprodi");
}

</script>
</body>
</html>