<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Detail Data Pegawai';
	$p_tbwidth = 800;
	$p_aktivitas = 'BIODATA';
	
	$p_model = mPegawai;
	
	$row = $p_model::getDetailPegawai($conn, $r_key);
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
<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:250px;overflow:auto">
<form name="pageformdet" id="pageformdet" method="post">
	<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
		<tr>
			<td colspan="2" class="DataBG" style="border:none;height:25px;"><?= $p_title ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="150px" style="white-space:nowrap">No. Dosen</td>
			<td  class="RightColumnBG" colspan="3"><?= $row['nodosen'] ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="150px" style="white-space:nowrap">Nama Dosen</td>
			<td  class="RightColumnBG" colspan="3"><?= $row['namalengkap'] ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="150px" style="white-space:nowrap">Unit Homebase</td>
			<td  class="RightColumnBG" colspan="3"><?= $row['namaunit'] ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="150px" style="white-space:nowrap">NPWP</td>
			<td  class="RightColumnBG" colspan="3"><?= $row['npwp'] ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="150px" style="white-space:nowrap">No. Rekening</td>
			<td  class="RightColumnBG" colspan="3"><?= $row['norekeninghonor'] ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="150px" style="white-space:nowrap">Atas Nama Rekening</td>
			<td  class="RightColumnBG" colspan="3"><?= $row['anrekeninghonor'] ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="150px" style="white-space:nowrap">Nama Bank</td>
			<td  class="RightColumnBG" colspan="3"><?= $row['namabank'] ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG" width="150px" style="white-space:nowrap">Cabang Bank</td>
			<td  class="RightColumnBG" colspan="3"><?= $row['cabangbankhonor'] ?></td>
		</tr>
	</table>	
</form>
</div>
<script type="text/javascript">
</script>
</body>
</html>
