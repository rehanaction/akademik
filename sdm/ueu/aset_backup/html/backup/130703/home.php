<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// cek apakah sudah login
	if(!Modul::isAuthenticated())
		Route::redirect($conf['menu_path']);
	
	// ada aksi ganti role
	$r_aksi = $_REQUEST['act'];
	if($r_aksi == 'chgrole') {
		list($r_role,$r_unit) = explode(':',CStr::removeSpecial($_REQUEST['key']));
		
		Modul::changeRole($r_role,$r_unit);
	}
	
	// properti halaman
	$p_title = 'Selamat Datang di SIM Aset';
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<div class="ViewTitle">Selamat Datang di SIM Aset Universitas Esa Unggul</div>
			<br>
			<table class="filterTable" width="100%">
				<tr>
					<td>
						Assalamualaikum Wr. Wb.<br><br>
						Selamat Datang di Sistem Informasi Manajemen Ast Universitas Esa Unggul<br><br>
						Wassalamualaikum Wr. Wb. 
					</td>
				</tr>
			</table>
			
		</div>
	</div>
</div>
</body>
</html>
