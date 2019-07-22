
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
		echo '<script type="text/javascript">alert("Terimakasih telah menggunakan SIM PMB STIE INABA")</script>';
		echo '<script type="text/javascript">window.location.href="index.php?page=home";</script>';
		//Route::navigate('home');
	}
            
?>
<html>
<head>
<title>Penerimaan Mahasiswa Baru</title>
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
			<a href="index.php"><img src="images/logo.png"></a>
		</span>
		<span class="col-md-10 col-sm-10" style="padding: 05px 25px;">
			<h3 style="margin:0;">Penerimaan Mahasiswa Baru</h3>
			Semester Ganjil 2018/2019
		</span>
	  </div>
    </div>
    <nav class="collapse navbar-collapse bs-navbar-collapse pull-right" role="navigation" style="padding: 25px 0;">
      <ul class="nav navbar-nav">
        <li> <a href="index.php">Beranda</a> </li>
<!--
        <li onClick="goView('list_pagu')" style="cursor:pointer;"> <a>Pagu Jurusan</a></li>
-->
        <!--li onClick="goView('list_jadwal')" style="cursor:pointer;"> <a><span class="glyphicon glyphicon-search"></span><br/>
          Informasi</a> </li-->
          <?php if(isset($_SESSION['PENDAFTARAN']['FRONT']['USERID'])) { ?>
<!--
        <li onClick="goView('pengumuman_lulus')" style="cursor:pointer;"> <a><span class="glyphicon glyphicon-info-sign"></span><br/>
          Informasi Kelulusan</a> </li>
-->
          <?php }?>
      </ul>
    </nav>
  </div>
</header>
