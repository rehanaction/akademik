<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	Modul::getFileAuth(null,true);
	
	// include
	require_once(Route::getModelPath('ta'));
	
	// properti halaman
	$p_title = 'Beban Bimbingan Dosen';
	$p_tbwidth = 500;
	$p_aktivitas = 'KULIAH';
	
	// mendapatkan data
	$a_data = mTa::getBebanPembimbing($conn);
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
<div style="width:<?= $p_tbwidth+30 ?>px;height:500px;overflow:auto">
<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<td colspan="4" class="DataBG"><?= $p_title ?></td>
	</tr>
	<tr>
		<th>No.</th>
		<th>NIP</th>
		<th>Nama</th>
		<th>Beban</th>
	</tr>
	<?	$i = 0;
		foreach($a_data as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
	?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td align="right"><?= $i ?>.</td>
		<td><?= $row['nip'] ?></td>
		<td><?= $row['namadosen'] ?></td>
		<td><?= $row['jumlahbimbingan'] ?></td>
	</tr>
	<?	}
		if($i == 0) {
	?>
	<tr>
		<td colspan="4" align="center">Data kosong</td>
	</tr>
	<?	} ?>
</table>
</div>
</body>
</html>