
<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
 
	require_once(Route::getModelPath('berita'));
	require_once(Route::getModelPath('jalur'));
	require_once(Route::getModelPath('pagu'));
	require_once(Route::getModelPath('periode'));
	require_once(Route::getModelPath('berita'));
	$page=1;
	if(isset($_GET['p'])){
		$page=$_GET['p'];
	}
	
	$jalur=mJalur::getJalurAktif($conn);
            
	if(isset($_POST['login'])){
		$user=CStr::removeSpecialAll($_POST['nopendaftaran']);
		$pass=CStr::removeSpecialAll($_POST['password']);
		
		$log=Modul::logInFront($conn, $user, $pass);
		if($log){
			Route::navigate('data_input');
		}else {
			$msg="No.Pendaftaran atau Password Anda tidak dikenali";
		}
		
	}if (isset($_POST['logout'])){
		Modul::logOutFront();
		echo '<script type="text/javascript">alert("Terimakasih telah menggunakan SIM PMB ESA UNGGUL")</script>';
		echo '<script type="text/javascript">window.location.href="index.php?page=home";</script>';
		//Route::navigate('home');
	}
            
?>
<html>
<head>
<title>Pendaftaran Universitas Esa Unggul Jakarta</title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<link href="images/favicon.png" rel="icon" type="image/x-icon" >
<link href="styles/home.css" rel="stylesheet" type="text/css" >
<link href="styles/finish.css" rel="stylesheet" type="text/css" >
<link href="styles/daftar.css" rel="stylesheet" type="text/css" >
<link href="styles/style.css" rel="stylesheet" type="text/css">
<link href="styles/officexp.css" rel="stylesheet" type="text/css">
<link href="styles/bootstrap.css" rel="stylesheet" type="text/css">
<link href="styles/home.css" rel="stylesheet" type="text/css">
<link href="styles/style.css" rel="stylesheet" type="text/css" >
<script type="text/javascript" src="scripts/jquery.js"></script>
<script type="text/javascript" src="scripts/bootstrap.js"></script>
<script type="text/javascript" src="scripts/common.js"></script>
<script type="text/javascript" src="scripts/foredit.js"></script>
<script type="text/javascript" src="scripts/calendar.js"></script>
<script type="text/javascript" src="scripts/calendar-id.js"></script>
<script type="text/javascript" src="scripts/calendar-setup.js"></script>
<link href="styles/calendar.css" rel="stylesheet" type="text/css">
<!--link href="style/ui-lightness/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css"-->
<!--script src="scripts/jquery-ui/jquery-ui-1.10.3.custom.js"></script-->
<script type="text/javascript" src="scripts/countdown.js"></script>

<style>
hr {margin:3px 0 3px 0}

</style>

</head>
<?
           # }
            ?>
<body >
<header class="navbar" role="banner">
  <div class="container">
    <div class="navbar-header col-md-6 col-sm-12">
      <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <div class="navbar-brand">
		<span class="col-md-2 col-sm-2" style="padding-left:0px;">
			<a href="home.php"><img src="images/logo.png"></a>
		</span>
		<span class="col-md-10 col-sm-10" style="padding: 15px 35px;">
			<h2 style="margin:0;">Seminar</h2>
			Universitas Esa Unggul Jakarta
		</span>
	  </div>
    </div>
    
      <div class="col-md-1"><nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
			<ul class="nav navbar-nav">
				<li> <a href="index.php"><small class="glyphicon glyphicon-home"></small> Beranda</a> </li>
			</ul>
		</nav>
      </div>   
    <div class="col-md-5 col-sm-12 search" align="right">
      <form class="form-inline" role="search">
      <div class="form-group">
        <input type="text" class="form-control" placeholder="Cari Seminar / Event di sini...">
      </div>
          <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Cari</button>
          </form>
      </div>
  </div>
</header>
