<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	ob_clean();
?>
<style>
	html, body{
		height:100%;
	}
	body{
		margin:0px;
	}
	#main_wrapper {
		background: #2D724F;
		background: radial-gradient(ellipse farthest-corner at center center , #47B26B 0%, #2D724F 100%) no-repeat fixed 0 0 transparent;
		font-family:Helvetica, Myriad Pro, Tahoma, Arial, sans-serif;
		height: 100%;
		margin:0px;
	}
	center#bg{
		background: url(images/icon.png) no-repeat bottom left;
		background-size:200px;
		height:100%;
	}
	#main_cont{
		margin: 0 auto;
		color:#fff;
		padding:15% 25%;
	}
	h1 {
		font-size: 35px;
		font-weight: bold;
		line-height: 1;
		margin: 30px 0 0;
		text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
		text-align:center;
	}
</style>
<link rel="stylesheet" href="style/buttons.css"/>
<div id="main_wrapper">
	<center id="bg">
		<div id="main_cont">
			<h1>Maaf, halaman tidak tersedia.</h1>
			<br>
			Silahkan hubungi webmaster atau admin untuk informasi lebih lanjut. Terimakasih.
			<br>
			<br><a class="button button-rounded button-flat-caution" href="<?= Route::navAddress('home') ?>">Kembali ke Halaman Utama</a>
		</div>
	</center>
</div>
