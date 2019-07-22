<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Seminar Universitas Esa Unggul Jakarta</title>
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
<link href="styles/calendar.css" rel="stylesheet" type="text/css">
<link href="styles/custom.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="scripts/jquery.js"></script>
<script type="text/javascript" src="scripts/bootstrap.js"></script>
<script type="text/javascript" src="scripts/common.js"></script>
<script type="text/javascript" src="scripts/foredit.js"></script>
<script type="text/javascript" src="scripts/calendar.js"></script>
<script type="text/javascript" src="scripts/calendar-id.js"></script>
<script type="text/javascript" src="scripts/calendar-setup.js"></script>
<script type="text/javascript" src="scripts/countdown.js"></script>
<style>
hr {margin:3px 0 3px 0}
</style>
</head>
<body >
<header class="navbar" role="banner">
	<div class="container">
		<div class="navbar-header col-md-6 col-sm-12">
			<button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
			<div class="navbar-brand">
				<span class="col-md-2 col-sm-2" style="padding-left:0px;">
					<a href="home.php"><img src="images/esa-unggul-logo.jpg" style="width: 100px;height: 100px;"></a>
				</span>
				<span class="col-md-10 col-sm-10" style="padding: 15px 35px;">
					<h2 style="margin:0;">Seminar</h2>
					Universitas Esa Unggul Jakarta
				</span>
			</div>
		</div>
		<div class="col-md-1">
			<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
				<ul class="nav navbar-nav">
					<li> <a href="<?php echo Route::navAddress('home') ?>"><small class="glyphicon glyphicon-home"></small> Beranda</a> </li>
				</ul>
			</nav>
		</div>   

		<div class="col-md-5 col-sm-12 search" align="right">
			<form class="form-inline" role="search">
				<div class="form-group">
					<input type="hidden" name="page" value="search">
					<input type="text" name="q" class="form-control" placeholder="Cari Seminar / Event di sini...">
				</div>
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Cari</button>
			</form>
		</div>
		
	</div>
</header>