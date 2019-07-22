<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once(Route::getModelPath('user'));
	
	// mengambil data
	$a_user = mUser::getListAdminProdi($conn);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Daftar Admin Prodi SIAKAD</title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>
<body>
	<div align="center">
	<p>Berikut ini adalah daftar user admin prodi yang bisa digunakan. Password awalnya adalah kosong (tidak diisi).</p>
	<table width="640" cellpadding="4" cellspacing="0" class="GridStyle">
		<tr>
			<th>Username</th>
			<th>Prodi</th>
			<th>Fakultas</th>
		</tr>
	<?	$i = 0;
		foreach($a_user as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
	?>
		<tr valign="top" class="<?= $rowstyle ?>">
			<td><?= $row['username'] ?></td>
			<td><?= $row['namaunit'] ?></td>
			<td><?= $row['fakultas'] ?></td>
		</tr>
	<?	} ?>
	</table>
	</div>

<script type="text/javascript">

function goLogin() {
	window.open("index.php?page=login");
}

</script>	

</body>
</html>