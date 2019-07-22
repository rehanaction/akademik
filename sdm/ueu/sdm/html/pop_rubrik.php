<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('bebandosen'));
	
	// variabel request
	$k = CStr::removeSpecial($_REQUEST['k']);
	
	// properti halaman
	$p_title = 'Daftar Rubrik Beban Dosen';
	$p_tbwidth = 600;
	$p_col = 3;
	
	$p_model = mBebanDosen;
	
	$a_data = $p_model::getRubrik($conn,$k);

?>	
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr class="DataBG" height="30px">
		<td align="center" colspan="<?= $p_col?>"><?= $p_title?></td>
	</tr>
		<th width="100">Kode</th>
		<th>Nama Kegiatan</th>
		<th width="100">SKS</th>
	</tr>
	<?							
		$i = 0;
		if(count($a_data)>0){
			foreach($a_data as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
	?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td align="center">
			<u onClick="goSend('<?= $row['idjeniskegiatan'] ?>','<?= $row['kodekegiatan'].' - '.preg_replace("/[^a-z0-9_\-\.]/i"," ",$row['namakegiatan']) ?>','<?= $row['sksmax'] ?>');" style="cursor:pointer;color:#3300FF;"><?= $row['kodekegiatan']; ?></u>
		</td>
		<td><?= $row['namakegiatan']?></td>
		<td align="center"><?= $row['sksmax']?></td>
	</tr>
	<?		}
		}
		if($i == 0) {
	?>
	<tr>
		<td colspan="<?= $p_col?>" align="center">Data kosong</td>
	</tr>
	<?	} ?>
	<tr>
		<td colspan="<?= $p_col?>" align="right" class="FootBG">&nbsp;</td>
	</tr>
</table>
				
<script type="text/javascript">

function goSend(idjeniskegiatan,namakegiatan,sksmax) {
	window.opener.document.getElementById("idjeniskegiatan").value = idjeniskegiatan;
	window.opener.document.getElementById("namakegiatan").value = namakegiatan;
	window.opener.document.getElementById("sks").value = sksmax;
	window.close();
}
</script>
</body>
</html>
