<?php

	header("Location: https://portal.inaba.ac.id");
  
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// cek apakah sudah login
	if(Modul::isAuthenticated())
		Route::navigate('menu');

	// include
	require_once(Route::getModelPath('user'));
	
	// variabel request
	$ok = $_SESSION[SITE_ID]['OK'];
	$alert = $_SESSION[SITE_ID]['ALERT'];
	unset($_SESSION[SITE_ID]['OK'],$_SESSION[SITE_ID]['ALERT']);
  
	// proses login
  if(!empty($_POST['txtUserID'])) {
    //$conn->debug=true;
    // mengambil data dari textbox
    $r_user = $_POST['txtUserID'];
    $r_password = $_POST['txtPassword'];
    //if($r_user == '201432022')
      //$conn->debug = true;
    $ok = mUser::logIn($conn,$r_user,$r_password);
    //print_r($ok);
    //if($r_user == '201432022'){
      //ini_set('display_errors',true);
      //die('kena');
    //}
    if($ok)
      Route::navigate('menu');
    else
      $alert = "Login gagal. Username atau Password anda salah.";
  }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title>Selamat Datang di Sistem Informasi Manajemen</title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<!-- Mobile Specific Metas======================================== -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>

<link rel="stylesheet" type="text/css" href="style/login.css">
<link rel="icon" type="image/x-icon" href="images/favicon.png">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen"/>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script type="text/javascript" src="js/libs/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>


</head>
<body onload="document.getElementById('txtUserID').focus()">
<div class="container"><!--wrapping konten-->
    	<div id="main_cont" class="well well-sm center-vertical">
          <form name="formlogin" method="post" action="">
            <div id="login" class="col-md-12">
              <div id="logo"><img class="img-responsive" src="images/login_back.jpg"/></div>
              <div class="clr"></div>
            </div><!--login-->
            <div class="col-md-12 color-divider">
            </div><!--strip color divider-->
            <div id="login_content" class="col-md-12 row">
              <div class="title col-sm-12 col-md-5 margin-up20px" >
                <h2>Sistem Informasi Manajemen <br/>
                  <strong><font size="3passionate">SEKOLAH TINGGI ILMU EKONOMI INABA</font></strong>
                </h2>
                
              </div>
              <div class="col-sm-12 col-md-7 row margin-up20px">
                <div class="teks-yellow col-sm-6 col-md-4">
                  <label for="txtUserID">Username</label>
                  <br/>
                  <input type="text" name="txtUserID" id="txtUserID" class="usrname" />
                </div>
                <div class="teks-yellow col-sm-6 col-md-4" style="height: 0;">
                  <label for="txtPassword">Password</label>
                  <br/>
                  <input type="password" name="txtPassword" class="pass" />
                </div>
                <div class="col-md-4 pull-right">
                  <input type="submit" name="submit" id="submit" value="Log In" />
                </div>
              </div><!--col-md-8-->
                <br/>
                <? if(!empty($alert)) { ?>
                <div class="<?= empty($ok) ? 'DivError' : 'DivSuccess' ?>">
                  <?= $alert ?>
                </div>
                <? } ?>

              </div><!--login content-->
          </form>
        </div><!--main-content-->
        <div class="login-bottom">
        </div><!--login-bottom-->
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

function goSiakad() {
	window.open("<?= $conf['view_dirsiakad'];?>");
}

</script>
</body>
</html>
